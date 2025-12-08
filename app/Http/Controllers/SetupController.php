<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SetupController
 * 
 * Handles system setup operations like running migrations and seeders.
 */
class SetupController extends Controller
{
    /**
     * Run migrations for inventory system.
     */
    public function runInventoryMigrations()
    {
        try {
            // Check if tables already exist
            $tablesExist = Schema::hasTable('item_units') && 
                          Schema::hasTable('warehouses') && 
                          Schema::hasTable('items') && 
                          Schema::hasTable('stock_movements');

            if ($tablesExist) {
                return response()->json([
                    'success' => true,
                    'message' => 'جداول نظام المخازن موجودة بالفعل',
                    'tables' => [
                        'item_units' => true,
                        'warehouses' => true,
                        'items' => true,
                        'stock_movements' => true,
                    ]
                ]);
            }

            // Run migrations
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2025_12_05_233617_create_item_units_table.php',
                '--force' => true
            ]);

            Artisan::call('migrate', [
                '--path' => 'database/migrations/2025_12_05_233618_create_warehouses_table.php',
                '--force' => true
            ]);

            Artisan::call('migrate', [
                '--path' => 'database/migrations/2025_12_05_233619_create_items_table.php',
                '--force' => true
            ]);

            Artisan::call('migrate', [
                '--path' => 'database/migrations/2025_12_05_233620_create_stock_movements_table.php',
                '--force' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تشغيل migrations بنجاح',
                'output' => Artisan::output()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تشغيل migrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run inventory system seeder.
     */
    public function runInventorySeeder()
    {
        try {
            // Check if data already exists
            $unitsCount = DB::table('item_units')->count();

            if ($unitsCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'البيانات الأساسية موجودة بالفعل',
                    'units_count' => $unitsCount
                ]);
            }

            // Run seeder
            Artisan::call('db:seed', [
                '--class' => 'InventorySystemSeeder',
                '--force' => true
            ]);

            $unitsCount = DB::table('item_units')->count();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة البيانات الأساسية بنجاح',
                'units_count' => $unitsCount,
                'output' => Artisan::output()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Setup complete inventory system (migrations + seeder).
     */
    public function setupInventorySystem()
    {
        try {
            $results = [];

            // Step 1: Run migrations
            $migrationsResponse = $this->runInventoryMigrations();
            $results['migrations'] = json_decode($migrationsResponse->getContent(), true);

            // Step 2: Run seeder
            $seederResponse = $this->runInventorySeeder();
            $results['seeder'] = json_decode($seederResponse->getContent(), true);

            return response()->json([
                'success' => true,
                'message' => 'تم إعداد نظام المخازن بنجاح',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعداد النظام',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check inventory system status.
     */
    public function checkInventoryStatus()
    {
        try {
            $status = [
                'tables' => [
                    'item_units' => Schema::hasTable('item_units'),
                    'warehouses' => Schema::hasTable('warehouses'),
                    'items' => Schema::hasTable('items'),
                    'stock_movements' => Schema::hasTable('stock_movements'),
                ],
                'data' => []
            ];

            if ($status['tables']['item_units']) {
                $status['data']['units_count'] = DB::table('item_units')->count();
            }

            if ($status['tables']['warehouses']) {
                $status['data']['warehouses_count'] = DB::table('warehouses')->count();
            }

            if ($status['tables']['items']) {
                $status['data']['items_count'] = DB::table('items')->count();
            }

            if ($status['tables']['stock_movements']) {
                $status['data']['movements_count'] = DB::table('stock_movements')->count();
            }

            $allTablesExist = !in_array(false, $status['tables']);

            return response()->json([
                'success' => true,
                'message' => $allTablesExist ? 'نظام المخازن جاهز' : 'نظام المخازن يحتاج إلى إعداد',
                'ready' => $allTablesExist,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فحص النظام',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
