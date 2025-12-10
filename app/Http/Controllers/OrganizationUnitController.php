<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganizationUnitController extends Controller
{
    public function index()
    {
        try {
            $units = Unit::orderBy('created_at', 'desc')->get();
            return view('organization.units.index', compact('units'));
        } catch (\Exception $e) {
            Log::error("OrganizationUnitController@index: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('organization.units.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:units,code',
                'name' => 'required|string|max:255',
                'name_en' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            Unit::create($validated);

            return redirect()->route('organization.units.index')
                ->with('success', 'تم إضافة الوحدة بنجاح');
        } catch (\Exception $e) {
            Log::error("OrganizationUnitController@store: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الوحدة: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            return view('organization.units.show', compact('unit'));
        } catch (\Exception $e) {
            Log::error("OrganizationUnitController@show: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء جلب بيانات الوحدة: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            return view('organization.units.edit', compact('unit'));
        } catch (\Exception $e) {
            Log::error("OrganizationUnitController@edit: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء جلب بيانات الوحدة: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:units,code,' . $id,
                'name' => 'required|string|max:255',
                'name_en' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $unit->update($validated);

            return redirect()->route('organization.units.show', $id)
                ->with('success', 'تم تحديث الوحدة بنجاح');
        } catch (\Exception $e) {
            Log::error("OrganizationUnitController@update: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الوحدة: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            
            // Check for related records before deletion
            if ($unit->organizations()->exists()) {
                return back()->with('error', 'لا يمكن حذف الوحدة لأنها تحتوي على مؤسسات مرتبطة');
            }

            $unit->delete();

            return redirect()->route('organization.units.index')
                ->with('success', 'تم حذف الوحدة بنجاح');
        } catch (\Exception $e) {
            Log::error("OrganizationUnitController@destroy: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الوحدة: ' . $e->getMessage());
        }
    }
}
