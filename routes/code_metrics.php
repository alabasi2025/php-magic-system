<?php

use App\Http\Controllers\CodeMetricsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Code Metrics Routes
|--------------------------------------------------------------------------
|
| Routes for Code Metrics v3.21.0 - Quality Analysis System
|
*/

Route::prefix('code-metrics')->name('code-metrics.')->group(function () {
    
    // Main dashboard
    Route::get('/', [CodeMetricsController::class, 'index'])->name('index');
    
    // Run new analysis
    Route::post('/analyze', [CodeMetricsController::class, 'analyze'])->name('analyze');
    
    // Show detailed analysis
    Route::get('/{id}', [CodeMetricsController::class, 'show'])->name('show');
    
    // Trends page
    Route::get('/trends/view', [CodeMetricsController::class, 'trends'])->name('trends');
    
    // Compare analyses
    Route::get('/compare/view', [CodeMetricsController::class, 'compare'])->name('compare');
    
    // Export analysis
    Route::get('/{id}/export', [CodeMetricsController::class, 'export'])->name('export');
    
    // Delete analysis
    Route::delete('/{id}', [CodeMetricsController::class, 'destroy'])->name('destroy');
});

// API Routes
Route::prefix('api/code-metrics')->name('api.code-metrics.')->middleware(['auth:sanctum'])->group(function () {
    
    // Get latest analysis
    Route::get('/latest', [CodeMetricsController::class, 'api'])->name('latest');
    
    // Run analysis via API
    Route::post('/analyze', [CodeMetricsController::class, 'analyze'])->name('analyze');
});
