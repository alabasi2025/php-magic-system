<?php

use Illuminate\Support\Facades\Route;

// يجب استبدال 'PartnerAccountingController' بالمسار الصحيح للـ Controller عند التطبيق الفعلي
// مثال: use App\Genes\PARTNER_ACCOUNTING\Controllers\PartnerAccountingController;

Route::prefix('partner-accounting')->middleware(['auth'])->group(function () {
    // Routes for Partner Accounting Management (CRUD-like)
    
    // index
    Route::get('/', [PartnerAccountingController::class, 'index'])->name('partner-accounting.index');
    
    // create
    Route::get('/create', [PartnerAccountingController::class, 'create'])->name('partner-accounting.create');
    
    // store
    Route::post('/', [PartnerAccountingController::class, 'store'])->name('partner-accounting.store');
    
    // edit
    Route::get('/{partner_accounting}/edit', [PartnerAccountingController::class, 'edit'])->name('partner-accounting.edit');
    
    // update
    Route::put('/{partner_accounting}', [PartnerAccountingController::class, 'update'])->name('partner-accounting.update');
    
    // destroy
    Route::delete('/{partner_accounting}', [PartnerAccountingController::class, 'destroy'])->name('partner-accounting.destroy');

    // Custom Routes
    
    // addTransaction
    Route::post('/transaction', [PartnerAccountingController::class, 'addTransaction'])->name('partner-accounting.addTransaction');
    
    // getBalance
    Route::get('/balance', [PartnerAccountingController::class, 'getBalance'])->name('partner-accounting.getBalance');
    
    // createSettlement
    Route::post('/settlement', [PartnerAccountingController::class, 'createSettlement'])->name('partner-accounting.createSettlement');
    
    // reports
    Route::get('/reports', [PartnerAccountingController::class, 'reports'])->name('partner-accounting.reports');
});
