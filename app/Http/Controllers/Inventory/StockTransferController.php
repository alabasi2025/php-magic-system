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
 * StockTransferController
 * 
 * إدارة أوامر التحويل المخزني (نقل البضائع بين المخازن)
 */
class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'toWarehouse', 'creator', 'approver', 'items.item'])
            ->where('movement_type', 'transfer')
            ->orderBy('created_at', 'desc');

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('to_warehouse_id')) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('movement_number', 'like', '%' . $request->search . '%');
        }

        $transfers = $query->paginate(15);
        $warehouses = Warehouse::where('is_active', true)->get();

        $totalOrders = StockMovement::where('movement_type', 'transfer')->count();
        $pendingOrders = StockMovement::where('movement_type', 'transfer')->where('status', 'pending')->count();
        $approvedOrders = StockMovement::where('movement_type', 'transfer')->where('status', 'approved')->count();

        return view('inventory.stock-transfer.index', compact('transfers', 'warehouses', 'totalOrders', 'pendingOrders', 'approvedOrders'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $nextNumber = $this->generateMovementNumber();

        return view('inventory.stock-transfer.create', compact('warehouses', 'items', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:warehouse_id',
            'movement_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $stockMovement = StockMovement::create([
                'movement_number' => $this->generateMovementNumber(),
                'movement_type' => 'transfer',
                'warehouse_id' => $validated['warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'movement_date' => $validated['movement_date'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                StockMovementItem::create([
                    'stock_movement_id' => $stockMovement->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.stock-transfer.index')
                ->with('success', 'تم إنشاء أمر التحويل بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockMovement $transfer)
    {
        $transfer->load(['warehouse', 'toWarehouse', 'creator', 'approver', 'items.item']);
        return view('inventory.stock-transfer.show', compact('transfer'));
    }

    public function destroy(StockMovement $transfer)
    {
        if ($transfer->status === 'approved') {
            return back()->with('error', 'لا يمكن حذف أمر معتمد');
        }

        $transfer->delete();
        return redirect()->route('inventory.stock-transfer.index')
            ->with('success', 'تم حذف أمر التحويل بنجاح');
    }

    public function approve(StockMovement $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'الأمر معتمد مسبقاً أو مرفوض');
        }

        $transfer->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد أمر التحويل بنجاح');
    }

    private function generateMovementNumber(): string
    {
        $prefix = 'TR';
        $date = now()->format('Ymd');
        $lastMovement = StockMovement::where('movement_type', 'transfer')
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
