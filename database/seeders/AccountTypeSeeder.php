<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountType;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountTypes = [
            [
                'key' => 'customer',
                'name_ar' => 'عميل',
                'name_en' => 'Customer',
                'icon' => 'fas fa-user-tie',
                'description' => 'حسابات العملاء',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'supplier',
                'name_ar' => 'مورد',
                'name_en' => 'Supplier',
                'icon' => 'fas fa-truck',
                'description' => 'حسابات الموردين',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'employee',
                'name_ar' => 'موظف',
                'name_en' => 'Employee',
                'icon' => 'fas fa-user',
                'description' => 'حسابات الموظفين',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'bank',
                'name_ar' => 'بنك',
                'name_en' => 'Bank',
                'icon' => 'fas fa-university',
                'description' => 'الحسابات البنكية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'cash',
                'name_ar' => 'صندوق نقدي',
                'name_en' => 'Cash',
                'icon' => 'fas fa-cash-register',
                'description' => 'الصناديق النقدية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'asset',
                'name_ar' => 'أصل',
                'name_en' => 'Asset',
                'icon' => 'fas fa-building',
                'description' => 'الأصول الثابتة',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'expense',
                'name_ar' => 'مصروف',
                'name_en' => 'Expense',
                'icon' => 'fas fa-money-bill-wave',
                'description' => 'حسابات المصروفات',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 7,
            ],
            [
                'key' => 'revenue',
                'name_ar' => 'إيراد',
                'name_en' => 'Revenue',
                'icon' => 'fas fa-chart-line',
                'description' => 'حسابات الإيرادات',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 8,
            ],
            [
                'key' => 'tax',
                'name_ar' => 'ضريبة',
                'name_en' => 'Tax',
                'icon' => 'fas fa-percentage',
                'description' => 'حسابات الضرائب',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 9,
            ],
            [
                'key' => 'partner',
                'name_ar' => 'شريك',
                'name_en' => 'Partner',
                'icon' => 'fas fa-handshake',
                'description' => 'حسابات الشركاء',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($accountTypes as $type) {
            AccountType::updateOrCreate(
                ['key' => $type['key']],
                $type
            );
        }
    }
}
