<?php

// Cleanup route to delete test items
Route::get('/cleanup-test-items', function () {
    try {
        $deletedCount = \App\Models\Item::where('sku', 'LIKE', 'TEST-%')->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} test items successfully",
            'deleted_count' => $deletedCount
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting test items',
            'error' => $e->getMessage()
        ], 500);
    }
});
