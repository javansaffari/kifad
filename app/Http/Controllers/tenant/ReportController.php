<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\{Transaction, Cheque, Debt, Account, Category, LoanInstallment};
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $accountId = $request->input('account_id');
        $categoryId = $request->input('category_id');
        $search = $request->input('search');
        $type = $request->input('type'); // income / expense / null

        // ۱. پایه کوئری تراکنش‌ها
        $baseQuery = Transaction::with(['mainCategory', 'subCategory', 'account', 'person', 'fromAccount', 'toAccount'])
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->when($accountId, fn($q) => $q->where(fn($q) => $q->where('from_account_id', $accountId)->orWhere('to_account_id', $accountId)))
            ->when($categoryId, fn($q) => $q->where('main_category_id', $categoryId))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($search, fn($q) => $q->where('description', 'like', "%$search%"));

        $transactions = (clone $baseQuery)->latest()->paginate(15)->withQueryString();

        // ۲. ماتریس ماهانه جامع
        $monthlyMatrix = [];
        $categories = Category::whereNull('parent_id')->get();
        $catMatrixExpense = [];
        $catMatrixIncome = [];

        $prevIncome = 0;

        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end = Carbon::create($year, $m, 1)->endOfMonth();

            $mTrans = Transaction::whereBetween('created_at', [$start, $end])
                ->when($accountId, fn($q) => $q->where(fn($q) => $q->where('from_account_id', $accountId)->orWhere('to_account_id', $accountId)))
                ->when($categoryId, fn($q) => $q->where('main_category_id', $categoryId))
                ->get();

            $inc = $mTrans->where('type', 'income')->sum('amount');
            $exp = $mTrans->where('type', 'expense')->sum('amount');

            $chequeIn = Cheque::whereBetween('issue_date', [$start, $end])->where('type', 'received')->sum('amount');
            $chequeOut = Cheque::whereBetween('issue_date', [$start, $end])->where('type', 'issued')->sum('amount');

            $mDebts = Debt::whereBetween('due_date', [$start, $end])->get();
            $debt = $mDebts->where('type', 'borrow')->sum('amount');
            $lend = $mDebts->where('type', 'lend')->sum('amount');

            $growth = $prevIncome > 0 ? round((($inc - $prevIncome) / $prevIncome) * 100, 1) : 0;
            $prevIncome = $inc;

            $monthlyMatrix[$m] = [
                'month_name' => $start->format('F'),
                'income' => $inc,
                'expense' => $exp,
                'balance' => $inc - $exp,
                'cheque_in' => $chequeIn,
                'cheque_out' => $chequeOut,
                'debt' => $debt,
                'lend' => $lend,
                'growth' => $growth
            ];

            // ماتریس دسته‌بندی‌ها
            foreach ($categories as $cat) {
                $catMatrixExpense[$cat->name][$m] = $mTrans->where('main_category_id', $cat->id)->where('type', 'expense')->sum('amount');
                $catMatrixIncome[$cat->name][$m] = $mTrans->where('main_category_id', $cat->id)->where('type', 'income')->sum('amount');
            }
        }

        $topExpenses = (clone $baseQuery)->where('type', 'expense')
            ->selectRaw('main_category_id, SUM(amount) as total')
            ->groupBy('main_category_id')
            ->with('mainCategory')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $topIncome = (clone $baseQuery)->where('type', 'income')
            ->selectRaw('main_category_id, SUM(amount) as total')
            ->groupBy('main_category_id')
            ->with('mainCategory')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('tenant.reports.index', [
            'transactions' => $transactions,
            'monthlyMatrix' => $monthlyMatrix,
            'catMatrixExpense' => $catMatrixExpense,
            'catMatrixIncome' => $catMatrixIncome,
            'topExpenses' => $topExpenses,
            'topIncome' => $topIncome,
            'year' => $year,
            'accounts' => Account::all(),
            'categories' => $categories,
            'upcomingLoans' => LoanInstallment::where('paid', false)->where('due_date', '>=', now())->orderBy('due_date')->take(5)->get(),
            'totalBalance' => Account::sum('balance'),
            'type' => $type,
        ]);
    }
}
