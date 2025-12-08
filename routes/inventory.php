<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseGroupController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\ItemUnitController;
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

Route::prefix('inventory')->name('inventory.')->group(function () {
    
    // Index (redirect to dashboard)
    Route::get('/', function () {
        return redirect()->route('inventory.dashboard');
    })->name('index');
    
    // Dashboard
    Route::get('/dashboard', [InventoryReportController::class, 'dashboard'])->name('dashboard');

    // Warehouse Groups Management
    Route::resource('warehouse-groups', WarehouseGroupController::class);
    
    // Warehouses Management
    Route::resource('warehouses', WarehouseController::class);
    Route::get('/warehouses/{warehouse}/stock-report', [WarehouseController::class, 'stockReport'])->name('warehouses.stock-report');

    // Item Units Management
    Route::resource('item-units', ItemUnitController::class);
    Route::post('/item-units/store-ajax', [ItemUnitController::class, 'storeAjax'])->name('item-units.store-ajax');
    Route::get('/item-units/get-active', [ItemUnitController::class, 'getActive'])->name('item-units.get-active');
    
    // Items Management
    Route::resource('items', ItemController::class);
    Route::get('/items/{item}/unit-conversions', [ItemController::class, 'getUnitConversions'])->name('items.unit-conversions');
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
