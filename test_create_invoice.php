<?php

use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;

Route::get('/test-create-invoice', function () {
    try {
        DB::beginTransaction();
        
        $data = [
            'invoice_type_id' => 1, // الدهمية
            'supplier_id' => 1,
            'warehouse_id' => 1,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'payment_method' => 'cash',
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'notes' => 'فاتورة اختبار تلقائية',
            'items' => [
                [
                    'item_id' => 1,
                    'quantity' => 10,
                    'price' => 100,
                    'discount' => 0
                ]
            ]
        ];
        
        $invoice = PurchaseInvoice::createPurchaseInvoice($data);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الفاتورة بنجاح!',
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'internal_number' => $invoice->internal_number,
                'invoice_type' => $invoice->invoiceType->name ?? 'غير محدد',
                'total_amount' => $invoice->total_amount,
                'created_at' => $invoice->created_at->format('Y-m-d H:i:s')
            ]
        ], 200);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'فشل إنشاء الفاتورة',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
