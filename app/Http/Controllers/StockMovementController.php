<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Services\StockMovementService;
use App\Http\Requests\StockMovementStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

class StockMovementController extends Controller
{
    protected $movementService;

    /**
     * تهيئة المتحكم.
     */
    public function __construct(StockMovementService $movementService)
    {
        $this->movementService = $movementService;
        // تطبيق سياسة الأمان (Authorization) على جميع الدوال
        $this->authorizeResource(StockMovement::class, 'movement');
    }

    /**
     * عرض سجل حركات المخزون (Index).
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحية لعرض السجل
        if (Gate::denies('viewAny', StockMovement::class)) {
            abort(403, 'غير مصرح لك بمشاهدة سجل حركات المخزون.');
        }

        try {
            $filters = $request->only(['warehouse_id', 'item_id', 'movement_type', 'start_date', 'end_date']);
            $movements = $this->movementService->getMovementsHistory($filters);

            return view('stock_movements.index', compact('movements', 'filters'));
        } catch (Exception $e) {
            // معالجة الأخطاء
            return back()->with('error', 'حدث خطأ أثناء جلب سجل الحركات: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج إنشاء حركة جديدة (Create).
     */
    public function create()
    {
        // التحقق من الصلاحية لإنشاء حركة
        if (Gate::denies('create', StockMovement::class)) {
            abort(403, 'غير مصرح لك بإنشاء حركات مخزون.');
        }

        // جلب البيانات اللازمة للعرض (مثل المخازن والأصناف)
        $warehouses = \App\Models\Warehouse::all();
        $items = \App\Models\Item::all();

        return view('stock_movements.create', compact('warehouses', 'items'));
    }

    /**
     * تخزين حركة مخزون جديدة (Store).
     */
    public function store(StockMovementStoreRequest $request)
    {
        // التحقق من الصلاحية يتم تلقائياً عبر StockMovementStoreRequest::authorize()

        try {
            // يجب تحويل الكمية إلى قيمة سالبة إذا كانت حركة "خروج"
            $data = $request->validated();
            if ($data['movement_type'] === 'out' || $data['movement_type'] === 'transfer') {
                $data['quantity'] = -$data['quantity'];
            }

            $movement = $this->movementService->createMovement($data);

            return redirect()->route('stock_movements.index')->with('success', 'تم تسجيل الحركة بنجاح. الرصيد الجديد: ' . $movement->balance_after);
        } catch (Exception $e) {
            // معالجة الأخطاء الناتجة عن منطق الأعمال (مثل الرصيد غير الكافي)
            return back()->withInput()->with('error', 'فشل تسجيل الحركة: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل حركة مخزون محددة (Show).
     */
    public function show(StockMovement $movement)
    {
        // التحقق من الصلاحية يتم تلقائياً عبر authorizeResource
        return view('stock_movements.show', compact('movement'));
    }

    // لا يتم دعم التعديل (Edit/Update) أو الحذف (Destroy) لحركات المخزون عادةً
    // لأنها سجلات تاريخية. أي تصحيح يتم عبر حركة "تسوية" جديدة.
    // لذلك، لن يتم تضمين دوال update و destroy.

    /**
     * تقرير حركة صنف محدد (ميزة إضافية).
     */
    public function itemReport(Request $request)
    {
        // التحقق من الصلاحية
        if (Gate::denies('viewReports', StockMovement::class)) {
            abort(403, 'غير مصرح لك بمشاهدة تقارير الحركة.');
        }

        $itemId = $request->input('item_id');
        $warehouseId = $request->input('warehouse_id');

        if (!$itemId) {
            return view('stock_movements.item_report', ['movements' => null, 'items' => \App\Models\Item::all()]);
        }

        $movements = $this->movementService->getItemMovementReport($itemId, $warehouseId);
        $item = \App\Models\Item::find($itemId);

        return view('stock_movements.item_report', compact('movements', 'item'));
    }

    /**
     * تقرير حركة مخزن محدد (ميزة إضافية).
     */
    public function warehouseReport(Request $request)
    {
        // التحقق من الصلاحية
        if (Gate::denies('viewReports', StockMovement::class)) {
            abort(403, 'غير مصرح لك بمشاهدة تقارير الحركة.');
        }

        $warehouseId = $request->input('warehouse_id');
        $itemId = $request->input('item_id');

        if (!$warehouseId) {
            return view('stock_movements.warehouse_report', ['movements' => null, 'warehouses' => \App\Models\Warehouse::all()]);
        }

        $movements = $this->movementService->getWarehouseMovementReport($warehouseId, $itemId);
        $warehouse = \App\Models\Warehouse::find($warehouseId);

        return view('stock_movements.warehouse_report', compact('movements', 'warehouse'));
    }
}
