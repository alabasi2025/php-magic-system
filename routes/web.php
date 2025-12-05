<?php

use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\JournalEntryTemplateController;
use App\Http\Controllers\JournalEntryAttachmentController;
use App\Http\Controllers\JournalEntrySearchController;
use Illuminate\Support\Facades\Route;

// ... مسارات أخرى

// Temporary: Allow access without auth for testing
Route::get('/', function () {
    return redirect('/journal-entries');
});

// Dashboard route (redirects to journal entries)
Route::get('/dashboard', function () {
    return redirect('/journal-entries');
})->name('dashboard');

// ============================================
// Dummy Routes (Temporary - All redirect to journal-entries)
// ============================================
Route::get('/accounting', fn() => redirect('/journal-entries'))->name('accounting.index');
Route::get('/chart-of-accounts', fn() => redirect('/journal-entries'))->name('chart-of-accounts.index');
Route::get('/intermediate-accounts', fn() => redirect('/journal-entries'))->name('intermediate-accounts.index');
Route::get('/cash-boxes', fn() => redirect('/journal-entries'))->name('cash-boxes.index');
Route::get('/customers', fn() => redirect('/journal-entries'))->name('customers.index');
Route::get('/inventory', fn() => redirect('/journal-entries'))->name('inventory.index');
Route::get('/purchases', fn() => redirect('/journal-entries'))->name('purchases.index');
Route::get('/sales', fn() => redirect('/journal-entries'))->name('sales.index');
Route::get('/hr', fn() => redirect('/journal-entries'))->name('hr.index');
Route::get('/manufacturing', fn() => redirect('/journal-entries'))->name('manufacturing.index');
Route::get('/assets', fn() => redirect('/journal-entries'))->name('assets.index');
Route::get('/loyalty', fn() => redirect('/journal-entries'))->name('loyalty.index');
Route::get('/insurance', fn() => redirect('/journal-entries'))->name('insurance.index');
Route::get('/genes', fn() => redirect('/journal-entries'))->name('genes.index');
Route::get('/partnership', fn() => redirect('/journal-entries'))->name('partnership.index');
Route::get('/holdings', fn() => redirect('/journal-entries'))->name('holdings.index');
Route::get('/units', fn() => redirect('/journal-entries'))->name('units.index');
Route::get('/departments', fn() => redirect('/journal-entries'))->name('departments.index');
Route::get('/organization/projects', fn() => redirect('/journal-entries'))->name('organization.projects.index');
Route::get('/developer', fn() => redirect('/journal-entries'))->name('developer.index');
Route::get('/developer/ai/code-generator', fn() => redirect('/journal-entries'))->name('developer.ai.code-generator');
Route::get('/developer/ai/code-refactor', fn() => redirect('/journal-entries'))->name('developer.ai.code-refactor');
Route::get('/developer/ai/code-review', fn() => redirect('/journal-entries'))->name('developer.ai.code-review');
Route::get('/developer/ai/bug-detector', fn() => redirect('/journal-entries'))->name('developer.ai.bug-detector');
Route::get('/developer/ai/documentation-generator', fn() => redirect('/journal-entries'))->name('developer.ai.documentation-generator');
Route::get('/developer/ai/test-generator', fn() => redirect('/journal-entries'))->name('developer.ai.test-generator');
Route::get('/developer/ai/performance-analyzer', fn() => redirect('/journal-entries'))->name('developer.ai.performance-analyzer');
Route::get('/developer/ai/security-scanner', fn() => redirect('/journal-entries'))->name('developer.ai.security-scanner');
Route::get('/developer/ai/api-generator', fn() => redirect('/journal-entries'))->name('developer.ai.api-generator');
Route::get('/developer/ai/database-optimizer', fn() => redirect('/journal-entries'))->name('developer.ai.database-optimizer');
Route::get('/developer/ai/code-translator', fn() => redirect('/journal-entries'))->name('developer.ai.code-translator');
Route::get('/developer/ai/assistant', fn() => redirect('/journal-entries'))->name('developer.ai.assistant');
Route::get('/developer/ai/settings', fn() => redirect('/journal-entries'))->name('developer.ai.settings');
Route::get('/developer/migrations', fn() => redirect('/journal-entries'))->name('developer.migrations');
Route::get('/developer/seeders', fn() => redirect('/journal-entries'))->name('developer.seeders');
Route::get('/developer/database-info', fn() => redirect('/journal-entries'))->name('developer.database-info');
Route::get('/developer/database-optimize', fn() => redirect('/journal-entries'))->name('developer.database-optimize');
Route::get('/developer/database-backup', fn() => redirect('/journal-entries'))->name('developer.database-backup');
Route::get('/developer/cache', fn() => redirect('/journal-entries'))->name('developer.cache');
Route::get('/developer/routes-list', fn() => redirect('/journal-entries'))->name('developer.routes-list');
Route::get('/developer/pint', fn() => redirect('/journal-entries'))->name('developer.pint');
Route::get('/developer/tests', fn() => redirect('/journal-entries'))->name('developer.tests');
Route::get('/developer/debugbar', fn() => redirect('/journal-entries'))->name('developer.debugbar');
Route::get('/developer/server-info', fn() => redirect('/journal-entries'))->name('developer.server-info');
Route::get('/developer/logs-viewer', fn() => redirect('/journal-entries'))->name('developer.logs-viewer');
Route::get('/developer/git/dashboard', fn() => redirect('/journal-entries'))->name('developer.git.dashboard');
Route::get('/developer/git/commit', fn() => redirect('/journal-entries'))->name('developer.git.commit');
Route::get('/developer/git/history', fn() => redirect('/journal-entries'))->name('developer.git.history');

