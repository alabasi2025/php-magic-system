<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @class DepartmentController
 * @package App\Http\Controllers\Organization
 * @brief متحكم (Controller) لإدارة أقسام الهيكل التنظيمي (Departments).
 *
 * يتضمن هذا المتحكم جميع عمليات CRUD (إنشاء، قراءة، تحديث، حذف)
 * مع تطبيق قواعد التحقق (Validation)، والصلاحيات (Authorization)،
 * والحذف الناعم (Soft Deletes)، والرسائل الومضية (Flash Messages).
 */
class DepartmentController extends Controller
{
    /**
     * @brief الدالة البانية (Constructor) لتطبيق سياسات الصلاحيات.
     */
    public function __construct()
    {
        // تطبيق سياسة الصلاحيات (Policy) على جميع الدوال
        // TODO: إنشاء DepartmentPolicy
        // $this->authorizeResource(Department::class, 'department');
    }

    /**
     * @brief عرض قائمة بجميع الأقسام.
     *
     * يتم تحميل العلاقات الضرورية (الوحدة الأم، القسم الأب، عدد المشاريع)
     * لتحسين الأداء وتقليل استعلامات قاعدة البيانات.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // استرجاع الأقسام مع تحميل العلاقات وعدد المشاريع
        $departments = Department::query()
            ->with(['unit', 'parentDepartment']) // تحميل علاقة الوحدة والقسم الأب
            ->withCount('projects') // حساب عدد المشاريع المرتبطة بالقسم
            ->orderBy('name')
            ->paginate(10);

        // عرض القائمة
        return view('organization.departments.index', compact('departments'));
    }

    /**
     * @brief عرض نموذج إنشاء قسم جديد.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // استرجاع البيانات اللازمة لنموذج الإنشاء (مثل الوحدات والأقسام الأخرى)
        $units = Unit::where('is_active', true)->get();
        $parentDepartments = Department::where('is_active', true)->get();

        return view('organization.departments.create', compact('units', 'parentDepartments'));
    }

    /**
     * @brief تخزين قسم جديد في قاعدة البيانات.
     *
     * يتم تطبيق قواعد التحقق الشاملة وتعيين حقول الصلاحيات.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. قواعد التحقق الشاملة (Validation)
        $validatedData = $request->validate($this->rules());

        // 2. إنشاء القسم وتعيين حقول الصلاحيات (Authorization)
        $department = Department::create(array_merge($validatedData, [
            'created_by' => Auth::id(), // تعيين المستخدم الذي أنشأ السجل
            'is_active' => $request->boolean('is_active'), // التأكد من أن القيمة منطقية
        ]));

        // 3. رسالة ومضية (Flash Message) للنجاح
        session()->flash('success', 'تم إنشاء القسم بنجاح: ' . $department->name);

        // 4. إعادة التوجيه إلى صفحة العرض
        return redirect()->route('organization.departments.show', $department);
    }

    /**
     * @brief عرض تفاصيل قسم محدد.
     *
     * يتم تحميل العلاقات المرتبطة (الوحدة، القسم الأب، المشاريع)
     *
     * @param \App\Models\Department $department
     * @return \Illuminate\View\View
     */
    public function show(Department $department)
    {
        // تحميل العلاقات الضرورية للعرض
        $department->load(['unit', 'parentDepartment', 'projects']);

        return view('organization.departments.show', compact('department'));
    }

    /**
     * @brief عرض نموذج تعديل قسم محدد.
     *
     * @param \App\Models\Department $department
     * @return \Illuminate\View\View
     */
    public function edit(Department $department)
    {
        // استرجاع البيانات اللازمة لنموذج التعديل
        $units = Unit::where('is_active', true)->get();
        // استبعاد القسم الحالي من قائمة الأقسام الأب المحتملة لتجنب الحلقات
        $parentDepartments = Department::where('is_active', true)
            ->where('id', '!=', $department->id)
            ->get();

        return view('organization.departments.edit', compact('department', 'units', 'parentDepartments'));
    }

    /**
     * @brief تحديث قسم محدد في قاعدة البيانات.
     *
     * يتم تطبيق قواعد التحقق الشاملة وتعيين حقول الصلاحيات.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Department $department)
    {
        // 1. قواعد التحقق الشاملة (Validation)
        $validatedData = $request->validate($this->rules($department->id));

        // 2. تحديث القسم وتعيين حقول الصلاحيات (Authorization)
        $department->update(array_merge($validatedData, [
            'updated_by' => Auth::id(), // تعيين المستخدم الذي قام بالتحديث
            'is_active' => $request->boolean('is_active'),
        ]));

        // 3. رسالة ومضية (Flash Message) للنجاح
        session()->flash('success', 'تم تحديث القسم بنجاح: ' . $department->name);

        // 4. إعادة التوجيه إلى صفحة العرض
        return redirect()->route('organization.departments.show', $department);
    }

    /**
     * @brief حذف (Soft Delete) قسم محدد.
     *
     * يتم استخدام الحذف الناعم (Soft Delete) بدلاً من الحذف النهائي.
     *
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Department $department)
    {
        // تطبيق الحذف الناعم (Soft Delete)
        $department->delete();

        // رسالة ومضية (Flash Message) للنجاح
        session()->flash('warning', 'تم أرشفة (حذف ناعم) القسم بنجاح: ' . $department->name);

        // إعادة التوجيه إلى صفحة القائمة
        return redirect()->route('organization.departments.index');
    }

    /**
     * @brief تعريف قواعد التحقق (Validation Rules) لعمليتي التخزين والتحديث.
     *
     * @param int|null $ignoreId معرف القسم المراد تجاهله في قاعدة فريدة (Unique Rule).
     * @return array
     */
    protected function rules(?int $ignoreId = null): array
    {
        return [
            // التحقق من وجود الوحدة الأم
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            // التحقق من وجود القسم الأب (إذا وجد)
            'parent_id' => ['nullable', 'integer', 'exists:departments,id'],
            // التحقق من أن الكود فريد في جدول الأقسام
            'code' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('departments')->ignore($ignoreId)],
            // التحقق من الاسم
            'name' => ['required', 'string', 'max:255'],
            // التحقق من النوع (افتراضاً أنه يجب أن يكون ضمن قائمة محددة)
            'type' => ['required', 'string', 'in:Operational,Support,Strategic'],
            // التحقق من معرف المدير (افتراضاً وجود جدول للمستخدمين)
            'manager_id' => ['nullable', 'integer', 'exists:users,id'],
            // التحقق من الميزانية
            'budget' => ['nullable', 'numeric', 'min:0'],
            // التحقق من حالة النشاط
            'is_active' => ['required', 'boolean'],
        ];
    }
}
