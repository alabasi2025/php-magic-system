<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntermediateAccountController;

/*
|--------------------------------------------------------------------------
| Intermediate Accounts Routes
|--------------------------------------------------------------------------
|
| All routes for the Intermediate Accounts module are defined here.
| They are grouped under the 'intermediate-accounts' prefix and protected by the 'auth' middleware.
|
*/

Route::prefix('intermediate-accounts')->middleware(['auth'])->group(function () {

    // Resource Routes: index, create, store, edit, update, destroy
    // We use Route::resource and specify the actions requested.
    Route::resource('/', IntermediateAccountController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Custom Route: toggleStatus
    Route::post('/{intermediate_account}/toggle-status', [IntermediateAccountController::class, 'toggleStatus'])
        ->name('intermediate-accounts.toggleStatus');

});
