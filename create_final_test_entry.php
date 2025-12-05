<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

try {
    echo "🔄 بدء إنشاء قيد اختبار نهائي...\n";
    
    // إنشاء القيد
    $entry = JournalEntry::create([
        'entry_number' => 'JE-FINAL-TEST',
        'entry_date' => '2025-12-06',
        'description' => 'قيد اختبار نهائي - تم إنشاؤه للاختبار الشامل',
        'status' => 'draft',
        'unit_id' => 1,
        'user_id' => 1,
        'created_by' => 1,
        'total_debit' => 15000.00,
        'total_credit' => 15000.00,
        'is_balanced' => true,
    ]);
    
    echo "✅ تم إنشاء القيد! ID: {$entry->id}\n";
    echo "رقم القيد: {$entry->entry_number}\n";
    
    // إضافة سطر مدين 1
    $detail1 = JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'account_id' => 1, // الصناديق
        'debit' => 10000.00,
        'credit' => 0.00,
        'description' => 'إيداع نقدي في الصندوق',
    ]);
    echo "✅ تم إنشاء سطر المدين 1! ID: {$detail1->id}\n";
    
    // إضافة سطر مدين 2
    $detail2 = JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'account_id' => 3, // صناديق العهدة
        'debit' => 5000.00,
        'credit' => 0.00,
        'description' => 'عهدة نقدية',
    ]);
    echo "✅ تم إنشاء سطر المدين 2! ID: {$detail2->id}\n";
    
    // إضافة سطر دائن
    $detail3 = JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'account_id' => 2, // صناديق استلام التحصيل والتوريد
        'debit' => 0.00,
        'credit' => 15000.00,
        'description' => 'تحصيل من العملاء',
    ]);
    echo "✅ تم إنشاء سطر الدائن! ID: {$detail3->id}\n";
    
    // التحقق من التوازن
    $totalDebit = $entry->details->sum('debit');
    $totalCredit = $entry->details->sum('credit');
    
    echo "\n🎉 تم إنشاء القيد بنجاح مع جميع التفاصيل!\n";
    echo "ID: {$entry->id}\n";
    echo "رقم القيد: {$entry->entry_number}\n";
    echo "التاريخ: {$entry->entry_date}\n";
    echo "الوصف: {$entry->description}\n";
    echo "عدد السطور: " . $entry->details->count() . "\n";
    echo "إجمالي المدين: {$totalDebit}\n";
    echo "إجمالي الدائن: {$totalCredit}\n";
    echo "متوازن: " . ($totalDebit == $totalCredit ? 'نعم ✅' : 'لا ❌') . "\n";
    
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "الملف: " . $e->getFile() . "\n";
    echo "السطر: " . $e->getLine() . "\n";
}
