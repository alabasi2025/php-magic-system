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

// AI Tools - POST routes with both naming conventions
Route::prefix('developer/ai')->name('developer.ai.')->group(function () {
    Route::post('/code-generator', [DeveloperController::class, 'aiCodeGenerator'])->name('code-generator');
    Route::post('/database-designer', [DeveloperController::class, 'aiDatabaseDesigner'])->name('database-designer');
    Route::post('/test-generator', [DeveloperController::class, 'aiTestGenerator'])->name('test-generator');
    Route::post('/code-review', [DeveloperController::class, 'aiCodeReview'])->name('code-review');
    Route::post('/bug-fixer', [DeveloperController::class, 'aiBugFixer'])->name('bug-fixer');
    Route::post('/documentation', [DeveloperController::class, 'aiDocumentationGenerator'])->name('documentation');
});


// ========================================
// AI Tools - أدوات الذكاء الاصطناعي (13 أداة)
// ========================================

// 1. مولد الأكواد
Route::get('/developer/ai/code-generator', [DeveloperController::class, 'getAiCodeGeneratorPage'])->name('developer.ai.code-generator');
Route::post('/developer/ai/code-generator', [DeveloperController::class, 'generateCrudWithAi'])->name('developer.ai.code-generator.post');

// 2. تحسين الكود
Route::get('/developer/ai/code-refactor', [DeveloperController::class, 'getAiCodeRefactorPage'])->name('developer.ai.code-refactor');
Route::post('/developer/ai/code-refactor', [DeveloperController::class, 'refactorCodeWithAi'])->name('developer.ai.code-refactor.post');

// 3. مراجعة الكود
Route::get('/developer/ai/code-review', [DeveloperController::class, 'getAiCodeReviewPage'])->name('developer.ai.code-review');
Route::post('/developer/ai/code-review', [DeveloperController::class, 'reviewCodeWithAi'])->name('developer.ai.code-review.post');

// 4. كشف الأخطاء
Route::get('/developer/ai/bug-detector', [DeveloperController::class, 'getAiBugDetectorPage'])->name('developer.ai.bug-detector');
Route::post('/developer/ai/bug-detector', [DeveloperController::class, 'detectBugsWithAi'])->name('developer.ai.bug-detector.post');

// 5. توليد التوثيق
Route::get('/developer/ai/documentation-generator', [DeveloperController::class, 'getAiDocumentationGeneratorPage'])->name('developer.ai.documentation-generator');
Route::post('/developer/ai/documentation-generator', [DeveloperController::class, 'generateDocumentationWithAi'])->name('developer.ai.documentation-generator.post');

// 6. مولد الاختبارات
Route::get('/developer/ai/test-generator', [DeveloperController::class, 'getAiTestGeneratorPage'])->name('developer.ai.test-generator');
Route::post('/developer/ai/test-generator', [DeveloperController::class, 'generateTestsWithAi'])->name('developer.ai.test-generator.post');

// 7. تحليل الأداء
Route::get('/developer/ai/performance-analyzer', [DeveloperController::class, 'getAiPerformanceAnalyzerPage'])->name('developer.ai.performance-analyzer');
Route::post('/developer/ai/performance-analyzer', [DeveloperController::class, 'analyzePerformanceWithAi'])->name('developer.ai.performance-analyzer.post');

// 8. فحص الأمان
Route::get('/developer/ai/security-scanner', [DeveloperController::class, 'getAiSecurityScannerPage'])->name('developer.ai.security-scanner');
Route::post('/developer/ai/security-scanner', [DeveloperController::class, 'scanSecurityWithAi'])->name('developer.ai.security-scanner.post');

// 9. مولد API
Route::get('/developer/ai/api-generator', [DeveloperController::class, 'getAiApiGeneratorPage'])->name('developer.ai.api-generator');
Route::post('/developer/ai/api-generator', [DeveloperController::class, 'generateApiWithAi'])->name('developer.ai.api-generator.post');

// 10. محسن قاعدة البيانات
Route::get('/developer/ai/database-optimizer', [DeveloperController::class, 'getAiDatabaseOptimizerPage'])->name('developer.ai.database-optimizer');
Route::post('/developer/ai/database-optimizer', [DeveloperController::class, 'optimizeDatabaseWithAi'])->name('developer.ai.database-optimizer.post');

// 11. مترجم الأكواد
Route::get('/developer/ai/code-translator', [DeveloperController::class, 'getAiCodeTranslatorPage'])->name('developer.ai.code-translator');
Route::post('/developer/ai/code-translator', [DeveloperController::class, 'translateCodeWithAi'])->name('developer.ai.code-translator.post');

