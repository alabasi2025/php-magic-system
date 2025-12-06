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
Route::get('/accounting', [\App\Http\Controllers\AccountingController::class, 'index'])->name('accounting.index');
Route::resource('chart-of-accounts', \App\Http\Controllers\ChartOfAccountsController::class);
Route::resource('intermediate-accounts', \App\Http\Controllers\IntermediateAccountController::class);
Route::resource('cash-boxes', \App\Http\Controllers\CashBoxController::class);
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
Route::get('/developer/server-info', [\App\Http\Controllers\DeveloperController::class, 'getServerInfoPage'])->name('developer.server-info');
Route::get('/developer/logs-viewer', [\App\Http\Controllers\DeveloperController::class, 'getLogsViewerPage'])->name('developer.logs-viewer');
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

// قوالب القيود اليومية الذكية
Route::resource('journal-templates', \App\Http\Controllers\JournalTemplateController::class);
Route::get('journal-templates/{journalTemplate}/use', [\App\Http\Controllers\JournalTemplateController::class, 'use'])->name('journal-templates.use');
Route::patch('journal-templates/{journalTemplate}/toggle', [\App\Http\Controllers\JournalTemplateController::class, 'toggle'])->name('journal-templates.toggle');
Route::post('journal-templates/{journalTemplate}/duplicate', [\App\Http\Controllers\JournalTemplateController::class, 'duplicate'])->name('journal-templates.duplicate');


// إعدادات الترقيم التلقائي
Route::get('settings/auto-numbering', [\App\Http\Controllers\AutoNumberingSettingController::class, 'index'])->name('auto-numbering.index');
Route::post('settings/auto-numbering', [\App\Http\Controllers\AutoNumberingSettingController::class, 'store'])->name('auto-numbering.store');


// التحقق الذكي من القيود
Route::get('journal-entries/{entry}/validate', [\App\Http\Controllers\JournalValidationController::class, 'validate'])->name('journal-entries.validate');

// ============================================
// البنوك والصناديق والسندات
// ============================================

// الحسابات البنكية
Route::resource('bank-accounts', \App\Http\Controllers\BankAccountController::class);

// سندات القبض
Route::resource('cash-receipts', \App\Http\Controllers\CashReceiptController::class);
Route::patch('cash-receipts/{cashReceipt}/approve', [\App\Http\Controllers\CashReceiptController::class, 'approve'])->name('cash-receipts.approve');
Route::patch('cash-receipts/{cashReceipt}/post', [\App\Http\Controllers\CashReceiptController::class, 'post'])->name('cash-receipts.post');
Route::patch('cash-receipts/{cashReceipt}/cancel', [\App\Http\Controllers\CashReceiptController::class, 'cancel'])->name('cash-receipts.cancel');

// سندات الصرف
Route::resource('cash-payments', \App\Http\Controllers\CashPaymentController::class);
Route::patch('cash-payments/{cashPayment}/approve', [\App\Http\Controllers\CashPaymentController::class, 'approve'])->name('cash-payments.approve');
Route::patch('cash-payments/{cashPayment}/post', [\App\Http\Controllers\CashPaymentController::class, 'post'])->name('cash-payments.post');
Route::patch('cash-payments/{cashPayment}/cancel', [\App\Http\Controllers\CashPaymentController::class, 'cancel'])->name('cash-payments.cancel');

// الصناديق النقدية
Route::resource('cash-boxes', \App\Http\Controllers\CashBoxController::class);

// ============================================
// الهيكل التنظيمي
// ============================================

// الوحدات التنظيمية
Route::resource('units', \App\Http\Controllers\OrganizationUnitController::class);

// الأقسام
Route::resource('departments', \App\Http\Controllers\DepartmentController::class);

// الشركات القابضة
Route::resource('holdings', \App\Http\Controllers\HoldingController::class);

// المشاريع
Route::resource('projects', \App\Http\Controllers\ProjectController::class);

// ============================================
// أدوات التطوير الإضافية
// ============================================

// Migrations
Route::get('/developer/migrations', [\App\Http\Controllers\DeveloperController::class, 'getMigrationsPage'])->name('developer.migrations');

// Seeders
Route::get('/developer/seeders', [\App\Http\Controllers\DeveloperController::class, 'getSeedersPage'])->name('developer.seeders');

// Database Info
Route::get('/developer/database-info', [\App\Http\Controllers\DeveloperController::class, 'getDatabaseInfoPage'])->name('developer.database-info');

// Database Optimize
Route::get('/developer/database-optimize', [\App\Http\Controllers\DeveloperController::class, 'getDatabaseOptimizePage'])->name('developer.database-optimize');

// Database Backup
Route::get('/developer/database-backup', [\App\Http\Controllers\DeveloperController::class, 'getDatabaseBackupPage'])->name('developer.database-backup');

// Cache
Route::get('/developer/cache', [\App\Http\Controllers\DeveloperController::class, 'getCachePage'])->name('developer.cache');

// Routes List
Route::get('/developer/routes-list', [\App\Http\Controllers\DeveloperController::class, 'getRoutesListPage'])->name('developer.routes-list');

// Pint
Route::get('/developer/pint', [\App\Http\Controllers\DeveloperController::class, 'getPintPage'])->name('developer.pint');

// Tests
Route::get('/developer/tests', [\App\Http\Controllers\DeveloperController::class, 'getTestsPage'])->name('developer.tests');

// Debugbar
Route::get('/developer/debugbar', [\App\Http\Controllers\DeveloperController::class, 'getDebugbarPage'])->name('developer.debugbar');

// System Info
Route::get('/developer/system-info', [\App\Http\Controllers\DeveloperController::class, 'getSystemInfoPage'])->name('developer.system-info');

