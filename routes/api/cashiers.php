<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashiers\CashierController;

/*
|--------------------------------------------------------------------------
| Cashiers API Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with 'api/cashiers' and use the 'auth:sanctum'
| middleware for authentication. They define the API endpoints for the
| Cashiers Gene (نظام الصرافين).
|
*/

Route::middleware('auth:sanctum')->prefix('cashiers')->group(function () {
    // Task 2016: Backend - نظام الصرافين (Cashiers) - Backend - Task 1
    // الهدف: إنشاء المسار الأساسي (Index) لنظام الصرافين.
    Route::get('/', [CashierController::class, 'index'])
        ->name('cashiers.index')
        ->middleware('can:view-cashiers'); // افتراض صلاحية لعرض الصرافين

    // يمكن إضافة مسارات أخرى هنا لاحقاً
    // Route::post('/', [CashierController::class, 'store'])->name('cashiers.store');
    // Route::get('/{cashier}', [CashierController::class, 'show'])->name('cashiers.show');
});