// 12. المساعد الذكي
Route::get('/developer/ai/assistant', [DeveloperController::class, 'getAiAssistantPage'])->name('developer.ai.assistant');
Route::post('/developer/ai/assistant', [DeveloperController::class, 'chatWithAiAssistant'])->name('developer.ai.assistant.post');

// 13. إعدادات AI
Route::get('/developer/ai/settings', [DeveloperController::class, 'getAiSettingsPage'])->name('developer.ai.settings');
Route::post('/developer/ai/settings', [DeveloperController::class, 'updateAiSettings'])->name('developer.ai.settings.post');
Route::post('/developer/ai/test-connection', [DeveloperController::class, 'testManusConnection'])->name('developer.ai.test-connection');

// 14. عارض سجلات المهام
Route::get('/developer/ai/task-viewer', [DeveloperController::class, 'getTaskViewerPage'])->name('developer.ai.task-viewer');
Route::get('/developer/ai/task/{taskId}', [DeveloperController::class, 'getTaskDetails'])->name('developer.ai.task-details');

// 15. محسن الكود (Code Optimizer)
Route::get('/developer/ai/code-optimizer', [DeveloperController::class, 'getCodeOptimizer'])->name('developer.ai.code-optimizer');
Route::post('/developer/ai/code-optimizer/analyze', [DeveloperController::class, 'analyzeCode'])->name('developer.ai.code-optimizer.analyze');
Route::post('/developer/ai/code-optimizer/optimize', [DeveloperController::class, 'optimizeCode'])->name('developer.ai.code-optimizer.optimize');
Route::post('/developer/ai/code-optimizer/quality', [DeveloperController::class, 'checkCodeQuality'])->name('developer.ai.code-optimizer.quality');


// ========================================
// الخانات الفرعية الإضافية - v2.8.8
// ========================================

// المراقبة والتصحيح
Route::get('/developer/debugbar', [DeveloperController::class, 'getDebugbar'])->name('developer.debugbar');
Route::get('/developer/telescope', [DeveloperController::class, 'getTelescope'])->name('developer.telescope');
Route::get('/developer/horizon', [DeveloperController::class, 'getHorizon'])->name('developer.horizon');

// قاعدة البيانات
Route::get('/developer/migrations', [DeveloperController::class, 'getMigrationsPage'])->name('developer.migrations');
Route::post('/developer/migrations/run', [DeveloperController::class, 'runMigrations'])->name('developer.migrations.run');
Route::get('/developer/seeders', [DeveloperController::class, 'getSeedersPage'])->name('developer.seeders');
Route::post('/developer/seeders/run-specific', [DeveloperController::class, 'runSpecificSeeder'])->name('developer.seeders.run-specific');
Route::post('/developer/seeders/run-all', [DeveloperController::class, 'runAllSeeders'])->name('developer.seeders.run-all');
Route::get('/developer/database-info', [DeveloperController::class, 'getDatabaseInfoPage'])->name('developer.database-info');
Route::get('/developer/database-optimize', [DeveloperController::class, 'getDatabaseOptimizePage'])->name('developer.database-optimize');
Route::post('/developer/database-optimize/run', [DeveloperController::class, 'runDatabaseOptimize'])->name('developer.database-optimize.run');
Route::get('/developer/database-backup', [DeveloperController::class, 'getDatabaseBackupPage'])->name('developer.database-backup');
Route::post('/developer/database-backup/create', [DeveloperController::class, 'createDatabaseBackup'])->name('developer.database-backup.create');
Route::get('/developer/database-backup/download/{file}', [DeveloperController::class, 'downloadBackup'])->name('developer.database-backup.download');

// أدوات الكود
Route::get('/developer/cache', [DeveloperController::class, 'getCachePage'])->name('developer.cache');
Route::post('/developer/cache/clear-all', [DeveloperController::class, 'clearAllCache'])->name('developer.cache.clear-all');
Route::post('/developer/cache/clear-single', [DeveloperController::class, 'clearSingleCache'])->name('developer.cache.clear-single');
Route::get('/developer/pint', [DeveloperController::class, 'getPintPage'])->name('developer.pint');
Route::post('/developer/pint/format', [DeveloperController::class, 'runPintFormat'])->name('developer.pint.format');
Route::get('/developer/tests', [DeveloperController::class, 'getTestsPage'])->name('developer.tests');
Route::post('/developer/tests/execute', [DeveloperController::class, 'executeTests'])->name('developer.tests.execute');
Route::get('/developer/routes-list', [DeveloperController::class, 'getRoutesListPage'])->name('developer.routes-list');

