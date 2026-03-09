<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\AccountingController;
use App\Http\Controllers\Tenant\ExpenseController;
use App\Http\Controllers\Tenant\IncomeController;
use App\Http\Controllers\Tenant\TransactionController;
use App\Http\Controllers\Tenant\ChequesController;
use App\Http\Controllers\Tenant\LoanController;
use App\Http\Controllers\Tenant\DebtController;
use App\Http\Controllers\Tenant\AccountController;
use App\Http\Controllers\Tenant\PersonController;
use App\Http\Controllers\Tenant\InvestmentController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Central\BillingController;
use App\Http\Controllers\Central\SupportController;
use App\Http\Controllers\Central\HelpController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::prefix('tenant')->name('tenant.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Accounting
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting');

    // Expenses (CRUD)
    Route::resource('expenses', ExpenseController::class)->names([
        'index' => 'expenses.index',
        'create' => 'expenses.create',
        'store' => 'expenses.store',
        'show' => 'expenses.show',
        'edit' => 'expenses.edit',
        'update' => 'expenses.update',
        'destroy' => 'expenses.destroy',
    ]);

    // Income (CRUD)
    Route::resource('income', IncomeController::class)->names([
        'index' => 'income.index',
        'create' => 'income.create',
        'store' => 'income.store',
        'show' => 'income.show',
        'edit' => 'income.edit',
        'update' => 'income.update',
        'destroy' => 'income.destroy',
    ]);


    // Transactions (CRUD)

    Route::post('transactions/transfer', [TransactionController::class, 'storeTransfer'])->name('transactions.storeTransfer');


    Route::resource('transactions', TransactionController::class)->names([
        'index' => 'transactions.index',
        'create' => 'transactions.create',
        'store' => 'transactions.store',
        'show' => 'transactions.show',
        'edit' => 'transactions.edit',
        'update' => 'transactions.update',
        'destroy' => 'transactions.destroy',
    ]);


    // Cheques (CRUD)
    Route::resource('cheques', ChequesController::class)->names([
        'index' => 'cheques.index',
        'create' => 'cheques.create',
        'store' => 'cheques.store',
        'show' => 'cheques.show',
        'edit' => 'cheques.edit',
        'update' => 'cheques.update',
        'destroy' => 'cheques.destroy',
    ]);

    // Loans (CRUD)
    Route::resource('loans', LoanController::class)->names([
        'index' => 'loans.index',
        'create' => 'loans.create',
        'store' => 'loans.store',
        'show' => 'loans.show',
        'edit' => 'loans.edit',
        'update' => 'loans.update',
        'destroy' => 'loans.destroy',
    ]);

    Route::post('loans/installments/{installment}/undo', [LoanController::class, 'undoInstallment'])
        ->name('loans.installments.undo');
    Route::post('loans/installments/{installment}/pay', [LoanController::class, 'payInstallment'])
        ->name('loans.installments.pay');


    // Debts (CRUD)
    Route::resource('debts', DebtController::class)->names([
        'index' => 'debts.index',
        'create' => 'debts.create',
        'store' => 'debts.store',
        'show' => 'debts.show',
        'edit' => 'debts.edit',
        'update' => 'debts.update',
        'destroy' => 'debts.destroy',
    ]);
    Route::post('debts/{debt}/pay', [DebtController::class, 'pay'])->name('debts.pay');

    // Accounts (CRUD)
    Route::post('accounts/{id}/balance', [AccountController::class, 'recalculateBalance'])->name('accounts.balance');

    Route::resource('accounts', AccountController::class)->names([
        'index' => 'accounts.index',
        'create' => 'accounts.create',
        'store' => 'accounts.store',
        'show' => 'accounts.show',
        'edit' => 'accounts.edit',
        'update' => 'accounts.update',
        'destroy' => 'accounts.destroy',
    ]);

    // Persons (CRUD)
    Route::resource('person', PersonController::class)->names([
        'index' => 'person.index',
        'create' => 'person.create',
        'store' => 'person.store',
        'show' => 'person.show',
        'edit' => 'person.edit',
        'update' => 'person.update',
        'destroy' => 'person.destroy',
    ]);

    Route::prefix('tenant')->name('tenant.')->group(function () {

        // Categories (CRUD)
        Route::resource('categories', CategoryController::class)->names([
            'index' => 'categories',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'show' => 'categories.show',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);
    });

    // Investments
    Route::get('/investments', [InvestmentController::class, 'index'])->name('investments');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});


Route::prefix('central')->name('central.')->group(function () {

    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::get('/help', [HelpController::class, 'index'])->name('help');
});
