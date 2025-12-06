<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemSetupController extends Controller
{
    /**
     * Show system setup page
     */
    public function index()
    {
        $status = $this->checkSystemStatus();
        return view('system-setup', compact('status'));
    }
    
    /**
     * Run migrations
     */
    public function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Migrations executed successfully',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error running migrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Run seeders
     */
    public function runSeeders()
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'InventorySystemSeeder',
                '--force' => true
            ]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Seeders executed successfully',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error running seeders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check system status
     */
    private function checkSystemStatus()
    {
        $status = [];
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $status['database'] = true;
        } catch (\Exception $e) {
            $status['database'] = false;
            $status['database_error'] = $e->getMessage();
        }
        
        // Check tables
        $tables = ['warehouses', 'items', 'item_units', 'stock_movements'];
        $status['tables'] = [];
        foreach ($tables as $table) {
            $status['tables'][$table] = Schema::hasTable($table);
        }
        
        // Check migrations
        try {
            $migrations = DB::table('migrations')->pluck('migration')->toArray();
            $inventoryMigrations = array_filter($migrations, function($m) {
                return str_contains($m, 'warehouse') || str_contains($m, 'item') || str_contains($m, 'stock');
            });
            $status['migrations_count'] = count($inventoryMigrations);
            $status['migrations'] = array_values($inventoryMigrations);
        } catch (\Exception $e) {
            $status['migrations_error'] = $e->getMessage();
        }
        
        // Check data
        if (isset($status['tables']['warehouses']) && $status['tables']['warehouses']) {
            try {
                $status['warehouses_count'] = DB::table('warehouses')->count();
                $status['items_count'] = DB::table('items')->count();
                $status['movements_count'] = DB::table('stock_movements')->count();
            } catch (\Exception $e) {
                $status['data_error'] = $e->getMessage();
            }
        }
        
        return $status;
    }
    
    /**
     * Get system diagnostic
     */
    public function diagnostic()
    {
        $status = $this->checkSystemStatus();
        return response()->json($status, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