// معلومات النظام
Route::get('/developer/server-info', [DeveloperController::class, 'getServerInfoPage'])->name('developer.server-info');
Route::get('/developer/system-monitor', [DeveloperController::class, 'getSystemMonitorPage'])->name('developer.system-monitor');
Route::get('/developer/logs-viewer', [DeveloperController::class, 'getLogsViewerPage'])->name('developer.logs-viewer');
Route::get('/developer/logs-viewer/{file}', [DeveloperController::class, 'viewLogFile'])->name('developer.logs-viewer.file');
Route::delete('/developer/logs-viewer/{file}', [DeveloperController::class, 'deleteLogFile'])->name('developer.logs-viewer.delete');

// Git والنشر
Route::get('/developer/git/dashboard', [DeveloperController::class, 'getGitDashboard'])->name('developer.git.dashboard');
Route::get('/developer/git/commit', [DeveloperController::class, 'getGitCommitPage'])->name('developer.git.commit');
Route::post('/developer/git/commit', [DeveloperController::class, 'gitCommit'])->name('developer.git.commit.post');
Route::post('/developer/git/push', [DeveloperController::class, 'gitPush'])->name('developer.git.push');
Route::get('/developer/git/history', [DeveloperController::class, 'getGitHistory'])->name('developer.git.history');
Route::get('/developer/git/status', [DeveloperController::class, 'getGitStatus'])->name('developer.git.status');


// Main Developer Dashboard
Route::get('/developer', [DeveloperController::class, 'index'])->name('developer.index');
Route::get('/developer/vue-dashboard', [DeveloperController::class, 'getVueDashboard'])->name('developer.vue-dashboard');

// AI Assistant Plus v3.18.0
Route::prefix('developer/ai-assistant-plus')->name('developer.ai-assistant-plus.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AiAssistantPlusController::class, 'index'])->name('index');
    Route::post('/chat', [\App\Http\Controllers\AiAssistantPlusController::class, 'chat'])->name('chat');
    Route::post('/analyze-code', [\App\Http\Controllers\AiAssistantPlusController::class, 'analyzeCode'])->name('analyze-code');
    Route::post('/generate-code', [\App\Http\Controllers\AiAssistantPlusController::class, 'generateCode'])->name('generate-code');
    Route::post('/fix-bug', [\App\Http\Controllers\AiAssistantPlusController::class, 'fixBug'])->name('fix-bug');
    Route::post('/refactor-code', [\App\Http\Controllers\AiAssistantPlusController::class, 'refactorCode'])->name('refactor-code');
    Route::post('/generate-tests', [\App\Http\Controllers\AiAssistantPlusController::class, 'generateTests'])->name('generate-tests');
    Route::post('/generate-documentation', [\App\Http\Controllers\AiAssistantPlusController::class, 'generateDocumentation'])->name('generate-documentation');
    Route::post('/security-scan', [\App\Http\Controllers\AiAssistantPlusController::class, 'securityScan'])->name('security-scan');
    Route::post('/optimize-performance', [\App\Http\Controllers\AiAssistantPlusController::class, 'optimizePerformance'])->name('optimize-performance');
    Route::post('/translate-code', [\App\Http\Controllers\AiAssistantPlusController::class, 'translateCode'])->name('translate-code');
    Route::post('/get-suggestions', [\App\Http\Controllers\AiAssistantPlusController::class, 'getSuggestions'])->name('get-suggestions');
    Route::post('/clear-conversation', [\App\Http\Controllers\AiAssistantPlusController::class, 'clearConversation'])->name('clear-conversation');
    Route::get('/usage-stats', [\App\Http\Controllers\AiAssistantPlusController::class, 'getUsageStats'])->name('usage-stats');
});

// ========================================
// Controller Generator v3.27.0 - Task 19/100
// ========================================
Route::prefix('developer/controller-generator')->name('developer.controller-generator.')->group(function () {
    Route::get('/', [App\Http\Controllers\ControllerGeneratorController::class, 'index'])->name('index');
    Route::post('/generate', [App\Http\Controllers\ControllerGeneratorController::class, 'generate'])->name('generate');
    Route::post('/preview', [App\Http\Controllers\ControllerGeneratorController::class, 'preview'])->name('preview');
    Route::post('/download', [App\Http\Controllers\ControllerGeneratorController::class, 'download'])->name('download');
    Route::get('/history', [App\Http\Controllers\ControllerGeneratorController::class, 'history'])->name('history');
});
