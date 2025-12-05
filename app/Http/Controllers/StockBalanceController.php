<?php

namespace App\Http\Controllers;

use App\Models\StockBalance;
use App\Services\StockBalanceService;
use App\Http\Requests\StockBalance\StoreStockBalanceRequest;
use App\Http\Requests\StockBalance\UpdateStockBalanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class StockBalanceController extends Controller
{
    protected StockBalanceService $stockBalanceService;

    public function __construct(StockBalanceService $stockBalanceService)
    {
        $this->stockBalanceService = $stockBalanceService;
        // تطبيق سياسة الأمان (Authorization) على جميع الدوال
        $this->authorizeResource(StockBalance::class, 'stock_balance');
    }

    /**
     * عرض قائمة بأرصدة المخزون. (عرض الرصيد الحالي)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // البحث والتصفية
        $balances = StockBalance::with(['warehouse', 'item'])
            ->when($request->filled('warehouse_id'), function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->filled('item_id'), function ($query) use ($request) {
                $query->where('item_id', $request->item_id);
            })
            ->paginate(15);

        return view('stock_balances.index', compact('balances'));
    }

    /**
     * عرض نموذج إنشاء رصيد مخزون جديد.
     *
     * @return View
     */
    public function create(): View
    {
        // يتم إنشاء الأرصدة عادةً بشكل آلي عند أول حركة إدخال، ولكن هذه الدالة للـ CRUD الأساسي
        // نفترض وجود متغيرات $warehouses و $items
        $warehouses = []; // يجب جلبها من قاعدة البيانات
        $items = []; // يجب جلبها من قاعدة البيانات
        return view('stock_balances.create', compact('warehouses', 'items'));
    }

    /**
     * تخزين رصيد مخزون جديد في قاعدة البيانات.
     *
     * @param StoreStockBalanceRequest $request
     * @return RedirectResponse
     */
    public function store(StoreStockBalanceRequest $request): RedirectResponse
    {
        try {
            $balance = StockBalance::create($request->validated());
            // يمكن استدعاء خدمة حساب متوسط التكلفة هنا إذا كان الرصيد الأولي غير صفر
            // $this->stockBalanceService->calculateAndSaveAverageCost($balance, $balance->last_cost, $balance->quantity);

            return redirect()->route('stock_balances.index')
                ->with('success', 'تم إنشاء رصيد المخزون بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الرصيد: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل رصيد مخزون محدد.
     *
     * @param StockBalance $stockBalance
     * @return View
     */
    public function show(StockBalance $stockBalance): View
    {
        return view('stock_balances.show', compact('stockBalance'));
    }

    /**
     * عرض نموذج تعديل رصيد مخزون محدد.
     *
     * @param StockBalance $stockBalance
     * @return View
     */
    public function edit(StockBalance $stockBalance): View
    {
        // نفترض وجود متغيرات $warehouses و $items
        $warehouses = []; // يجب جلبها من قاعدة البيانات
        $items = []; // يجب جلبها من قاعدة البيانات
        return view('stock_balances.edit', compact('stockBalance', 'warehouses', 'items'));
    }

    /**
     * تحديث رصيد مخزون محدد في قاعدة البيانات.
     *
     * @param UpdateStockBalanceRequest $request
     * @param StockBalance $stockBalance
     * @return RedirectResponse
     */
    public function update(UpdateStockBalanceRequest $request, StockBalance $stockBalance): RedirectResponse
    {
        try {
            $stockBalance->update($request->validated());

            return redirect()->route('stock_balances.index')
                ->with('success', 'تم تحديث رصيد المخزون بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الرصيد: ' . $e->getMessage());
        }
    }

    /**
     * حذف رصيد مخزون محدد من قاعدة البيانات.
     *
     * @param StockBalance $stockBalance
     * @return RedirectResponse
     */
    public function destroy(StockBalance $stockBalance): RedirectResponse
    {
        try {
            $stockBalance->delete();

            return redirect()->route('stock_balances.index')
                ->with('success', 'تم حذف رصيد المخزون بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->with('error', 'حدث خطأ أثناء حذف الرصيد: ' . $e->getMessage());
        }
    }

    // الدوال الإضافية للميزات المطلوبة

    /**
     * عرض تنبيهات الحد الأدنى للمخزون.
     *
     * @param Request $request
     * @return View
     */
    public function minimumStockAlerts(Request $request): View
    {
        // التحقق من صلاحية العرض
        if (Gate::denies('view-alerts', StockBalance::class)) {
            abort(403, 'غير مصرح لك بعرض تنبيهات الحد الأدنى.');
        }

        $warehouseId = $request->input('warehouse_id');
        $alerts = $this->stockBalanceService->getMinimumStockAlerts($warehouseId);

        return view('stock_balances.alerts', [
            'alerts' => $alerts,
            'title' => 'تنبيهات الحد الأدنى للمخزون'
        ]);
    }

    /**
     * عرض تقرير الأصناف الراكدة.
     *
     * @param Request $request
     * @return View
     */
    public function slowMovingReport(Request $request): View
    {
        // التحقق من صلاحية العرض
        if (Gate::denies('view-reports', StockBalance::class)) {
            abort(403, 'غير مصرح لك بعرض تقرير الأصناف الراكدة.');
        }

        $days = $request->input('days', 90);
        $warehouseId = $request->input('warehouse_id');
        $report = $this->stockBalanceService->getSlowMovingItemsReport($days, $warehouseId);

        return view('stock_balances.slow_moving_report', [
            'report' => $report,
            'days' => $days,
            'title' => 'تقرير الأصناف الراكدة'
        ]);
    }
}
