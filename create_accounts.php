<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChartAccount;

// الحصول على ID الدليل المحاسبي
$chartGroupId = 1; // دليل أعمال الموظفين - الحديدة

echo "إنشاء الحسابات الأساسية...\n\n";

// 1. الحساب الرئيسي: الصناديق
$mainAccount = ChartAccount::create([
    'chart_group_id' => $chartGroupId,
    'parent_id' => null,
    'code' => '001',
    'name' => 'الصناديق',
    'is_parent' => 1, // حساب رئيسي
    'account_type' => null,
    'intermediate_for' => null,
    'description' => 'حساب رئيسي لتنظيم جميع أنواع الصناديق',
    'is_active' => 1,
]);

echo "✓ تم إنشاء الحساب الرئيسي: {$mainAccount->name} (كود: {$mainAccount->code})\n";

// 2. الحساب الفرعي الأول: صناديق استلام التحصيل والتوريد
$account1 = ChartAccount::create([
    'chart_group_id' => $chartGroupId,
    'parent_id' => $mainAccount->id,
    'code' => '001-001',
    'name' => 'صناديق استلام التحصيل والتوريد',
    'is_parent' => 0, // حساب فرعي
    'account_type' => 'intermediate', // حساب وسيط
    'intermediate_for' => 'cash_boxes', // للصناديق
    'description' => 'حساب وسيط لصناديق استلام التحصيل والتوريد',
    'is_active' => 1,
    'is_linked' => 0,
]);

echo "✓ تم إنشاء الحساب الفرعي: {$account1->name} (كود: {$account1->code})\n";

// 3. الحساب الفرعي الثاني: صناديق العهدة
$account2 = ChartAccount::create([
    'chart_group_id' => $chartGroupId,
    'parent_id' => $mainAccount->id,
    'code' => '001-002',
    'name' => 'صناديق العهدة',
    'is_parent' => 0, // حساب فرعي
    'account_type' => 'intermediate', // حساب وسيط
    'intermediate_for' => 'cash_boxes', // للصناديق
    'description' => 'حساب وسيط لصناديق العهدة',
    'is_active' => 1,
    'is_linked' => 0,
]);

echo "✓ تم إنشاء الحساب الفرعي: {$account2->name} (كود: {$account2->code})\n";

// 4. الحساب الفرعي الثالث: صناديق سلف الموظفين
$account3 = ChartAccount::create([
    'chart_group_id' => $chartGroupId,
    'parent_id' => $mainAccount->id,
    'code' => '001-003',
    'name' => 'صناديق سلف الموظفين',
    'is_parent' => 0, // حساب فرعي
    'account_type' => 'intermediate', // حساب وسيط
    'intermediate_for' => 'cash_boxes', // للصناديق
    'description' => 'حساب وسيط لصناديق سلف الموظفين',
    'is_active' => 1,
    'is_linked' => 0,
]);

echo "✓ تم إنشاء الحساب الفرعي: {$account3->name} (كود: {$account3->code})\n";

echo "\n✅ تم إنشاء جميع الحسابات بنجاح!\n";
echo "إجمالي الحسابات المنشأة: 4 حسابات\n";
