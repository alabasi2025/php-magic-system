<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the chart_group_id (assuming it's 8 based on the URL)
        $chartGroupId = 8;
        
        // Get the parent account (الأصول المتداولة - 1100)
        $parentAccount = DB::table('chart_accounts')
            ->where('code', '1100')
            ->where('chart_group_id', $chartGroupId)
            ->first();
        
        if (!$parentAccount) {
            echo "Parent account not found!\n";
            return;
        }
        
        $accounts = [
            [
                'code' => '1165',
                'name' => 'مخازن الديزل',
                'name_en' => 'Diesel Warehouses',
                'description' => 'حساب مخزون الديزل والوقود',
            ],
            [
                'code' => '1166',
                'name' => 'مخازن المواد الخام',
                'name_en' => 'Raw Materials Warehouses',
                'description' => 'حساب مخزون المواد الخام والمستلزمات الأولية',
            ],
            [
                'code' => '1167',
                'name' => 'مخازن المنتجات النهائية',
                'name_en' => 'Finished Products Warehouses',
                'description' => 'حساب مخزون المنتجات النهائية الجاهزة للبيع',
            ],
            [
                'code' => '1168',
                'name' => 'مخازن قطع الغيار',
                'name_en' => 'Spare Parts Warehouses',
                'description' => 'حساب مخزون قطع الغيار والمستلزمات',
            ],
            [
                'code' => '1169',
                'name' => 'مخازن الأدوات المكتبية',
                'name_en' => 'Office Supplies Warehouses',
                'description' => 'حساب مخزون الأدوات والمستلزمات المكتبية',
            ],
        ];
        
        foreach ($accounts as $accountData) {
            // Check if account already exists
            $existing = DB::table('chart_accounts')
                ->where('code', $accountData['code'])
                ->where('chart_group_id', $chartGroupId)
                ->first();
            
            if ($existing) {
                echo "Account {$accountData['code']} already exists, skipping...\n";
                continue;
            }
            
            DB::table('chart_accounts')->insert([
                'chart_group_id' => $chartGroupId,
                'parent_id' => $parentAccount->id,
                'level' => $parentAccount->level + 1,
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'name_en' => $accountData['name_en'],
                'is_parent' => false,
                'account_type' => 'warehouse',
                'is_linked' => false,
                'description' => $accountData['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "✅ Added account: {$accountData['name']} ({$accountData['code']})\n";
        }
        
        echo "\n✅ تم إضافة 5 حسابات من نوع مخزون بنجاح!\n";
    }
}
