<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChartGroup;
use App\Models\ChartAccount;

// إنشاء الدليل الموحد
$chartGroup = ChartGroup::create([
    'unit_id' => 1,
    'code' => 'UCA-001',
    'name' => 'الدليل المحاسبي الموحد',
    'name_en' => 'Unified Chart of Accounts',
    'type' => 'master_chart',
    'is_active' => 1,
    'is_master' => 1,
]);

echo "✅ تم إنشاء الدليل! ID: {$chartGroup->id}\n";

// إنشاء الحسابات الرئيسية الستة
$accounts = [
    [
        'code' => '1000',
        'name' => 'الأصول',
        'name_en' => 'Assets',
        'account_type' => 'asset',
    ],
    [
        'code' => '2000',
        'name' => 'الخصوم',
        'name_en' => 'Liabilities',
        'account_type' => 'liability',
    ],
    [
        'code' => '3000',
        'name' => 'حقوق الملكية',
        'name_en' => 'Equity',
        'account_type' => 'equity',
    ],
    [
        'code' => '4000',
        'name' => 'الإيرادات',
        'name_en' => 'Revenue',
        'account_type' => 'revenue',
    ],
    [
        'code' => '5000',
        'name' => 'المصروفات',
        'name_en' => 'Expenses',
        'account_type' => 'expense',
    ],
    [
        'code' => '6000',
        'name' => 'الحسابات الوسيطة',
        'name_en' => 'Intermediate Accounts',
        'account_type' => 'asset',
    ],
];

foreach ($accounts as $accountData) {
    $account = ChartAccount::create([
        'chart_group_id' => $chartGroup->id,
        'code' => $accountData['code'],
        'name' => $accountData['name'],
        'name_en' => $accountData['name_en'],
        'account_type' => $accountData['account_type'],
        'level' => 1,
        'is_parent' => 1,
        'is_active' => 1,
        'balance' => 0,
    ]);
    
    echo "✅ تم إنشاء الحساب: {$account->code} - {$account->name}\n";
}

echo "\n🎉 تم إنشاء الدليل الموحد بنجاح مع 6 حسابات رئيسية!\n";
