<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PurchaseInvoice;
use App\Models\Warehouse;

// البحث عن مخزن الدهمية
$warehouse = Warehouse::where('name', 'like', '%الدهمية%')->first();

if (!$warehouse) {
    echo "❌ مخزن الدهمية غير موجود!\n";
    exit(1);
}

echo "✅ تم العثور على مخزن الدهمية - ID: {$warehouse->id}\n";
echo "   الاسم: {$warehouse->name}\n\n";

// تحديث الفاتورة رقم 8
$invoice = PurchaseInvoice::find(8);

if (!$invoice) {
    echo "❌ الفاتورة رقم 8 غير موجودة!\n";
    exit(1);
}

echo "✅ تم العثور على الفاتورة رقم {$invoice->invoice_number}\n";
echo "   warehouse_id قبل التحديث: " . ($invoice->warehouse_id ?? 'NULL') . "\n";

// تحديث warehouse_id
$invoice->warehouse_id = $warehouse->id;
$invoice->save();

echo "✅ تم تحديث الفاتورة بنجاح!\n";
echo "   warehouse_id بعد التحديث: {$invoice->warehouse_id}\n";
echo "\n🎉 الآن يمكنك استلام البضاعة من مخزن الدهمية!\n";
