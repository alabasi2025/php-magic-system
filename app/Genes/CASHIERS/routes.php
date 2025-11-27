<?php

use Illuminate\Support\Facades\Route;
use App\Genes\CASHIERS\Controllers\CashierController;
use App\Genes\CASHIERS\Controllers\CashierTransactionController;
use App\Genes\CASHIERS\Controllers\CashierSettlementController;

/*
|--------------------------------------------------------------------------
| CASHIERS Gene Routes
|--------------------------------------------------------------------------
|
| مسارات جين الصرافين (CASHIERS)
| يحتوي على مسارات إدارة الصرافين والمعاملات والتسويات
|
*/

// مجموعة مسارات الصرافين
Route::prefix('cashiers')->name('cashiers.')->group(function () {
    
    // مسارات الصرافين الأساسية
    Route::get('/', [CashierController::class, 'index'])->name('index');
    Route::get('/create', [CashierController::class, 'create'])->name('create');
    Route::post('/', [CashierController::class, 'store'])->name('store');
    Route::get('/{id}', [CashierController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [CashierController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CashierController::class, 'update'])->name('update');
    Route::delete('/{id}', [CashierController::class, 'destroy'])->name('destroy');
    
    // مسارات معاملات الصرافين
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [CashierTransactionController::class, 'index'])->name('index');
        Route::get('/create', [CashierTransactionController::class, 'create'])->name('create');
        Route::post('/', [CashierTransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [CashierTransactionController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [CashierTransactionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [CashierTransactionController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [CashierTransactionController::class, 'cancel'])->name('cancel');
    });
    
    // مسارات تسويات الصرافين
    Route::prefix('settlements')->name('settlements.')->group(function () {
        Route::get('/', [CashierSettlementController::class, 'index'])->name('index');
        Route::get('/create', [CashierSettlementController::class, 'create'])->name('create');
        Route::post('/', [CashierSettlementController::class, 'store'])->name('store');
        Route::get('/{id}', [CashierSettlementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CashierSettlementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CashierSettlementController::class, 'update'])->name('update');
        Route::delete('/{id}', [CashierSettlementController::class, 'destroy'])->name('destroy');
    });
});

// مسارات API للصرافين
Route::prefix('api/cashiers')->name('api.cashiers.')->group(function () {
    
    // API الصرافين
    Route::get('/', [CashierController::class, 'index'])->name('index');
    Route::post('/', [CashierController::class, 'store'])->name('store');
    Route::get('/{id}', [CashierController::class, 'show'])->name('show');
    Route::put('/{id}', [CashierController::class, 'update'])->name('update');
    Route::delete('/{id}', [CashierController::class, 'destroy'])->name('destroy');
    
    // API المعاملات
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [CashierTransactionController::class, 'index'])->name('index');
        Route::post('/', [CashierTransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [CashierTransactionController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [CashierTransactionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [CashierTransactionController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [CashierTransactionController::class, 'cancel'])->name('cancel');
    });
    
    // API التسويات
    Route::prefix('settlements')->name('settlements.')->group(function () {
        Route::get('/', [CashierSettlementController::class, 'index'])->name('index');
        Route::post('/', [CashierSettlementController::class, 'store'])->name('store');
        Route::get('/{id}', [CashierSettlementController::class, 'show'])->name('show');
        Route::put('/{id}', [CashierSettlementController::class, 'update'])->name('update');
        Route::delete('/{id}', [CashierSettlementController::class, 'destroy'])->name('destroy');
    });
});
