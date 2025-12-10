<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseInvoice;
use App\Models\Warehouse;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseReceiptController extends Controller
{
    /**
     * Display a listing of purchase receipts.
     */
    public function index()
    {
        $receipts = PurchaseReceipt::with(['supplier', 'warehouse', 'purchaseInvoice'])
            ->latest()
            ->paginate(20);
            
        return view('purchases.receipts.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new purchase receipt.
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('purchases.receipts.create', compact('warehouses', 'suppliers'));
    }

    /**
     * Get invoices by warehouse (AJAX)
     */
    public function getInvoicesByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        
        // جلب الفواتير المعتمدة التي لم يتم استلامها بالكامل
        $invoices = PurchaseInvoice::with(['supplier', 'items.item'])
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'approved')
            ->whereDoesntHave('receipts', function($query) {
                $query->where('status', 'approved');
            })
            ->get();
        
        return response()->json($invoices);
    }

    /**
     * Store a newly created purchase receipt.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // إنشاء رقم استلام تلقائي
            $receiptNumber = 'REC-' . date('YmdHis');
            
            // إنشاء استلام البضاعة
            $receipt = PurchaseReceipt::create([
                'receipt_number' => $receiptNumber,
                'purchase_invoice_id' => $validated['purchase_invoice_id'],
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'receipt_date' => $validated['receipt_date'],
                'reference_number' => $validated['reference_number'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // إضافة الأصناف
            foreach ($validated['items'] as $item) {
                $receipt->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'] ?? 0,
                    'quantity_received' => $item['quantity_received'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // إذا كانت الحالة "معتمدة" → تحديث المخزون
            if ($validated['status'] === 'approved') {
                $this->updateInventory($receipt);
                $receipt->update([
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }

            DB::commit();
            
            return redirect()->route('purchases.receipts.index')
                ->with('success', 'تم إنشاء استلام البضاعة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء استلام البضاعة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase receipt.
     */
    public function show($id)
    {
        $receipt = PurchaseReceipt::with(['supplier', 'warehouse', 'purchaseInvoice', 'items.item'])
            ->findOrFail($id);
            
        return view('purchases.receipts.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified purchase receipt.
     */
    public function edit($id)
    {
        $receipt = PurchaseReceipt::with(['items'])->findOrFail($id);
        $warehouses = Warehouse::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('purchases.receipts.edit', compact('receipt', 'warehouses', 'suppliers'));
    }

    /**
     * Update the specified purchase receipt.
     */
    public function update(Request $request, $id)
    {
        $receipt = PurchaseReceipt::findOrFail($id);
        
        // لا يمكن تعديل استلام معتمد
        if ($receipt->status === 'approved') {
            return back()->with('error', 'لا يمكن تعديل استلام معتمد');
        }

        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $receipt->update($validated);

            // إذا تم اعتماد الاستلام → تحديث المخزون
            if ($validated['status'] === 'approved' && $receipt->status !== 'approved') {
                $this->updateInventory($receipt);
                $receipt->update([
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }

            DB::commit();
            
            return redirect()->route('purchases.receipts.show', $receipt->id)
                ->with('success', 'تم تحديث استلام البضاعة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث استلام البضاعة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase receipt.
     */
    public function destroy($id)
    {
        $receipt = PurchaseReceipt::findOrFail($id);
        
        // لا يمكن حذف استلام معتمد
        if ($receipt->status === 'approved') {
            return back()->with('error', 'لا يمكن حذف استلام معتمد');
        }

        $receipt->delete();
        
        return redirect()->route('purchases.receipts.index')
            ->with('success', 'تم حذف استلام البضاعة بنجاح');
    }

    /**
     * Approve the specified purchase receipt.
     */
    public function approve($id)
    {
        $receipt = PurchaseReceipt::findOrFail($id);
        
        if ($receipt->status === 'approved') {
            return back()->with('info', 'الاستلام معتمد بالفعل');
        }

        DB::beginTransaction();
        try {
            $receipt->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // تحديث المخزون
            $this->updateInventory($receipt);

            DB::commit();
            
            return redirect()->route('purchases.receipts.show', $receipt->id)
                ->with('success', 'تم اعتماد استلام البضاعة وتحديث المخزون');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء اعتماد الاستلام: ' . $e->getMessage());
        }
    }

    /**
     * Reject the specified purchase receipt.
     */
    public function reject($id)
    {
        $receipt = PurchaseReceipt::findOrFail($id);
        
        if ($receipt->status === 'approved') {
            return back()->with('error', 'لا يمكن رفض استلام معتمد');
        }

        $receipt->update([
            'status' => 'rejected',
        ]);
        
        return redirect()->route('purchases.receipts.show', $receipt->id)
            ->with('success', 'تم رفض استلام البضاعة');
    }

    /**
     * Update inventory based on receipt items.
     * تحديث المخزون بناءً على أصناف الاستلام
     */
    private function updateInventory(PurchaseReceipt $receipt)
    {
        foreach ($receipt->items as $item) {
            // تحديث كمية الصنف في المخزن
            DB::table('item_warehouse')->updateOrInsert(
                [
                    'item_id' => $item->item_id,
                    'warehouse_id' => $receipt->warehouse_id,
                ],
                [
                    'quantity' => DB::raw("quantity + {$item->quantity_received}"),
                    'updated_at' => now(),
                ]
            );

            // إنشاء حركة مخزون
            DB::table('stock_movements')->insert([
                'item_id' => $item->item_id,
                'warehouse_id' => $receipt->warehouse_id,
                'movement_type' => 'in',
                'quantity' => $item->quantity_received,
                'reference_type' => 'purchase_receipt',
                'reference_id' => $receipt->id,
                'movement_date' => $receipt->receipt_date,
                'notes' => "استلام بضاعة - فاتورة رقم: {$receipt->purchaseInvoice->invoice_number}",
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
