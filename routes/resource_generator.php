<?php

use App\Http\Controllers\ResourceGeneratorController;
use Illuminate\Support\Facades\Route;

/**
 * Resource Generator Routes
 * مسارات مولد API Resources
 *
 * @version v3.30.0
 * @author Manus AI
 */

Route::prefix('resource-generator')->name('resource-generator.')->group(function () {
    
    // Web Routes
    Route::get('/', [ResourceGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [ResourceGeneratorController::class, 'create'])->name('create');
    Route::post('/', [ResourceGeneratorController::class, 'store'])->name('store');
    Route::get('/{id}', [ResourceGeneratorController::class, 'show'])->name('show');
    Route::delete('/{id}', [ResourceGeneratorController::class, 'destroy'])->name('destroy');
    
    // AJAX Routes
    Route::get('/model-attributes', [ResourceGeneratorController::class, 'getModelAttributes'])->name('model-attributes');
    Route::post('/preview', [ResourceGeneratorController::class, 'preview'])->name('preview');
});
