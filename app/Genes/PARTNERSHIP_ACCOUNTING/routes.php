<?php

use Illuminate\Support\Facades\Route;
use App\Genes\PARTNERSHIP_ACCOUNTING\Controllers\PartnerController;
use App\Genes\PARTNERSHIP_ACCOUNTING\Controllers\RevenueController;
use App\Genes\PARTNERSHIP_ACCOUNTING\Controllers\ExpenseController;
use App\Genes\PARTNERSHIP_ACCOUNTING\Controllers\ProfitController;
use App\Genes\PARTNERSHIP_ACCOUNTING\Controllers\PartnershipReportController;

/*
|--------------------------------------------------------------------------
| PARTNERSHIP_ACCOUNTING Gene Routes
|--------------------------------------------------------------------------
|
| مسارات جين محاسبة الشراكات
| نظام إدارة الشراكات والإيرادات والمصروفات وتوزيع الأرباح
|
*/

Route::prefix('api/partnership')->middleware(['auth:api'])->group(function () {
    
    // إدارة الشركاء
    Route::prefix('partners')->group(function () {
        Route::get('/', [PartnerController::class, 'index']);
        Route::post('/', [PartnerController::class, 'store']);
        Route::get('/{partner}', [PartnerController::class, 'show']);
        Route::put('/{partner}', [PartnerController::class, 'update']);
        Route::delete('/{partner}', [PartnerController::class, 'destroy']);
        
        // نسب الملكية
        Route::get('/{partner}/shares', [PartnerController::class, 'getShares']);
        Route::post('/{partner}/shares', [PartnerController::class, 'updateShares']);
    });
    
    // إدارة الإيرادات
    Route::prefix('revenues')->group(function () {
        Route::get('/', [RevenueController::class, 'index']);
        Route::post('/', [RevenueController::class, 'store']);
        Route::get('/{revenue}', [RevenueController::class, 'show']);
        Route::put('/{revenue}', [RevenueController::class, 'update']);
        Route::delete('/{revenue}', [RevenueController::class, 'destroy']);
        
        // إيرادات حسب المشروع
        Route::get('/project/{projectId}', [RevenueController::class, 'byProject']);
        // إيرادات حسب الوحدة
        Route::get('/unit/{unitId}', [RevenueController::class, 'byUnit']);
    });
    
    // إدارة المصروفات
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index']);
        Route::post('/', [ExpenseController::class, 'store']);
        Route::get('/{expense}', [ExpenseController::class, 'show']);
        Route::put('/{expense}', [ExpenseController::class, 'update']);
        Route::delete('/{expense}', [ExpenseController::class, 'destroy']);
        
        // مصروفات حسب المشروع
        Route::get('/project/{projectId}', [ExpenseController::class, 'byProject']);
        // مصروفات حسب الوحدة
        Route::get('/unit/{unitId}', [ExpenseController::class, 'byUnit']);
        // مصروفات حسب النوع
        Route::get('/by-type', [ExpenseController::class, 'byType']);
    });
    
    // حساب وتوزيع الأرباح
    Route::prefix('profits')->group(function () {
        // حساب الأرباح
        Route::post('/calculate', [ProfitController::class, 'calculate']);
        Route::get('/calculations', [ProfitController::class, 'listCalculations']);
        Route::get('/calculations/{calculation}', [ProfitController::class, 'showCalculation']);
        
        // توزيع الأرباح
        Route::post('/distribute/{calculation}', [ProfitController::class, 'distribute']);
        Route::get('/distributions', [ProfitController::class, 'listDistributions']);
        Route::get('/distributions/{distribution}', [ProfitController::class, 'showDistribution']);
        
        // حسابات الأرباح حسب الوحدة
        Route::get('/unit/{unitId}', [ProfitController::class, 'byUnit']);
        // حسابات الأرباح حسب الشريك
        Route::get('/partner/{partnerId}', [ProfitController::class, 'byPartner']);
    });
    
    // التقارير
    Route::prefix('reports')->group(function () {
        // تقرير الإيرادات
        Route::get('/revenues', [PartnershipReportController::class, 'revenuesReport']);
        
        // تقرير المصروفات
        Route::get('/expenses', [PartnershipReportController::class, 'expensesReport']);
        
        // تقرير الأرباح
        Route::get('/profits', [PartnershipReportController::class, 'profitsReport']);
        
        // تقرير توزيع الأرباح
        Route::get('/distributions', [PartnershipReportController::class, 'distributionsReport']);
        
        // تقرير ملخص الشراكة
        Route::get('/partnership-summary/{unitId}', [PartnershipReportController::class, 'partnershipSummary']);
        
        // تقرير مقارنة المحطات
        Route::get('/projects-comparison', [PartnershipReportController::class, 'projectsComparison']);
    });
});
