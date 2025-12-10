<?php
// تحديث الفاتورة رقم 8 لربطها بمخزن الدهمية (ID=1)
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PurchaseInvoice;

try {
    $invoice = PurchaseInvoice::find(8);
    
    if ($invoice) {
        $invoice->warehouse_id = 1; // مخزن الدهمية
        $invoice->save();
        
        echo "✅ تم تحديث الفاتورة رقم 8 بنجاح!\n";
        echo "warehouse_id = " . $invoice->warehouse_id . "\n";
    } else {
        echo "❌ الفاتورة رقم 8 غير موجودة\n";
    }
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
