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
