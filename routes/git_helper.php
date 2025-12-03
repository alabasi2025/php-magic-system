<?php

use App\Http\Controllers\GitHelperController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Git Helper Routes
|--------------------------------------------------------------------------
|
| Routes for Git Helper v3.22.0 - Smart Git Assistant
|
*/

Route::prefix('git-helper')->name('git-helper.')->group(function () {
    
    // Main dashboard
    Route::get('/', [GitHelperController::class, 'index'])->name('index');
    
    // Status
    Route::get('/status', [GitHelperController::class, 'status'])->name('status');
    
    // Diff
    Route::get('/diff', [GitHelperController::class, 'diff'])->name('diff');
    
    // Commit
    Route::post('/commit', [GitHelperController::class, 'commit'])->name('commit');
    
    // Push
    Route::post('/push', [GitHelperController::class, 'push'])->name('push');
    
    // Pull
    Route::post('/pull', [GitHelperController::class, 'pull'])->name('pull');
    
    // Branches
    Route::get('/branches', [GitHelperController::class, 'branches'])->name('branches');
    Route::post('/branches/create', [GitHelperController::class, 'createBranch'])->name('branches.create');
    Route::post('/branches/switch', [GitHelperController::class, 'switchBranch'])->name('branches.switch');
    
    // History
    Route::get('/history', [GitHelperController::class, 'history'])->name('history');
    
    // Operations
    Route::get('/operations', [GitHelperController::class, 'operations'])->name('operations');
    
    // AI Features
    Route::post('/generate-commit-message', [GitHelperController::class, 'generateCommitMessage'])->name('generate-commit-message');
    Route::post('/analyze-changes', [GitHelperController::class, 'analyzeChanges'])->name('analyze-changes');
});
