<?php

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/create-diesel-purchase-invoice', function () {
    try {
        DB::beginTransaction();
        
        // 1. البحث عن المورد "محمود الحجة"
        $supplier = Supplier::where('name', 'like', '%محمود الحجة%')->first();
        
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'المورد "محمود الحجة" غير موجود',
                'available_suppliers' => Supplier::select('id', 'name')->get()
            ]);
        }
        
        // 2. البحث عن صنف الديزل
        $dieselItem = Item::where('name', 'ديزل')->orWhere('sku', 'DIESEL-001')->first();
        
        if (!$dieselItem) {
            return response()->json([
                'success' => false,
                'message' => 'صنف الديزل غير موجود',
                'available_items' => Item::select('id', 'sku', 'name')->get()
            ]);
        }
        
        // 3. البحث عن مخزن الدهمية
        $warehouse = Warehouse::where('name', 'like', '%الدهمية%')->first();
        
        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'مخزن الدهمية غير موجود',
                'available_warehouses' => Warehouse::select('id', 'name')->get()
            ]);
        }
        
        // 4. إنشاء فاتورة المشتريات
        $invoice = PurchaseInvoice::create([
            'invoice_number' => 'PUR-' . date('Ymd') . '-' . rand(1000, 9999),
            'invoice_date' => now(),
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'payment_method' => 'cash',
            'status' => 'approved',
            'subtotal' => 466000, // 1000 لتر × 466 ريال
            'discount' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total' => 466000,
            'paid_amount' => 0,
            'remaining_amount' => 466000,
            'notes' => 'فاتورة شراء ديزل من محمود الحجة - 1000 لتر'
        ]);
        
        // 5. إضافة صنف الديزل للفاتورة
        $invoiceItem = PurchaseInvoiceItem::create([
            'purchase_invoice_id' => $invoice->id,
            'item_id' => $dieselItem->id,
            'quantity' => 1000,
            'unit_price' => 466,
            'discount' => 0,
            'total' => 466000
        ]);
        
        // 6. تحديث المخزون (إضافة حركة مخزنية)
        $stockMovement = StockMovement::create([
            'item_id' => $dieselItem->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'in',
            'quantity' => 1000,
            'unit_price' => 466,
            'total_value' => 466000,
            'reference_type' => 'purchase_invoice',
            'reference_id' => $invoice->id,
            'movement_date' => now(),
            'notes' => 'استلام ديزل من فاتورة مشتريات ' . $invoice->invoice_number
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء فاتورة المشتريات بنجاح',
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'supplier' => $supplier->name,
                'warehouse' => $warehouse->name,
                'item' => $dieselItem->name,
                'quantity' => 1000,
                'unit_price' => 466,
                'total' => 466000,
                'status' => $invoice->status
            ],
            'stock_movement' => [
                'id' => $stockMovement->id,
                'type' => 'in',
                'quantity' => 1000,
                'warehouse' => $warehouse->name
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إنشاء الفاتورة',
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
