<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Tenant\Transaction;
use App\Models\Tenant\Account;
use App\Models\Tenant\Cheque;
use App\Models\Tenant\LoanInstallment;
use App\Models\Tenant\Debt;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ----------------------------
        // Get selected year and month (defaults to current)
        // ----------------------------
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $currentMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        $today = Carbon::today();
        $daysInMonth = $currentMonth->daysInMonth;
        $weekDays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
        $firstWeekday = ($startOfMonth->dayOfWeekIso + 1) % 7;

        // ----------------------------
        // Monthly statistics
        // ----------------------------
        $currentMonthIncome = Transaction::where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $currentMonthExpense = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $walletBalance = Account::sum('balance');
        $incomeExpenseDiff = $currentMonthIncome - $currentMonthExpense;
        $checksReceived = Cheque::where('type', 'received')->count();
        $checksIssued = Cheque::where('type', 'issued')->count();

        // ----------------------------
        // Calendar events
        // ----------------------------
        $calendarEvents = $this->buildCalendarEvents($startOfMonth, $endOfMonth);

        // ----------------------------
        // Previous and next months for navigation
        // ----------------------------
        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('tenant.dashboard.index', compact(
            'year',
            'month',
            'today',
            'currentMonth',
            'startOfMonth',
            'endOfMonth',
            'daysInMonth',
            'weekDays',
            'firstWeekday',
            'currentMonthIncome',
            'currentMonthExpense',
            'walletBalance',
            'incomeExpenseDiff',
            'checksReceived',
            'checksIssued',
            'calendarEvents',
            'prevMonth',
            'nextMonth'
        ));
    }

    // ----------------------------
    // Build calendar events (extensible)
    // ----------------------------
    private function buildCalendarEvents($start, $end)
    {
        $events = [];
        $this->loanInstallments($events, $start, $end);
        $this->debts($events, $start, $end);
        return $events;
    }

    // ----------------------------
    // Loan installment events
    // ----------------------------
    private function loanInstallments(&$events, $start, $end)
    {
        $items = LoanInstallment::whereBetween('due_date', [$start, $end])->get();
        foreach ($items as $item) {
            $dateKey = Carbon::parse($item->due_date)->format('Y-m-d');
            $events[$dateKey][] = [
                'title' => "قسط وام {$item->installment_number}",
                'amount' => $item->amount,
                'category' => 'expense',
                'type' => 'loan'
            ];
        }
    }

    // ----------------------------
    // Debt reminder events
    // ----------------------------
    private function debts(&$events, $start, $end)
    {
        $items = Debt::where('reminder', true)
            ->whereBetween('due_date', [$start, $end])
            ->get();
        foreach ($items as $item) {
            $dateKey = Carbon::parse($item->due_date)->format('Y-m-d');
            $events[$dateKey][] = [
                'title' => "بدهی {$item->type}",
                'amount' => $item->amount - $item->paid_amount,
                'category' => 'expense',
                'type' => 'debt'
            ];
        }
    }
}
