<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Models\Tenant\Loan;
use App\Models\Tenant\LoanInstallment;
use App\Models\Tenant\Transaction;
use App\Models\Tenant\Account;

class LoanController extends Controller
{
    /* ======================================================
     *  LIST LOANS
     * ====================================================== */
    public function index()
    {
        $loans = Loan::withCount([
            'installments as paid_count' => fn($q) => $q->where('paid', true),
            'installments as unpaid_count' => fn($q) => $q->where('paid', false),
        ])->get();

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

        return view('tenant.loans.index', compact('loans', 'banks'));
    }

    /* ======================================================
     *  CREATE LOAN
     * ====================================================== */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'bank' => 'required|string|max:255',
            'start_date' => 'required|string',
            'installments_paid' => 'required|integer|min:0',
            'installments_remaining' => 'required|integer|min:0',
            'installment_amount' => 'required|numeric|min:1',
            'due_day' => 'required|integer|min:1|max:31',
            'due_type' => 'required|in:ماهانه,سالانه',
        ]);

        DB::transaction(function () use ($validated) {
            $startDate = Jalalian::fromFormat('Y/m/j', str_replace('-', '/', $validated['start_date']))->toCarbon();
            $totalInstallments = $validated['installments_paid'] + $validated['installments_remaining'];

            $loan = Loan::create([
                'title' => $validated['title'],
                'amount' => $validated['amount'],
                'bank' => $validated['bank'],
                'start_date' => $startDate,
                'installment_amount' => $validated['installment_amount'],
                'installment_due_day' => $validated['due_day'],
                'due_type' => $validated['due_type'],
            ]);

            for ($i = 1; $i <= $totalInstallments; $i++) {
                $dueDate = $this->calculateDueDate($startDate, $validated['due_type'], $i, $validated['due_day']);
                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $i,
                    'amount' => $validated['installment_amount'],
                    'due_date' => $dueDate,
                    'paid' => $i <= $validated['installments_paid'],
                    'paid_at' => $i <= $validated['installments_paid'] ? $dueDate : null,
                ]);
            }

            $this->syncCounters($loan);
        });

        return redirect()->route('tenant.loans.index')->with('success', 'وام با موفقیت ثبت شد.');
    }

    /* ======================================================
     *  SHOW LOAN
     * ====================================================== */
    public function show(Loan $loan)
    {
        $installments = $loan->installments()->orderBy('installment_number')->get();
        $accounts = Account::all(); // برای انتخاب حساب پرداخت
        return view('tenant.loans.show', compact('loan', 'installments', 'accounts'));
    }

    /* ======================================================
     *  UPDATE LOAN
     * ====================================================== */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'bank' => 'required|string|max:255',
            'start_date' => 'required|string',
            'installment_amount' => 'required|numeric|min:1',
            'due_day' => 'required|integer|min:1|max:31',
            'due_type' => 'required|in:ماهانه,سالانه',
        ]);

        DB::transaction(function () use ($validated, $loan) {
            $startDate = Jalalian::fromFormat('Y/m/d', str_replace('-', '/', $validated['start_date']))->toCarbon();

            $loan->update([
                'title' => $validated['title'],
                'amount' => $validated['amount'],
                'bank' => $validated['bank'],
                'start_date' => $startDate,
                'installment_amount' => $validated['installment_amount'],
                'installment_due_day' => $validated['due_day'],
                'due_type' => $validated['due_type'],
            ]);

            // بازسازی اقساط
            $loan->installments()->delete();
            $total = ceil($loan->amount / $loan->installment_amount);
            for ($i = 1; $i <= $total; $i++) {
                $dueDate = $this->calculateDueDate($startDate, $validated['due_type'], $i, $validated['due_day']);
                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $i,
                    'amount' => $validated['installment_amount'],
                    'due_date' => $dueDate,
                ]);
            }

            $this->syncCounters($loan);
        });

        return redirect()->route('tenant.loans.index')->with('success', 'وام بروزرسانی شد.');
    }

    /* ======================================================
     *  DELETE LOAN
     * ====================================================== */
    public function destroy(Loan $loan)
    {
        $loan->delete();
        return back()->with('success', 'وام حذف شد.');
    }

    /* ======================================================
     *  PAY INSTALLMENT
     * ====================================================== */
    public function payInstallment(Request $request, LoanInstallment $installment)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id'
        ]);

        DB::transaction(function () use ($request, $installment) {

            $installment->update([
                'paid' => true,
                'paid_at' => now(),
            ]);

            $account = Account::findOrFail($request->account_id);
            $account->balance -= $installment->amount;
            $account->save();

            Transaction::create([
                'type' => 'expense',
                'amount' => $installment->amount,
                'date' => now(),
                'main_category_id' => 1,
                'sub_category_id' => null,
                'from_account_id' => $account->id,
                'person_id' => null,
                'description' => "پرداخت قسط وام: {$installment->loan->title} (#{$installment->installment_number})",
                'tags' => ['وام'],
            ]);

            $this->syncCounters($installment->loan);
        });

        return back()->with('success', 'پرداخت قسط ثبت شد و هزینه اضافه شد.');
    }

    /* ======================================================
     *  UNDO INSTALLMENT PAYMENT
     * ====================================================== */
    public function undoInstallment(LoanInstallment $installment)
    {
        DB::transaction(function () use ($installment) {

            $installment->update([
                'paid' => false,
                'paid_at' => null,
            ]);

            $this->syncCounters($installment->loan);
        });

        return back()->with('success', 'پرداخت قسط لغو شد.');
    }

    /* ======================================================
     *  HELPERS
     * ====================================================== */
    private function syncCounters(Loan $loan)
    {
        $loan->update([
            'installments_paid' => $loan->installments()->where('paid', true)->count(),
            'installments_remaining' => $loan->installments()->where('paid', false)->count(),
        ]);
    }

    private function calculateDueDate(Carbon $startDate, string $type, int $number, $day): Carbon
    {
        $day = (int)$day;
        $date = match ($type) {
            'سالانه' => $startDate->copy()->addYears($number - 1),
            default => $startDate->copy()->addMonths($number - 1),
        };
        $day = min($day, $date->daysInMonth);
        return $date->day($day);
    }
}
