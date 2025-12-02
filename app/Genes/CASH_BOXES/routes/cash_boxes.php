<?php

use Illuminate\Support\Facades\Route;
use App\Genes\CASH_BOXES\Controllers\CashBoxController;

/*
|--------------------------------------------------------------------------
| CASH_BOXES Routes
|--------------------------------------------------------------------------
|
| Here is where you can register CASH_BOXES routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "web" middleware group. Enjoy building your CASH_BOXES!
|
*/

Route::prefix('cash-boxes')
    ->middleware(['auth'])
    ->group(function () {
        // Resource Routes for Cash Boxes (index, create, store, edit, update, destroy)
        Route::get('/', [CashBoxController::class, 'index'])->name('cash-boxes.index');
        Route::get('/create', [CashBoxController::class, 'create'])->name('cash-boxes.create');
        Route::post('/', [CashBoxController::class, 'store'])->name('cash-boxes.store');
        Route::get('/{cash_box}/edit', [CashBoxController::class, 'edit'])->name('cash-boxes.edit');
        Route::put('/{cash_box}', [CashBoxController::class, 'update'])->name('cash-boxes.update');
        Route::delete('/{cash_box}', [CashBoxController::class, 'destroy'])->name('cash-boxes.destroy');

        // Custom Routes
        Route::post('/{cash_box}/add-transaction', [CashBoxController::class, 'addTransaction'])->name('cash-boxes.add-transaction');
        Route::get('/{cash_box}/transactions', [CashBoxController::class, 'getTransactions'])->name('cash-boxes.get-transactions');
        Route::get('/reports', [CashBoxController::class, 'reports'])->name('cash-boxes.reports');
    });
