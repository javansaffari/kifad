<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Transaction;
use App\Models\Tenant\Category;
use App\Models\Tenant\Account;
use App\Models\Tenant\Person;
use App\Models\Tenant\Tag;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['mainCategory', 'subCategory', 'fromAccount', 'person'])
            ->latest()
            ->paginate(15);

        $expenseCategories = Category::where('type', 'expense')->get()->groupBy('parent_id');
        $incomeCategories = Category::where('type', 'income')->get()->groupBy('parent_id');

        $accounts = Account::all();
        $persons = Person::all();
        $tags = Tag::pluck('name')->toArray();

        // Prepare chart data
        $incomeChartData = [];
        $expenseChartData = [];

        foreach ($transactions as $tr) {
            $mainCat = $tr->mainCategory?->name ?? 'نامشخص';

            if ($tr->type === 'income') {
                $incomeChartData[$mainCat] = ($incomeChartData[$mainCat] ?? 0) + $tr->amount;
            } else {
                $expenseChartData[$mainCat] = ($expenseChartData[$mainCat] ?? 0) + $tr->amount;
            }
        }

        return view('tenant.transactions.index', compact(
            'transactions',
            'expenseCategories',
            'incomeCategories',
            'accounts',
            'persons',
            'tags',
            'incomeChartData',
            'expenseChartData'
        ));
    }

    public function storeTransfer(Request $request)
    {
        // Validate the transfer form inputs
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'date' => 'required|date',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'person_id' => 'nullable|exists:persons,id',
            'description' => 'nullable|string',
        ]);

        // Find the source and destination accounts
        $fromAccount = Account::findOrFail($data['from_account_id']);
        $toAccount = Account::findOrFail($data['to_account_id']);

        // Check if the source account has enough balance
        if ($fromAccount->balance < $data['amount']) {
            return back()->withErrors(['amount' => 'موجودی حساب مبدا کافی نیست.'])->withInput();
        }

        // Deduct the amount from the source account
        $fromAccount->balance -= $data['amount'];
        $fromAccount->save();

        // Add the amount to the destination account
        $toAccount->balance += $data['amount'];
        $toAccount->save();

        // Create the transfer transaction
        Transaction::create([
            'type' => 'transfer',
            'amount' => $data['amount'],
            'date' => $data['date'],
            'from_account_id' => $data['from_account_id'],
            'to_account_id' => $data['to_account_id'],
            'person_id' => $data['person_id'] ?? null,
            'description' => $data['description'] ?? '-',
            'main_category_id' => 1,
        ]);

        // Redirect back with success message
        return redirect()->route('tenant.transactions.index')
            ->with('success', 'انتقال با موفقیت انجام شد.');
    }
}
