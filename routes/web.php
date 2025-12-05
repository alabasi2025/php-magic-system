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
