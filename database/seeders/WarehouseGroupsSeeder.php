<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the chart_group_id
        $chartGroupId = 8;
        
        // Get the warehouse accounts
        $accounts = [
            '1165' => 'مخازن الديزل',
            '1166' => 'مخازن المواد الخام',
            '1167' => 'مخازن المنتجات النهائية',
            '1168' => 'مخازن قطع الغيار',
            '1169' => 'مخازن الأدوات المكتبية',
        ];
        
        $warehouseGroups = [
            [
                'code' => 'WG001',
                'name' => 'مجموعة مخازن الديزل',
                'description' => 'مجموعة خاصة بمخازن الديزل والوقود',
                'account_code' => '1165',
            ],
            [
                'code' => 'WG002',
                'name' => 'مجموعة مخازن المواد الخام',
                'description' => 'مجموعة خاصة بمخازن المواد الخام والمستلزمات الأولية',
                'account_code' => '1166',
            ],
            [
                'code' => 'WG003',
                'name' => 'مجموعة مخازن المنتجات النهائية',
                'description' => 'مجموعة خاصة بمخازن المنتجات النهائية الجاهزة للبيع',
                'account_code' => '1167',
            ],
            [
                'code' => 'WG004',
                'name' => 'مجموعة مخازن قطع الغيار',
                'description' => 'مجموعة خاصة بمخازن قطع الغيار والمستلزمات',
                'account_code' => '1168',
            ],
            [
                'code' => 'WG005',
                'name' => 'مجموعة مخازن الأدوات المكتبية',
                'description' => 'مجموعة خاصة بمخازن الأدوات والمستلزمات المكتبية',
                'account_code' => '1169',
            ],
        ];
        
        foreach ($warehouseGroups as $groupData) {
            // Get the account
            $account = DB::table('chart_accounts')
                ->where('code', $groupData['account_code'])
                ->where('chart_group_id', $chartGroupId)
                ->first();
            
            if (!$account) {
                echo "❌ Account {$groupData['account_code']} not found, skipping group {$groupData['code']}...\n";
                continue;
            }
            
            // Check if group already exists
            $existing = DB::table('warehouse_groups')
                ->where('code', $groupData['code'])
                ->first();
            
            if ($existing) {
                echo "⚠️  Group {$groupData['code']} already exists, skipping...\n";
                continue;
            }
            
            DB::table('warehouse_groups')->insert([
                'code' => $groupData['code'],
                'name' => $groupData['name'],
                'description' => $groupData['description'],
                'account_id' => $account->id,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "✅ Added warehouse group: {$groupData['name']} ({$groupData['code']}) → Account: {$accounts[$groupData['account_code']]} ({$groupData['account_code']})\n";
        }
        
        echo "\n🎉 تم إضافة 5 مجموعات مخازن وربطها بالحسابات المحاسبية بنجاح!\n";
    }
}
