<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\InventoryReportController;

/*
|--------------------------------------------------------------------------
| Inventory System Routes
|--------------------------------------------------------------------------
|
| Routes for the Inventory Management System v4.1.0
|
*/

Route::middleware(['auth'])->prefix('inventory')->name('inventory.')->group(function () {
    
    // Index (redirect to dashboard)
    Route::get('/', function () {
        return redirect()->route('inventory.dashboard');
    })->name('index');
    
    // Dashboard
    Route::get('/dashboard', [InventoryReportController::class, 'dashboard'])->name('dashboard');

    // Warehouses Management
    Route::resource('warehouses', WarehouseController::class);
    Route::get('/warehouses/{warehouse}/stock-report', [WarehouseController::class, 'stockReport'])->name('warehouses.stock-report');

    // Items Management
    Route::resource('items', ItemController::class);
    Route::get('/items-below-min-stock', [ItemController::class, 'belowMinStock'])->name('items.below-min-stock');

    // Stock Movements
    Route::resource('stock-movements', StockMovementController::class);
    Route::patch('/stock-movements/{stockMovement}/approve', [StockMovementController::class, 'approve'])->name('stock-movements.approve');
    Route::patch('/stock-movements/{stockMovement}/reject', [StockMovementController::class, 'reject'])->name('stock-movements.reject');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/current-stock', [InventoryReportController::class, 'currentStockReport'])->name('current-stock');
        Route::get('/movements', [InventoryReportController::class, 'movementsReport'])->name('movements');
        Route::get('/below-min-stock', [InventoryReportController::class, 'belowMinStockReport'])->name('below-min-stock');
        Route::get('/dormant-items', [InventoryReportController::class, 'dormantItemsReport'])->name('dormant-items');
        Route::get('/stock-value', [InventoryReportController::class, 'stockValueReport'])->name('stock-value');
        Route::get('/item-history/{item}', [InventoryReportController::class, 'itemMovementHistory'])->name('item-history');
        Route::get('/export-current-stock', [InventoryReportController::class, 'exportCurrentStock'])->name('export-current-stock');
    });
});
