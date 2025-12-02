<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * @brief وحدة التحكم الخاصة بالوحدات التنظيمية (UnitController).
 *
 * تتولى هذه الوحدة إدارة عمليات CRUD الكاملة للوحدات التنظيمية.
 * تم تطبيق متطلبات التحقق من الصحة، العلاقات، التخويل، والحذف الناعم.
 */
class UnitController extends Controller
{
    /**
     * @brief عرض قائمة بالوحدات التنظيمية.
     *
     * يتم جلب الوحدات مع العلاقات الضرورية (Holding, Parent, Manager)
     * وحساب عدد الأقسام والمشاريع المرتبطة (withCount).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // @note: افتراض وجود علاقات departments و projects في نموذج Unit
        $units = Unit::query()
            ->with(['holding', 'parent', 'manager']) // العلاقات المطلوبة
            ->withCount(['departments', 'projects']) // حساب عدد الأقسام والمشاريع
            ->when($request->has('trashed'), function ($query) {
                // تضمين المحذوفة حذفا ناعما إذا طلب ذلك
                $query->withTrashed();
            })
            ->orderBy('code')
            ->paginate(10);

        // @note: افتراض وجود عرض (view) لعرض القائمة
        return view('organization.units.index', compact('units'));
    }

    /**
     * @brief عرض نموذج إنشاء وحدة تنظيمية جديدة.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // @note: افتراض وجود نماذج Holding و User لاختيارها
        $holdings = \App\Models\Organization\Holding::all(); // افتراض وجود نموذج Holding
        $units = Unit::all(); // للوحدة الأم
        $managers = \App\Models\User::all(); // افتراض وجود نموذج User للمديرين

        return view('organization.units.create', compact('holdings', 'units', 'managers'));
    }

    /**
     * @brief تخزين وحدة تنظيمية جديدة في قاعدة البيانات.
     *
     * يتم تطبيق التحقق من الصحة الشامل وتعيين حقول التخويل (created_by).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        try {
            DB::beginTransaction();

            $unit = Unit::create(array_merge($validatedData, [
                'created_by' => Auth::id(), // حقل التخويل: من أنشأ السجل
                'is_active' => $request->boolean('is_active'),
            ]));

            DB::commit();

            // رسالة فلاش للنجاح
            session()->flash('success', 'تم إنشاء الوحدة التنظيمية بنجاح: ' . $unit->name);
            return redirect()->route('organization.units.index');

        } catch (\Exception $e) {
            DB::rollBack();
            // رسالة فلاش للفشل
            session()->flash('error', 'فشل في إنشاء الوحدة التنظيمية. يرجى المحاولة مرة أخرى.');
            return back()->withInput();
        }
    }

    /**
     * @brief عرض تفاصيل وحدة تنظيمية محددة.
     *
     * يتم جلب الوحدة مع العلاقات الضرورية (CreatedBy, UpdatedBy)
     * وتضمين المحذوفة حذفا ناعما إذا كانت محذوفة.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        // @note: استخدام findOrFail مع withTrashed للتعامل مع الحذف الناعم
        $unit = Unit::withTrashed()
            ->with(['holding', 'parent', 'manager', 'createdBy', 'updatedBy']) // علاقات التخويل والعلاقات الأساسية
            ->withCount(['departments', 'projects'])
            ->findOrFail($id);

        return view('organization.units.show', compact('unit'));
    }

    /**
     * @brief عرض نموذج تعديل وحدة تنظيمية محددة.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $unit = Unit::withTrashed()->findOrFail($id);
        $holdings = \App\Models\Organization\Holding::all();
        $units = Unit::where('id', '!=', $id)->get(); // للوحدة الأم، استبعاد الوحدة نفسها
        $managers = \App\Models\User::all();

        return view('organization.units.edit', compact('unit', 'holdings', 'units', 'managers'));
    }

    /**
     * @brief تحديث وحدة تنظيمية محددة في قاعدة البيانات.
     *
     * يتم تطبيق التحقق من الصحة الشامل وتعيين حقل التخويل (updated_by).
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $unit = Unit::withTrashed()->findOrFail($id);

        // قواعد التحقق مع استثناء الوحدة الحالية من قاعدة unique
        $validatedData = $request->validate($this->validationRules($unit->id));

        try {
            DB::beginTransaction();

            $unit->update(array_merge($validatedData, [
                'updated_by' => Auth::id(), // حقل التخويل: من قام بالتحديث
                'is_active' => $request->boolean('is_active'),
            ]));

            DB::commit();

            // رسالة فلاش للنجاح
            session()->flash('success', 'تم تحديث الوحدة التنظيمية بنجاح: ' . $unit->name);
            return redirect()->route('organization.units.index');

        } catch (\Exception $e) {
            DB::rollBack();
            // رسالة فلاش للفشل
            session()->flash('error', 'فشل في تحديث الوحدة التنظيمية. يرجى المحاولة مرة أخرى.');
            return back()->withInput();
        }
    }

    /**
     * @brief حذف (حذف ناعم) وحدة تنظيمية محددة.
     *
     * يتم تطبيق الحذف الناعم (Soft Delete).
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $unit = Unit::findOrFail($id);

        try {
            $unit->delete(); // تطبيق الحذف الناعم

            // رسالة فلاش للنجاح
            session()->flash('success', 'تم حذف الوحدة التنظيمية بنجاح (حذف ناعم): ' . $unit->name);
            return redirect()->route('organization.units.index');

        } catch (\Exception $e) {
            // رسالة فلاش للفشل
            session()->flash('error', 'فشل في حذف الوحدة التنظيمية. يرجى المحاولة مرة أخرى.');
            return back();
        }
    }

    /**
     * @brief قواعد التحقق من الصحة (Validation Rules) المستخدمة في Store و Update.
     *
     * @param int|null $unitId معرف الوحدة لاستثنائها من قاعدة unique في التحديث.
     * @return array
     */
    private function validationRules(?int $unitId = null): array
    {
        return [
            'holding_id' => ['required', 'exists:holdings,id'],
            'parent_id' => ['nullable', 'exists:units,id'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('units', 'code')->ignore($unitId), // قاعدة unique مع استثناء
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'], // يمكن أن تكون قائمة محددة (enum)
            'manager_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['boolean'],
        ];
    }
}
