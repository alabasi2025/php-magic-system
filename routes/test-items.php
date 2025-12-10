<?php

use Illuminate\Support\Facades\Route;
use App\Models\Item;

Route::get('/test-items-simple', function () {
    try {
        $count = Item::count();
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => 'Items table accessible'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
