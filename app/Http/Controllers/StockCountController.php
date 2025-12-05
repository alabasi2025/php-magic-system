<?php

namespace App\Http\Controllers;

use App\Models\StockCount;
use App\Services\StockCountService;
use App\Http\Requests\StockCountStoreRequest;
use App\Http\Requests\StockCountUpdateQuantitiesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * المتحكم الخاص بعمليات الجرد.
 */
class StockCountController extends Controller
{
    protected StockCountService $stockCountService;

    public function __construct(StockCountService $stockCountService)
    {
        $this->stockCountService = $stockCountService;
        // تطبيق سياسة الأمان على جميع الدوال
        $this->authorizeResource(StockCount::class, 'stockCount');
    }

    /**
     * عرض قائمة بعمليات الجرد.
     */
    public function index()
    {
        $stockCounts = StockCount::with(['warehouse', 'creator'])
            ->latest()
            ->paginate(10);

        return view('stock_counts.index', compact('stockCounts'));
    }

    /**
     * عرض نموذج إنشاء عملية جرد جديدة.
     */
    public function create()
    {
        // نفترض وجود قائمة بالمخازن والأصناف
        $warehouses = \App\Models\Warehouse::all();
        $items = \App\Models\Item::all();

        return view('stock_counts.create', compact('warehouses', 'items'));
    }

    /**
     * تخزين عملية جرد جديدة في قاعدة البيانات.
     */
    public function store(StockCountStoreRequest $request)
    {
        try {
            $itemsCollection = collect($request->items);
            $stockCount = $this->stockCountService->createStockCount(
                $request->validated(),
                $itemsCollection,
                Auth::id()
            );

            return redirect()->route('stock_counts.edit', $stockCount)
                ->with('success', 'تم إنشاء عملية الجرد بنجاح. يرجى إدخال الكميات الفعلية.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'فشل في إنشاء عملية الجرد: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل عملية جرد محددة.
     */
    public function show(StockCount $stockCount)
    {
        $stockCount->load(['warehouse', 'creator', 'approver', 'details.item']);
        return view('stock_counts.show', compact('stockCount'));
    }

    /**
     * عرض نموذج تعديل عملية جرد (لإدخال الكميات الفعلية).
     */
    public function edit(StockCount $stockCount)
    {
        // التحقق من الصلاحية مرة أخرى للتأكد من حالة الجرد
        Gate::authorize('update', $stockCount);

        $stockCount->load('details.item');
        return view('stock_counts.edit', compact('stockCount'));
    }

    /**
     * تحديث الكميات الفعلية لعملية الجرد.
     */
    public function update(StockCountUpdateQuantitiesRequest $request, StockCount $stockCount)
    {
        // التحقق من الصلاحية مرة أخرى للتأكد من حالة الجرد
        Gate::authorize('update', $stockCount);

        try {
            $this->stockCountService->enterActualQuantities($stockCount, $request->details);

            return redirect()->route('stock_counts.show', $stockCount)
                ->with('success', 'تم إدخال الكميات الفعلية بنجاح. الجرد الآن قيد المراجعة.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'فشل في تحديث الكميات: ' . $e->getMessage());
        }
    }

    /**
     * دالة إضافية: الموافقة على الجرد وتعديل المخزون.
     */
    public function approve(StockCount $stockCount)
    {
        Gate::authorize('approve', $stockCount);

        try {
            $this->stockCountService->approveAndAdjustStock($stockCount, Auth::id());

            return redirect()->route('stock_counts.show', $stockCount)
                ->with('success', 'تمت الموافقة على الجرد وتعديل المخزون بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل في الموافقة على الجرد: ' . $e->getMessage());
        }
    }

    /**
     * حذف عملية جرد.
     */
    public function destroy(StockCount $stockCount)
    {
        Gate::authorize('delete', $stockCount);

        try {
            $this->stockCountService->deleteStockCount($stockCount);

            return redirect()->route('stock_counts.index')
                ->with('success', 'تم حذف عملية الجرد بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل في حذف عملية الجرد: ' . $e->getMessage());
        }
    }
}
