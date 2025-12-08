<?php

namespace App\Http\Controllers;

use App\Models\WarehouseGroup;
use App\Models\ChartAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseGroupController extends Controller
{
    /**
     * Display a listing of warehouse groups.
     */
    public function index()
    {
        $groups = WarehouseGroup::with(['account', 'warehouses'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('inventory.warehouse-groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new warehouse group.
     */
    public function create()
    {
        // Get only warehouse type accounts for dropdown
        $accounts = ChartAccount::where('account_type', 'warehouse')
            ->orderBy('code')
            ->get();
        
        return view('inventory.warehouse-groups.create', compact('accounts'));
    }

    /**
     * Store a newly created warehouse group in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouse_groups,code',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'account_id' => 'nullable|exists:chart_accounts,id',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $group = WarehouseGroup::create($validated);

            return redirect()
                ->route('inventory.warehouse-groups.index')
                ->with('success', 'تم إضافة مجموعة المخازن بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة مجموعة المخازن: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified warehouse group.
     */
    public function show(WarehouseGroup $warehouseGroup)
    {
        $warehouseGroup->load(['account', 'warehouses.manager']);
        
        return view('inventory.warehouse-groups.show', compact('warehouseGroup'));
    }

    /**
     * Show the form for editing the specified warehouse group.
     */
    public function edit(WarehouseGroup $warehouseGroup)
    {
        // Get only warehouse type accounts for dropdown
        $accounts = ChartAccount::where('account_type', 'warehouse')
            ->orderBy('code')
            ->get();
        
        return view('inventory.warehouse-groups.edit', compact('warehouseGroup', 'accounts'));
    }

    /**
     * Update the specified warehouse group in storage.
     */
    public function update(Request $request, WarehouseGroup $warehouseGroup)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouse_groups,code,' . $warehouseGroup->id,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'account_id' => 'nullable|exists:chart_accounts,id',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $warehouseGroup->update($validated);

            return redirect()
                ->route('inventory.warehouse-groups.index')
                ->with('success', 'تم تحديث مجموعة المخازن بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث مجموعة المخازن: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified warehouse group from storage.
     */
    public function destroy(WarehouseGroup $warehouseGroup)
    {
        try {
            // Check if group has warehouses
            if ($warehouseGroup->warehouses()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'لا يمكن حذف المجموعة لأنها تحتوي على مخازن. يرجى حذف أو نقل المخازن أولاً.');
            }

            $warehouseGroup->delete();

            return redirect()
                ->route('inventory.warehouse-groups.index')
                ->with('success', 'تم حذف مجموعة المخازن بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف مجموعة المخازن: ' . $e->getMessage());
        }
    }
}
