<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountController;

/*
|--------------------------------------------------------------------------
| Chart of Accounts Routes
|--------------------------------------------------------------------------
|
| مسارات دليل الحسابات
|
*/

Route::middleware(['auth'])->group(function () {
    
    // عرض الشجرة الهرمية
    Route::get('/chart-of-accounts/tree', [ChartOfAccountController::class, 'tree'])
        ->name('chart-of-accounts.tree');
    
    // Resource routes
    Route::resource('chart-of-accounts', ChartOfAccountController::class);
    
});
