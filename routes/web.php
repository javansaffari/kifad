<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::prefix('tenant')->name('tenant.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\tenant\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/accounting', [\App\Http\Controllers\tenant\AccountingController::class, 'index'])->name('accounting');
    Route::get('/expenses', [\App\Http\Controllers\tenant\ExpenseController::class, 'index'])->name('expenses');
    Route::get('/income', [\App\Http\Controllers\tenant\IncomeController::class, 'index'])->name('income');
    Route::get('/transactions', [\App\Http\Controllers\tenant\TransactionController::class, 'index'])->name('transactions');
    Route::get('/checks', [\App\Http\Controllers\tenant\CheckController::class, 'index'])->name('checks');
    Route::get('/loans', [\App\Http\Controllers\tenant\LoanController::class, 'index'])->name('loans');
    Route::get('/debts', [\App\Http\Controllers\tenant\DebtController::class, 'index'])->name('debts');
    Route::get('/accounts', [\App\Http\Controllers\tenant\AccountController::class, 'index'])->name('accounts');
    Route::get('/people', [\App\Http\Controllers\tenant\PersonController::class, 'index'])->name('people');
    Route::get('/investments', [\App\Http\Controllers\tenant\InvestmentController::class, 'index'])->name('investments');
    Route::get('/reports', [\App\Http\Controllers\tenant\ReportController::class, 'index'])->name('reports');
});


Route::prefix('central')->name('central.')->group(function () {

    Route::get('/billing', [\App\Http\Controllers\central\BillingController::class, 'index'])->name('billing');
    Route::get('/support', [\App\Http\Controllers\central\SupportController::class, 'index'])->name('support');
    Route::get('/help', [\App\Http\Controllers\central\HelpController::class, 'index'])->name('help');
});
