<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetupController;

/*
|--------------------------------------------------------------------------
| Setup Routes
|--------------------------------------------------------------------------
|
| Routes for system setup operations.
|
*/

Route::prefix('setup')->name('setup.')->group(function () {
    
    // Check inventory system status
    Route::get('/inventory/status', [SetupController::class, 'checkInventoryStatus'])
        ->name('inventory.status');
    
    // Run inventory migrations
    Route::get('/inventory/migrations', [SetupController::class, 'runInventoryMigrations'])
        ->name('inventory.migrations');
    
    // Run inventory seeder
    Route::get('/inventory/seeder', [SetupController::class, 'runInventorySeeder'])
        ->name('inventory.seeder');
    
    // Setup complete inventory system
    Route::get('/inventory/setup', [SetupController::class, 'setupInventorySystem'])
        ->name('inventory.setup');
});
