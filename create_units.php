<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Holding;
use App\Models\Unit;

// إنشاء الشركة القابضة
$holding = Holding::create([
    'code' => 'ALABBASI',
    'name' => 'مجموعة العباسي القابضة',
    'is_active' => 1,
]);

echo "✅ تم إنشاء الشركة القابضة: {$holding->name}\n";

// إنشاء الوحدة الأولى: وحدة العباسي خاص
$unit1 = Unit::create([
    'holding_id' => $holding->id,
    'code' => 'ALABBASI-PRIVATE',
    'name' => 'وحدة العباسي خاص',
    'type' => 'company',
    'is_active' => 1,
]);

echo "✅ تم إنشاء الوحدة الأولى: {$unit1->name}\n";

// إنشاء الوحدة الثانية: وحدة أعمال الحديدة
$unit2 = Unit::create([
    'holding_id' => $holding->id,
    'code' => 'HODEIDAH-BUSINESS',
    'name' => 'وحدة أعمال الحديدة',
    'type' => 'company',
    'is_active' => 1,
]);

echo "✅ تم إنشاء الوحدة الثانية: {$unit2->name}\n";

echo "\n🎉 تم إنشاء جميع الوحدات بنجاح!\n";
