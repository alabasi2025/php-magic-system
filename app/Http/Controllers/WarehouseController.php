<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * WarehouseController
 * 
 * Handles CRUD operations for warehouses.
 */
class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     */
    public function index(Request $request)
    {
        $query = Warehouse::with('manager');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $warehouses = $query->latest()->paginate(15);

        return view('inventory.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        $managers = User::all();
        return view('inventory.warehouses.create', compact('managers'));
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code',
            'name' => 'required|string|max:200',
            'location' => 'nullable|string|max:500',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $warehouse = Warehouse::create($validated);

        return redirect()
            ->route('inventory.warehouses.index')
            ->with('success', 'تم إنشاء المخزن بنجاح');
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['manager', 'stockMovements.item']);
        
        // Get current stock levels
        $stockLevels = \DB::table('stock_movements')
            ->select(
                'item_id',
                \DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock')
            )
            ->where('warehouse_id', $warehouse->id)
            ->where('status', 'approved')
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        return view('inventory.warehouses.show', compact('warehouse', 'stockLevels'));
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        $managers = User::all();
        return view('inventory.warehouses.edit', compact('warehouse', 'managers'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')->ignore($warehouse->id)],
            'name' => 'required|string|max:200',
            'location' => 'nullable|string|max:500',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $warehouse->update($validated);

        return redirect()
            ->route('inventory.warehouses.index')
            ->with('success', 'تم تحديث المخزن بنجاح');
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has stock movements
        if ($warehouse->stockMovements()->exists()) {
            return redirect()
                ->route('inventory.warehouses.index')
                ->with('error', 'لا يمكن حذف المخزن لوجود حركات مخزون مرتبطة به');
        }

        $warehouse->delete();

        return redirect()
            ->route('inventory.warehouses.index')
            ->with('success', 'تم حذف المخزن بنجاح');
    }

    /**
     * Get stock report for a specific warehouse.
     */
    public function stockReport(Warehouse $warehouse)
    {
        $stockData = \DB::table('stock_movements')
            ->join('items', 'stock_movements.item_id', '=', 'items.id')
            ->join('item_units', 'items.unit_id', '=', 'item_units.id')
            ->select(
                'items.id',
                'items.sku',
                'items.name',
                'item_units.name as unit_name',
                \DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock'),
                'items.min_stock',
                'items.max_stock',
                'items.unit_price',
                \DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) * items.unit_price as stock_value')
            )
            ->where('stock_movements.warehouse_id', $warehouse->id)
            ->where('stock_movements.status', 'approved')
            ->groupBy('items.id', 'items.sku', 'items.name', 'item_units.name', 'items.min_stock', 'items.max_stock', 'items.unit_price')
            ->having('current_stock', '>', 0)
            ->get();

        return view('inventory.warehouses.stock-report', compact('warehouse', 'stockData'));
    }
}
