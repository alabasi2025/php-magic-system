<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * ItemUnitController
 * 
 * Handles CRUD operations for item units (measurement units).
 */
class ItemUnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index()
    {
        $units = ItemUnit::latest()->paginate(20);
        return view('inventory.item-units.index', compact('units'));
    }

    /**
     * Store a newly created unit (AJAX).
     */
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:item_units,name',
            'name_en' => 'nullable|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        try {
            // Generate unique code
            $code = strtoupper(substr($validated['name'], 0, 3)) . rand(100, 999);
            while (ItemUnit::where('code', $code)->exists()) {
                $code = strtoupper(substr($validated['name'], 0, 3)) . rand(100, 999);
            }

            $validated['code'] = $code;
            $validated['status'] = 'active';

            $unit = ItemUnit::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الوحدة بنجاح',
                'unit' => $unit,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الوحدة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all active units (AJAX).
     */
    public function getActive()
    {
        $units = ItemUnit::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'name_en', 'symbol']);

        return response()->json([
            'success' => true,
            'units' => $units,
        ]);
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create()
    {
        return view('inventory.item-units.create');
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:item_units,code',
            'name' => 'required|string|max:100|unique:item_units,name',
            'name_en' => 'nullable|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $unit = ItemUnit::create($validated);

        return redirect()
            ->route('inventory.item-units.index')
            ->with('success', 'تم إنشاء الوحدة بنجاح');
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(ItemUnit $itemUnit)
    {
        return view('inventory.item-units.edit', compact('itemUnit'));
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, ItemUnit $itemUnit)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('item_units', 'code')->ignore($itemUnit->id)],
            'name' => ['required', 'string', 'max:100', Rule::unique('item_units', 'name')->ignore($itemUnit->id)],
            'name_en' => 'nullable|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $itemUnit->update($validated);

        return redirect()
            ->route('inventory.item-units.index')
            ->with('success', 'تم تحديث الوحدة بنجاح');
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(ItemUnit $itemUnit)
    {
        try {
            // Check if unit is used by any items
            if ($itemUnit->items()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'لا يمكن حذف الوحدة لأنها مستخدمة في أصناف');
            }

            $itemUnit->delete();

            return redirect()
                ->route('inventory.item-units.index')
                ->with('success', 'تم حذف الوحدة بنجاح');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف الوحدة: ' . $e->getMessage());
        }
    }
}
