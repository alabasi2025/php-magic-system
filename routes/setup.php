<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetupController;

/*
|--------------------------------------------------------------------------
| Setup Routes
|--------------------------------------------------------------------------
|
| Routes for system setup operations.
|
*/

Route::prefix('setup')->name('setup.')->group(function () {
    
    // Check inventory system status
    Route::get('/inventory/status', [SetupController::class, 'checkInventoryStatus'])
        ->name('inventory.status');
    
    // Run inventory migrations
    Route::get('/inventory/migrations', [SetupController::class, 'runInventoryMigrations'])
        ->name('inventory.migrations');
    
    // Run inventory seeder
    Route::get('/inventory/seeder', [SetupController::class, 'runInventorySeeder'])
        ->name('inventory.seeder');
    
    // Setup complete inventory system
    Route::get('/inventory/setup', [SetupController::class, 'setupInventorySystem'])
        ->name('inventory.setup');
    
    // Test items page
    Route::get('/test-items', function() {
        try {
            $units = \App\Models\ItemUnit::all();
            $items = \App\Models\Item::all();
            
            return response()->json([
                'success' => true,
                'units_count' => $units->count(),
                'items_count' => $items->count(),
                'sample_units' => $units->take(3)->map(function($u) {
                    return ['code' => $u->code, 'name' => $u->name];
                }),
                'controller_test' => 'Testing ItemController...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    });
    
    // Check and create item_unit_conversions table
    Route::get('/check-item-unit-conversions', function() {
        try {
            $exists = \DB::getSchemaBuilder()->hasTable('item_unit_conversions');
            
            if (!$exists) {
                // Run the specific migration
                \Artisan::call('migrate', [
                    '--path' => 'database/migrations/2025_12_09_000001_create_item_unit_conversions_table.php',
                    '--force' => true
                ]);
                
                $exists = \DB::getSchemaBuilder()->hasTable('item_unit_conversions');
            }
            
            return response()->json([
                'success' => true,
                'table_exists' => $exists,
                'message' => $exists ? 'Table exists and ready' : 'Failed to create table',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
    
    // Seed item_units
    Route::get('/seed-item-units', function() {
        try {
            $units = [
                ['name' => 'قطعة', 'symbol' => 'قطعة', 'description' => 'قطعة واحدة', 'status' => 'active'],
                ['name' => 'كيلوجرام', 'symbol' => 'كجم', 'description' => 'كيلوجرام', 'status' => 'active'],
                ['name' => 'لتر', 'symbol' => 'لتر', 'description' => 'لتر', 'status' => 'active'],
                ['name' => 'متر', 'symbol' => 'م', 'description' => 'متر', 'status' => 'active'],
                ['name' => 'كرتون', 'symbol' => 'كرتون', 'description' => 'كرتون', 'status' => 'active'],
                ['name' => 'دزينة', 'symbol' => 'دزينة', 'description' => '12 قطعة', 'status' => 'active'],
                ['name' => 'حزمة', 'symbol' => 'حزمة', 'description' => 'حزمة', 'status' => 'active'],
                ['name' => 'طقم', 'symbol' => 'طقم', 'description' => 'طقم', 'status' => 'active'],
                ['name' => 'جرام', 'symbol' => 'جم', 'description' => 'جرام', 'status' => 'active'],
                ['name' => 'ملليلتر', 'symbol' => 'مل', 'description' => 'ملليلتر', 'status' => 'active'],
                ['name' => 'سنتيمتر', 'symbol' => 'سم', 'description' => 'سنتيمتر', 'status' => 'active'],
                ['name' => 'باليت', 'symbol' => 'باليت', 'description' => 'باليت', 'status' => 'active'],
            ];
            
            $inserted = 0;
            foreach ($units as $unit) {
                $exists = \App\Models\ItemUnit::where('name', $unit['name'])->exists();
                if (!$exists) {
                    \App\Models\ItemUnit::create($unit);
                    $inserted++;
                }
            }
            
            $total = \App\Models\ItemUnit::count();
            
            return response()->json([
                'success' => true,
                'inserted' => $inserted,
                'total' => $total,
                'message' => "تم إضافة {$inserted} وحدة جديدة. الإجمالي: {$total}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Debug: View last error
    Route::get('/last-error', function() {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!\File::exists($logFile)) {
                return response()->json(['error' => 'Log file not found']);
            }
            
            $content = \File::get($logFile);
            $lines = explode("\n", $content);
            
            // Get last 200 lines
            $lastLines = array_slice($lines, -200);
            
            return response('<pre>' . htmlspecialchars(implode("\n", $lastLines)) . '</pre>');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Check items table structure
    Route::get('/check-items-table', function() {
        try {
            $exists = \Schema::hasTable('items');
            
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'table_exists' => false,
                    'message' => 'جدول items غير موجود'
                ]);
            }
            
            $columns = \DB::select('DESCRIBE items');
            
            return response()->json([
                'success' => true,
                'table_exists' => true,
                'columns' => $columns,
                'sample_data' => \DB::table('items')->limit(3)->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Run all migrations
    Route::get('/run-migrations', function() {
        try {
            \Artisan::call('migrate', ['--force' => true]);
            $output = \Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تشغيل migrations بنجاح',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Add Diesel Item
    Route::get('/add-diesel', function() {
        try {
            \DB::beginTransaction();
            
            $literUnit = \App\Models\ItemUnit::where('name', 'لتر')->first();
            
            if (!$literUnit) {
                throw new \Exception('وحدة اللتر غير موجودة');
            }
            
            // Check if already exists
            $existing = \App\Models\Item::where('code', 'DIESEL-001')->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'item_id' => $existing->id,
                    'message' => 'صنف الديزل موجود مسبقاً'
                ]);
            }
            
            // Get first category or create one
            $category = \App\Models\Inventory\Category::first();
            if (!$category) {
                $category = \App\Models\Inventory\Category::create([
                    'name' => 'مواد وقود',
                    'description' => 'مواد وقود ومحروقات',
                    'is_active' => true,
                ]);
            }
            
            $item = \App\Models\Item::create([
                'code' => 'DIESEL-001',
                'name' => 'الديزل',
                'description' => 'ديزل للمحركات',
                'category_id' => $category->id,
                'unit_id' => $literUnit->id,
                'min_stock' => 100,
                'max_stock' => 10000,
                'cost_price' => 5.00,
                'selling_price' => 5.00,
                'is_active' => true,
            ]);
            
            $units = [
                ['name' => 'لتر', 'capacity' => 1, 'price' => 5.00, 'is_primary' => true],
                ['name' => 'دبة', 'capacity' => 20, 'price' => 95.00, 'is_primary' => false],
                ['name' => 'برميل', 'capacity' => 200, 'price' => 950.00, 'is_primary' => false],
            ];
            
            foreach ($units as $unitData) {
                $unit = \App\Models\ItemUnit::where('name', $unitData['name'])->first();
                if ($unit) {
                    \App\Models\ItemUnitConversion::create([
                        'item_id' => $item->id,
                        'item_unit_id' => $unit->id,
                        'capacity' => $unitData['capacity'],
                        'price' => $unitData['price'],
                        'is_primary' => $unitData['is_primary'],
                    ]);
                }
            }
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'item_id' => $item->id,
                'message' => 'تم إضافة صنف الديزل بنجاح'
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Clear all caches
    Route::get('/clear-cache', function() {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح جميع الـ caches بنجاح',
                'caches_cleared' => ['cache', 'config', 'route', 'view']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
});

// Fix units status to active
Route::get('/setup/fix-units-status', function() {
    try {
        $updated = \App\Models\ItemUnit::query()->update(['status' => 'active']);
        
        return response()->json([
            'success' => true,
            'updated' => $updated,
            'total' => \App\Models\ItemUnit::count(),
            'message' => "تم تحديث {$updated} وحدة إلى active"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
