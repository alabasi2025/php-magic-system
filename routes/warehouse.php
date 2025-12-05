<?php

use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockCountController;
use App\Http\Controllers\StockBalanceController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WarehouseDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Warehouse Routes
|--------------------------------------------------------------------------
|
| مسارات نظام المخازن الكامل
|
*/

Route::middleware(['auth'])->group(function () {
    
    // ============================================
    // لوحة التحكم (Dashboard)
    // ============================================
    Route::get('/warehouse/dashboard', [WarehouseDashboardController::class, 'index'])
        ->name('warehouse.dashboard');
    
    // ============================================
    // إدارة المخازن (Warehouses)
    // ============================================
    Route::resource('warehouses', WarehouseController::class);
    Route::post('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])
        ->name('warehouses.toggle-status');
    
    // ============================================
    // إدارة الأصناف (Items)
    // ============================================
    Route::resource('items', ItemController::class);
    Route::post('items/{item}/toggle-status', [ItemController::class, 'toggleStatus'])
        ->name('items.toggle-status');
    
    // ============================================
    // إدارة الفئات (Categories)
    // ============================================
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
        ->name('categories.toggle-status');
    
    // ============================================
    // إدارة الوحدات (Units)
    // ============================================
    Route::resource('units', UnitController::class);
    Route::post('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])
        ->name('units.toggle-status');
    Route::post('units/convert', [UnitController::class, 'convert'])
        ->name('units.convert');
    
    // ============================================
    // حركات الإدخال (Stock In)
    // ============================================
    Route::resource('stock-ins', StockInController::class);
    Route::post('stock-ins/{stockIn}/post', [StockInController::class, 'post'])
        ->name('stock-ins.post');
    Route::post('stock-ins/{stockIn}/cancel', [StockInController::class, 'cancel'])
        ->name('stock-ins.cancel');
    
    // ============================================
    // حركات الإخراج (Stock Out)
    // ============================================
    Route::resource('stock-outs', StockOutController::class);
    Route::post('stock-outs/{stockOut}/post', [StockOutController::class, 'post'])
        ->name('stock-outs.post');
    Route::post('stock-outs/{stockOut}/cancel', [StockOutController::class, 'cancel'])
        ->name('stock-outs.cancel');
    
    // ============================================
    // التحويلات بين المخازن (Stock Transfers)
    // ============================================
    Route::resource('stock-transfers', StockTransferController::class);
    Route::post('stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])
        ->name('stock-transfers.approve');
    Route::post('stock-transfers/{stockTransfer}/reject', [StockTransferController::class, 'reject'])
        ->name('stock-transfers.reject');
    
    // ============================================
    // الجرد (Stock Count)
    // ============================================
    Route::resource('stock-counts', StockCountController::class);
    Route::post('stock-counts/{stockCount}/approve', [StockCountController::class, 'approve'])
        ->name('stock-counts.approve');
    Route::post('stock-counts/{stockCount}/reject', [StockCountController::class, 'reject'])
        ->name('stock-counts.reject');
    
    // ============================================
    // رصيد المخزون (Stock Balance)
    // ============================================
    Route::get('stock-balances', [StockBalanceController::class, 'index'])
        ->name('stock-balances.index');
    Route::get('stock-balances/low-stock', [StockBalanceController::class, 'lowStock'])
        ->name('stock-balances.low-stock');
    Route::get('stock-balances/slow-moving', [StockBalanceController::class, 'slowMoving'])
        ->name('stock-balances.slow-moving');
    
    // ============================================
    // حركات المخزون (Stock Movements)
    // ============================================
    Route::get('stock-movements', [StockMovementController::class, 'index'])
        ->name('stock-movements.index');
    Route::get('stock-movements/item/{item}', [StockMovementController::class, 'byItem'])
        ->name('stock-movements.by-item');
    Route::get('stock-movements/warehouse/{warehouse}', [StockMovementController::class, 'byWarehouse'])
        ->name('stock-movements.by-warehouse');
    
    // ============================================
    // الموردون (Suppliers)
    // ============================================
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers/{supplier}/transactions', [SupplierController::class, 'transactions'])
        ->name('suppliers.transactions');
    Route::get('suppliers/{supplier}/balance', [SupplierController::class, 'balance'])
        ->name('suppliers.balance');
    
    // ============================================
    // العملاء (Customers)
    // ============================================
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/transactions', [CustomerController::class, 'transactions'])
        ->name('customers.transactions');
    Route::get('customers/{customer}/balance', [CustomerController::class, 'balance'])
        ->name('customers.balance');
    
    // ============================================
    // التقارير (Reports)
    // ============================================
    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/stock-balance', 'stockBalance')->name('stock-balance');
        Route::get('/item-movements', 'itemMovements')->name('item-movements');
        Route::get('/stock-valuation', 'stockValuation')->name('stock-valuation');
        Route::get('/slow-moving-items', 'slowMovingItems')->name('slow-moving-items');
        Route::get('/low-stock-items', 'lowStockItems')->name('low-stock-items');
        Route::get('/fast-moving-items', 'fastMovingItems')->name('fast-moving-items');
        Route::get('/purchases', 'purchases')->name('purchases');
        Route::get('/sales', 'sales')->name('sales');
    });
    
});
