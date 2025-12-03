<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PolicyGeneratorController;

/**
 * Policy Generator Routes
 * 
 * مسارات مولد Policies - المهمة 23/100
 * Policy Generator Routes - Task 23/100
 * 
 * @version v3.31.0
 * @author Manus AI
 */

// ========== Web Routes ==========

Route::prefix('policy-generator')->name('policy-generator.')->group(function () {
    
    // Index & Create
    Route::get('/', [PolicyGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [PolicyGeneratorController::class, 'create'])->name('create');
    
    // Store & Preview
    Route::post('/store', [PolicyGeneratorController::class, 'store'])->name('store');
    Route::post('/preview', [PolicyGeneratorController::class, 'preview'])->name('preview');
    
    // Download
    Route::get('/download/{name}', [PolicyGeneratorController::class, 'download'])->name('download');
});

// ========== API Routes ==========

Route::prefix('api/policy-generator')->name('api.policy-generator.')->group(function () {
    
    // API Endpoints
    Route::get('/list', [PolicyGeneratorController::class, 'list'])->name('list');
    Route::post('/generate', [PolicyGeneratorController::class, 'store'])->name('generate');
    Route::post('/preview', [PolicyGeneratorController::class, 'preview'])->name('preview');
});