// Temporary: Removed auth middleware for testing
// Route::middleware(['auth'])->group(function () {
// if (true) {
    
    // ============================================
    // مسارات القيود اليومية (Journal Entries)
    // ============================================
    Route::prefix('journal-entries')->name('journal-entries.')->controller(JournalEntryController::class)->group(function () {
        // القائمة الرئيسية
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{journal_entry}', 'show')->name('show');
        Route::get('/{journal_entry}/edit', 'edit')->name('edit');
        Route::put('/{journal_entry}', 'update')->name('update');
        Route::delete('/{journal_entry}', 'destroy')->name('destroy');
        
        // التصدير والاستيراد
        Route::get('/export', 'export')->name('export');
        Route::get('/import', 'import')->name('import');
        Route::post('/import', 'processImport')->name('process_import');
        Route::get('/template', 'downloadTemplate')->name('download_template');
        
        // القيد العكسي
        Route::post('/{journal_entry}/reverse', 'reverse')->name('reverse');
        
        // التحليل
        Route::get('/analytics', 'analytics')->name('analytics');
        Route::get('/analytics/data', 'getAnalyticsData')->name('analytics.data');
        
        // السجل التاريخي
        Route::get('/{journal_entry}/audit', 'auditTrail')->name('audit');
    });
    
    // ============================================
    // مسارات قوالب القيود (Templates)
    // ============================================
    Route::resource('journal-entry-templates', JournalEntryTemplateController::class)->names([
        'index' => 'templates.index',
        'create' => 'templates.create',
        'store' => 'templates.store',
        'show' => 'templates.show',
        'edit' => 'templates.edit',
        'update' => 'templates.update',
        'destroy' => 'templates.destroy',
    ]);
    
    // تطبيق قالب
    Route::post('/journal-entry-templates/{template}/apply', [JournalEntryTemplateController::class, 'apply'])
        ->name('templates.apply');
    
    // ============================================
    // مسارات المرفقات (Attachments)
    // ============================================
    Route::resource('journal-entry-attachments', JournalEntryAttachmentController::class)->names([
        'index' => 'attachments.index',
        'create' => 'attachments.create',
        'store' => 'attachments.store',
        'show' => 'attachments.show',
        'edit' => 'attachments.edit',
        'update' => 'attachments.update',
        'destroy' => 'attachments.destroy',
    ]);
    
    // تحميل مرفق
    Route::get('/journal-entry-attachments/{attachment}/download', [JournalEntryAttachmentController::class, 'download'])
        ->name('attachments.download');
    
    // ============================================
    // مسارات البحث (Search)
    // ============================================
    Route::prefix('journal-entries/search')->name('search.')->controller(JournalEntrySearchController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'search')->name('search');
        Route::post('/save', 'saveSearch')->name('save');
        Route::get('/saved', 'savedSearches')->name('saved');
        Route::post('/apply/{id}', 'applySearch')->name('apply');
        Route::delete('/saved/{id}', 'deleteSavedSearch')->name('delete');
    });
    
// });
