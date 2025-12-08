<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemUnitConversion;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    // Get unit IDs
    $literUnit = ItemUnit::where('name', 'لتر')->first();
    
    if (!$literUnit) {
        throw new Exception('وحدة اللتر غير موجودة');
    }
    
    // Create item
    $item = Item::create([
        'sku' => 'DIESEL-001',
        'name' => 'الديزل',
        'description' => 'ديزل للمحركات',
        'unit_id' => $literUnit->id,
        'min_stock' => 100,
        'max_stock' => 10000,
        'unit_price' => 5.00,
        'status' => 'active',
    ]);
    
    // Add unit conversions
    $units = [
        ['name' => 'لتر', 'capacity' => 1, 'price' => 5.00, 'is_primary' => true],
        ['name' => 'دبة', 'capacity' => 20, 'price' => 95.00, 'is_primary' => false],
        ['name' => 'برميل', 'capacity' => 200, 'price' => 950.00, 'is_primary' => false],
    ];
    
    foreach ($units as $unitData) {
        $unit = ItemUnit::where('name', $unitData['name'])->first();
        if ($unit) {
            ItemUnitConversion::create([
                'item_id' => $item->id,
                'item_unit_id' => $unit->id,
                'capacity' => $unitData['capacity'],
                'price' => $unitData['price'],
                'is_primary' => $unitData['is_primary'],
            ]);
        }
    }
    
    DB::commit();
    
    echo json_encode([
        'success' => true,
        'item_id' => $item->id,
        'message' => 'تم إضافة صنف الديزل بنجاح'
    ]);
    
} catch (Exception $e) {
    DB::rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
