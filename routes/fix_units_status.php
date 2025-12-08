<?php

use Illuminate\Support\Facades\Route;
use App\Models\ItemUnit;

Route::get('/fix-units-status', function () {
    $updated = ItemUnit::query()->update(['status' => 'active']);
    
    return response()->json([
        'success' => true,
        'updated' => $updated,
        'total' => ItemUnit::count(),
        'message' => "تم تحديث {$updated} وحدة إلى active"
    ]);
});
