<?php

use Illuminate\Support\Facades\Route;
use App\Genes\INTERMEDIATE_ACCOUNTS\Controllers\TransactionLinkingController;

// Controller موجود في App\Genes\INTERMEDIATE_ACCOUNTS\Controllers\TransactionLinkingController

// مجموعة Routes لربط المعاملات، مسبوقة بـ 'transaction-linking'
Route::prefix('transaction-linking')->group(function () {

    // GET /transaction-linking
    // عرض قائمة بروابط المعاملات
    Route::get('/', [TransactionLinkingController::class, 'index'])->name('transaction-linking.index');

    // POST /transaction-linking
    // ربط معاملة جديدة
    Route::post('/', [TransactionLinkingController::class, 'store'])->name('transaction-linking.store');

    // DELETE /transaction-linking/{id}
    // إلغاء ربط معاملة
    Route::delete('/{id}', [TransactionLinkingController::class, 'destroy'])->name('transaction-linking.destroy');

    // POST /transaction-linking/auto-link
    // ربط المعاملات تلقائيًا
    Route::post('/auto-link', [TransactionLinkingController::class, 'autoLink'])->name('transaction-linking.autoLink');

    // GET /transaction-linking/available-transactions
    // جلب قائمة بالمعاملات المتاحة للربط
    Route::get('/available-transactions', [TransactionLinkingController::class, 'getAvailableTransactions'])->name('transaction-linking.getAvailableTransactions');

});
