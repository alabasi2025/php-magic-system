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
use App\Http\Controllers\ChartOfAccountsController;
use App\Http\Controllers\MasterChartController;
use App\Http\Controllers\CashBoxController;
use App\Http\Controllers\IntermediateAccountController;
use App\Http\Controllers\JournalEntryController;

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

// Chart of Accounts Routes
Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
    Route::get('/', [ChartOfAccountsController::class, 'index'])->name('index');
    Route::get('/create', [ChartOfAccountsController::class, 'create'])->name('create');
    Route::post('/', [ChartOfAccountsController::class, 'store'])->name('store');
    Route::get('/{id}', [ChartOfAccountsController::class, 'show'])->name('show');
    Route::post('/add-account', [ChartOfAccountsController::class, 'addAccount'])->name('add-account');
    Route::put('/update-account/{id}', [ChartOfAccountsController::class, 'updateAccount'])->name('update-account');
    Route::delete('/delete-account/{id}', [ChartOfAccountsController::class, 'deleteAccount'])->name('delete-account');
});

// Master Chart Routes
Route::prefix('master-chart')->name('master-chart.')->group(function () {
    Route::get('/unit/{unitId}', [MasterChartController::class, 'show'])->name('show');
    Route::get('/unit/{unitId}/intermediate', [MasterChartController::class, 'showIntermediateMaster'])->name('intermediate');
    Route::post('/unit/{unitId}/initialize', [MasterChartController::class, 'initialize'])->name('initialize');
});

