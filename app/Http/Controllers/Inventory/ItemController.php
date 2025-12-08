<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemUnitConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * ItemController - Enhanced with Multi-Unit Support
 * 
 * Handles CRUD operations for inventory items with multiple unit conversions.
 */
class ItemController extends Controller
{
    /**
     * Display a listing of items.
     */
    public function index(Request $request)
    {
        $query = Item::with(['unit', 'unitConversions.unit']);

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
        // إزالة شرط status مؤقتاً للاختبار
        $units = ItemUnit::orderBy('name')->get();
        \Log::info('Units count in create:', ['count' => $units->count()]);
        return view('inventory.items.create_new', compact('units'));
    }

    /**
     * Store a newly created item in storage with multiple units.
     */
    public function store(Request $request)
    {
        // Debug: Check received data
        \Log::info('Item Store Request', [
            'all_data' => $request->all(),
            'units' => $request->input('units'),
            'primary_unit' => $request->input('primary_unit')
        ]);
        
        // Validation
        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:items,sku',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
            'barcode' => 'nullable|string|max:100|unique:items,barcode',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'units' => 'required|array|min:1',
            'units.*.unit_id' => 'required|exists:item_units,id',
            'units.*.capacity' => 'required|numeric|min:0.0001',
            'units.*.price' => 'nullable|numeric|min:0',
            'primary_unit' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            // Get primary unit info
            $primaryUnitIndex = $validated['primary_unit'];
            $primaryUnitId = $validated['units'][$primaryUnitIndex]['unit_id'];

            // Create item
            $itemData = [
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'unit_id' => $primaryUnitId, // الوحدة الرئيسية
                'min_stock' => $validated['min_stock'],
                'max_stock' => $validated['max_stock'],
                'unit_price' => $validated['units'][$primaryUnitIndex]['price'] ?? 0,
                'barcode' => $validated['barcode'] ?? null,
                'status' => $validated['status'],
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $itemData['image_path'] = $request->file('image')->store('items', 'public');
            }

            $item = Item::create($itemData);

            // Create unit conversions
            $sortOrder = 0;
            foreach ($validated['units'] as $index => $unitData) {
                ItemUnitConversion::create([
                    'item_id' => $item->id,
                    'unit_id' => $unitData['unit_id'],
                    'capacity' => $unitData['capacity'],
                    'is_primary' => ($index == $primaryUnitIndex),
                    'price' => $unitData['price'] ?? null,
                    'sort_order' => $sortOrder++,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('inventory.items.index')
                ->with('success', 'تم إنشاء الصنف بنجاح مع ' . count($validated['units']) . ' وحدات');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الصنف: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        $item->load(['unit', 'unitConversions.unit', 'stockMovements.warehouse']);
        $item->current_stock = $item->getTotalStock();

        return view('inventory.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $units = ItemUnit::where('status', 'active')->orderBy('name')->get();
        $item->load('unitConversions.unit');
        
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
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('items', 'barcode')->ignore($item->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'units' => 'required|array|min:1',
            'units.*.unit_id' => 'required|exists:item_units,id',
            'units.*.capacity' => 'required|numeric|min:0.0001',
            'units.*.price' => 'nullable|numeric|min:0',
            'primary_unit' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            // Get primary unit info
            $primaryUnitIndex = $validated['primary_unit'];
            $primaryUnitId = $validated['units'][$primaryUnitIndex]['unit_id'];

            // Update item
            $itemData = [
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'unit_id' => $primaryUnitId,
                'min_stock' => $validated['min_stock'],
                'max_stock' => $validated['max_stock'],
                'unit_price' => $validated['units'][$primaryUnitIndex]['price'] ?? 0,
                'barcode' => $validated['barcode'] ?? null,
                'status' => $validated['status'],
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($item->image_path) {
                    Storage::disk('public')->delete($item->image_path);
                }
                $itemData['image_path'] = $request->file('image')->store('items', 'public');
            }

            $item->update($itemData);

            // Delete old unit conversions
            $item->unitConversions()->delete();

            // Create new unit conversions
            $sortOrder = 0;
            foreach ($validated['units'] as $index => $unitData) {
                ItemUnitConversion::create([
                    'item_id' => $item->id,
                    'unit_id' => $unitData['unit_id'],
                    'capacity' => $unitData['capacity'],
                    'is_primary' => ($index == $primaryUnitIndex),
                    'price' => $unitData['price'] ?? null,
                    'sort_order' => $sortOrder++,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('inventory.items.show', $item)
                ->with('success', 'تم تحديث الصنف بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الصنف: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        try {
            // Check if item has stock movements
            if ($item->stockMovements()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'لا يمكن حذف الصنف لأنه يحتوي على حركات مخزنية');
            }

            // Delete image
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }

            // Delete unit conversions
            $item->unitConversions()->delete();

            // Delete item
            $item->delete();

            return redirect()
                ->route('inventory.items.index')
                ->with('success', 'تم حذف الصنف بنجاح');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف الصنف: ' . $e->getMessage());
        }
    }

    /**
     * Get unit conversions for an item (AJAX).
     */
    public function getUnitConversions(Item $item)
    {
        $conversions = $item->unitConversions()->with('unit')->ordered()->get();
        
        return response()->json([
            'success' => true,
            'conversions' => $conversions,
        ]);
    }
}
