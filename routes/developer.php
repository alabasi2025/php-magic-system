<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeveloperController;

/*
|--------------------------------------------------------------------------
| Developer Routes
|--------------------------------------------------------------------------
|
| نظام المطور الشامل v2.8.1
| جميع routes نظام المطور
|
*/

// Dashboard
Route::get('/developer', [DeveloperController::class, 'getDashboard'])->name('developer.dashboard');

// Artisan Commands
Route::prefix('developer/artisan')->name('developer.artisan.')->group(function () {
    Route::get('/', [DeveloperController::class, 'getArtisanCommands'])->name('index');
    Route::post('/execute', [DeveloperController::class, 'executeArtisanCommand'])->name('execute');
});

// Code Generator
Route::prefix('developer/code-generator')->name('developer.code-generator.')->group(function () {
    Route::get('/', [DeveloperController::class, 'getCodeGenerator'])->name('index');
    Route::post('/crud', [DeveloperController::class, 'generateCRUD'])->name('crud');
    Route::post('/api', [DeveloperController::class, 'generateAPI'])->name('api');
    Route::post('/migration', [DeveloperController::class, 'generateMigration'])->name('migration');
    Route::post('/seeder', [DeveloperController::class, 'generateSeeder'])->name('seeder');
    Route::post('/policy', [DeveloperController::class, 'generatePolicy'])->name('policy');
    Route::post('/module', [DeveloperController::class, 'generateCompleteModule'])->name('module');
});

// Database Manager
Route::prefix('developer/database')->name('developer.database.')->group(function () {
    Route::get('/', [DeveloperController::class, 'getDatabaseInfo'])->name('info');
    Route::get('/tables', [DeveloperController::class, 'getTables'])->name('tables');
    Route::get('/table/{table}/structure', [DeveloperController::class, 'getTableStructure'])->name('table.structure');
    Route::get('/table/{table}/data', [DeveloperController::class, 'getTableData'])->name('table.data');
    Route::post('/query', [DeveloperController::class, 'executeQuery'])->name('query');
    Route::get('/migrations', [DeveloperController::class, 'getMigrations'])->name('migrations');
});

// System Monitor
Route::prefix('developer/monitor')->name('developer.monitor.')->group(function () {
    Route::get('/system-info', [DeveloperController::class, 'getSystemInfo'])->name('system-info');
    Route::get('/performance', [DeveloperController::class, 'getPerformanceMetrics'])->name('performance');
    Route::get('/server', [DeveloperController::class, 'getServerInfo'])->name('server');
    Route::get('/application', [DeveloperController::class, 'getApplicationInfo'])->name('application');
    Route::get('/database-performance', [DeveloperController::class, 'getDatabasePerformance'])->name('database-performance');
});

// Cache Manager
Route::prefix('developer/cache')->name('developer.cache.')->group(function () {
    Route::get('/', [DeveloperController::class, 'getCacheOverview'])->name('overview');
    Route::get('/keys', [DeveloperController::class, 'getCacheKeys'])->name('keys');
    Route::get('/key/{key}', [DeveloperController::class, 'getCacheValue'])->name('key.value');
    Route::delete('/key/{key}', [DeveloperController::class, 'deleteCacheKey'])->name('key.delete');
    Route::post('/clear', [DeveloperController::class, 'clearCache'])->name('clear');
    Route::get('/stats', [DeveloperController::class, 'getCacheStats'])->name('stats');
});

// Logs Viewer
Route::prefix('developer/logs')->name('developer.logs.')->group(function () {
    Route::get('/', [DeveloperController::class, 'getLogFiles'])->name('index');
    Route::get('/{file}', [DeveloperController::class, 'viewLogFile'])->name('view');
    Route::get('/{file}/download', [DeveloperController::class, 'downloadLogFile'])->name('download');
    Route::post('/{file}/clear', [DeveloperController::class, 'clearLogFile'])->name('clear');
    Route::delete('/{file}', [DeveloperController::class, 'deleteLogFile'])->name('delete');
    Route::get('/{file}/analyze', [DeveloperController::class, 'analyzeLog'])->name('analyze');
});

// AI Tools
Route::prefix('developer/ai')->name('developer.ai.')->group(function () {
    Route::post('/code-generator', [DeveloperController::class, 'aiCodeGenerator'])->name('code-generator');
    Route::post('/database-designer', [DeveloperController::class, 'aiDatabaseDesigner'])->name('database-designer');
    Route::post('/test-generator', [DeveloperController::class, 'aiTestGenerator'])->name('test-generator');
    Route::post('/code-review', [DeveloperController::class, 'aiCodeReview'])->name('code-review');
    Route::post('/bug-fixer', [DeveloperController::class, 'aiBugFixer'])->name('bug-fixer');
    Route::post('/documentation', [DeveloperController::class, 'aiDocumentationGenerator'])->name('documentation');
});

// AI Code Generator UI Routes
Route::get('/developer/ai/code-generator', [DeveloperController::class, 'getAiCodeGeneratorPage'])->name('ai.code-generator');
Route::post('/developer/ai/code-generator', [DeveloperController::class, 'generateCrudWithAi'])->name('ai.code-generator.post');
Route::post('/developer/ai/migration', [DeveloperController::class, 'generateMigrationWithAi'])->name('ai.migration');
Route::post('/developer/ai/api-resource', [DeveloperController::class, 'generateApiResourceWithAi'])->name('ai.api-resource');
Route::post('/developer/ai/tests', [DeveloperController::class, 'generateTestsWithAi'])->name('ai.tests');

// AI Helper Tools Routes
Route::post('/developer/ai/code-review', [DeveloperController::class, 'reviewCodeWithAi'])->name('ai.code-review');
Route::post('/developer/ai/bug-fixer', [DeveloperController::class, 'fixBugWithAi'])->name('ai.bug-fixer');
Route::post('/developer/ai/test-generator', [DeveloperController::class, 'generateTestsWithAiHelper'])->name('ai.test-generator');
Route::post('/developer/ai/documentation', [DeveloperController::class, 'generateDocumentationWithAi'])->name('ai.documentation');