// Cash Boxes Routes
Route::prefix('cash-boxes')->name('cash-boxes.')->group(function () {
    Route::get('/', [CashBoxController::class, 'index'])->name('index');
    Route::get('/create', [CashBoxController::class, 'create'])->name('create');
    Route::post('/', [CashBoxController::class, 'store'])->name('store');
    Route::get('/{id}', [CashBoxController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [CashBoxController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CashBoxController::class, 'update'])->name('update');
    Route::delete('/{id}', [CashBoxController::class, 'destroy'])->name('destroy');
    Route::post('/get-intermediate-accounts', [CashBoxController::class, 'getIntermediateAccounts'])->name('get-intermediate-accounts');
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

// Telescope Routes (Only in development)
if (config('app.env') !== 'production' || config('app.debug')) {
    Route::middleware('auth')->group(function () {
        Route::telescope();
    });
}

// مسارات نظام المطور - v2.6.0
Route::prefix('developer')->name('developer.')->group(function () {
    Route::get('/', [App\Http\Controllers\DeveloperController::class, 'index'])->name('index');
    Route::post('/migrations/run', [App\Http\Controllers\DeveloperController::class, 'runMigrations'])->name('migrations.run');
    Route::post('/seeders/run', [App\Http\Controllers\DeveloperController::class, 'runSeeders'])->name('seeders.run');
    Route::post('/seeders/run-all', [App\Http\Controllers\DeveloperController::class, 'runAllSeeders'])->name('seeders.run-all');
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

// Gene: ORGANIZATIONAL_STRUCTURE Routes
require __DIR__.'/../app/Genes/ORGANIZATIONAL_STRUCTURE/routes.php';

// Gene: CASH_BOXES Routes
require __DIR__.'/../app/Genes/CASH_BOXES/routes.php';

// Login Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Clients Routes
Route::get('/clients', [CustomerController::class, 'index'])->name('clients.index');

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

// Journal Entries Routes (Accounting Vouchers)
Route::prefix('journal-entries')->name('journal-entries.')->group(function () {
    Route::get('/', [JournalEntryController::class, 'index'])->name('index');
    Route::get('/create', [JournalEntryController::class, 'create'])->name('create');
    Route::post('/', [JournalEntryController::class, 'store'])->name('store');
    Route::get('/{id}', [JournalEntryController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [JournalEntryController::class, 'edit'])->name('edit');
    Route::put('/{id}', [JournalEntryController::class, 'update'])->name('update');
    Route::delete('/{id}', [JournalEntryController::class, 'destroy'])->name('destroy');
});

// Intermediate Accounts Routes
Route::prefix('intermediate-accounts')->name('intermediate-accounts.')->group(function () {
    Route::get('/', [IntermediateAccountController::class, 'index'])->name('index');
    Route::get('/create', [IntermediateAccountController::class, 'create'])->name('create');
    Route::post('/', [IntermediateAccountController::class, 'store'])->name('store');
    Route::get('/{id}', [IntermediateAccountController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [IntermediateAccountController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IntermediateAccountController::class, 'update'])->name('update');
    Route::delete('/{id}', [IntermediateAccountController::class, 'destroy'])->name('destroy');
    
    // AJAX Routes
    Route::get('/api/by-chart-group/{chartGroupId}', [IntermediateAccountController::class, 'getByChartGroup'])->name('api.by-chart-group');
    Route::get('/api/by-unit/{unitId}', [IntermediateAccountController::class, 'getByUnit'])->name('api.by-unit');
});

// Organization Structure Routes are now loaded from ORGANIZATIONAL_STRUCTURE Gene
// See: app/Genes/ORGANIZATIONAL_STRUCTURE/routes.php



// AI Tools Routes
Route::prefix('ai-tools')->name('ai-tools.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AIToolsController::class, 'dashboard'])->name('dashboard');
    Route::get('/code-assistant', [App\Http\Controllers\AIToolsController::class, 'codeAssistant'])->name('code-assistant');
    Route::get('/design-to-code', [App\Http\Controllers\AIToolsController::class, 'designToCode'])->name('design-to-code');
    Route::get('/nlp-code-generator', [App\Http\Controllers\AIToolsController::class, 'nlpCodeGenerator'])->name('nlp-code-generator');
    Route::get('/performance-analyzer', [App\Http\Controllers\AIToolsController::class, 'performanceAnalyzer'])->name('performance-analyzer');
    Route::get('/security-scanner', [App\Http\Controllers\SecurityScannerController::class, 'index'])->name('security-scanner');
    Route::post('/security-scanner/scan', [App\Http\Controllers\SecurityScannerController::class, 'scan'])->name('security-scanner.scan');
    Route::post('/security-scanner/scan-file', [App\Http\Controllers\SecurityScannerController::class, 'scanFile'])->name('security-scanner.scan-file');
    Route::post('/security-scanner/scan-directory', [App\Http\Controllers\SecurityScannerController::class, 'scanDirectory'])->name('security-scanner.scan-directory');
    Route::get('/security-scanner/recommendations', [App\Http\Controllers\SecurityScannerController::class, 'recommendations'])->name('security-scanner.recommendations');
    Route::post('/security-scanner/export', [App\Http\Controllers\SecurityScannerController::class, 'exportReport'])->name('security-scanner.export');
    Route::get('/code-refactoring', [App\Http\Controllers\AIToolsController::class, 'codeRefactoring'])->name('code-refactoring');
    Route::get('/code-review-assistant', [App\Http\Controllers\AIToolsController::class, 'codeReviewAssistant'])->name('code-review-assistant');
    Route::get('/interactive-doc-generator', [App\Http\Controllers\AIToolsController::class, 'interactiveDocGenerator'])->name('interactive-doc-generator');
    Route::get('/project-chatbot', [App\Http\Controllers\AIToolsController::class, 'projectChatbot'])->name('project-chatbot');
    Route::get('/advanced-test-generator', [App\Http\Controllers\AIToolsController::class, 'advancedTestGenerator'])->name('advanced-test-generator');
    Route::get('/error-analyzer', [App\Http\Controllers\AIToolsController::class, 'errorAnalyzer'])->name('error-analyzer');
    Route::get('/project-planning-assistant', [App\Http\Controllers\AIToolsController::class, 'projectPlanningAssistant'])->name('project-planning-assistant');
    Route::get('/productivity-analyzer', [App\Http\Controllers\AIToolsController::class, 'productivityAnalyzer'])->name('productivity-analyzer');
    
    // Performance Analyzer API
    Route::post('/performance-analyzer/analyze', [App\Http\Controllers\DeveloperController::class, 'analyzePerformanceWithAi'])->name('performance-analyzer.analyze');
    
    // Refactoring Tool (Task 11 - v3.19.0)
    Route::get('/refactoring-tool', [App\Http\Controllers\RefactoringToolController::class, 'index'])->name('refactoring-tool');
});
