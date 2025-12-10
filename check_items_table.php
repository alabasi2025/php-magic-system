<?php
/**
 * Check Items Table Structure
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== فحص بنية جدول items ===\n\n";

// Get table structure
$columns = DB::select("DESCRIBE items");

echo "الحقول الموجودة في جدول items:\n";
echo str_repeat("-", 80) . "\n";
printf("%-20s %-20s %-10s %-10s %-10s\n", "Field", "Type", "Null", "Key", "Default");
echo str_repeat("-", 80) . "\n";

foreach ($columns as $column) {
    printf("%-20s %-20s %-10s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key,
        $column->Default ?? 'NULL'
    );
}

echo "\n=== فحص عدد الأصناف الموجودة ===\n";
$count = DB::table('items')->count();
echo "عدد الأصناف: {$count}\n";

if ($count > 0) {
    echo "\n=== آخر صنف تم إضافته ===\n";
    $lastItem = DB::table('items')->orderBy('id', 'desc')->first();
    print_r($lastItem);
}

echo "\n=== فحص وحدات القياس ===\n";
$units = DB::table('item_units')->get();
echo "عدد الوحدات: " . $units->count() . "\n";
foreach ($units as $unit) {
    echo "- ID: {$unit->id}, Name: {$unit->name}\n";
}
