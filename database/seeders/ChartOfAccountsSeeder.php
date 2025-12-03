<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartGroup;
use App\Models\ChartAccount;
use App\Models\Unit;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على أول وحدة متاحة
        $unit = Unit::first();
        
        if (!$unit) {
            $this->command->warn('⚠️ لا توجد وحدات في النظام. يرجى إنشاء وحدة أولاً.');
            return;
        }

        $this->command->info("📊 إنشاء الأدلة المحاسبية المبسطة للوحدة: {$unit->name}");

        // 1. دليل أعمال الموظفين
        $employeeChart = ChartGroup::create([
            'unit_id' => $unit->id,
            'code' => 'EMP',
            'name' => 'دليل أعمال الموظفين',
            'type' => 'payroll',
            'description' => 'دليل محاسبي مبسط لإدارة حسابات الموظفين والرواتب والمستحقات',
            'color' => '#3B82F6',
            'icon' => 'fa-user-tie',
            'is_active' => true,
            'created_by' => 1,
        ]);

        // حسابات دليل الموظفين
        $empRoot = ChartAccount::create([
            'chart_group_id' => $employeeChart->id,
            'code' => 'EMP-001',
            'name' => 'حسابات الموظفين',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $employeeChart->id,
            'code' => 'EMP-001-001',
            'name' => 'الرواتب والأجور',
            'type' => 'detail',
            'parent_id' => $empRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $employeeChart->id,
            'code' => 'EMP-001-002',
            'name' => 'البدلات والحوافز',
            'type' => 'detail',
            'parent_id' => $empRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $employeeChart->id,
            'code' => 'EMP-001-003',
            'name' => 'السلف والمستحقات',
            'type' => 'detail',
            'parent_id' => $empRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $this->command->info("✅ تم إنشاء دليل أعمال الموظفين");

        // 2. دليل الحسابات النهائية
        $finalChart = ChartGroup::create([
            'unit_id' => $unit->id,
            'code' => 'FIN',
            'name' => 'دليل الحسابات النهائية',
            'type' => 'final_accounts',
            'description' => 'دليل محاسبي للحسابات الختامية وإعداد القوائم المالية',
            'color' => '#10B981',
            'icon' => 'fa-file-invoice-dollar',
            'is_active' => true,
            'created_by' => 1,
        ]);

        // حسابات دليل الحسابات النهائية
        $finRoot = ChartAccount::create([
            'chart_group_id' => $finalChart->id,
            'code' => 'FIN-001',
            'name' => 'الإيرادات',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $finalChart->id,
            'code' => 'FIN-001-001',
            'name' => 'إيرادات المبيعات',
            'type' => 'detail',
            'parent_id' => $finRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $finExp = ChartAccount::create([
            'chart_group_id' => $finalChart->id,
            'code' => 'FIN-002',
            'name' => 'المصروفات',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $finalChart->id,
            'code' => 'FIN-002-001',
            'name' => 'مصروفات إدارية',
            'type' => 'detail',
            'parent_id' => $finExp->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $this->command->info("✅ تم إنشاء دليل الحسابات النهائية");

        // 3. دليل الميزانية
        $budgetChart = ChartGroup::create([
            'unit_id' => $unit->id,
            'code' => 'BUD',
            'name' => 'دليل الميزانية',
            'type' => 'budget',
            'description' => 'دليل محاسبي لإدارة الميزانيات والتخطيط المالي',
            'color' => '#F59E0B',
            'icon' => 'fa-chart-pie',
            'is_active' => true,
            'created_by' => 1,
        ]);

        // حسابات دليل الميزانية
        $budRoot = ChartAccount::create([
            'chart_group_id' => $budgetChart->id,
            'code' => 'BUD-001',
            'name' => 'الأصول',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $budgetChart->id,
            'code' => 'BUD-001-001',
            'name' => 'الأصول المتداولة',
            'type' => 'detail',
            'parent_id' => $budRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $budLiab = ChartAccount::create([
            'chart_group_id' => $budgetChart->id,
            'code' => 'BUD-002',
            'name' => 'الخصوم',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $budgetChart->id,
            'code' => 'BUD-002-001',
            'name' => 'الخصوم المتداولة',
            'type' => 'detail',
            'parent_id' => $budLiab->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $this->command->info("✅ تم إنشاء دليل الميزانية");

        // 4. دليل المشاريع
        $projectChart = ChartGroup::create([
            'unit_id' => $unit->id,
            'code' => 'PRJ',
            'name' => 'دليل المشاريع',
            'type' => 'projects',
            'description' => 'دليل محاسبي لإدارة حسابات المشاريع ومراكز التكلفة',
            'color' => '#8B5CF6',
            'icon' => 'fa-project-diagram',
            'is_active' => true,
            'created_by' => 1,
        ]);

        // حسابات دليل المشاريع
        $prjRoot = ChartAccount::create([
            'chart_group_id' => $projectChart->id,
            'code' => 'PRJ-001',
            'name' => 'تكاليف المشاريع',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $projectChart->id,
            'code' => 'PRJ-001-001',
            'name' => 'تكاليف مباشرة',
            'type' => 'detail',
            'parent_id' => $prjRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $projectChart->id,
            'code' => 'PRJ-001-002',
            'name' => 'تكاليف غير مباشرة',
            'type' => 'detail',
            'parent_id' => $prjRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $this->command->info("✅ تم إنشاء دليل المشاريع");

        // 5. دليل الأصول
        $assetsChart = ChartGroup::create([
            'unit_id' => $unit->id,
            'code' => 'AST',
            'name' => 'دليل الأصول',
            'type' => 'assets',
            'description' => 'دليل محاسبي لإدارة الأصول الثابتة والاستهلاك',
            'color' => '#EF4444',
            'icon' => 'fa-warehouse',
            'is_active' => true,
            'created_by' => 1,
        ]);

        // حسابات دليل الأصول
        $astRoot = ChartAccount::create([
            'chart_group_id' => $assetsChart->id,
            'code' => 'AST-001',
            'name' => 'الأصول الثابتة',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $assetsChart->id,
            'code' => 'AST-001-001',
            'name' => 'المباني والإنشاءات',
            'type' => 'detail',
            'parent_id' => $astRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $assetsChart->id,
            'code' => 'AST-001-002',
            'name' => 'الآلات والمعدات',
            'type' => 'detail',
            'parent_id' => $astRoot->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $astDep = ChartAccount::create([
            'chart_group_id' => $assetsChart->id,
            'code' => 'AST-002',
            'name' => 'مجمع الاستهلاك',
            'type' => 'group',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        ChartAccount::create([
            'chart_group_id' => $assetsChart->id,
            'code' => 'AST-002-001',
            'name' => 'استهلاك المباني',
            'type' => 'detail',
            'parent_id' => $astDep->id,
            'level' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        $this->command->info("✅ تم إنشاء دليل الأصول");

        $this->command->info("🎉 تم إنشاء جميع الأدلة المحاسبية المبسطة بنجاح!");
    }
}
