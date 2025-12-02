<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على أول وحدة
        $unit = Unit::first();

        if (!$unit) {
            $this->command->warn('⚠️  لا توجد وحدات في النظام. يرجى إضافة وحدة أولاً.');
            return;
        }

        $this->command->info("🌱 بدء إضافة بيانات تجريبية لدليل الحسابات للوحدة: {$unit->name}");

        DB::beginTransaction();

        try {
            // 1. الأصول (Assets)
            $assets = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'code' => '1',
                'name' => 'الأصول',
                'name_en' => 'Assets',
                'account_level' => 'parent',
                'account_type' => 'asset',
                'is_active' => true,
                'is_root' => true,
                'level' => 1,
                'full_code' => '1',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 1.1 الأصول المتداولة
            $currentAssets = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $assets->id,
                'code' => '10',
                'name' => 'الأصول المتداولة',
                'name_en' => 'Current Assets',
                'account_level' => 'parent',
                'account_type' => 'asset',
                'is_active' => true,
                'level' => 2,
                'full_code' => '1.10',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 1.1.1 النقدية والبنوك
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $currentAssets->id,
                'code' => '1010',
                'name' => 'الصندوق الرئيسي',
                'name_en' => 'Main Cash Box',
                'account_level' => 'sub',
                'account_type' => 'asset',
                'analytical_type' => 'cash_box',
                'preferred_currencies' => ['SAR', 'USD'],
                'is_active' => true,
                'level' => 3,
                'full_code' => '1.10.1010',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $currentAssets->id,
                'code' => '1020',
                'name' => 'البنك الأهلي',
                'name_en' => 'Al Ahli Bank',
                'account_level' => 'sub',
                'account_type' => 'asset',
                'analytical_type' => 'bank',
                'preferred_currencies' => ['SAR', 'USD', 'EUR'],
                'is_active' => true,
                'level' => 3,
                'full_code' => '1.10.1020',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 1.1.2 العملاء
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $currentAssets->id,
                'code' => '1100',
                'name' => 'حسابات العملاء',
                'name_en' => 'Accounts Receivable',
                'account_level' => 'sub',
                'account_type' => 'asset',
                'analytical_type' => 'customer',
                'preferred_currencies' => ['SAR'],
                'is_active' => true,
                'level' => 3,
                'full_code' => '1.10.1100',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 2. الخصوم (Liabilities)
            $liabilities = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'code' => '2',
                'name' => 'الخصوم',
                'name_en' => 'Liabilities',
                'account_level' => 'parent',
                'account_type' => 'liability',
                'is_active' => true,
                'is_root' => true,
                'level' => 1,
                'full_code' => '2',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 2.1 الخصوم المتداولة
            $currentLiabilities = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $liabilities->id,
                'code' => '20',
                'name' => 'الخصوم المتداولة',
                'name_en' => 'Current Liabilities',
                'account_level' => 'parent',
                'account_type' => 'liability',
                'is_active' => true,
                'level' => 2,
                'full_code' => '2.20',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 2.1.1 الموردين
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $currentLiabilities->id,
                'code' => '2010',
                'name' => 'حسابات الموردين',
                'name_en' => 'Accounts Payable',
                'account_level' => 'sub',
                'account_type' => 'liability',
                'analytical_type' => 'supplier',
                'preferred_currencies' => ['SAR', 'USD'],
                'is_active' => true,
                'level' => 3,
                'full_code' => '2.20.2010',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 3. حقوق الملكية (Equity)
            $equity = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'code' => '3',
                'name' => 'حقوق الملكية',
                'name_en' => 'Equity',
                'account_level' => 'parent',
                'account_type' => 'equity',
                'is_active' => true,
                'is_root' => true,
                'level' => 1,
                'full_code' => '3',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 3.1 رأس المال
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $equity->id,
                'code' => '3010',
                'name' => 'رأس المال',
                'name_en' => 'Capital',
                'account_level' => 'sub',
                'account_type' => 'equity',
                'analytical_type' => 'partner',
                'preferred_currencies' => ['SAR'],
                'is_active' => true,
                'level' => 2,
                'full_code' => '3.3010',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 4. الإيرادات (Revenue)
            $revenue = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'code' => '4',
                'name' => 'الإيرادات',
                'name_en' => 'Revenue',
                'account_level' => 'parent',
                'account_type' => 'revenue',
                'is_active' => true,
                'is_root' => true,
                'level' => 1,
                'full_code' => '4',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 4.1 إيرادات المبيعات
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $revenue->id,
                'code' => '4010',
                'name' => 'إيرادات المبيعات',
                'name_en' => 'Sales Revenue',
                'account_level' => 'sub',
                'account_type' => 'revenue',
                'preferred_currencies' => ['SAR', 'USD'],
                'is_active' => true,
                'level' => 2,
                'full_code' => '4.4010',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 5. المصروفات (Expenses)
            $expenses = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'code' => '5',
                'name' => 'المصروفات',
                'name_en' => 'Expenses',
                'account_level' => 'parent',
                'account_type' => 'expense',
                'is_active' => true,
                'is_root' => true,
                'level' => 1,
                'full_code' => '5',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 5.1 المصروفات التشغيلية
            $operatingExpenses = ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $expenses->id,
                'code' => '50',
                'name' => 'المصروفات التشغيلية',
                'name_en' => 'Operating Expenses',
                'account_level' => 'parent',
                'account_type' => 'expense',
                'is_active' => true,
                'level' => 2,
                'full_code' => '5.50',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 5.1.1 الرواتب
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $operatingExpenses->id,
                'code' => '5010',
                'name' => 'الرواتب والأجور',
                'name_en' => 'Salaries and Wages',
                'account_level' => 'sub',
                'account_type' => 'expense',
                'analytical_type' => 'employee',
                'preferred_currencies' => ['SAR'],
                'is_active' => true,
                'level' => 3,
                'full_code' => '5.50.5010',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // 5.1.2 الإيجارات
            ChartOfAccount::create([
                'unit_id' => $unit->id,
                'parent_id' => $operatingExpenses->id,
                'code' => '5020',
                'name' => 'الإيجارات',
                'name_en' => 'Rent',
                'account_level' => 'sub',
                'account_type' => 'expense',
                'preferred_currencies' => ['SAR'],
                'is_active' => true,
                'level' => 3,
                'full_code' => '5.50.5020',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            DB::commit();

            $this->command->info('✅ تم إضافة بيانات تجريبية لدليل الحسابات بنجاح!');
            $this->command->info('📊 تم إضافة:');
            $this->command->info('   - 5 حسابات جذرية (أصول، خصوم، حقوق ملكية، إيرادات، مصروفات)');
            $this->command->info('   - 4 حسابات رئيسية');
            $this->command->info('   - 9 حسابات فرعية');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ في إضافة البيانات: ' . $e->getMessage());
        }
    }
}
