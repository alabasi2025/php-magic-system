<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * StockMovementController
 * 
 * Handles CRUD operations for stock movements.
 */
class StockMovementController extends Controller
{
    protected $stockMovementService;

    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'item', 'creator', 'approver']);

        // Filter by movement type
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        // Search by movement number
        if ($request->filled('search')) {
            $query->where('movement_number', 'like', "%{$request->search}%");
        }

        $movements = $query->latest()->paginate(15);
        $warehouses = Warehouse::active()->get();

        return view('inventory.movements.index', compact('movements', 'warehouses'));
    }

    /**
     * Show the form for creating a new stock movement.
     */
    public function create(Request $request)
    {
        $movementType = $request->get('type', 'stock_in');
        $warehouses = Warehouse::active()->get();
        $items = Item::active()->get();

        return view('inventory.movements.create', compact('movementType', 'warehouses', 'items'));
    }

    /**
     * Store a newly created stock movement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movement_type' => 'required|in:stock_in,stock_out,transfer,adjustment,return',
            'warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required_if:movement_type,transfer|nullable|exists:warehouses,id|different:warehouse_id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'movement_date' => 'required|date',
            'reference_id' => 'nullable|integer',
            'reference_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate movement number
            $validated['movement_number'] = $this->stockMovementService->generateMovementNumber($validated['movement_type']);
            $validated['created_by'] = Auth::id();
            $validated['status'] = 'pending';

            // Create stock movement
            $movement = StockMovement::create($validated);

            // If transfer, create the corresponding entry for destination warehouse
            if ($validated['movement_type'] === 'transfer') {
                $this->stockMovementService->createTransferEntry($movement);
            }

            DB::commit();

            return redirect()
                ->route('stock-movements.index')
                ->with('success', 'تم إنشاء حركة المخزون بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء حركة المخزون: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified stock movement.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['warehouse', 'toWarehouse', 'item.unit', 'creator', 'approver', 'journalEntry']);
        return view('inventory.movements.show', compact('stockMovement'));
    }

    /**
     * Approve a stock movement.
     */
    public function approve(StockMovement $stockMovement)
    {
        if ($stockMovement->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'لا يمكن اعتماد هذه الحركة');
        }

        try {
            DB::beginTransaction();

            // Update movement status
            $stockMovement->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Create accounting journal entry
            $journalEntry = $this->stockMovementService->createJournalEntry($stockMovement);
            
            if ($journalEntry) {
                $stockMovement->update(['journal_entry_id' => $journalEntry->id]);
            }

            // Check if item is below minimum stock and create alert
            $this->stockMovementService->checkStockLevel($stockMovement->item_id);

            DB::commit();

            return redirect()
                ->route('stock-movements.show', $stockMovement)
                ->with('success', 'تم اعتماد حركة المخزون بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء اعتماد حركة المخزون: ' . $e->getMessage());
        }
    }

    /**
     * Reject a stock movement.
     */
    public function reject(Request $request, StockMovement $stockMovement)
    {
        if ($stockMovement->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'لا يمكن رفض هذه الحركة');
        }

        $stockMovement->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => ($stockMovement->notes ?? '') . "\n\nسبب الرفض: " . $request->input('rejection_reason', 'غير محدد'),
        ]);

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'تم رفض حركة المخزون');
    }

    /**
     * Remove the specified stock movement from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        // Only allow deletion of pending movements
        if ($stockMovement->status !== 'pending') {
            return redirect()
                ->route('stock-movements.index')
                ->with('error', 'لا يمكن حذف حركة مخزون معتمدة أو مرفوضة');
        }

        $stockMovement->delete();

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'تم حذف حركة المخزون بنجاح');
    }
}
