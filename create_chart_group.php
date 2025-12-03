<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// البحث عن وحدة أعمال الحديدة
$unit = App\Models\Unit::where('code', 'HODEIDAH-BUSINESS')->first();

if (!$unit) {
    echo "Error: Unit not found!\n";
    exit(1);
}

// إنشاء دليل أعمال الموظفين
$chartGroup = App\Models\ChartGroup::create([
    'unit_id' => $unit->id,
    'code' => 'EMP-HOD',
    'name' => 'دليل أعمال الموظفين - الحديدة',
    'type' => 'payroll',
    'description' => 'دليل محاسبي مبسط لإدارة حسابات الموظفين والرواتب',
    'color' => '#3B82F6',
    'icon' => 'fa-user-tie',
    'is_active' => 1
]);

echo "✅ تم إنشاء الدليل المحاسبي بنجاح!\n";
echo "ID: {$chartGroup->id}\n";
echo "الكود: {$chartGroup->code}\n";
echo "الاسم: {$chartGroup->name}\n";
