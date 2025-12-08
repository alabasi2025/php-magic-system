<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DieselItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على معرف وحدة "لتر"
        $literUnit = DB::table('units')->where('name', 'لتر')->first();
        
        if (!$literUnit) {
            $this->command->error('لم يتم العثور على وحدة "لتر" في قاعدة البيانات');
            return;
        }

        // التحقق من عدم وجود صنف بنفس الرمز
        $existingItem = DB::table('items')->where('sku', 'DIESEL-001')->first();
        
        if ($existingItem) {
            $this->command->info('صنف الديزل موجود بالفعل');
            return;
        }

        // إضافة صنف الديزل
        DB::table('items')->insert([
            'sku' => 'DIESEL-001',
            'barcode' => null,
            'name' => 'الديزل',
            'description' => 'وقود الديزل - للاستخدام في المعدات والآليات',
            'unit_id' => $literUnit->id,
            'unit_price' => 5.00, // 5 ريال للتر
            'min_stock' => 100,
            'max_stock' => 10000,
            'status' => 'active',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ تم إضافة صنف الديزل بنجاح');
    }
}
