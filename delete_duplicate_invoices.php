<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PurchaseInvoice;

echo "🔍 البحث عن الفواتير المكررة...\n\n";

// البحث عن الفواتير ذات الأرقام الداخلية المكررة
$duplicates = PurchaseInvoice::select('internal_number')
    ->groupBy('internal_number')
    ->havingRaw('COUNT(*) > 1')
    ->pluck('internal_number');

if ($duplicates->isEmpty()) {
    echo "✅ لا توجد فواتير مكررة!\n";
    exit(0);
}

echo "⚠️  تم العثور على " . $duplicates->count() . " رقم مكرر:\n";
foreach ($duplicates as $number) {
    echo "   - $number\n";
}

echo "\n🗑️  حذف الفواتير المكررة (الاحتفاظ بالأحدث فقط)...\n\n";

foreach ($duplicates as $internalNumber) {
    $invoices = PurchaseInvoice::where('internal_number', $internalNumber)
        ->orderBy('created_at', 'desc')
        ->get();
    
    // الاحتفاظ بالأول (الأحدث) وحذف الباقي
    $keep = $invoices->first();
    $toDelete = $invoices->skip(1);
    
    echo "📋 الرقم الداخلي: $internalNumber\n";
    echo "   ✅ الاحتفاظ بـ: ID={$keep->id}, تاريخ={$keep->created_at}\n";
    
    foreach ($toDelete as $invoice) {
        echo "   ❌ حذف: ID={$invoice->id}, تاريخ={$invoice->created_at}\n";
        $invoice->delete();
    }
    
    echo "\n";
}

echo "✅ تم حذف جميع الفواتير المكررة بنجاح!\n";
