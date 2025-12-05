<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Services\StockInService;
use App\Http\Requests\StockInStoreRequest;
use App\Http\Requests\StockInUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Exception;

class StockInController extends Controller
{
    protected StockInService $stockInService;

    public function __construct(StockInService $stockInService)
    {
        $this->stockInService = $stockInService;
        // تطبيق سياسة الصلاحيات على جميع الدوال
        $this->authorizeResource(StockIn::class, 'stock_in');
    }

    /**
     * عرض قائمة بإذونات الإدخال. (CRUD - Read All)
     */
    public function index(Request $request): View
    {
        // يمكن إضافة منطق التصفية والبحث والترتيب هنا
        $stockIns = StockIn::with(['warehouse', 'supplier', 'createdBy'])
                            ->latest()
                            ->paginate(10);

        return view('stock_ins.index', compact('stockIns'));
    }

    /**
     * عرض نموذج إنشاء إذن إدخال جديد. (CRUD - Create Form)
     */
    public function create(): View
    {
        // يجب تمرير قائمة المخازن والموردين والأصناف إلى الواجهة
        // نفترض وجود نماذج Warehouse, Supplier, Item
        $warehouses = \App\Models\Warehouse::all();
        $suppliers = \App\Models\Supplier::all();
        $items = \App\Models\Item::all();

        return view('stock_ins.create', compact('warehouses', 'suppliers', 'items'));
    }

    /**
     * تخزين إذن إدخال جديد في قاعدة البيانات. (CRUD - Create Action)
     */
    public function store(StockInStoreRequest $request): RedirectResponse
    {
        try {
            $stockIn = $this->stockInService->createStockIn($request->validated());
            
            return redirect()->route('stock_ins.show', $stockIn)
                             ->with('success', 'تم إنشاء إذن الإدخال بنجاح. رقم الإذن: ' . $stockIn->number);
        } catch (Exception $e) {
            // معالجة الأخطاء الناتجة عن منطق الخدمة
            return back()->withInput()->with('error', 'فشل في إنشاء إذن الإدخال: ' . $e->getMessage());
        }
    }

    /**
     * عرض إذن إدخال محدد. (CRUD - Read Single)
     */
    public function show(StockIn $stockIn): View
    {
        // تحميل التفاصيل والعلاقات اللازمة
        $stockIn->load(['warehouse', 'supplier', 'createdBy', 'details.item']);

        return view('stock_ins.show', compact('stockIn'));
    }

    /**
     * عرض نموذج تعديل إذن إدخال موجود. (CRUD - Update Form)
     */
    public function edit(StockIn $stockIn): View|RedirectResponse
    {
        // التحقق من الصلاحية مرة أخرى، خاصة حالة الإذن
        if ($stockIn->status !== 'Draft') {
            return redirect()->route('stock_ins.show', $stockIn)
                             ->with('error', 'لا يمكن تعديل إذن إدخال ليس في حالة المسودة.');
        }

        $stockIn->load('details');
        $warehouses = \App\Models\Warehouse::all();
        $suppliers = \App\Models\Supplier::all();
        $items = \App\Models\Item::all();

        return view('stock_ins.edit', compact('stockIn', 'warehouses', 'suppliers', 'items'));
    }

    /**
     * تحديث إذن إدخال محدد في قاعدة البيانات. (CRUD - Update Action)
     */
    public function update(StockInUpdateRequest $request, StockIn $stockIn): RedirectResponse
    {
        try {
            $stockIn = $this->stockInService->updateStockIn($stockIn, $request->validated());

            return redirect()->route('stock_ins.show', $stockIn)
                             ->with('success', 'تم تحديث إذن الإدخال بنجاح.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'فشل في تحديث إذن الإدخال: ' . $e->getMessage());
        }
    }

    /**
     * حذف إذن إدخال محدد من قاعدة البيانات. (CRUD - Delete)
     */
    public function destroy(StockIn $stockIn): RedirectResponse
    {
        try {
            $this->stockInService->deleteStockIn($stockIn);

            return redirect()->route('stock_ins.index')
                             ->with('success', 'تم حذف إذن الإدخال بنجاح.');
        } catch (Exception $e) {
            return back()->with('error', 'فشل في حذف إذن الإدخال: ' . $e->getMessage());
        }
    }

    /**
     * دالة إضافية: ترحيل إذن الإدخال.
     */
    public function complete(StockIn $stockIn): RedirectResponse
    {
        // التحقق من الصلاحية لعملية الترحيل
        if (Gate::denies('complete', $stockIn)) {
            abort(403, 'غير مصرح لك بترحيل هذا الإذن.');
        }

        try {
            $this->stockInService->completeStockIn($stockIn);

            return redirect()->route('stock_ins.show', $stockIn)
                             ->with('success', 'تم ترحيل إذن الإدخال بنجاح وتحديث أرصدة المخزون.');
        } catch (Exception $e) {
            return back()->with('error', 'فشل في ترحيل إذن الإدخال: ' . $e->getMessage());
        }
    }
}
