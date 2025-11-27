<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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
    Route::get('/', function () { return view('modules.accounting'); })->name('index');
});

Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', function () { return view('modules.customers'); })->name('index');
});

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', function () { return view('modules.inventory'); })->name('index');
});

Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/', function () { return view('modules.purchases'); })->name('index');
});

Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', function () { return view('modules.sales'); })->name('index');
});

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', function () { return view('modules.projects'); })->name('index');
});

Route::prefix('hr')->name('hr.')->group(function () {
    Route::get('/', function () { return view('modules.hr'); })->name('index');
});

Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
    Route::get('/', function () { return view('modules.manufacturing'); })->name('index');
});

Route::prefix('assets')->name('assets.')->group(function () {
    Route::get('/', function () { return view('modules.assets'); })->name('index');
});

Route::prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', function () { return view('modules.loyalty'); })->name('index');
});

Route::prefix('insurance')->name('insurance.')->group(function () {
    Route::get('/', function () { return view('modules.insurance'); })->name('index');
});

Route::prefix('genes')->name('genes.')->group(function () {
    Route::get('/', function () { return view('modules.genes'); })->name('index');
});
