<?php

use App\Http\Controllers\ManusApiController;
use Illuminate\Support\Facades\Route;

/**
 * مسارات Manus API
 * 
 * جميع المسارات محمية بـ middleware auth
 */

Route::prefix('manus')->middleware(['auth'])->name('manus.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [ManusApiController::class, 'dashboard'])->name('dashboard');
    
    // Transactions
    Route::get('/transactions', [ManusApiController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/{id}', [ManusApiController::class, 'showTransaction'])->name('transactions.show');
    
    // Stats & Reports
    Route::get('/stats', [ManusApiController::class, 'stats'])->name('stats');
    Route::get('/reports', [ManusApiController::class, 'reports'])->name('reports');
    Route::post('/reports/generate', [ManusApiController::class, 'generateReport'])->name('reports.generate');
    
    // API Endpoints
    Route::post('/chat', [ManusApiController::class, 'chat'])->name('chat');
    Route::post('/completion', [ManusApiController::class, 'completion'])->name('completion');
    Route::post('/embedding', [ManusApiController::class, 'embedding'])->name('embedding');
    Route::post('/image', [ManusApiController::class, 'generateImage'])->name('image');
    Route::post('/audio', [ManusApiController::class, 'transcribeAudio'])->name('audio');
    
    // Usage
    Route::get('/usage', [ManusApiController::class, 'usage'])->name('usage');
    Route::get('/balance', [ManusApiController::class, 'balance'])->name('balance');
});

// Webhooks (public - no auth)
Route::post('/api/webhooks/manus', [ManusApiController::class, 'handleWebhook'])->name('manus.webhook');
