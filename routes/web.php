<?php

use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

// ... مسارات أخرى ...

Route::middleware(['auth'])->group(function () {
    // مسارات حركات المخزون
    Route::resource('stock_movements', StockMovementController::class)->only([
        'index', 'create', 'store', 'show'
    ]);

    // مسارات التقارير الإضافية
    Route::get('stock_movements/item-report', [StockMovementController::class, 'itemReport'])->name('stock_movements.item_report');
    Route::get('stock_movements/warehouse-report', [StockMovementController::class, 'warehouseReport'])->name('stock_movements.warehouse_report');
});
