<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/debug/last-error', function () {
    $logFile = storage_path('logs/laravel.log');
    
    if (!File::exists($logFile)) {
        return response()->json(['error' => 'Log file not found']);
    }
    
    $content = File::get($logFile);
    $lines = explode("\n", $content);
    
    // Get last 100 lines
    $lastLines = array_slice($lines, -100);
    
    return response()->json([
        'last_lines' => implode("\n", $lastLines)
    ]);
});

Route::get('/debug/test-item-store', function () {
    try {
        $data = [
            'sku' => 'TEST-' . time(),
            'name' => 'Test Item',
            'min_stock' => 10,
            'max_stock' => 100,
            'status' => 'active',
            'units' => [
                [
                    'unit_id' => 10, // لتر
                    'capacity' => 1,
                    'price' => null
                ]
            ],
            'primary_unit' => 0
        ];
        
        $item = \App\Models\Item::create([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'min_stock' => $data['min_stock'],
            'max_stock' => $data['max_stock'],
            'status' => $data['status'],
        ]);
        
        return response()->json([
            'success' => true,
            'item' => $item,
            'message' => 'Item created successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
