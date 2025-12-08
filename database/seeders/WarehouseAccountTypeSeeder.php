<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseAccountTypeSeeder extends Seeder
{
    public function run()
    {
        // Check if warehouse type already exists
        $exists = DB::table('account_types')->where('key', 'warehouse')->exists();
        
        if (!$exists) {
            DB::table('account_types')->insert([
                'key' => 'warehouse',
                'name' => 'مخزون',
                'name_ar' => 'مخزون',
                'name_en' => 'Warehouse',
                'icon' => 'fas fa-warehouse',
                'sort_order' => 11,
                'is_active' => true,
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info('✅ تم إضافة نوع حساب "مخزون" بنجاح!');
        } else {
            $this->command->info('ℹ️  نوع حساب "مخزون" موجود بالفعل.');
        }
    }
}
