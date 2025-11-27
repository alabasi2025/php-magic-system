<?php

use Illuminate\Support\Facades\Route;
use App\Genes\WALLETS\Controllers\WalletController;
use App\Genes\WALLETS\Controllers\WalletTransactionController;
use App\Genes\WALLETS\Controllers\WalletTransferController;

/*
|--------------------------------------------------------------------------
| WALLETS Gene Routes
|--------------------------------------------------------------------------
|
| مسارات جين المحافظ (WALLETS)
| يحتوي على مسارات إدارة المحافظ والمعاملات والتحويلات
|
*/

// مجموعة مسارات المحافظ
Route::prefix('wallets')->name('wallets.')->group(function () {
    
    // مسارات المحافظ الأساسية
    Route::get('/', [WalletController::class, 'index'])->name('index');
    Route::get('/create', [WalletController::class, 'create'])->name('create');
    Route::post('/', [WalletController::class, 'store'])->name('store');
    Route::get('/{id}', [WalletController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [WalletController::class, 'edit'])->name('edit');
    Route::put('/{id}', [WalletController::class, 'update'])->name('update');
    Route::delete('/{id}', [WalletController::class, 'destroy'])->name('destroy');
    
    // مسارات معاملات المحافظ
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [WalletTransactionController::class, 'index'])->name('index');
        Route::get('/create', [WalletTransactionController::class, 'create'])->name('create');
        Route::post('/', [WalletTransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [WalletTransactionController::class, 'show'])->name('show');
        Route::post('/{id}/reverse', [WalletTransactionController::class, 'reverse'])->name('reverse');
    });
    
    // مسارات التحويلات بين المحافظ
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [WalletTransferController::class, 'index'])->name('index');
        Route::get('/create', [WalletTransferController::class, 'create'])->name('create');
        Route::post('/', [WalletTransferController::class, 'store'])->name('store');
        Route::get('/{id}', [WalletTransferController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [WalletTransferController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [WalletTransferController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [WalletTransferController::class, 'cancel'])->name('cancel');
    });
});

// مسارات API للمحافظ
Route::prefix('api/wallets')->name('api.wallets.')->group(function () {
    
    // API المحافظ
    Route::get('/', [WalletController::class, 'index'])->name('index');
    Route::post('/', [WalletController::class, 'store'])->name('store');
    Route::get('/{id}', [WalletController::class, 'show'])->name('show');
    Route::put('/{id}', [WalletController::class, 'update'])->name('update');
    Route::delete('/{id}', [WalletController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/balance', [WalletController::class, 'getBalance'])->name('balance');
    
    // API المعاملات
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [WalletTransactionController::class, 'index'])->name('index');
        Route::post('/', [WalletTransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [WalletTransactionController::class, 'show'])->name('show');
        Route::post('/{id}/reverse', [WalletTransactionController::class, 'reverse'])->name('reverse');
    });
    
    // API التحويلات
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [WalletTransferController::class, 'index'])->name('index');
        Route::post('/', [WalletTransferController::class, 'store'])->name('store');
        Route::get('/{id}', [WalletTransferController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [WalletTransferController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [WalletTransferController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [WalletTransferController::class, 'cancel'])->name('cancel');
    });
});
