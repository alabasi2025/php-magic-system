<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

try {
    // إنشاء القيد
    $entry = JournalEntry::create([
        'entry_number' => 'JE-TEST-001',
        'entry_date' => '2025-12-06',
        'description' => 'قيد تجريبي بسيط',
        'status' => 'draft',
        'unit_id' => 1,
        'created_by' => 1
    ]);

    // إضافة سطر مدين
    JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'chart_account_id' => 1,
        'debit' => 5000,
        'credit' => 0,
        'description' => 'مدين'
    ]);

    // إضافة سطر دائن
    JournalEntryDetail::create([
        'journal_entry_id' => $entry->id,
        'chart_account_id' => 2,
        'debit' => 0,
        'credit' => 5000,
        'description' => 'دائن'
    ]);

    echo "✅ تم إنشاء القيد بنجاح!\n";
    echo "ID: " . $entry->id . "\n";
    echo "رقم القيد: " . $entry->entry_number . "\n";
    echo "التاريخ: " . $entry->entry_date . "\n";
    echo "الوصف: " . $entry->description . "\n";

} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "الملف: " . $e->getFile() . "\n";
    echo "السطر: " . $e->getLine() . "\n";
}
