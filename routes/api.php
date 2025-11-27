<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('accounts', AccountController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('transactions', TransactionController::class);

Route::prefix('reports')->group(function () {
    Route::get('/monthly-expenses', [ReportController::class, 'monthlyExpenses']);
    Route::get('/expenses-by-category', [ReportController::class, 'expensesByCategory']);
    Route::get('/monthly-evolution', [ReportController::class, 'monthlyEvolution']);
    Route::get('/total-assets', [ReportController::class, 'totalAssets']);
});

use App\Http\Controllers\Api\CreditCardController;
use App\Http\Controllers\Api\CreditCardExpenseController;
use App\Http\Controllers\Api\FixedExpenseController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\ProjectionController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReceivableController;

Route::apiResource('investments', InvestmentController::class);
Route::apiResource('credit-cards', CreditCardController::class);
Route::apiResource('receivables', ReceivableController::class);
Route::post('/receivables/{receivable}/mark-as-paid', [ReceivableController::class, 'markAsPaid']);
Route::apiResource('fixed-expenses', FixedExpenseController::class);
Route::post('fixed-expenses/{fixedExpense}/mark-paid', [FixedExpenseController::class, 'markAsPaid']);
Route::apiResource('goals', GoalController::class);
Route::post('goals/{goal}/progress', [GoalController::class, 'updateProgress']);
Route::prefix('credit-cards/{creditCard}')->group(function () {
    Route::get('/expenses', [CreditCardExpenseController::class, 'index']);
    Route::post('/expenses', [CreditCardExpenseController::class, 'store']);
    Route::delete('/expenses/{expense}', [CreditCardExpenseController::class, 'destroy']);
});
Route::prefix('quotes')->group(function () {
    Route::get('/currencies', [QuoteController::class, 'currencies']);
    Route::get('/cryptocurrencies', [QuoteController::class, 'cryptocurrencies']);
    Route::get('/all', [QuoteController::class, 'all']);
});
Route::prefix('projection')->group(function () {
    Route::post('/calculate', [ProjectionController::class, 'calculate']);
    Route::get('/categories', [ProjectionController::class, 'categories']);
    Route::get('/accounts', [ProjectionController::class, 'accounts']);
    Route::get('/fixed-expenses', [ProjectionController::class, 'fixedExpenses']);
});
