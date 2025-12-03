<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MiddlewareGeneratorController;

/**
 * 🛡️ Routes: Middleware Generator
 * 
 * مسارات نظام توليد Middleware الذكي
 * 
 * @version 3.28.0
 * @since 2025-12-03
 */

// Web Routes
Route::prefix('middleware-generator')->name('middleware-generator.')->group(function () {
    
    // Index & Create
    Route::get('/', [MiddlewareGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [MiddlewareGeneratorController::class, 'create'])->name('create');
    
    // Generate & Preview
    Route::post('/generate', [MiddlewareGeneratorController::class, 'generate'])->name('generate');
    Route::post('/preview', [MiddlewareGeneratorController::class, 'preview'])->name('preview');
    
    // Save & Download
    Route::post('/save', [MiddlewareGeneratorController::class, 'save'])->name('save');
    Route::get('/download', [MiddlewareGeneratorController::class, 'download'])->name('download');
    
    // List & Delete
    Route::get('/list', [MiddlewareGeneratorController::class, 'list'])->name('list');
    Route::delete('/{name}', [MiddlewareGeneratorController::class, 'delete'])->name('delete');
});

// API Routes
Route::prefix('api/middleware-generator')->name('api.middleware-generator.')->group(function () {
    
    // API Endpoints
    Route::post('/generate', [MiddlewareGeneratorController::class, 'apiGenerate'])->name('generate');
    Route::post('/preview', [MiddlewareGeneratorController::class, 'apiPreview'])->name('preview');
    Route::get('/list', [MiddlewareGeneratorController::class, 'apiList'])->name('list');
});
