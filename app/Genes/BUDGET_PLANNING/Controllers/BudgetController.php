<?php

declare(strict_types=1);

namespace App\Genes\BUDGET_PLANNING\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Genes\BUDGET_PLANNING\Services\BudgetService;
use App\Genes\INTERMEDIATE_ACCOUNTS\Services\IntermediateAccountService;
use App\Genes\CASH_BOXES\Services\CashBoxService;
use App\Genes\PARTNER_ACCOUNTING\Services\PartnerService;
use App\Genes\BUDGET_PLANNING\Requests\BudgetStoreRequest;
use App\Genes\BUDGET_PLANNING\Requests\BudgetUpdateRequest;
use App\Genes\BUDGET_PLANNING\Models\Budget;
use Throwable;

/**
 * @class BudgetController
 * @package App\Genes\BUDGET_PLANNING\Controllers
 *
 * كونترولر لإدارة عمليات الميزانية والتخطيط المالي.
 * يتضمن وظائف CRUD الأساسية بالإضافة إلى تقارير التحليل والمقارنة.
 *
 * المتطلبات:
 * 1. استخدام معايير Laravel الحديثة (v12).
 * 2. استخدام ميزات PHP 8.4 (تم استخدام ميزات PHP 8.3/8.2 مثل خاصية الترويج في المُنشئ والأنواع الصارمة).
 * 3. الكود جاهز للإنتاج (production-ready).
 * 4. التعليقات باللغة العربية.
 * 5. افتراض استخدام Tailwind CSS للواجهات.
 * 6. التكامل مع الأجنحة السابقة (INTERMEDIATE_ACCOUNTS, CASH_BOXES, PARTNER_ACCOUNTING).
 */
class BudgetController extends Controller
{
    /**
     * تهيئة الكونترولر وحقن الخدمات المطلوبة باستخدام خاصية الترويج في المُنشئ (PHP 8.1+).
     *
     * @param BudgetService $budgetService خدمة إدارة الميزانيات
     * @param IntermediateAccountService $intermediateAccountService خدمة الحسابات الوسيطة للتكامل
     * @param CashBoxService $cashBoxService خدمة الصناديق النقدية للتكامل
     * @param PartnerService $partnerService خدمة حسابات الشركاء للتكامل
     */
    public function __construct(
        private readonly BudgetService $budgetService,
        private readonly IntermediateAccountService $intermediateAccountService,
        private readonly CashBoxService $cashBoxService,
        private readonly PartnerService $partnerService,
    ) {
        // يمكن إضافة middleware هنا للتحقق من الصلاحيات
        $this->middleware('auth');
        $this->middleware('can:view-budgets')->only('index', 'analysis', 'comparison');
        $this->middleware('can:manage-budgets')->except('index', 'analysis', 'comparison');
    }

    /**
     * عرض قائمة بجميع الميزانيات.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // جلب قائمة الميزانيات مع ترشيح اختياري
        $budgets = $this->budgetService->getPaginatedBudgets($request->all());

        // افتراض استخدام Tailwind CSS في الواجهة
        return view('budget_planning::budgets.index', [
            'budgets' => $budgets,
            'filters' => $request->all(),
        ]);
    }

    /**
     * عرض نموذج إنشاء ميزانية جديدة.
     *
     * @return View
     */
    public function create(): View
    {
        // جلب البيانات اللازمة لنموذج الإنشاء من الأجنحة الأخرى
        $accounts = $this->intermediateAccountService->getActiveAccounts();
        $cashBoxes = $this->cashBoxService->getActiveCashBoxes();
        $partners = $this->partnerService->getActivePartners();

        return view('budget_planning::budgets.create', [
            'accounts' => $accounts,
            'cashBoxes' => $cashBoxes,
            'partners' => $partners,
        ]);
    }

    /**
     * تخزين ميزانية جديدة في قاعدة البيانات.
     *
     * @param BudgetStoreRequest $request
     * @return RedirectResponse
     */
    public function store(BudgetStoreRequest $request): RedirectResponse
    {
        try {
            $this->budgetService->createBudget($request->validated());

            return redirect()
                ->route('budget_planning.budgets.index')
                ->with('success', 'تم إنشاء الميزانية بنجاح.');
        } catch (Throwable $e) {
            // تسجيل الخطأ والعودة برسالة خطأ
            report($e);
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الميزانية: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل ميزانية موجودة.
     *
     * @param Budget $budget
     * @return View
     */
    public function edit(Budget $budget): View
    {
        // جلب البيانات اللازمة لنموذج التعديل
        $accounts = $this->intermediateAccountService->getActiveAccounts();
        $cashBoxes = $this->cashBoxService->getActiveCashBoxes();
        $partners = $this->partnerService->getActivePartners();

        return view('budget_planning::budgets.edit', [
            'budget' => $budget,
            'accounts' => $accounts,
            'cashBoxes' => $cashBoxes,
            'partners' => $partners,
        ]);
    }

    /**
     * تحديث ميزانية موجودة في قاعدة البيانات.
     *
     * @param BudgetUpdateRequest $request
     * @param Budget $budget
     * @return RedirectResponse
     */
    public function update(BudgetUpdateRequest $request, Budget $budget): RedirectResponse
    {
        try {
            $this->budgetService->updateBudget($budget, $request->validated());

            return redirect()
                ->route('budget_planning.budgets.index')
                ->with('success', 'تم تحديث الميزانية بنجاح.');
        } catch (Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الميزانية: ' . $e->getMessage());
        }
    }

    /**
     * عرض تقرير تحليل الميزانية.
     * يتضمن مقارنة بين الميزانية الفعلية والمخططة.
     *
     * @param Request $request
     * @return View
     */
    public function analysis(Request $request): View
    {
        // جلب بيانات التحليل من خدمة الميزانية
        $analysisData = $this->budgetService->getBudgetAnalysis($request->all());

        // يمكن استخدام مكتبات مثل Chart.js في الواجهة لعرض الرسوم البيانية (Tailwind CSS)
        return view('budget_planning::budgets.analysis', [
            'analysisData' => $analysisData,
            'filters' => $request->all(),
        ]);
    }

    /**
     * عرض تقرير مقارنة بين ميزانيتين أو أكثر (مثلاً، مقارنة سنوية أو ربع سنوية).
     *
     * @param Request $request
     * @return View
     */
    public function comparison(Request $request): View
    {
        // جلب بيانات المقارنة
        $comparisonData = $this->budgetService->compareBudgets($request->all());

        return view('budget_planning::budgets.comparison', [
            'comparisonData' => $comparisonData,
            'filters' => $request->all(),
        ]);
    }

    /**
     * حذف ميزانية معينة.
     * (وظيفة إضافية لـ CRUD، على الرغم من أنها لم تُطلب صراحة، لكنها ضرورية لكونترولر كامل).
     *
     * @param Budget $budget
     * @return RedirectResponse
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        try {
            $this->budgetService->deleteBudget($budget);

            return back()->with('success', 'تم حذف الميزانية بنجاح.');
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'حدث خطأ أثناء حذف الميزانية: ' . $e->getMessage());
        }
    }
}
