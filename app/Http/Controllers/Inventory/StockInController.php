<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * StockInController
 * 
 * إدارة أوامر التوريد المخزني (إدخال البضائع)
 */
class StockInController extends Controller
{
    /**
     * Display a listing of stock in orders.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'creator', 'approver', 'items.item'])
            ->where('movement_type', 'stock_in')
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('movement_number', 'like', '%' . $request->search . '%');
        }

        $stockIns = $query->paginate(15);
        $warehouses = Warehouse::where('status', 'active')->get();

        // Statistics
        $totalOrders = StockMovement::where('movement_type', 'stock_in')->count();
        $pendingOrders = StockMovement::where('movement_type', 'stock_in')->where('status', 'pending')->count();
        $approvedOrders = StockMovement::where('movement_type', 'stock_in')->where('status', 'approved')->count();

        return view('inventory.stock-in.index', compact('stockIns', 'warehouses', 'totalOrders', 'pendingOrders', 'approvedOrders'));
    }

    /**
     * Show the form for creating a new stock in order.
     */
    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        $items = Item::where('status', 'active')->get();
        $nextNumber = $this->generateMovementNumber();

        return view('inventory.stock-in.create', compact('warehouses', 'items', 'nextNumber'));
    }

    /**
     * Store a newly created stock in order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create stock movement
            $stockMovement = StockMovement::create([
                'movement_number' => $this->generateMovementNumber(),
                'movement_type' => 'stock_in',
                'warehouse_id' => $validated['warehouse_id'],
                'movement_date' => $validated['movement_date'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Create movement items
            foreach ($validated['items'] as $itemData) {
                StockMovementItem::create([
                    'stock_movement_id' => $stockMovement->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.stock-in.index')
                ->with('success', 'تم إنشاء أمر التوريد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء أمر التوريد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified stock in order.
     */
    public function show(StockMovement $stockIn)
    {
        $stockIn->load(['warehouse', 'creator', 'approver', 'items.item']);
        
        return view('inventory.stock-in.show', compact('stockIn'));
    }

    /**
     * Show the form for editing the specified stock in order.
     */
    public function edit(StockMovement $stockIn)
    {
        if ($stockIn->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل أمر معتمد أو مرفوض');
        }

        $stockIn->load('items');
        $warehouses = Warehouse::where('status', 'active')->get();
        $items = Item::where('status', 'active')->get();

        return view('inventory.stock-in.edit', compact('stockIn', 'warehouses', 'items'));
    }

    /**
     * Update the specified stock in order.
     */
    public function update(Request $request, StockMovement $stockIn)
    {
        if ($stockIn->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل أمر معتمد أو مرفوض');
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update stock movement
            $stockIn->update([
                'warehouse_id' => $validated['warehouse_id'],
                'movement_date' => $validated['movement_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete old items and create new ones
            $stockIn->items()->delete();

            foreach ($validated['items'] as $itemData) {
                StockMovementItem::create([
                    'stock_movement_id' => $stockIn->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.stock-in.index')
                ->with('success', 'تم تحديث أمر التوريد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث أمر التوريد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified stock in order.
     */
    public function destroy(StockMovement $stockIn)
    {
        if ($stockIn->status === 'approved') {
            return back()->with('error', 'لا يمكن حذف أمر معتمد');
        }

        try {
            $stockIn->delete();
            return redirect()->route('inventory.stock-in.index')
                ->with('success', 'تم حذف أمر التوريد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف أمر التوريد');
        }
    }

    /**
     * Approve stock in order.
     */
    public function approve(StockMovement $stockIn)
    {
        if ($stockIn->status !== 'pending') {
            return back()->with('error', 'الأمر معتمد مسبقاً أو مرفوض');
        }

        try {
            DB::beginTransaction();

            $stockIn->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // TODO: Update inventory quantities
            // foreach ($stockIn->items as $item) {
            //     // Update ItemWarehouse quantity
            // }

            DB::commit();

            return back()->with('success', 'تم اعتماد أمر التوريد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء اعتماد الأمر');
        }
    }

    /**
     * Generate unique movement number.
     */
    private function generateMovementNumber(): string
    {
        $prefix = 'IN';
        $date = now()->format('Ymd');
        $lastMovement = StockMovement::where('movement_type', 'stock_in')
            ->where('movement_number', 'like', $prefix . $date . '%')
            ->orderBy('movement_number', 'desc')
            ->first();

        if ($lastMovement) {
            $lastNumber = intval(substr($lastMovement->movement_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }
}
