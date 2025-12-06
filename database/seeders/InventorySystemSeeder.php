<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemUnit;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

/**
 * Inventory System Seeder
 * 
 * Seeds initial data for the Inventory Management System v4.1.0
 */
class InventorySystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Seed Item Units
            $this->seedItemUnits();

            // Seed Sample Warehouses (optional)
            $this->seedSampleWarehouses();

            DB::commit();
            
            $this->command->info('✅ Inventory System seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding Inventory System: ' . $e->getMessage());
        }
    }

    /**
     * Seed common item units.
     */
    protected function seedItemUnits(): void
    {
        $units = [
            ['code' => 'PCS', 'name' => 'قطعة', 'name_en' => 'Piece', 'symbol' => 'pcs', 'status' => 'active'],
            ['code' => 'BOX', 'name' => 'كرتون', 'name_en' => 'Box/Carton', 'symbol' => 'box', 'status' => 'active'],
            ['code' => 'PLT', 'name' => 'باليت', 'name_en' => 'Pallet', 'symbol' => 'plt', 'status' => 'active'],
            ['code' => 'KG', 'name' => 'كيلوجرام', 'name_en' => 'Kilogram', 'symbol' => 'kg', 'status' => 'active'],
            ['code' => 'G', 'name' => 'جرام', 'name_en' => 'Gram', 'symbol' => 'g', 'status' => 'active'],
            ['code' => 'L', 'name' => 'لتر', 'name_en' => 'Liter', 'symbol' => 'L', 'status' => 'active'],
            ['code' => 'ML', 'name' => 'ملليلتر', 'name_en' => 'Milliliter', 'symbol' => 'ml', 'status' => 'active'],
            ['code' => 'M', 'name' => 'متر', 'name_en' => 'Meter', 'symbol' => 'm', 'status' => 'active'],
            ['code' => 'CM', 'name' => 'سنتيمتر', 'name_en' => 'Centimeter', 'symbol' => 'cm', 'status' => 'active'],
            ['code' => 'SET', 'name' => 'طقم', 'name_en' => 'Set', 'symbol' => 'set', 'status' => 'active'],
            ['code' => 'DOZEN', 'name' => 'دزينة', 'name_en' => 'Dozen', 'symbol' => 'dz', 'status' => 'active'],
            ['code' => 'PACK', 'name' => 'حزمة', 'name_en' => 'Pack', 'symbol' => 'pack', 'status' => 'active'],
        ];

        foreach ($units as $unit) {
            ItemUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }

        $this->command->info('  ✓ Item units seeded');
    }

    /**
     * Seed sample warehouses (optional - for testing).
     */
    protected function seedSampleWarehouses(): void
    {
        // Only seed if no warehouses exist
        if (Warehouse::count() > 0) {
            $this->command->info('  ⊘ Warehouses already exist, skipping sample data');
            return;
        }

        $warehouses = [
            [
                'code' => 'WH-MAIN',
                'name' => 'المخزن الرئيسي',
                'location' => 'صنعاء - شارع الزبيري',
                'status' => 'active',
                'description' => 'المخزن الرئيسي للشركة',
            ],
            [
                'code' => 'WH-SEC',
                'name' => 'المخزن الفرعي',
                'location' => 'عدن - المعلا',
                'status' => 'active',
                'description' => 'مخزن فرعي في عدن',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }

        $this->command->info('  ✓ Sample warehouses seeded');
    }
}
