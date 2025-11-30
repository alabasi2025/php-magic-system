<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\GeneController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Main Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/api/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

// Module Routes
Route::prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/', [AccountingController::class, 'index'])->name('index');
});

Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
});

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
});

Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('index');
});

Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('index');
});

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
});

Route::prefix('hr')->name('hr.')->group(function () {
    Route::get('/', [HrController::class, 'index'])->name('index');
});

Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
    Route::get('/', [ManufacturingController::class, 'index'])->name('index');
});

Route::prefix('assets')->name('assets.')->group(function () {
    Route::get('/', [AssetController::class, 'index'])->name('index');
});

Route::prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', [LoyaltyController::class, 'index'])->name('index');
});

Route::prefix('insurance')->name('insurance.')->group(function () {
    Route::get('/', [InsuranceController::class, 'index'])->name('index');
});

Route::prefix('genes')->name('genes.')->group(function () {
    Route::get('/', [GeneController::class, 'index'])->name('index');
});

// مسارات نظام المطور - v2.6.0
Route::prefix('developer')->name('developer.')->group(function () {
    Route::get('/', [App\Http\Controllers\DeveloperController::class, 'index'])->name('index');
    Route::post('/migrations/run', [App\Http\Controllers\DeveloperController::class, 'runMigrations'])->name('migrations.run');
    Route::post('/seeders/run', [App\Http\Controllers\DeveloperController::class, 'runSeeders'])->name('seeders.run');
    Route::get('/database/info', [App\Http\Controllers\DeveloperController::class, 'databaseInfo'])->name('database.info');
    Route::post('/database/optimize', [App\Http\Controllers\DeveloperController::class, 'optimizeDatabase'])->name('database.optimize');
    Route::post('/database/backup', [App\Http\Controllers\DeveloperController::class, 'backupDatabase'])->name('database.backup');
    Route::post('/cache/clear', [App\Http\Controllers\DeveloperController::class, 'clearCache'])->name('cache.clear');
    Route::post('/pint/run', [App\Http\Controllers\DeveloperController::class, 'runPint'])->name('pint.run');
    Route::post('/tests/run', [App\Http\Controllers\DeveloperController::class, 'runTests'])->name('tests.run');
    Route::get('/routes', [App\Http\Controllers\DeveloperController::class, 'showRoutes'])->name('routes');
    Route::get('/system-info', [App\Http\Controllers\DeveloperController::class, 'systemInfo'])->name('system.info');
    Route::get('/logs', [App\Http\Controllers\DeveloperController::class, 'showLogs'])->name('logs');
});

// Developer Routes
require __DIR__.'/developer.php';

// Manus API Routes
require __DIR__.'/manus.php';

// Gene: PARTNERSHIP_ACCOUNTING Routes
require __DIR__.'/../app/Genes/PARTNERSHIP_ACCOUNTING/routes.php';

// Partnership Accounting Web Routes
Route::prefix('partnership')->name('partnership.')->group(function () {
    Route::get('/', [App\Http\Controllers\PartnershipController::class, 'index'])->name('index');
    Route::get('/partners', [App\Http\Controllers\PartnershipController::class, 'partners'])->name('partners.index');
    Route::get('/revenues', [App\Http\Controllers\PartnershipController::class, 'revenues'])->name('revenues.index');
    Route::get('/expenses', [App\Http\Controllers\PartnershipController::class, 'expenses'])->name('expenses.index');
    Route::get('/profits', [App\Http\Controllers\PartnershipController::class, 'profits'])->name('profits.index');
    Route::get('/reports', [App\Http\Controllers\PartnershipController::class, 'reports'])->name('reports.index');
    Route::get('/settings', [App\Http\Controllers\PartnershipController::class, 'settings'])->name('settings');
});
