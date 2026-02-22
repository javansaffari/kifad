<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Account;
use App\Models\Tenant\Transaction;

class AccountController extends Controller
{
    /**
     * Display a listing of accounts with chart data.
     */
    public function index(Request $request)
    {
        $accountTypes = [
            'پول نقد',
            'حساب جاری',
            'حساب پس‌انداز',
            'حساب سپرده',
            'حساب وکالتی',
            'حساب مشترک',
            'حساب ارزی',
            'سایر',
        ];

        $banks = [
            'بانک آینده',
            'بانک اقتصاد نوین',
            'بانک انصار',
            'بانک ایران زمین',
            'بانک پارسیان',
            'بانک پاسارگاد',
            'بانک تجارت',
            'بانک توسعه تعاون',
            'بانک توسعه صادرات ایران',
            'بانک صنعت و معدن',
            'بانک سینا',
            'بانک سپه',
            'بانک شهر',
            'بانک سرمایه',
            'بانک قرض‌الحسنه رسالت',
            'بانک قرض‌الحسنه مهر ایران',
            'بانک رفاه کارگران',
            'بانک حکمت ایرانیان',
            'بانک خاورمیانه',
            'بانک گردشگری',
            'بانک مسکن',
            'بانک ملت',
            'بانک ملی ایران',
            'بانک کارآفرین',
            'بانک صادرات ایران',
            'پست بانک ایران',
            'موسسه اعتباری کوثر',
            'موسسه اعتباری ملل',
            'موسسه اعتباری نور',
            'موسسه اعتباری توسعه',
            'سایر',
        ];
        // Search filter
        $search = $request->input('search');

        $accountsQuery = Account::query();

        if ($search) {
            $accountsQuery->where('title', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhere('bank', 'like', "%{$search}%");
        }

        // Get all accounts
        $accounts = $accountsQuery->latest()->get();

        // Prepare chart data grouped by type
        $chartData = $accounts->groupBy('type')->map(function ($items) {
            return $items->sum('balance');
        });

        $totalBalance = $accounts->sum('balance');

        return view('tenant.accounts.index', compact(
            'accounts',
            'accountTypes',
            'banks',
            'chartData',
            'totalBalance'
        ));
    }

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'type' => 'required|string',
            'bank' => 'required|string',
            'desc' => 'nullable|string|max:500',
        ]);

        Account::create([
            'title' => $request->title,
            'balance' => $request->balance,
            'type' => $request->type,
            'bank' => $request->bank,
            'description' => $request->desc,
        ]);

        return redirect()->route('tenant.accounts.index')
            ->with('success', 'حساب با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified account with its transactions.
     */
    public function show(Account $account)
    {
        // get transactions where this account is sender or receiver
        $transactions = Transaction::where(function ($q) use ($account) {
            $q->where('from_account_id', $account->id)
                ->orWhere('to_account_id', $account->id);
        })
            ->with(['mainCategory', 'subCategory', 'person'])
            ->latest()
            ->paginate(15);

        // calculate incoming & outgoing totals
        $totalIncoming = $transactions->getCollection()->where('to_account_id', $account->id)->sum('amount');
        $totalOutgoing = $transactions->getCollection()->where('from_account_id', $account->id)->sum('amount');

        return view('tenant.accounts.show', compact(
            'account',
            'transactions',
            'totalIncoming',
            'totalOutgoing'
        ));
    }

    /**
     * Show the form for editing an account.
     */
    public function edit(Account $account)
    {
        $accountTypes = [
            'پول نقد',
            'حساب جاری',
            'حساب پس‌انداز',
            'حساب سپرده',
            'حساب وکالتی',
            'حساب مشترک',
            'حساب ارزی',
            'سایر',
        ];
        $banks = [
            'بانک آینده',
            'بانک اقتصاد نوین',
            'بانک انصار',
            'بانک ایران زمین',
            'بانک پارسیان',
            'بانک پاسارگاد',
            'بانک تجارت',
            'بانک توسعه تعاون',
            'بانک توسعه صادرات ایران',
            'بانک صنعت و معدن',
            'بانک سینا',
            'بانک سپه',
            'بانک شهر',
            'بانک سرمایه',
            'بانک قرض‌الحسنه رسالت',
            'بانک قرض‌الحسنه مهر ایران',
            'بانک رفاه کارگران',
            'بانک حکمت ایرانیان',
            'بانک خاورمیانه',
            'بانک گردشگری',
            'بانک مسکن',
            'بانک ملت',
            'بانک ملی ایران',
            'بانک کارآفرین',
            'بانک صادرات ایران',
            'پست بانک ایران',
            'موسسه اعتباری کوثر',
            'موسسه اعتباری ملل',
            'موسسه اعتباری نور',
            'موسسه اعتباری توسعه',
            'سایر',
        ];

        // Get all accounts for table & chart
        $accounts = Account::latest()->get();

        // Total balance
        $totalBalance = $accounts->sum('balance');

        // Chart data grouped by type
        $chartData = $accounts->groupBy('type')->map(fn($items) => $items->sum('balance'));

        return view('tenant.accounts.edit', compact(
            'account',
            'accounts',
            'accountTypes',
            'banks',
            'totalBalance',
            'chartData'
        ));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'type' => 'required|string',
            'bank' => 'required|string',
            'desc' => 'nullable|string|max:500',
        ]);

        $account->update([
            'title' => $request->title,
            'balance' => $request->balance,
            'type' => $request->type,
            'bank' => $request->bank,
            'description' => $request->desc,
        ]);

        return redirect()->route('tenant.accounts.index')
            ->with('success', 'حساب با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified account.
     */
    /**
     * Remove the specified account.
     */
    public function destroy(Account $account)
    {
        // Check if account is used in any transaction (from or to)
        $hasTransactions = Transaction::where('from_account_id', $account->id)
            ->orWhere('to_account_id', $account->id)
            ->exists();

        if ($hasTransactions) {
            return redirect()->route('tenant.accounts.index')
                ->with('error', 'این حساب قابل حذف نیست، زیرا در تراکنش‌ها استفاده شده است.');
        }

        $account->delete();

        return redirect()->route('tenant.accounts.index')
            ->with('success', 'حساب با موفقیت حذف شد.');
    }
}
