<?php

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Routes File
 * 
 * 💡 الفكرة:
 * ملف يحتوي على جميع المسارات (Routes) الخاصة بجين الحسابات الوسيطة.
 * 
 * 🎯 المسارات:
 * - Intermediate Accounts Management
 * - Transactions Management
 * - Linking Operations
 * - Reports
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */

use Illuminate\Support\Facades\Route;
use App\Genes\INTERMEDIATE_ACCOUNTS\Controllers\IntermediateAccountController;
use App\Genes\INTERMEDIATE_ACCOUNTS\Controllers\TransactionController;
use App\Genes\INTERMEDIATE_ACCOUNTS\Controllers\ReportController;

// Prefix: /api/genes/intermediate-accounts
Route::prefix('api/genes/intermediate-accounts')->middleware(['auth:api'])->group(function () {
    
    // ========================================
    // Intermediate Accounts Management
    // ========================================
    Route::prefix('accounts')->group(function () {
        // CRUD Operations
        Route::get('/', [IntermediateAccountController::class, 'index']); // List all
        Route::post('/', [IntermediateAccountController::class, 'store']); // Create new
        Route::get('/{id}', [IntermediateAccountController::class, 'show']); // View details
        Route::put('/{id}', [IntermediateAccountController::class, 'update']); // Update
        Route::delete('/{id}', [IntermediateAccountController::class, 'destroy']); // Delete
        
        // Status Management
        Route::post('/{id}/activate', [IntermediateAccountController::class, 'activate']); // Activate
        Route::post('/{id}/deactivate', [IntermediateAccountController::class, 'deactivate']); // Deactivate
        
        // Special Queries
        Route::get('/status/unbalanced', [IntermediateAccountController::class, 'unbalanced']); // Unbalanced accounts
        Route::get('/status/pending', [IntermediateAccountController::class, 'withPendingTransactions']); // With pending transactions
    });
    
    // ========================================
    // Transactions Management
    // ========================================
    Route::prefix('transactions')->group(function () {
        // CRUD Operations
        Route::get('/', [TransactionController::class, 'index']); // List all
        Route::post('/', [TransactionController::class, 'store']); // Create new
        Route::get('/{id}', [TransactionController::class, 'show']); // View details
        Route::post('/{id}/cancel', [TransactionController::class, 'cancel']); // Cancel transaction
        
        // Linking Operations
        Route::post('/link', [TransactionController::class, 'link']); // Link transactions
        Route::delete('/link/{linkId}', [TransactionController::class, 'unlink']); // Unlink transactions
        Route::post('/auto-link', [TransactionController::class, 'autoLink']); // Auto-link transactions
        
        // Queries
        Route::get('/status/unlinked', [TransactionController::class, 'unlinked']); // Unlinked transactions
        Route::get('/balance/summary', [TransactionController::class, 'balanceSummary']); // Balance summary
    });
    
    // ========================================
    // Reports
    // ========================================
    Route::prefix('reports')->group(function () {
        // Report Types
        Route::get('/unlinked-transactions', [ReportController::class, 'unlinkedTransactions']); // Unlinked transactions report
        Route::get('/balance', [ReportController::class, 'balance']); // Balance report
        Route::get('/movement', [ReportController::class, 'movement']); // Movement report
        Route::get('/linking', [ReportController::class, 'linking']); // Linking report
        Route::get('/performance', [ReportController::class, 'performance']); // Performance report
        Route::get('/aging', [ReportController::class, 'aging']); // Aging report
        
        // Export
        Route::post('/export', [ReportController::class, 'export']); // Export report (PDF/Excel)
    });
});

/**
 * 📝 API Documentation
 * 
 * ========================================
 * Intermediate Accounts Management
 * ========================================
 * 
 * GET    /api/genes/intermediate-accounts/accounts
 * POST   /api/genes/intermediate-accounts/accounts
 * GET    /api/genes/intermediate-accounts/accounts/{id}
 * PUT    /api/genes/intermediate-accounts/accounts/{id}
 * DELETE /api/genes/intermediate-accounts/accounts/{id}
 * POST   /api/genes/intermediate-accounts/accounts/{id}/activate
 * POST   /api/genes/intermediate-accounts/accounts/{id}/deactivate
 * GET    /api/genes/intermediate-accounts/accounts/status/unbalanced
 * GET    /api/genes/intermediate-accounts/accounts/status/pending
 * 
 * ========================================
 * Transactions Management
 * ========================================
 * 
 * GET    /api/genes/intermediate-accounts/transactions
 * POST   /api/genes/intermediate-accounts/transactions
 * GET    /api/genes/intermediate-accounts/transactions/{id}
 * POST   /api/genes/intermediate-accounts/transactions/{id}/cancel
 * POST   /api/genes/intermediate-accounts/transactions/link
 * DELETE /api/genes/intermediate-accounts/transactions/link/{linkId}
 * POST   /api/genes/intermediate-accounts/transactions/auto-link
 * GET    /api/genes/intermediate-accounts/transactions/status/unlinked
 * GET    /api/genes/intermediate-accounts/transactions/balance/summary
 * 
 * ========================================
 * Reports
 * ========================================
 * 
 * GET    /api/genes/intermediate-accounts/reports/unlinked-transactions
 * GET    /api/genes/intermediate-accounts/reports/balance
 * GET    /api/genes/intermediate-accounts/reports/movement
 * GET    /api/genes/intermediate-accounts/reports/linking
 * GET    /api/genes/intermediate-accounts/reports/performance
 * GET    /api/genes/intermediate-accounts/reports/aging
 * POST   /api/genes/intermediate-accounts/reports/export
 */
