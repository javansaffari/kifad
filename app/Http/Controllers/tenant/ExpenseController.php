<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Transaction;
use App\Models\Tenant\Category;
use App\Models\Tenant\Account;
use App\Models\Tenant\Person;
use App\Models\Tenant\Tag;

class ExpenseController extends Controller
{
    /** Display all expenses */
    public function index()
    {
        $expenses = Transaction::where('type', 'expense')
            ->with(['mainCategory', 'subCategory', 'fromAccount', 'person'])
            ->latest()
            ->get();

        $categories = Category::where('type', 'expense')->get()->groupBy('parent_id');
        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();

        // Prepare chart data by main category
        $chartData = [];
        foreach ($expenses as $ex) {
            $mainCat = $ex->mainCategory?->name ?? 'نامشخص';
            $chartData[$mainCat] = ($chartData[$mainCat] ?? 0) + $ex->amount;
        }

        return view('tenant.expenses.index', compact('expenses', 'categories', 'accounts', 'persons', 'tags', 'chartData'));
    }

    /** Show form to create expense */
    public function create()
    {
        $categories = Category::where('type', 'expense')->get()->groupBy('parent_id');
        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();

        return view('tenant.expenses.create', compact('categories', 'accounts', 'persons', 'tags'));
    }

    /** Store new expense */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'date' => 'required|date',
            'category' => 'required|exists:categories,id',
            'subcategory' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'account' => 'required|exists:accounts,id',
            'person' => 'nullable|exists:persons,id',
            'desc' => 'nullable|string',
        ]);

        // Deduct amount from account balance
        $account = Account::findOrFail($data['account']);
        $account->balance -= $data['amount'];
        $account->save();

        // Add new tags if they don't exist
        $tags = [];
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tags[] = $tag->name;
            }
        }

        Transaction::create([
            'type' => 'expense',
            'amount' => $data['amount'],
            'date' => $data['date'],
            'main_category_id' => $data['category'],
            'sub_category_id' => $data['subcategory'] ?? null,
            'from_account_id' => $data['account'],
            'person_id' => $data['person'] ?? null,
            'description' => $data['desc'] ?? '-',
            'tags' => $tags,
        ]);

        return redirect()->route('tenant.expenses')->with('success', 'هزینه با موفقیت ایجاد شد.');
    }

    /** Show form to edit an expense */
    public function edit(Transaction $transaction)
    {
        $categories = Category::where('type', 'expense')->get()->groupBy('parent_id');
        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();


        return view('tenant.expenses.edit', compact('transaction', 'categories', 'accounts', 'persons', 'tags'));
    }

    /** Update an expense */
    public function update(Request $request, Transaction $expense)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'date' => 'required|date',
            'category' => 'required|exists:categories,id',
            'subcategory' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'account' => 'required|exists:accounts,id',
            'person' => 'nullable|exists:persons,id',
            'desc' => 'nullable|string',
        ]);

        // Restore previous amount to account balance
        $oldAccount = Account::findOrFail($expense->from_account_id);
        $oldAccount->balance += $expense->amount;
        $oldAccount->save();

        // Deduct new amount from selected account
        $newAccount = Account::findOrFail($data['account']);
        $newAccount->balance -= $data['amount'];
        $newAccount->save();

        // Handle tags
        $tags = [];
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tags[] = $tag->name;
            }
        }

        $expense->update([
            'amount' => $data['amount'],
            'date' => $data['date'],
            'main_category_id' => $data['category'],
            'sub_category_id' => $data['subcategory'] ?? null,
            'from_account_id' => $data['account'],
            'person_id' => $data['person'] ?? null,
            'description' => $data['desc'] ?? '-',
            'tags' => $tags,
        ]);

        return redirect()->route('tenant.expenses')->with('success', 'هزینه با موفقیت به‌روزرسانی شد.');
    }

    /** Delete an expense */
    public function destroy(Transaction $expense)
    {
        // Restore amount to account
        $account = Account::findOrFail($expense->from_account_id);
        $account->balance += $expense->amount;
        $account->save();

        $expense->delete();
        return redirect()->route('tenant.expenses')->with('success', 'Expense deleted successfully.');
    }

    /** Show a single expense */
    public function show(Transaction $expense)
    {
        return view('tenant.expenses.show', compact('expense'));
    }
}
