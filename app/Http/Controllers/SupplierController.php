<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    /**
     * بناء المتحكم وتعيين الخدمة.
     */
    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
        // تطبيق سياسة الأمان (Authorization)
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\Supplier')->only('index');
        $this->middleware('can:create,App\Models\Supplier')->only(['create', 'store']);
        $this->middleware('can:view,supplier')->only(['show', 'history']);
        $this->middleware('can:update,supplier')->only(['edit', 'update']);
        $this->middleware('can:delete,supplier')->only('destroy');
    }

    /**
     * عرض قائمة بجميع الموردين. (CRUD: Read - Index)
     */
    public function index(Request $request)
    {
        $suppliers = $this->supplierService->getAllSuppliers($request->all());
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * عرض نموذج إنشاء مورد جديد. (CRUD: Create - Form)
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * تخزين مورد جديد في قاعدة البيانات. (CRUD: Create - Store)
     */
    public function store(Request $request)
    {
        // التحقق من الصحة (Validation)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'phone')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')],
            'address' => 'nullable|string',
            'initial_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $supplier = $this->supplierService->createSupplier($validatedData);
            return redirect()->route('suppliers.index')->with('success', 'تم إنشاء المورد بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المورد: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مورد محدد. (CRUD: Read - Show)
     */
    public function show(Supplier $supplier)
    {
        // إعادة حساب الرصيد لضمان الدقة (ميزة إضافية)
        $this->supplierService->recalculateBalance($supplier);
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * عرض نموذج تعديل مورد موجود. (CRUD: Update - Form)
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * تحديث بيانات مورد موجود في قاعدة البيانات. (CRUD: Update - Store)
     */
    public function update(Request $request, Supplier $supplier)
    {
        // التحقق من الصحة (Validation)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            // تجاهل رقم الهاتف والبريد الإلكتروني الحالي عند التحقق من التفرد
            'phone' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'phone')->ignore($supplier->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($supplier->id)],
            'address' => 'nullable|string',
            'initial_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $supplier = $this->supplierService->updateSupplier($supplier, $validatedData);
            return redirect()->route('suppliers.index')->with('success', 'تم تحديث بيانات المورد بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المورد: ' . $e->getMessage());
        }
    }

    /**
     * حذف مورد من قاعدة البيانات. (CRUD: Delete)
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $this->supplierService->deleteSupplier($supplier);
            return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء (مثل وجود تعاملات مرتبطة)
            return back()->with('error', 'فشل حذف المورد: ' . $e->getMessage());
        }
    }

    /**
     * عرض تاريخ التعاملات للمورد. (ميزة إضافية)
     */
    public function history(Supplier $supplier)
    {
        // التأكد من أن المستخدم لديه صلاحية عرض المورد
        if (Gate::denies('view', $supplier)) {
            abort(403, 'غير مصرح لك بمشاهدة سجل التعاملات لهذا المورد.');
        }

        $transactions = $this->supplierService->getTransactionHistory($supplier);
        return view('suppliers.history', compact('supplier', 'transactions'));
    }

    /**
     * إعادة حساب رصيد المورد. (ميزة إضافية)
     */
    public function recalculate(Supplier $supplier)
    {
        // التأكد من أن المستخدم لديه صلاحية التحديث
        if (Gate::denies('update', $supplier)) {
            abort(403, 'غير مصرح لك بإعادة حساب رصيد هذا المورد.');
        }

        try {
            $this->supplierService->recalculateBalance($supplier);
            return back()->with('success', 'تم إعادة حساب رصيد المورد بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إعادة حساب الرصيد: ' . $e->getMessage());
        }
    }
}
