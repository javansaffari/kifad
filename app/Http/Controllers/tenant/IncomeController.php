<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Transaction;
use App\Models\Tenant\Category;
use App\Models\Tenant\Account;
use App\Models\Tenant\Person;
use App\Models\Tenant\Tag;

class IncomeController extends Controller
{
    /** Display all incomes */
    public function index()
    {
        $incomes = Transaction::where('type', 'income')
            ->with(['mainCategory', 'subCategory', 'toAccount', 'person'])
            ->latest()
            ->get();

        $categories = Category::where('type', 'income')->get()->groupBy('parent_id');
        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();

        // Prepare chart data by main category
        $chartData = [];
        foreach ($incomes as $inc) {
            $mainCat = $inc->mainCategory?->name ?? 'نامشخص';
            $chartData[$mainCat] = ($chartData[$mainCat] ?? 0) + $inc->amount;
        }

        return view('tenant.income.index', compact('incomes', 'categories', 'accounts', 'persons', 'tags', 'chartData'));
    }

    /** Show form to create income */
    public function create()
    {
        $categories = Category::where('type', 'income')->get()->groupBy('parent_id');
        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();

        return view('tenant.income.create', compact('categories', 'accounts', 'persons', 'tags'));
    }

    /** Store new income */
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

        // Add amount to account balance
        $account = Account::findOrFail($data['account']);
        $account->balance += $data['amount'];
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
            'type' => 'income',
            'amount' => $data['amount'],
            'date' => $data['date'],
            'main_category_id' => $data['category'],
            'sub_category_id' => $data['subcategory'] ?? null,
            'to_account_id' => $data['account'], // به حساب اضافه می‌شود
            'person_id' => $data['person'] ?? null,
            'description' => $data['desc'] ?? '-',
            'tags' => $tags,
        ]);

        return redirect()->route('tenant.income')->with('success', 'درآمد با موفقیت ایجاد شد.');
    }

    /** Show form to edit an income */
    public function edit(Transaction $income)
    {
        $categories = Category::where('type', 'income')->get()->groupBy('parent_id');
        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();

        return view('tenant.income.edit', compact('income', 'categories', 'accounts', 'persons', 'tags'));
    }

    /** Update an income */
    public function update(Request $request, Transaction $income)
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
        $oldAccount = Account::findOrFail($income->to_account_id);
        $oldAccount->balance -= $income->amount; // کم می‌کنیم
        $oldAccount->save();

        // Add new amount to selected account
        $newAccount = Account::findOrFail($data['account']);
        $newAccount->balance += $data['amount'];
        $newAccount->save();

        // Handle tags
        $tags = [];
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tags[] = $tag->name;
            }
        }

        $income->update([
            'amount' => $data['amount'],
            'date' => $data['date'],
            'main_category_id' => $data['category'],
            'sub_category_id' => $data['subcategory'] ?? null,
            'to_account_id' => $data['account'],
            'person_id' => $data['person'] ?? null,
            'description' => $data['desc'] ?? '-',
            'tags' => $tags,
        ]);

        return redirect()->route('tenant.income.index')->with('success', 'درآمد با موفقیت به‌روزرسانی شد.');
    }

    /** Delete an income */
    public function destroy(Transaction $income)
    {
        // Restore amount to account
        $account = Account::findOrFail($income->to_account_id);
        $account->balance -= $income->amount;
        $account->save();

        $income->delete();
        return redirect()->route('tenant.income.index')->with('success', 'درآمد حذف شد.');
    }

    /** Show a single income */
    public function show(Transaction $income)
    {
        return view('tenant.income.show', compact('income'));
    }
}
