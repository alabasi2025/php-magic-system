<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MigrationGeneratorController;

/**
 * 🧬 Routes: Migration Generator
 * 
 * مسارات نظام توليد الـ migrations الذكي
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */

// Web Routes
Route::prefix('migration-generator')->name('migration-generator.')->group(function () {
    
    // Index & Create
    Route::get('/', [MigrationGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [MigrationGeneratorController::class, 'create'])->name('create');
    
    // Generate Methods
    Route::post('/generate/text', [MigrationGeneratorController::class, 'generateFromText'])->name('generate-text');
    Route::post('/generate/json', [MigrationGeneratorController::class, 'generateFromJson'])->name('generate-json');
    Route::post('/generate/template', [MigrationGeneratorController::class, 'generateFromTemplate'])->name('generate-template');
    
    // Show & Update
    Route::get('/{id}', [MigrationGeneratorController::class, 'show'])->name('show');
    Route::put('/{id}', [MigrationGeneratorController::class, 'update'])->name('update');
    
    // Actions
    Route::post('/{id}/save-file', [MigrationGeneratorController::class, 'saveToFile'])->name('save-file');
    Route::get('/{id}/download', [MigrationGeneratorController::class, 'download'])->name('download');
    Route::delete('/{id}', [MigrationGeneratorController::class, 'destroy'])->name('destroy');
});

// API Routes
Route::prefix('api/migration-generator')->name('api.migration-generator.')->group(function () {
    
    // API Endpoints
    Route::get('/', [MigrationGeneratorController::class, 'apiIndex'])->name('index');
    Route::post('/generate', [MigrationGeneratorController::class, 'apiGenerate'])->name('generate');
    Route::get('/{id}', [MigrationGeneratorController::class, 'apiShow'])->name('show');
});
