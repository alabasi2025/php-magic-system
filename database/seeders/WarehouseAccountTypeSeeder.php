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
            // Get the structure from existing records
            $firstRecord = DB::table('account_types')->first();
            $data = [
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add fields based on existing structure
            if (property_exists($firstRecord, 'code')) {
                $data['code'] = 'warehouse';
            }
            if (property_exists($firstRecord, 'key')) {
                $data['key'] = 'warehouse';
            }
            if (property_exists($firstRecord, 'name')) {
                $data['name'] = 'مخزون';
            }
            if (property_exists($firstRecord, 'name_ar')) {
                $data['name_ar'] = 'مخزون';
            }
            if (property_exists($firstRecord, 'name_en')) {
                $data['name_en'] = 'Warehouse';
            }
            if (property_exists($firstRecord, 'icon')) {
                $data['icon'] = 'fas fa-warehouse';
            }
            if (property_exists($firstRecord, 'sort_order')) {
                $data['sort_order'] = 11;
            }
            if (property_exists($firstRecord, 'is_active')) {
                $data['is_active'] = true;
            }
            if (property_exists($firstRecord, 'is_system')) {
                $data['is_system'] = false;
            }
            
            DB::table('account_types')->insert($data);
            
            $this->command->info('✅ تم إضافة نوع حساب "مخزون" بنجاح!');
        } else {
            $this->command->info('ℹ️  نوع حساب "مخزون" موجود بالفعل.');
        }
    }
}
