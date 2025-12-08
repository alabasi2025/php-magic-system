<?php

use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\JournalEntryTemplateController;
use App\Http\Controllers\JournalEntryAttachmentController;
use App\Http\Controllers\JournalEntrySearchController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// System Setup Routes (temporary - remove in production)
// Note: These routes bypass CSRF protection for easier setup
Route::get('/system-setup', [\App\Http\Controllers\SystemSetupController::class, 'index'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/system-setup/run-migrations', [\App\Http\Controllers\SystemSetupController::class, 'runMigrations'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/system-setup/run-seeders', [\App\Http\Controllers\SystemSetupController::class, 'runSeeders'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/system-setup/clear-cache', [\App\Http\Controllers\SystemSetupController::class, 'clearCache'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/system-diagnostic', [\App\Http\Controllers\SystemSetupController::class, 'diagnostic']);

// Test warehouse controller (temporary)
Route::get('/test-warehouses', function () {
    try {
        $warehouses = \App\Models\Warehouse::with('manager')->latest()->paginate(15);
        return response()->json([
            'success' => true,
            'count' => $warehouses->total(),
            'data' => $warehouses->items()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Simple GET route to run warehouse account type seeder (temporary)
Route::get('/run-warehouse-seeder', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\WarehouseAccountTypeSeeder',
            '--force' => true
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response()->json([
            'success' => true,
            'message' => 'Warehouse account type seeder executed successfully',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error running seeder',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Simple GET route to run migrations (temporary)
Route::get('/run-migrations-now', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response()->json([
            'success' => true,
            'message' => 'Migrations executed successfully',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error running migrations',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Old diagnostic route
Route::get('/system-diagnostic-old', function () {
    $diagnostics = [];
    
    // Check database connection
    try {
        DB::connection()->getPdo();
        $diagnostics['database_connection'] = '✅ Connected';
    } catch (\Exception $e) {
        $diagnostics['database_connection'] = '❌ Failed: ' . $e->getMessage();
    }
    
    // Check tables
    $tables = ['warehouses', 'items', 'item_units', 'stock_movements', 'users'];
    foreach ($tables as $table) {
        $diagnostics['table_' . $table] = Schema::hasTable($table) ? '✅ Exists' : '❌ Missing';
    }
    
    // Check migrations
    try {
        $migrations = DB::table('migrations')->pluck('migration')->toArray();
        $diagnostics['migrations_count'] = count($migrations);
        $inventoryMigrations = array_filter($migrations, function($m) {
            return str_contains($m, 'warehouse') || str_contains($m, 'item') || str_contains($m, 'stock');
        });
        $diagnostics['inventory_migrations'] = array_values($inventoryMigrations);
    } catch (\Exception $e) {
        $diagnostics['migrations'] = '❌ Error: ' . $e->getMessage();
    }
    
    // Try to get warehouse count
    try {
        $warehouseCount = DB::table('warehouses')->count();
        $diagnostics['warehouses_count'] = $warehouseCount;
    } catch (\Exception $e) {
        $diagnostics['warehouses_query'] = '❌ Error: ' . $e->getMessage();
    }
    
    // Laravel info
    $diagnostics['laravel_version'] = app()->version();
    $diagnostics['php_version'] = phpversion();
    $diagnostics['environment'] = app()->environment();
    
    return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

// ... مسارات أخرى

// Temporary: Allow access without auth for testing
Route::get('/', function () {
    return view('home');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('home');
})->name('dashboard');

// ============================================
// Dummy Routes (Temporary - All redirect to journal-entries)
// ============================================
Route::get('/accounting', [\App\Http\Controllers\AccountingController::class, 'index'])->name('accounting.index');
Route::resource('chart-of-accounts', \App\Http\Controllers\ChartOfAccountsController::class);
Route::post('chart-of-accounts/add-account', [\App\Http\Controllers\ChartOfAccountsController::class, 'addAccount'])->name('chart-of-accounts.add-account');
Route::get('chart-of-accounts/get-account/{id}', [\App\Http\Controllers\ChartOfAccountsController::class, 'getAccount'])->name('chart-of-accounts.get-account');
Route::put('chart-of-accounts/update-account/{id}', [\App\Http\Controllers\ChartOfAccountsController::class, 'updateAccount'])->name('chart-of-accounts.update-account');
Route::delete('chart-of-accounts/delete-account/{id}', [\App\Http\Controllers\ChartOfAccountsController::class, 'deleteAccount'])->name('chart-of-accounts.delete-account');
Route::resource('chart-types', \App\Http\Controllers\ChartTypeController::class);
Route::resource('intermediate-accounts', \App\Http\Controllers\IntermediateAccountController::class);
Route::resource('cash-boxes', \App\Http\Controllers\CashBoxController::class);
Route::get('/customers', fn() => redirect('/journal-entries'))->name('customers.index');
// Inventory System Routes (v4.1.0)
require __DIR__.'/inventory.php';
// Purchases System Routes (v4.1.0)
require __DIR__.'/purchases.php';
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


// Inventory System Routes
require __DIR__.'/inventory.php';

// Financial Settings Routes
Route::prefix('financial-settings')->name('financial-settings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\FinancialSettingsController::class, 'index'])->name('index');
    
    // Account Types
    Route::post('/account-types', [\App\Http\Controllers\FinancialSettingsController::class, 'storeAccountType'])->name('account-types.store');
    Route::put('/account-types/{id}', [\App\Http\Controllers\FinancialSettingsController::class, 'updateAccountType'])->name('account-types.update');
    Route::delete('/account-types/{id}', [\App\Http\Controllers\FinancialSettingsController::class, 'deleteAccountType'])->name('account-types.delete');
    
    // Account Groups
    Route::get('/account-groups', [\App\Http\Controllers\FinancialSettingsController::class, 'getAccountGroups'])->name('account-groups.index');
    Route::get('/account-groups/{id}', [\App\Http\Controllers\FinancialSettingsController::class, 'getAccountGroup'])->name('account-groups.show');
    Route::post('/account-groups', [\App\Http\Controllers\FinancialSettingsController::class, 'storeAccountGroup'])->name('account-groups.store');
    Route::put('/account-groups/{id}', [\App\Http\Controllers\FinancialSettingsController::class, 'updateAccountGroup'])->name('account-groups.update');
    Route::delete('/account-groups/{id}', [\App\Http\Controllers\FinancialSettingsController::class, 'deleteAccountGroup'])->name('account-groups.delete');
});


// Temporary route to clear all cache (REMOVE AFTER USE!)
Route::get('/clear-all-cache-now', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'تم مسح جميع أنواع الـ Cache بنجاح!',
            'cleared' => [
                'view_cache' => 'cleared',
                'application_cache' => 'cleared',
                'config_cache' => 'cleared',
                'route_cache' => 'cleared',
                'optimization_cache' => 'cleared'
            ],
            'next_step' => 'يمكنك الآن تحديث صفحة الدليل المحاسبي وإضافة حساب جديد - حقل مجموعة الحسابات سيظهر!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Temporary route to add employees account group
Route::get('/add-employees-group-now', function() {
    try {
        $group = \App\Models\AccountGroup::create([
            'code' => 'EMPLOYEES',
            'name' => 'حسابات الموظفين',
            'description' => 'حسابات خاصة بالرواتب والأجور والمستحقات والسلف والخصومات',
            'sort_order' => 5,
            'is_active' => 1
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة مجموعة حسابات الموظفين بنجاح!',
            'data' => $group
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth');

// Temporary route to add account types directly
Route::get('/add-account-types-now', function() {
    try {
        $accountTypes = [
            [
                'key' => 'general',
                'name_ar' => 'عام',
                'name_en' => 'General',
                'icon' => 'fas fa-folder',
                'description' => 'حسابات عامة',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 0,
            ],
            [
                'key' => 'customer',
                'name_ar' => 'عميل',
                'name_en' => 'Customer',
                'icon' => 'fas fa-user-tie',
                'description' => 'حسابات العملاء',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'supplier',
                'name_ar' => 'مورد',
                'name_en' => 'Supplier',
                'icon' => 'fas fa-truck',
                'description' => 'حسابات الموردين',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'employee',
                'name_ar' => 'موظف',
                'name_en' => 'Employee',
                'icon' => 'fas fa-user',
                'description' => 'حسابات الموظفين',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'bank',
                'name_ar' => 'بنك',
                'name_en' => 'Bank',
                'icon' => 'fas fa-university',
                'description' => 'الحسابات البنكية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'cash',
                'name_ar' => 'صندوق نقدي',
                'name_en' => 'Cash',
                'icon' => 'fas fa-cash-register',
                'description' => 'الصناديق النقدية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'asset',
                'name_ar' => 'أصل',
                'name_en' => 'Asset',
                'icon' => 'fas fa-building',
                'description' => 'الأصول الثابتة',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'expense',
                'name_ar' => 'مصروف',
                'name_en' => 'Expense',
                'icon' => 'fas fa-money-bill-wave',
                'description' => 'حسابات المصروفات',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 7,
            ],
            [
                'key' => 'revenue',
                'name_ar' => 'إيراد',
                'name_en' => 'Revenue',
                'icon' => 'fas fa-chart-line',
                'description' => 'حسابات الإيرادات',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 8,
            ],
            [
                'key' => 'tax',
                'name_ar' => 'ضريبة',
                'name_en' => 'Tax',
                'icon' => 'fas fa-percentage',
                'description' => 'حسابات الضرائب',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 9,
            ],
            [
                'key' => 'partner',
                'name_ar' => 'شريك',
                'name_en' => 'Partner',
                'icon' => 'fas fa-handshake',
                'description' => 'حسابات الشركاء',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 10,
            ],
        ];

        $added = [];
        foreach ($accountTypes as $type) {
            // إضافة حقول للتوافق مع الجدول القديم
            $type['name'] = $type['name_ar'];
            $type['code'] = strtoupper($type['key']);
            
            $existing = \App\Models\AccountType::where('key', $type['key'])->first();
            if (!$existing) {
                $created = \App\Models\AccountType::create($type);
                $added[] = $created;
            }
        }
        
        $count = \App\Models\AccountType::count();
        
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة أنواع الحسابات بنجاح!',
            'added_count' => count($added),
            'total_count' => $count,
            'next_step' => 'افتح صفحة الإعدادات المالية: /financial-settings'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Temporary route to add three sub accounts
Route::get('/add-three-accounts-now', function() {
    try {
        $accounts = [];
        
        // 1. صناديق التحصيل والتوريد (نوع: صندوق نقدي)
        $account1 = \App\Models\ChartAccount::create([
            'chart_group_id' => 8,
            'parent_id' => null,
            'level' => 1,
            'code' => '1110',
            'name' => 'صناديق التحصيل والتوريد',
            'name_en' => 'Collection and Supply Cash Boxes',
            'is_parent' => false,
            'account_type' => 'cash',
            'account_group_id' => null,
            'description' => 'حساب لإدارة صناديق التحصيل والتوريد',
            'is_active' => true,
            'created_by' => 1,
        ]);
        $accounts[] = $account1;
        
        // 2. بنك الكريمي (نوع: بنك)
        $account2 = \App\Models\ChartAccount::create([
            'chart_group_id' => 8,
            'parent_id' => null,
            'level' => 1,
            'code' => '1120',
            'name' => 'بنك الكريمي',
            'name_en' => 'Al-Kuraimi Bank',
            'is_parent' => false,
            'account_type' => 'bank',
            'account_group_id' => null,
            'description' => 'حساب بنك الكريمي',
            'is_active' => true,
            'created_by' => 1,
        ]);
        $accounts[] = $account2;
        
        // 3. موردين الديزل (نوع: مورد)
        $account3 = \App\Models\ChartAccount::create([
            'chart_group_id' => 8,
            'parent_id' => null,
            'level' => 1,
            'code' => '2110',
            'name' => 'موردين الديزل',
            'name_en' => 'Diesel Suppliers',
            'is_parent' => false,
            'account_type' => 'supplier',
            'account_group_id' => null,
            'description' => 'حساب موردين الديزل',
            'is_active' => true,
            'created_by' => 1,
        ]);
        $accounts[] = $account3;
        
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الحسابات الثلاثة بنجاح!',
            'accounts' => $accounts,
            'next_step' => 'افتح الدليل المحاسبي: /chart-of-accounts/8'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Clear cache and refresh version
Route::get('/clear-cache-version', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return response()->json([
        'success' => true,
        'version' => config('version.version'),
        'number' => config('version.number')
    ]);
});

// Test update account
Route::get('/test-update-account-16', function() {
    $account = \App\Models\ChartAccount::find(16);
    if (!$account) {
        return response()->json(['error' => 'Account not found']);
    }
    
    $before = [
        'account_group_id' => $account->account_group_id,
        'is_parent' => $account->is_parent
    ];
    
    $account->update([
        'is_parent' => false,
        'account_group_id' => 6
    ]);
    
    $after = [
        'account_group_id' => $account->account_group_id,
        'is_parent' => $account->is_parent
    ];
    
    return response()->json([
        'success' => true,
        'before' => $before,
        'after' => $after,
        'account' => $account
    ]);
});


// Test routes for account groups
Route::get('/test-add-account-group', function() {
    $group = \App\Models\AccountGroup::create([
        'name' => 'مجموعة اختبار ' . time(),
        'code' => 'TEST' . time(),
        'description' => 'مجموعة للاختبار',
        'sort_order' => 999,
        'is_active' => true
    ]);
    return response()->json(['success' => true, 'group' => $group]);
});

Route::get('/test-update-account-group/{id}', function($id) {
    $group = \App\Models\AccountGroup::find($id);
    $before = ['name' => $group->name, 'code' => $group->code];
    $group->update(['name' => 'مجموعة معدلة', 'code' => 'UPDATED']);
    $after = ['name' => $group->name, 'code' => $group->code];
    return response()->json(['success' => true, 'before' => $before, 'after' => $after]);
});

Route::get('/test-delete-account-group/{id}', function($id) {
    $group = \App\Models\AccountGroup::find($id);
    $group->delete();
    return response()->json(['success' => true, 'message' => 'تم الحذف']);
});
