<?php

use Illuminate\Support\Facades\Route;
use App\Genes\BUDGET_PLANNING\Controllers\BudgetController;
use App\Genes\BUDGET_PLANNING\Controllers\BudgetItemController;

/*
|--------------------------------------------------------------------------
| مسارات تخطيط الميزانية (BUDGET_PLANNING Routes)
|--------------------------------------------------------------------------
|
| هذه المسارات مخصصة لإدارة الميزانيات، بنود الميزانية، وتقارير الميزانية.
| يتم تجميعها تحت البادئة 'budgets' لضمان تنظيم المسارات.
|
| المتطلبات:
| 1. استخدام معايير Laravel الحديثة (v12).
| 2. الكود جاهز للإنتاج (Production-ready).
| 3. التعليقات باللغة العربية.
| 4. التكامل مع الأجنحة السابقة (INTERMEDIATE_ACCOUNTS, CASH_BOXES, PARTNER_ACCOUNTING).
|
*/

Route::group(['prefix' => 'budgets', 'as' => 'budgets.', 'middleware' => ['auth', 'verified']], function () {

    // ------------------------------------------------------------------
    // مسارات إدارة الميزانيات الرئيسية (Budget Management)
    // ------------------------------------------------------------------

    // مسارات CRUD للميزانيات الرئيسية (إنشاء، قراءة، تحديث، حذف)
    Route::resource('main', BudgetController::class)->names('main');

    // ------------------------------------------------------------------
    // مسارات بنود الميزانية (Budget Items)
    // ------------------------------------------------------------------

    // مسارات CRUD لبنود الميزانية المتداخلة تحت الميزانية الرئيسية
    // المسار سيكون على شكل: /budgets/main/{main}/items
    Route::resource('main.items', BudgetItemController::class)
        ->except(['index']) // يتم عرض بنود الميزانية عادةً ضمن صفحة عرض الميزانية الرئيسية (show)
        ->names('items');

    // ------------------------------------------------------------------
    // مسارات التقارير والإجراءات الخاصة (Reports and Special Actions)
    // ------------------------------------------------------------------

    // تقرير الميزانية مقابل الأداء الفعلي (Budget vs Actual Report)
    // يستخدم لمقارنة المبالغ المخططة بالمبالغ الفعلية المسجلة في النظام المحاسبي.
    Route::get('reports/actual-vs-budget', [BudgetController::class, 'actualVsBudgetReport'])
        ->name('reports.actual_vs_budget');

    // مسار لنسخ ميزانية سابقة لإنشاء ميزانية جديدة بسرعة
    Route::post('{budget}/duplicate', [BudgetController::class, 'duplicate'])
        ->name('duplicate');

    // مسار لتغيير حالة الميزانية (مثل: قيد المراجعة، معتمدة، مغلقة)
    Route::patch('{budget}/status', [BudgetController::class, 'updateStatus'])
        ->name('update_status');

    // ------------------------------------------------------------------
    // مسارات التكامل مع الأجنحة الأخرى (Integration Data)
    // ------------------------------------------------------------------

    // مسار لجلب البيانات اللازمة من الأجنحة الأخرى (مثل قائمة الحسابات الوسيطة، الصناديق، الشركاء)
    // هذا المسار يستخدمه الواجهة الأمامية لجلب خيارات القوائم المنسدلة عند إنشاء بند ميزانية.
    Route::get('data/integration', [BudgetController::class, 'getIntegrationData'])
        ->name('data.integration');

});
