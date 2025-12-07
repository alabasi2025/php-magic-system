<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChartGroup;
use App\Models\ChartAccount;
use Illuminate\Support\Facades\DB;

class SetupUnifiedChart extends Command
{
    protected $signature = 'chart:setup-unified';
    protected $description = 'حذف جميع الأدلة القديمة وإنشاء دليل محاسبي موحد';

    public function handle()
    {
        $this->info('🚀 بدء عملية إعادة هيكلة الأدلة المحاسبية...');
        
        // 1. حذف جميع الحسابات
        $this->info('🗑️  حذف جميع الحسابات القديمة...');
        ChartAccount::truncate();
        
        // 2. حذف جميع الأدلة
        $this->info('🗑️  حذف جميع الأدلة القديمة...');
        ChartGroup::truncate();
        
        // 3. إنشاء الدليل الموحد
        $this->info('📚 إنشاء الدليل المحاسبي الموحد...');
        $chartGroup = ChartGroup::create([
            'code' => 'HODEIDAH-UNIFIED',
            'name_ar' => 'الدليل المحاسبي الموحد',
            'name_en' => 'Unified Chart of Accounts',
            'description' => 'الدليل المحاسبي الموحد لوحدة أعمال الحديدة - وفق المعايير المحاسبية الدولية',
            'unit_id' => 1,
            'chart_type' => 'master',
            'is_master' => true,
            'is_active' => true,
        ]);
        
        // 4. إضافة الحسابات الرئيسية الستة
        $this->info('➕ إضافة الحسابات الرئيسية...');
        
        $accounts = [
            [
                'code' => '1000',
                'name_ar' => 'الأصول',
                'name_en' => 'Assets',
                'account_type' => 'asset',
            ],
            [
                'code' => '2000',
                'name_ar' => 'الخصوم',
                'name_en' => 'Liabilities',
                'account_type' => 'liability',
            ],
            [
                'code' => '3000',
                'name_ar' => 'حقوق الملكية',
                'name_en' => 'Equity',
                'account_type' => 'equity',
            ],
            [
                'code' => '4000',
                'name_ar' => 'الإيرادات',
                'name_en' => 'Revenue',
                'account_type' => 'revenue',
            ],
            [
                'code' => '5000',
                'name_ar' => 'المصروفات',
                'name_en' => 'Expenses',
                'account_type' => 'expense',
            ],
            [
                'code' => '6000',
                'name_ar' => 'الحسابات الوسيطة',
                'name_en' => 'Intermediate Accounts',
                'account_type' => 'asset',
            ],
        ];
        
        foreach ($accounts as $account) {
            ChartAccount::create([
                'chart_group_id' => $chartGroup->id,
                'code' => $account['code'],
                'name_ar' => $account['name_ar'],
                'name_en' => $account['name_en'],
                'account_type' => $account['account_type'],
                'level' => 1,
                'is_parent' => true,
                'is_active' => true,
            ]);
            
            $this->info("   ✅ {$account['code']} - {$account['name_ar']}");
        }
        
        $this->info('');
        $this->info('🎉 تم بنجاح! الدليل المحاسبي الموحد جاهز!');
        $this->info('📊 الدليل: ' . $chartGroup->name_ar);
        $this->info('🔢 عدد الحسابات: 6');
        
        return 0;
    }
}
