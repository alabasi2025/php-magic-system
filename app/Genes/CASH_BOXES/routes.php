<?php
/**
 * CASH_BOXES Gene Routes
 * 
 * مسارات جين الصناديق النقدية
 * يتضمن مسارات إدارة الصناديق النقدية والعمليات والتقارير
 */
use Illuminate\Support\Facades\Route;
use App\Genes\CASH_BOXES\Controllers\CashBoxController;
use App\Genes\CASH_BOXES\Controllers\CashBoxReportController;

// Cash Boxes Routes with 'alabasi' prefix
Route::prefix('alabasi')->name('alabasi.')->group(function () {
    // Cash Boxes - الصناديق النقدية
    Route::resource('cash-boxes', CashBoxController::class);
    
    // Cash Box Reports - تقارير الصناديق
    Route::prefix('cash-boxes/{cashBox}')->name('cash-boxes.')->group(function () {
        Route::get('transactions', [CashBoxController::class, 'transactions'])->name('transactions');
        Route::get('report', [CashBoxReportController::class, 'show'])->name('report');
    });
    
    // General Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('cash-boxes', [CashBoxReportController::class, 'index'])->name('cash-boxes');
        Route::get('cash-boxes/summary', [CashBoxReportController::class, 'summary'])->name('cash-boxes.summary');
    });
});
