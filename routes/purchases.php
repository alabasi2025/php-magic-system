<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Purchases\SupplierController;
use App\Http\Controllers\Purchases\PurchaseOrderController;
use App\Http\Controllers\Purchases\PurchaseReceiptController;
use App\Http\Controllers\Purchases\PurchaseInvoiceController;
use App\Http\Controllers\Purchases\PurchaseReportController;

/**
 * Purchase System Routes
 * مسارات نظام المشتريات v4.1.0
 * 
 * جميع المسارات محمية بـ middleware auth
 */

Route::middleware(['auth'])->prefix('purchases')->name('purchases.')->group(function () {
    
    // Dashboard
    // لوحة التحكم
    Route::get('/dashboard', [PurchaseReportController::class, 'dashboard'])->name('dashboard');
    
    // Suppliers Management
    // إدارة الموردين
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers/{supplier}/transactions', [SupplierController::class, 'transactions'])->name('suppliers.transactions');
    
    // Purchase Orders Management
    // إدارة أوامر الشراء
    Route::resource('orders', PurchaseOrderController::class);
    Route::patch('orders/{order}/approve', [PurchaseOrderController::class, 'approve'])->name('orders.approve');
    Route::patch('orders/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/items', [PurchaseOrderController::class, 'addItem'])->name('orders.items.add');
    Route::put('orders/{order}/items/{item}', [PurchaseOrderController::class, 'updateItem'])->name('orders.items.update');
    Route::delete('orders/{order}/items/{item}', [PurchaseOrderController::class, 'deleteItem'])->name('orders.items.delete');
    
    // Purchase Receipts Management
    // إدارة استلام البضاعة
    Route::resource('receipts', PurchaseReceiptController::class);
    Route::patch('receipts/{receipt}/approve', [PurchaseReceiptController::class, 'approve'])->name('receipts.approve');
    Route::patch('receipts/{receipt}/reject', [PurchaseReceiptController::class, 'reject'])->name('receipts.reject');
    Route::post('receipts/{receipt}/items', [PurchaseReceiptController::class, 'addItem'])->name('receipts.items.add');
    Route::delete('receipts/{receipt}/items/{item}', [PurchaseReceiptController::class, 'deleteItem'])->name('receipts.items.delete');
    
    // Purchase Invoices Management
    // إدارة فواتير الموردين
    Route::resource('invoices', PurchaseInvoiceController::class);
    Route::patch('invoices/{invoice}/approve', [PurchaseInvoiceController::class, 'approve'])->name('invoices.approve');
    Route::post('invoices/{invoice}/payments', [PurchaseInvoiceController::class, 'recordPayment'])->name('invoices.payments.record');
    Route::post('invoices/{invoice}/items', [PurchaseInvoiceController::class, 'addItem'])->name('invoices.items.add');
    Route::put('invoices/{invoice}/items/{item}', [PurchaseInvoiceController::class, 'updateItem'])->name('invoices.items.update');
    Route::delete('invoices/{invoice}/items/{item}', [PurchaseInvoiceController::class, 'deleteItem'])->name('invoices.items.delete');
    
    // Reports
    // التقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/orders', [PurchaseReportController::class, 'ordersReport'])->name('orders');
        Route::get('/by-supplier', [PurchaseReportController::class, 'bySupplierReport'])->name('by-supplier');
        Route::get('/by-item', [PurchaseReportController::class, 'byItemReport'])->name('by-item');
        Route::get('/due-invoices', [PurchaseReportController::class, 'dueInvoicesReport'])->name('due-invoices');
        Route::get('/supplier-performance', [PurchaseReportController::class, 'supplierPerformanceReport'])->name('supplier-performance');
        Route::get('/export', [PurchaseReportController::class, 'export'])->name('export');
    });
    
    // API Routes for AJAX requests
    // مسارات API لطلبات AJAX
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
        Route::get('/orders/{order}/items', [PurchaseOrderController::class, 'getItems'])->name('orders.items');
        Route::get('/suppliers/{supplier}/balance', [SupplierController::class, 'getBalance'])->name('suppliers.balance');
    });
});
