<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * ItemController
 * 
 * Handles CRUD operations for inventory items.
 */
class ItemController extends Controller
{
    /**
     * Display a listing of items.
     */
    public function index(Request $request)
    {
        $query = Item::with('unit');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by below min stock
        if ($request->filled('below_min_stock') && $request->below_min_stock == '1') {
            $query->belowMinStock();
        }

        // Search by name, SKU, or barcode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(15);

        // Calculate current stock for each item
        foreach ($items as $item) {
            $item->current_stock = $item->getTotalStock();
        }

        return view('inventory.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $units = ItemUnit::active()->get();
        return view('inventory.items.create', compact('units'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:items,sku',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:item_units,id',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
            'unit_price' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:100|unique:items,barcode',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item = Item::create($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'تم إنشاء الصنف بنجاح');
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        $item->load(['unit', 'stockMovements.warehouse']);
        $item->current_stock = $item->getTotalStock();
        
        // Get stock by warehouse
        $stockByWarehouse = \DB::table('stock_movements')
            ->join('warehouses', 'stock_movements.warehouse_id', '=', 'warehouses.id')
            ->select(
                'warehouses.id',
                'warehouses.name',
                \DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock')
            )
            ->where('stock_movements.item_id', $item->id)
            ->where('stock_movements.status', 'approved')
            ->groupBy('warehouses.id', 'warehouses.name')
            ->having('current_stock', '>', 0)
            ->get();

        return view('inventory.items.show', compact('item', 'stockByWarehouse'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $units = ItemUnit::active()->get();
        return view('inventory.items.edit', compact('item', 'units'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', Rule::unique('items', 'sku')->ignore($item->id)],
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:item_units,id',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
            'unit_price' => 'required|numeric|min:0',
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('items', 'barcode')->ignore($item->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'تم تحديث الصنف بنجاح');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        // Check if item has stock movements
        if ($item->stockMovements()->exists()) {
            return redirect()
                ->route('items.index')
                ->with('error', 'لا يمكن حذف الصنف لوجود حركات مخزون مرتبطة به');
        }

        // Delete image if exists
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'تم حذف الصنف بنجاح');
    }

    /**
     * Get items below minimum stock level.
     */
    public function belowMinStock()
    {
        $items = Item::with('unit')
            ->belowMinStock()
            ->get();

        foreach ($items as $item) {
            $item->current_stock = $item->getTotalStock();
        }

        return view('inventory.items.below-min-stock', compact('items'));
    }
}
