<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    protected WarehouseService $warehouseService;

    /**
     * تهيئة المتحكم وحقن خدمة المخازن.
     */
    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
        // تطبيق سياسة الأمان على جميع الدوال باستثناء الإحصائيات
        $this->authorizeResource(Warehouse::class, 'warehouse');
    }

    /**
     * عرض قائمة بجميع المخازن.
     */
    public function index(Request $request): View
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (viewAny)
        $warehouses = $this->warehouseService->getAllWarehouses(10);

        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * عرض نموذج إنشاء مخزن جديد.
     */
    public function create(): View
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (create)
        // يجب تمرير قائمة المدراء المحتملين (Users) إلى الواجهة
        $managers = \App\Models\User::all(); // مثال
        return view('warehouses.create', compact('managers'));
    }

    /**
     * تخزين مخزن جديد في قاعدة البيانات.
     */
    public function store(WarehouseStoreRequest $request): RedirectResponse
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (create)
        // يتم التحقق من الصحة عبر WarehouseStoreRequest

        try {
            $this->warehouseService->createWarehouse($request->validated());
            return redirect()->route('warehouses.index')->with('success', 'تم إنشاء المخزن بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء وإعادة التوجيه مع رسالة خطأ
            return back()->withInput()->with('error', 'فشل إنشاء المخزن: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مخزن معين.
     */
    public function show(Warehouse $warehouse): View
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (view)
        return view('warehouses.show', compact('warehouse'));
    }

    /**
     * عرض نموذج تعديل مخزن موجود.
     */
    public function edit(Warehouse $warehouse): View
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (update)
        $managers = \App\Models\User::all(); // مثال
        return view('warehouses.edit', compact('warehouse', 'managers'));
    }

    /**
     * تحديث بيانات مخزن موجود في قاعدة البيانات.
     */
    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse): RedirectResponse
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (update)
        // يتم التحقق من الصحة عبر WarehouseUpdateRequest

        try {
            $this->warehouseService->updateWarehouse($warehouse, $request->validated());
            return redirect()->route('warehouses.index')->with('success', 'تم تحديث بيانات المخزن بنجاح.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'فشل تحديث المخزن: ' . $e->getMessage());
        }
    }

    /**
     * حذف مخزن من قاعدة البيانات.
     */
    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        // يتم التحقق من الصلاحية تلقائياً عبر authorizeResource (delete)

        try {
            $this->warehouseService->deleteWarehouse($warehouse);
            return redirect()->route('warehouses.index')->with('success', 'تم حذف المخزن بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل حذف المخزن: ' . $e->getMessage());
        }
    }

    /**
     * تبديل حالة تفعيل المخزن (تفعيل/تعطيل).
     */
    public function toggleStatus(Warehouse $warehouse): RedirectResponse
    {
        // التحقق من الصلاحية بشكل يدوي للدوال الإضافية
        $this->authorize('toggleStatus', $warehouse);

        try {
            $this->warehouseService->toggleStatus($warehouse);
            $status = $warehouse->is_active ? 'تفعيل' : 'تعطيل';
            return back()->with('success', "تم $status المخزن بنجاح.");
        } catch (\Exception $e) {
            return back()->with('error', 'فشل تغيير حالة المخزن: ' . $e->getMessage());
        }
    }

    /**
     * عرض إحصائيات المخازن.
     */
    public function statistics(): View
    {
        // التحقق من الصلاحية بشكل يدوي
        $this->authorize('viewStatistics', Warehouse::class);

        $statistics = $this->warehouseService->getWarehousesStatistics();

        return view('warehouses.statistics', compact('statistics'));
    }
}
