<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    /**
     * بناء المتحكم وتعيين الخدمة.
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
        // تطبيق سياسة الأمان (Authorization)
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\Customer')->only('index');
        $this->middleware('can:create,App\Models\Customer')->only(['create', 'store']);
        $this->middleware('can:view,customer')->only(['show', 'history']);
        $this->middleware('can:update,customer')->only(['edit', 'update']);
        $this->middleware('can:delete,customer')->only('destroy');
    }

    /**
     * عرض قائمة بجميع العملاء. (CRUD: Read - Index)
     */
    public function index(Request $request)
    {
        $customers = $this->customerService->getAllCustomers($request->all());
        return view('customers.index', compact('customers'));
    }

    /**
     * عرض نموذج إنشاء عميل جديد. (CRUD: Create - Form)
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * تخزين عميل جديد في قاعدة البيانات. (CRUD: Create - Store)
     */
    public function store(Request $request)
    {
        // التحقق من الصحة (Validation)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers', 'phone')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')],
            'address' => 'nullable|string',
            'initial_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $customer = $this->customerService->createCustomer($validatedData);
            return redirect()->route('customers.index')->with('success', 'تم إنشاء العميل بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل عميل محدد. (CRUD: Read - Show)
     */
    public function show(Customer $customer)
    {
        // إعادة حساب الرصيد لضمان الدقة (ميزة إضافية)
        $this->customerService->recalculateBalance($customer);
        return view('customers.show', compact('customer'));
    }

    /**
     * عرض نموذج تعديل عميل موجود. (CRUD: Update - Form)
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * تحديث بيانات عميل موجود في قاعدة البيانات. (CRUD: Update - Store)
     */
    public function update(Request $request, Customer $customer)
    {
        // التحقق من الصحة (Validation)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            // تجاهل رقم الهاتف والبريد الإلكتروني الحالي عند التحقق من التفرد
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'address' => 'nullable|string',
            'initial_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $customer = $this->customerService->updateCustomer($customer, $validatedData);
            return redirect()->route('customers.index')->with('success', 'تم تحديث بيانات العميل بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث العميل: ' . $e->getMessage());
        }
    }

    /**
     * حذف عميل من قاعدة البيانات. (CRUD: Delete)
     */
    public function destroy(Customer $customer)
    {
        try {
            $this->customerService->deleteCustomer($customer);
            return redirect()->route('customers.index')->with('success', 'تم حذف العميل بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء (مثل وجود تعاملات مرتبطة)
            return back()->with('error', 'فشل حذف العميل: ' . $e->getMessage());
        }
    }

    /**
     * عرض تاريخ التعاملات للعميل. (ميزة إضافية)
     */
    public function history(Customer $customer)
    {
        // التأكد من أن المستخدم لديه صلاحية عرض العميل
        if (Gate::denies('view', $customer)) {
            abort(403, 'غير مصرح لك بمشاهدة سجل التعاملات لهذا العميل.');
        }

        $transactions = $this->customerService->getTransactionHistory($customer);
        return view('customers.history', compact('customer', 'transactions'));
    }

    /**
     * إعادة حساب رصيد العميل. (ميزة إضافية)
     */
    public function recalculate(Customer $customer)
    {
        // التأكد من أن المستخدم لديه صلاحية التحديث
        if (Gate::denies('update', $customer)) {
            abort(403, 'غير مصرح لك بإعادة حساب رصيد هذا العميل.');
        }

        try {
            $this->customerService->recalculateBalance($customer);
            return back()->with('success', 'تم إعادة حساب رصيد العميل بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إعادة حساب الرصيد: ' . $e->getMessage());
        }
    }
}
