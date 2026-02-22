<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Debt;
use App\Models\Tenant\Account;
use App\Models\Tenant\Person;
use App\Models\Tenant\Tag;
use App\Models\Tenant\Transaction;

class DebtController extends Controller
{
    // Display debts with chart
    public function index()
    {
        $accounts = Account::all();
        $persons  = Person::all();
        $tags     = Tag::all();

        $debts = Debt::with(['account', 'person'])->latest()->get();

        // Prepare chart data
        $chartData = [
            'بدهی (قرض گرفته شده)' => $debts->where('type', 'borrow')->sum('amount'),
            'طلب (قرض داده شده)'   => $debts->where('type', 'lend')->sum('amount'),
        ];

        return view('tenant.debts.index', compact('debts', 'accounts', 'persons', 'tags', 'chartData'));
    }

    // Store new debt or lend
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:borrow,lend',
            'amount' => 'required|integer|min:1',
            'due_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'person_id' => 'required|exists:persons,id',
            'tags' => 'nullable|array',
            'description' => 'nullable|string',
            'create_transaction' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {

            // 1️⃣ Create debt with paid_amount = 0
            $debt = Debt::create([
                'type' => $request->type,
                'amount' => $request->amount,
                'due_date' => $request->due_date,
                'account_id' => $request->account_id,
                'person_id' => $request->person_id,
                'tags' => $request->tags,
                'description' => $request->description,
                'paid_amount' => 0,
            ]);

            // 2️⃣ Optional: create transaction immediately WITHOUT marking debt as paid
            if ($request->boolean('create_transaction')) {
                $account = Account::findOrFail($request->account_id);
                $isBorrow = $request->type === 'borrow';

                // Auto-generate professional description
                $autoDescription = $isBorrow
                    ? "دریافت {$request->amount} ریال قرض از {$debt->person->name} (حساب: {$account->title})"
                    : "پرداخت {$request->amount} ریال طلب به {$debt->person->name} (حساب: {$account->title})";

                Transaction::create([
                    'type' => $isBorrow ? 'income' : 'expense',
                    'amount' => $request->amount,
                    'date' => now()->toDateString(),
                    'main_category_id' => 1,
                    'sub_category_id' => null,
                    'tags' => $request->tags,
                    'from_account_id' => $isBorrow ? null : $account->id,
                    'to_account_id' => $isBorrow ? $account->id : null,
                    'person_id' => $request->person_id,
                    'description' => $autoDescription,
                ]);

                // Update account balance based on type
                if ($isBorrow) {
                    $account->balance += $request->amount;
                } else {
                    $account->balance -= $request->amount;
                }
                $account->save();
            }
        });

        return back()->with('success', 'بدهی/طلب با موفقیت ثبت شد.');
    }

    // Edit debt
    public function edit(Debt $debt)
    {
        $accounts = Account::all();
        $persons  = Person::all();
        $tags     = Tag::all();

        return view('tenant.debts.edit', compact('debt', 'accounts', 'persons', 'tags'));
    }

    // Update debt
    public function update(Request $request, Debt $debt)
    {
        $request->validate([
            'type' => 'required|in:borrow,lend',
            'amount' => 'required|integer|min:1',
            'due_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'person_id' => 'required|exists:persons,id',
        ]);

        $debt->update($request->only([
            'type',
            'amount',
            'due_date',
            'account_id',
            'person_id',
            'tags',
            'description'
        ]));

        return back()->with('success', 'بدهی/طلب با موفقیت بروزرسانی شد.');
    }

    // Delete debt
    public function destroy(Debt $debt)
    {
        $debt->delete();
        return back()->with('success', 'بدهی/طلب با موفقیت حذف شد.');
    }

    // Record a payment
    public function pay(Request $request, Debt $debt)
    {
        $remaining = $debt->amount - $debt->paid_amount;

        $request->validate([
            'amount' => "required|numeric|min:1|max:$remaining",
            'account_id' => 'required|exists:accounts,id',
        ]);

        DB::transaction(function () use ($request, $debt) {

            $account = Account::findOrFail($request->account_id);
            $isBorrow = $debt->type === 'borrow';

            // 1️⃣ Update debt paid_amount
            $debt->paid_amount += $request->amount;
            $debt->save();

            // 2️⃣ Auto-generate professional description for transaction
            $autoDescription = $isBorrow
                ? "پرداخت {$request->amount} ریال بدهی به {$debt->person->name} (حساب: {$account->title})"
                : "دریافت {$request->amount} ریال طلب از {$debt->person->name} (حساب: {$account->title})";

            // 3️⃣ Create related transaction with the professional description
            Transaction::create([
                'type' => $isBorrow ? 'expense' : 'income',
                'amount' => $request->amount,
                'date' => now()->toDateString(),
                'main_category_id' => 1,
                'sub_category_id' => null,
                'tags' => $debt->tags,
                'from_account_id' => $isBorrow ? $account->id : null,
                'to_account_id' => $isBorrow ? null : $account->id,
                'person_id' => $debt->person_id,
                'description' => $autoDescription,
            ]);

            // 4️⃣ Update account balance based on type
            if ($isBorrow) {
                $account->balance -= $request->amount;
            } else {
                $account->balance += $request->amount;
            }
            $account->save();
        });

        return back()->with('success', 'پرداخت با موفقیت ثبت شد.');
    }
}
