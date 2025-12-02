<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Unit; // للاستخدام في العلاقات
use App\Models\Department; // للاستخدام في العلاقات
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // الدالة البانية (Constructor) لتطبيق سياسة الصلاحيات (Authorization)
    public function __construct()
    {
        // تطبيق middleware 'auth' لضمان تسجيل دخول المستخدم
        $this->middleware('auth');
        // يمكن استخدام $this->authorizeResource(Project::class, 'project'); إذا كانت هناك سياسة (Policy) محددة
    }

    /**
     * عرض قائمة بجميع المشاريع.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحية لعرض القائمة
        // Gate::authorize('viewAny', Project::class);

        // جلب المشاريع مع العلاقات المطلوبة (Unit, Department, Manager, Client)
        // واستخدام withCount لحساب عدد المهام (افتراضاً بوجود علاقة tasks)
        $projects = Project::with(['unit', 'department', 'manager', 'client'])
                           ->withCount('tasks') // افتراض وجود علاقة 'tasks'
                           ->latest()
                           ->paginate(10);

        // افتراض أننا نمرر البيانات إلى View
        return view('organization.projects.index', compact('projects'));
    }

    /**
     * عرض نموذج إنشاء مشروع جديد.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // التحقق من الصلاحية لإنشاء مشروع
        // Gate::authorize('create', Project::class);

        // جلب البيانات اللازمة لملء النموذج (الوحدات، الأقسام، المدراء، العملاء)
        $units = Unit::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        // افتراض وجود Models لـ Manager و Client (قد تكون User أو Models أخرى)
        $managers = \App\Models\User::all();
        $clients = \App\Models\Client::all();

        return view('organization.projects.create', compact('units', 'departments', 'managers', 'clients'));
    }

    /**
     * تخزين مشروع جديد في قاعدة البيانات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحية لتخزين مشروع
        // Gate::authorize('create', Project::class);

        // التحقق الشامل من صحة البيانات (Validation)
        $validatedData = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'manager_id' => ['required', 'exists:users,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'revenue' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'priority' => ['required', 'string', 'max:50'],
        ], [
            'required' => 'حقل :attribute مطلوب.',
            'unique' => 'قيمة :attribute مستخدمة بالفعل.',
            'exists' => 'قيمة :attribute غير صالحة.',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
            'integer' => 'حقل :attribute يجب أن يكون عدداً صحيحاً.',
            'min' => 'قيمة :attribute يجب أن لا تقل عن :min.',
            'max' => 'قيمة :attribute يجب أن لا تزيد عن :max.',
        ]);

        // إضافة حقل created_by للتفويض (Authorization)
        $validatedData['created_by'] = Auth::id();

        // إنشاء المشروع
        $project = Project::create($validatedData);

        // رسالة فلاش (Flash Message) للنجاح
        return redirect()->route('projects.index')->with('success', 'تم إنشاء المشروع بنجاح.');
    }

    /**
     * عرض تفاصيل مشروع محدد.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        // التحقق من الصلاحية لعرض المشروع
        // Gate::authorize('view', $project);

        // جلب المشروع مع العلاقات المطلوبة
        $project->load(['unit', 'department', 'manager', 'client', 'createdBy', 'updatedBy']);

        // افتراض أننا نمرر البيانات إلى View
        return view('organization.projects.show', compact('project'));
    }

    /**
     * عرض نموذج تعديل مشروع محدد.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        // التحقق من الصلاحية لتعديل المشروع (يجب أن يكون هو المنشئ أو لديه صلاحية التعديل)
        // Gate::authorize('update', $project);

        // جلب البيانات اللازمة لملء النموذج
        $units = Unit::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $managers = \App\Models\User::all();
        $clients = \App\Models\Client::all();

        return view('organization.projects.edit', compact('project', 'units', 'departments', 'managers', 'clients'));
    }

    /**
     * تحديث مشروع محدد في قاعدة البيانات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        // التحقق من الصلاحية لتحديث المشروع
        // Gate::authorize('update', $project);

        // التحقق الشامل من صحة البيانات (Validation)
        $validatedData = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            // التأكد من أن الكود فريد باستثناء المشروع الحالي
            'code' => ['required', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($project->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'manager_id' => ['required', 'exists:users,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'revenue' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'priority' => ['required', 'string', 'max:50'],
        ], [
            'required' => 'حقل :attribute مطلوب.',
            'unique' => 'قيمة :attribute مستخدمة بالفعل.',
            'exists' => 'قيمة :attribute غير صالحة.',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
            'integer' => 'حقل :attribute يجب أن يكون عدداً صحيحاً.',
            'min' => 'قيمة :attribute يجب أن لا تقل عن :min.',
            'max' => 'قيمة :attribute يجب أن لا تزيد عن :max.',
        ]);

        // إضافة حقل updated_by للتفويض (Authorization)
        $validatedData['updated_by'] = Auth::id();

        // تحديث المشروع
        $project->update($validatedData);

        // رسالة فلاش (Flash Message) للنجاح
        return redirect()->route('projects.index')->with('success', 'تم تحديث المشروع بنجاح.');
    }

    /**
     * حذف مشروع محدد (Soft Delete).
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        // التحقق من الصلاحية لحذف المشروع
        // Gate::authorize('delete', $project);

        // تطبيق الحذف الناعم (Soft Delete)
        $project->delete();

        // رسالة فلاش (Flash Message) للنجاح
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح (حذف ناعم).');
    }

    /**
     * استعادة مشروع محذوف (Soft Delete).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        // جلب المشروع المحذوف فقط
        $project = Project::onlyTrashed()->findOrFail($id);

        // التحقق من الصلاحية لاستعادة المشروع
        // Gate::authorize('restore', $project);

        // استعادة المشروع
        $project->restore();

        // رسالة فلاش (Flash Message) للنجاح
        return redirect()->route('projects.index')->with('success', 'تم استعادة المشروع بنجاح.');
    }

    /**
     * حذف مشروع محدد نهائياً من قاعدة البيانات.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        // جلب المشروع المحذوف فقط
        $project = Project::onlyTrashed()->findOrFail($id);

        // التحقق من الصلاحية للحذف النهائي
        // Gate::authorize('forceDelete', $project);

        // الحذف النهائي
        $project->forceDelete();

        // رسالة فلاش (Flash Message) للنجاح
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع نهائياً من قاعدة البيانات.');
    }
}
