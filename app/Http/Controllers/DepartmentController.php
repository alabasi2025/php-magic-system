<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    public function index()
    {
        try {
            $departments = Department::with(['unit', 'manager'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            return view('organization.departments.index', compact('departments'));
        } catch (\Exception $e) {
            Log::error("DepartmentController@index: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('organization.departments.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:departments,code',
                'name' => 'required|string|max:255',
                'name_en' => 'nullable|string|max:255',
                'unit_id' => 'nullable|exists:units,id',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            Department::create($validated);

            return redirect()->route('organization.departments.index')
                ->with('success', 'تم إضافة القسم بنجاح');
        } catch (\Exception $e) {
            Log::error("DepartmentController@store: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة القسم: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $department = Department::with(['unit', 'manager'])->findOrFail($id);
            return view('organization.departments.show', compact('department'));
        } catch (\Exception $e) {
            Log::error("DepartmentController@show: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء جلب بيانات القسم: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $department = Department::findOrFail($id);
            return view('organization.departments.edit', compact('department'));
        } catch (\Exception $e) {
            Log::error("DepartmentController@edit: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء جلب بيانات القسم: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $department = Department::findOrFail($id);
            
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:departments,code,' . $id,
                'name' => 'required|string|max:255',
                'name_en' => 'nullable|string|max:255',
                'unit_id' => 'nullable|exists:units,id',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $department->update($validated);

            return redirect()->route('organization.departments.show', $id)
                ->with('success', 'تم تحديث القسم بنجاح');
        } catch (\Exception $e) {
            Log::error("DepartmentController@update: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القسم: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);
            
            // Check for related records before deletion
            if ($department->employees()->exists()) {
                return back()->with('error', 'لا يمكن حذف القسم لأنه يحتوي على موظفين مرتبطين');
            }

            $department->delete();

            return redirect()->route('organization.departments.index')
                ->with('success', 'تم حذف القسم بنجاح');
        } catch (\Exception $e) {
            Log::error("DepartmentController@destroy: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف القسم: ' . $e->getMessage());
        }
    }
}
