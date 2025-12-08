<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\WarehouseGroup;

class DahmiyaWarehouseSeeder extends Seeder
{
    public function run()
    {
        // Get WG001 - مجموعة مخازن الديزل
        $warehouseGroup = WarehouseGroup::where('code', 'WG001')->first();
        
        if (!$warehouseGroup) {
            return "Error: Warehouse group WG001 not found!";
        }
        
        // Create warehouse
        Warehouse::create([
            'code' => 'WH-DHM-001',
            'name' => 'الدهمية',
            'warehouse_group_id' => $warehouseGroup->id,
            'status' => 'active',
            'description' => 'مخزن الدهمية - مخزن للديزل',
        ]);
        
        return "✅ تم إضافة مخزن 'الدهمية' بنجاح وربطه بمجموعة مخازن الديزل (WG001)!";
    }
}
