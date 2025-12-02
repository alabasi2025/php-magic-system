<?php

use Illuminate\Support\Facades\Route;
use App\Genes\ACCOUNTING_REPORTS\Controllers\ReportController;
// يمكن استيراد وحدات تحكم أخرى من الأجنحة السابقة للتكامل
// use App\Genes\INTERMEDIATE_ACCOUNTS\Controllers\IntermediateAccountController;
// use App\Genes\CASH_BOXES\Controllers\CashBoxController;
// use App\Genes\PARTNER_ACCOUNTING\Controllers\PartnerController;

/*
|--------------------------------------------------------------------------
| مسارات تقارير المحاسبة (ACCOUNTING_REPORTS Gene Routes)
|--------------------------------------------------------------------------
|
| يتم تجميع جميع المسارات المتعلقة بتقارير المحاسبة ضمن المجموعة التالية.
| البادئة (prefix) هي 'accounting-reports'.
|
*/

Route::group([
    'prefix' => 'accounting-reports',
    'as' => 'accounting_reports.', // اسم المسار المسبوق
    'middleware' => ['web', 'auth', 'gene:ACCOUNTING_REPORTS'], // افتراضياً، يتطلب جلسة، مصادقة، وتفعيل الجين
], function () {

    // مسار لوحة تحكم التقارير - ملخص عام
    Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');

    // تقرير ميزان المراجعة
    Route::get('trial-balance', [ReportController::class, 'trialBalance'])->name('trial_balance');
    Route::post('trial-balance/generate', [ReportController::class, 'generateTrialBalance'])->name('trial_balance.generate');

    // تقرير قائمة الدخل
    Route::get('income-statement', [ReportController::class, 'incomeStatement'])->name('income_statement');
    Route::post('income-statement/generate', [ReportController::class, 'generateIncomeStatement'])->name('income_statement.generate');

    // تقرير الميزانية العمومية
    Route::get('balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance_sheet');
    Route::post('balance-sheet/generate', [ReportController::class, 'generateBalanceSheet'])->name('balance_sheet.generate');

    // تقرير دفتر الأستاذ العام
    Route::get('general-ledger', [ReportController::class, 'generalLedger'])->name('general_ledger');
    Route::post('general-ledger/generate', [ReportController::class, 'generateGeneralLedger'])->name('general_ledger.generate');

    // تقرير قائمة التدفقات النقدية
    Route::get('cash-flow', [ReportController::class, 'cashFlow'])->name('cash_flow');
    Route::post('cash-flow/generate', [ReportController::class, 'generateCashFlow'])->name('cash_flow.generate');

    // --------------------------------------------------------------------
    // مسارات التكامل مع الأجنحة السابقة (INTERMEDIATE_ACCOUNTS, CASH_BOXES, PARTNER_ACCOUNTING)
    // --------------------------------------------------------------------

    // تقرير حركة الحسابات الوسيطة (تكامل مع INTERMEDIATE_ACCOUNTS)
    Route::get('intermediate-accounts-report', [ReportController::class, 'intermediateAccountsReport'])->name('intermediate_accounts_report');

    // تقرير حركة الصناديق (تكامل مع CASH_BOXES)
    Route::get('cash-boxes-report', [ReportController::class, 'cashBoxesReport'])->name('cash_boxes_report');

    // تقرير كشف حساب الشركاء (تكامل مع PARTNER_ACCOUNTING)
    Route::get('partner-statement/{partner}', [ReportController::class, 'partnerStatement'])->name('partner_statement');

    // مسار لعرض واجهة إعدادات التقارير (مثال على استخدام Tailwind CSS في الواجهات)
    Route::get('settings', [ReportController::class, 'settings'])->name('settings');
});
