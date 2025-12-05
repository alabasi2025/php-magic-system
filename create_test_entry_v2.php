<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

try {
    echo "🔄 بدء إنشاء القيد...\n";
    
    // إنشاء القيد
    $entry = JournalEntry::create([
        'entry_number' => 'JE-TEST-004',
        'entry_date' => '2025-12-06',
        'description' => 'قيد تجريبي مع details',
        'status' => 'draft',
        'unit_id' => 1,
        'user_id' => 1,
        'total_debit' => 5000,
        'total_credit' => 5000,
        'is_balanced' => true,
        'created_by' => 1
    ]);

    echo "✅ تم إنشاء القيد! ID: " . $entry->id . "\n";
    echo "رقم القيد: " . $entry->entry_number . "\n";

    // إضافة سطر مدين
    echo "🔄 إضافة سطر مدين...\n";
    $debitDetail = JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'account_id' => 1,
        'debit' => 5000,
        'credit' => 0,
        'description' => 'مدين'
    ]);
    echo "✅ تم إنشاء سطر المدين! ID: " . $debitDetail->id . "\n";

    // إضافة سطر دائن
    echo "🔄 إضافة سطر دائن...\n";
    $creditDetail = JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'account_id' => 2,
        'debit' => 0,
        'credit' => 5000,
        'description' => 'دائن'
    ]);
    echo "✅ تم إنشاء سطر الدائن! ID: " . $creditDetail->id . "\n";

    echo "\n🎉 تم إنشاء القيد بنجاح مع جميع التفاصيل!\n";
    echo "ID: " . $entry->id . "\n";
    echo "رقم القيد: " . $entry->entry_number . "\n";
    echo "التاريخ: " . $entry->entry_date . "\n";
    echo "الوصف: " . $entry->description . "\n";
    echo "عدد السطور: " . $entry->details()->count() . "\n";

} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "الملف: " . $e->getFile() . "\n";
    echo "السطر: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
