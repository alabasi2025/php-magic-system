<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Warehouse; // افتراض وجود هذا النموذج
use App\Models\Customer; // افتراض وجود هذا النموذج
use App\Models\Item; // افتراض وجود هذا النموذج
use App\Services\StockOutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class StockOutController extends Controller
{
    protected $stockOutService;

    /**
     * تهيئة المتحكم.
     *
     * @param StockOutService $stockOutService
     */
    public function __construct(StockOutService $stockOutService)
    {
        $this->stockOutService = $stockOutService;
        // تطبيق سياسة الأمان (Authorization)
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\StockOut')->only('index');
        $this->middleware('can:create,App\Models\StockOut')->only(['create', 'store']);
        $this->middleware('can:view,stockOut')->only('show');
        $this->middleware('can:update,stockOut')->only(['edit', 'update']);
        $this->middleware('can:delete,stockOut')->only('destroy');
        $this->middleware('can:cancel,stockOut')->only('cancel'); // دالة إضافية للإلغاء
    }

    /**
     * عرض قائمة بأذونات الإخراج.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stockOuts = StockOut::with(['warehouse', 'customer', 'creator'])
            ->latest()
            ->paginate(10);

        return view('stock_outs.index', compact('stockOuts'));
    }

    /**
     * عرض نموذج إنشاء إذن إخراج جديد.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        $customers = Customer::all();
        $items = Item::all(); // لجلب قائمة الأصناف في نموذج الإدخال
        return view('stock_outs.create', compact('warehouses', 'customers', 'items'));
    }

    /**
     * تخزين إذن إخراج جديد في قاعدة البيانات.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. التحقق من الصحة (Validation)
        $validatedData = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.item_id' => ['required', 'exists:items,id'],
            'details.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'details.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            // 2. استخدام طبقة الخدمة لمعالجة منطق الأعمال
            $stockOut = $this->stockOutService->createStockOut($validatedData);

            return redirect()->route('stock_outs.index')
                ->with('success', 'تم إنشاء إذن الإخراج رقم ' . $stockOut->number . ' بنجاح وتم خصم الكميات من المخزون.');

        } catch (ValidationException $e) {
            // معالجة أخطاء التحقق من توفر الكمية من طبقة الخدمة
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // معالجة الأخطاء العامة
            return back()->with('error', 'حدث خطأ أثناء إنشاء إذن الإخراج: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * عرض تفاصيل إذن إخراج محدد.
     *
     * @param StockOut $stockOut
     * @return \Illuminate\View\View
     */
    public function show(StockOut $stockOut)
    {
        $stockOut->load('details.item', 'warehouse', 'customer', 'creator');
        return view('stock_outs.show', compact('stockOut'));
    }

    /**
     * إلغاء إذن إخراج (دالة إضافية).
     *
     * @param StockOut $stockOut
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(StockOut $stockOut)
    {
        // التحقق من الأمان مرة أخرى (Authorization)
        if (Gate::denies('cancel', $stockOut)) {
            abort(403, 'غير مصرح لك بإلغاء إذن الإخراج هذا.');
        }

        try {
            $this->stockOutService->cancelStockOut($stockOut);

            return redirect()->route('stock_outs.index')
                ->with('success', 'تم إلغاء إذن الإخراج رقم ' . $stockOut->number . ' بنجاح وتم إعادة الكميات إلى المخزون.');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إلغاء إذن الإخراج: ' . $e->getMessage());
        }
    }

    // ملاحظة: تم حذف دالتي edit و update من المتحكم
    // لأن منطق الأعمال في StockOutService يمنع التحديث المباشر بعد الخصم من المخزون.
    // يمكن استبدالهما بدالة show و زر "إلغاء" ثم "إنشاء جديد".

    /**
     * حذف إذن إخراج.
     *
     * @param StockOut $stockOut
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(StockOut $stockOut)
    {
        // في نظام حقيقي، يجب التأكد من أن الإذن ملغي قبل الحذف الفعلي
        if ($stockOut->status !== 'canceled') {
            return back()->with('error', 'لا يمكن حذف إذن إخراج لم يتم إلغاؤه بعد.');
        }

        try {
            $stockOut->delete();
            return redirect()->route('stock_outs.index')
                ->with('success', 'تم حذف إذن الإخراج رقم ' . $stockOut->number . ' بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف إذن الإخراج: ' . $e->getMessage());
        }
    }
}
