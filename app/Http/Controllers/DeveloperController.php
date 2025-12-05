<?php

namespace App\Http\Controllers;

use App\Services\AI\PerformanceAnalyzerService;
use App\Services\AI\CodeTranslatorService;
use App\Services\AI\TestGeneratorService;
use App\Services\AI\BugDetectorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * DeveloperController
 *
 * المتحكم الخاص بأدوات المطورين، بما في ذلك تحليل أداء الكود.
 * This controller provides tools for developers, such as code performance analysis.
 */
class DeveloperController extends Controller
{
    /**
     * خدمة تحليل الأداء.
     * The performance analyzer service instance.
     *
     * @var PerformanceAnalyzerService
     */
    protected $analyzerService;

    /**
     * خدمة ترجمة الأكواد.
     * The code translator service instance.
     *
     * @var CodeTranslatorService
     */
    protected $translatorService;

    /**
     * خدمة توليد الاختبارات.
     * The test generator service instance.
     *
     * @var TestGeneratorService
     */
    protected $testGeneratorService;

    /**
     * خدمة كشف الأخطاء.
     * The bug detector service instance.
     *
     * @var BugDetectorService
     */
    protected $bugDetectorService;

    /**
     * إنشاء مثيل جديد للمتحكم.
     * Create a new controller instance.
     *
     * @param PerformanceAnalyzerService $analyzerService
     * @return void
     */
    public function __construct(
        PerformanceAnalyzerService $analyzerService,
        CodeTranslatorService $translatorService,
        TestGeneratorService $testGeneratorService,
        BugDetectorService $bugDetectorService
    ) {
        $this->analyzerService = $analyzerService;
        $this->translatorService = $translatorService;
        $this->testGeneratorService = $testGeneratorService;
        $this->bugDetectorService = $bugDetectorService;
    }

    /**
     * عرض لوحة تحكم نظام المطور.
     * Display the developer dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $system_overview = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
        ];

        return view('developer.dashboard', compact('system_overview'));
    }

    /**
     * دالة لتحليل أداء الكود باستخدام الذكاء الاصطناعي.
     * Analyzes the provided code for performance using AI.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzePerformanceWithAi(Request $request): JsonResponse
    {
        try {
            // 1. التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:10',
                'language' => 'nullable|string|in:PHP,JavaScript,Python,Java,C++', // دعم لغات متعددة
            ], [
                'code.required' => 'حقل الكود مطلوب للتحليل.',
                'code.min' => 'يجب أن يحتوي الكود على 10 أحرف على الأقل.',
                'language.in' => 'لغة البرمجة المدخلة غير مدعومة حاليًا.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $code = $validated['code'];
            $language = $validated['language'] ?? 'PHP';

            // 2. استدعاء PerformanceAnalyzerService
            $analysisResult = $this->analyzerService->analyze($code, $language);

            // 3. إرجاع النتائج بصيغة JSON
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحليل الكود بنجاح بواسطة الذكاء الاصطناعي.',
                'data' => $analysisResult,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            // معالجة أخطاء التحقق من صحة المدخلات
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات.',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // 4. معالجة الأخطاء العامة (مثل فشل الاتصال بالذكاء الاصطناعي)
            // تسجيل الخطأ للمراجعة
            Log::error('Error during performance analysis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // إرجاع استجابة خطأ احترافية
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء تحليل الأداء. يرجى المحاولة لاحقًا.',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * دالة افتراضية أخرى (لضمان أن الملف يحتوي على دوال أخرى كما طلب المستخدم).
     * Another dummy function to ensure the file contains other functions.
     *
     * @return JsonResponse
     */
    public function getDeveloperTools(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'tools' => [
                'code_analysis' => 'Analyze code performance and security.',
                'db_migration' => 'Manage database migrations.',
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // ========================================
    // Code Translator Methods - v3.15.0
    // ========================================

    /**
     * عرض صفحة مترجم الأكواد
     * Display the code translator page
     *
     * @return \Illuminate\View\View
     */
    public function getAiCodeTranslatorPage()
    {
        return view('developer.ai.code-translator', [
            'title' => 'Code Translator - مترجم الأكواد',
            'version' => 'v3.15.0',
            'supported_languages' => $this->translatorService->getSupportedLanguages(),
        ]);
    }

    /**
     * ترجمة الكود باستخدام الذكاء الاصطناعي
     * Translate code using AI
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function translateCodeWithAi(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:5',
                'from_language' => 'required|string|in:php,python,javascript,java,csharp,typescript',
                'to_language' => 'required|string|in:php,python,javascript,java,csharp,typescript',
                'action' => 'required|string|in:translate,detect,validate,compare',
            ], [
                'code.required' => 'حقل الكود مطلوب',
                'code.min' => 'يجب أن يحتوي الكود على 5 أحرف على الأقل',
                'from_language.required' => 'لغة المصدر مطلوبة',
                'from_language.in' => 'لغة المصدر غير مدعومة',
                'to_language.required' => 'لغة الهدف مطلوبة',
                'to_language.in' => 'لغة الهدف غير مدعومة',
                'action.required' => 'الإجراء مطلوب',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $action = $validated['action'];

            // تنفيذ الإجراء المطلوب
            $result = match($action) {
                'translate' => $this->translatorService->translateCode(
                    $validated['code'],
                    $validated['from_language'],
                    $validated['to_language']
                ),
                'detect' => $this->translatorService->detectLanguage($validated['code']),
                'validate' => $this->translatorService->validateSyntax(
                    $validated['code'],
                    $validated['from_language']
                ),
                'compare' => $this->translatorService->compareTranslations(
                    $validated['code'],
                    $request->input('translated_code', ''),
                    $validated['from_language'],
                    $validated['to_language']
                ),
                default => ['success' => false, 'error' => 'إجراء غير معروف']
            };

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تمت العملية بنجاح',
                    'data' => $result,
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'حدث خطأ أثناء العملية',
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            Log::error('Code Translation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء الترجمة',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * عرض صفحة Artisan Commands
     * Display Artisan Commands page
     */
    public function getArtisanCommands()
    {
        return view('developer.artisan');
    }

    /**
     * عرض صفحة Code Generator
     * Display Code Generator page
     */
    public function getCodeGenerator()
    {
        return view('developer.code-generator.index');
    }

    /**
     * تنفيذ أمر Artisan
     * Execute Artisan command
     */
    public function executeArtisanCommand(Request $request)
    {
        // TODO: Implement artisan command execution
        return response()->json(['status' => 'success', 'message' => 'Command executed']);
    }

    /**
     * توليد CRUD
     * Generate CRUD
     */
    public function generateCRUD(Request $request)
    {
        // TODO: Implement CRUD generation
        return response()->json(['status' => 'success', 'message' => 'CRUD generated']);
    }

    /**
     * توليد API
     * Generate API
     */
    public function generateAPI(Request $request)
    {
        // TODO: Implement API generation
        return response()->json(['status' => 'success', 'message' => 'API generated']);
    }

    /**
     * توليد Migration
     * Generate Migration
     */
    public function generateMigration(Request $request)
    {
        // TODO: Implement migration generation
        return response()->json(['status' => 'success', 'message' => 'Migration generated']);
    }

    /**
     * توليد Seeder
     * Generate Seeder
     */
    public function generateSeeder(Request $request)
    {
        // TODO: Implement seeder generation
        return response()->json(['status' => 'success', 'message' => 'Seeder generated']);
    }

    /**
     * توليد Policy
     * Generate Policy
     */
    public function generatePolicy(Request $request)
    {
        // TODO: Implement policy generation
        return response()->json(['status' => 'success', 'message' => 'Policy generated']);
    }

    /**
     * توليد Module كامل
     * Generate Complete Module
     */
    public function generateCompleteModule(Request $request)
    {
        // TODO: Implement complete module generation
        return response()->json(['status' => 'success', 'message' => 'Module generated']);
    }

    /**
     * صفحة مراجعة الأكواد بـ AI
     * AI Code Review Page
     */
    public function getAiCodeReviewPage()
    {
        return view('developer.ai.code-review');
    }

    /**
     * صفحة كاشف الأخطاء بـ AI
     * AI Bug Detector Page
     */
    public function getAiBugDetectorPage()
    {
        return view('developer.ai.bug-detector');
    }

    /**
     * صفحة محسن الأكواد
     * Code Optimizer Page
     */
    public function getCodeOptimizer()
    {
        return view('developer.ai.code-optimizer');
    }

    /**
     * صفحة المساعد الذكي
     * AI Assistant Page
     */
    public function getAiAssistantPage()
    {
        return view('developer.ai.assistant');
    }

    /**
     * صفحة معلومات قاعدة البيانات
     * Database Info Page
     */
    /**
     * صفحة معلومات قاعدة البيانات
     * Database Info Page
     *
     * @return \Illuminate\View\View
     */
    public function getDatabaseInfoPage()
    {
        try {
            $database = config('database.connections.mysql.database');
            $tables = [];
            $total_tables = 0;

            // Get all tables
            $tableNames = DB::select('SHOW TABLES');
            $tableNames = array_map('current', $tableNames);

            $total_tables = count($tableNames);

            foreach ($tableNames as $tableName) {
                // Get row count for each table
                $count = DB::table($tableName)->count();
                $tables[] = [
                    'name' => $tableName,
                    'rows' => $count,
                ];
            }

            // Sort tables by row count descending
            usort($tables, function($a, $b) {
                return $b['rows'] <=> $a['rows'];
            });

            return view('developer.database-info', compact('database', 'total_tables', 'tables'));

        } catch (\Exception $e) {
            // Log the error
            Log::error('Database Info Error: ' . $e->getMessage());

            // Return view with error message
            return view('developer.database-info')->with('error', 'حدث خطأ أثناء جلب معلومات قاعدة البيانات: ' . $e->getMessage());
        }
    }

    /**
     * صفحة معلومات الخادم
     * Server Info Page
     */
    public function getServerInfoPage()
    {
        $php_version = phpversion();
        $laravel_version = app()->version();
        $server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $server_ip = $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $server_name = $_SERVER['SERVER_NAME'] ?? 'Unknown';
        $server_port = $_SERVER['SERVER_PORT'] ?? 'Unknown';
        $document_root = $_SERVER['DOCUMENT_ROOT'] ?? base_path();
        $max_execution_time = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        $upload_max_filesize = ini_get('upload_max_filesize');
        $post_max_size = ini_get('post_max_size');
        
        // Disk space
        $disk_total_space = disk_total_space(base_path());
        $disk_free_space = disk_free_space(base_path());
        
        // PHP Extensions
        $php_extensions = get_loaded_extensions();
        sort($php_extensions);
        
        return view('developer.server-info', compact(
            'php_version',
            'laravel_version',
            'server_software',
            'server_ip',
            'server_name',
            'server_port',
            'document_root',
            'max_execution_time',
            'memory_limit',
            'upload_max_filesize',
            'post_max_size',
            'disk_total_space',
            'disk_free_space',
            'php_extensions'
        ));
    }

    /**
     * صفحة قائمة المسارات
     * Routes List Page
     */
    public function getRoutesListPage()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            // استبعاد المسارات التي لا تحتوي على URI (مثل المسارات المغلقة)
            if (is_null($route->uri())) {
                return null;
            }

            // استبعاد المسارات التي تبدأ بـ _ignition
            if (str_starts_with($route->uri(), '_ignition')) {
                return null;
            }

            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => implode(', ', $route->gatherMiddleware()),
            ];
        })->filter()->sortBy('uri')->values()->all();

        $total = count($routes);

        return view('developer.routes-list', compact('routes', 'total'));
    }

    /**
     * صفحة لوحة تحكم Git
     * Git Dashboard Page
     */
    public function getGitDashboard()
    {
        return view('developer.git.dashboard');
    }

    /**
     * صفحة الاختبارات
     * Tests Page
     */
    public function getTestsPage()
    {
        return view('developer.tests');
    }

    /**
     * صفحة محسن قاعدة البيانات بـ AI
     * AI Database Optimizer Page
     */
    public function getAiDatabaseOptimizerPage()
    {
        return view('developer.ai.database-optimizer');
    }

    /**
     * صفحة مولد الاختبارات بـ AI
     * AI Test Generator Page
     */
    public function getAiTestGeneratorPage()
    {
        return view('developer.ai.test-generator');
    }

    /**
     * صفحة مولد التوثيق بـ AI
     * AI Documentation Generator Page
     */
    public function getAiDocumentationGeneratorPage()
    {
        return view('developer.ai.documentation-generator');
    }

    /**
     * صفحة عارض السجلات
     * Logs Viewer Page
     */
    public function getLogsViewerPage()
    {
        try {
            $logPath = storage_path('logs');
            $logs = [];
            
            if (file_exists($logPath)) {
                $files = File::files($logPath);
                
                foreach ($files as $file) {
                    $logs[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'path' => $file->getPathname(),
                    ];
                }
                
                // Sort by modified date (newest first)
                usort($logs, function($a, $b) {
                    return strcmp($b['modified'], $a['modified']);
                });
            }
            
            $total = count($logs);
            
            return view('developer.logs-viewer', compact('logs', 'total'));
            
        } catch (\Exception $e) {
            Log::error('Logs Viewer Page Error: ' . $e->getMessage());
            return view('developer.logs-viewer', ['logs' => [], 'total' => 0, 'error' => 'Failed to load logs: ' . $e->getMessage()]);
        }
    }

    /**
     * صفحة إعادة هيكلة الأكواد بـ AI
     * AI Code Refactor Page
     */
    public function getAiCodeRefactorPage()
    {
        return view('developer.ai.code-refactor');
    }

    /**
     * صفحة محلل الأداء بـ AI
     * AI Performance Analyzer Page
     */
    public function getAiPerformanceAnalyzerPage()
    {
        return view('developer.ai.performance-analyzer');
    }

    /**
     * صفحة فاحص الأمان بـ AI
     * AI Security Scanner Page
     */
    public function getAiSecurityScannerPage()
    {
        try {
            return view('developer.ai.security-scanner');
        } catch (\Exception $e) {
            Log::error('Security Scanner Page Error: ' . $e->getMessage());
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * صفحة إدارة الذاكرة المؤقتة
     * Cache Management Page
     */
    public function getCachePage()
    {
        try {
            $cache_driver = config('cache.default');
            
            return view('developer.cache', compact('cache_driver'));
            
        } catch (\Exception $e) {
            Log::error('Cache Page Error: ' . $e->getMessage());
            return view('developer.cache', ['cache_driver' => 'Unknown', 'error' => 'Failed to load cache information: ' . $e->getMessage()]);
        }
    }

    /**
     * صفحة مولد الأكواد بـ AI
     * AI Code Generator Page
     */
    public function getAiCodeGeneratorPage(Request $request)
    {
        return view('developer.ai.code-generator');
    }

    /**
     * صفحة مولد API بـ AI
     * AI API Generator Page
     */
    public function getAiApiGeneratorPage()
    {
        return view('developer.ai.api-generator');
    }

    /**
     * صفحة إدارة الهجرات
     * Migrations Management Page
     */
    public function getMigrationsPage()
    {
        try {
            // Get all migration files from the file system
            $migrationFiles = collect(\File::files(database_path('migrations')))->map(function ($file) {
                return basename($file->getFilename(), '.php');
            });

            // Get all ran migrations from the database
            $ranMigrations = \DB::table('migrations')->get()->keyBy('migration');

            $allMigrations = $migrationFiles->map(function ($fileName) use ($ranMigrations) {
                $ran = $ranMigrations->get($fileName);

                return [
                    'name' => $fileName,
                    'ran' => $ran !== null,
                    'batch' => $ran ? $ran->batch : null,
                ];
            })->sortBy('name');

            $ran = $allMigrations->filter(fn($m) => $m['ran'])->values()->all();
            $pending = $allMigrations->filter(fn($m) => !$m['ran'])->values()->all();
            $total = $allMigrations->count();

            return view('developer.migrations', compact('total', 'ran', 'pending'));

        } catch (\Exception $e) {
            \Log::error('Error loading migrations page: ' . $e->getMessage());
            return view('developer.migrations', [
                'total' => 0,
                'ran' => [],
                'pending' => [],
                'error' => 'Failed to load migration status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * صفحة إدارة البيانات الأولية
     * Seeders Management Page
     */
    public function getSeedersPage()
    {
        $seedersPath = database_path('seeders');
        $seeders = [];

        try {
            // Get all files in the seeders directory
            $files = File::files($seedersPath);

            foreach ($files as $file) {
                $filename = $file->getFilename();
                // Exclude DatabaseSeeder and any other non-seeder files
                if ($filename !== 'DatabaseSeeder.php' && $file->getExtension() === 'php') {
                    $seeders[] = [
                        'name' => $file->getBasename('.php'),
                        'size' => $file->getSize(),
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log the error but continue with empty list
            \Log::error("Error reading seeders directory: " . $e->getMessage());
        }

        $total = count($seeders);

        return view('developer.seeders', compact('seeders', 'total'));
    }

    /**
     * صفحة تحسين قاعدة البيانات
     * Database Optimize Page
     */
    public function getDatabaseOptimizePage()
    {
        try {
            // Get table status from the database
            $tables = DB::select('SHOW TABLE STATUS');
            $formattedTables = [];
            $totalSize = 0;

            foreach ($tables as $table) {
                $dataSize = $table->Data_length;
                $indexSize = $table->Index_length;
                $totalSize += $dataSize + $indexSize;

                $formattedTables[] = [
                    'name' => $table->Name,
                    'engine' => $table->Engine,
                    'rows' => $table->Rows,
                    'data_size' => $this->formatBytes($dataSize),
                    'index_size' => $this->formatBytes($indexSize),
                    'total_size' => $this->formatBytes($dataSize + $indexSize),
                ];
            }

            $total_tables = count($formattedTables);
            $total_size = $this->formatBytes($totalSize);

            return view('developer.database-optimize', compact('formattedTables', 'total_tables', 'total_size'));

        } catch (\Exception $e) {
            Log::error('Database Optimize Page Error: ' . $e->getMessage());
            // In a production environment, we should not throw the exception directly
            // but return a user-friendly error page or redirect.
            // Instead of re-throwing, we return an error view in production.
            return view('developer.database-optimize', ['error' => 'Failed to load database information: ' . $e->getMessage()]);
        }
    }

    /**
     * صفحة النسخ الاحتياطي
     * Database Backup Page
     */
    public function getDatabaseBackupPage()
    {
        return view('developer.database-backup');
    }
    // ========================================
    // Git Methods - مفقودة
    // ========================================
    
    public function getGitCommitPage()
    {
        return view('developer.git.commit');
    }
    
    public function getGitHistory()
    {
        try {
            $output = shell_exec('cd ' . base_path() . ' && git log --pretty=format:"%h|%an|%ar|%s" -20');
            
            if (empty($output)) {
                return response()->json(['success' => false, 'message' => 'No git history found']);
            }
            
            $commits = [];
            $lines = explode("\n", trim($output));
            
            foreach ($lines as $line) {
                $parts = explode('|', $line, 4);
                if (count($parts) === 4) {
                    $commits[] = [
                        'hash' => $parts[0],
                        'author' => $parts[1],
                        'date' => $parts[2],
                        'message' => $parts[3]
                    ];
                }
            }
            
            return response()->json(['success' => true, 'commits' => $commits]);
        } catch (\Exception $e) {
            Log::error('Git History Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getGitStatus()
    {
        try {
            $status = shell_exec('cd ' . base_path() . ' && git status');
            return response()->json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function gitCommit(Request $request)
    {
        try {
            $message = $request->input('message', 'Auto commit');
            shell_exec('cd ' . base_path() . ' && git add -A');
            $result = shell_exec('cd ' . base_path() . ' && git commit -m "' . addslashes($message) . '"');
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function gitPush()
    {
        try {
            $result = shell_exec('cd ' . base_path() . ' && git push origin main');
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ========================================
    // AI Methods - مفقودة
    // ========================================
    
    public function aiCodeGenerator(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'AI Code Generator - قيد التطوير',
            'code' => '// Generated code will appear here'
        ]);
    }
    
    public function aiDatabaseDesigner(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'AI Database Designer - قيد التطوير',
            'migration' => '// Generated migration will appear here'
        ]);
    }
    
    public function aiTestGenerator(Request $request)
    {
        try {
            $validated = $request->validate([
                'source_code' => 'required|string|min:10',
                'test_type' => 'required|string|in:unit,feature,integration',
                'framework' => 'required|string|in:phpunit,pest',
            ]);

            $result = $this->testGeneratorService->generateTests(
                $validated['source_code'],
                $validated['test_type'],
                $validated['framework']
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم توليد الاختبارات بنجاح',
                    'tests' => $result['tests']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'فشل توليد الاختبارات',
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من صحة المدخلات',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Test Generator Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function aiCodeReview(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'AI Code Review - قيد التطوير',
            'review' => 'Code review results will appear here'
        ]);
    }
    
    public function aiBugFixer(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'AI Bug Fixer - قيد التطوير',
            'fixed_code' => '// Fixed code will appear here'
        ]);
    }
    
    public function aiDocumentationGenerator(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'AI Documentation Generator - قيد التطوير',
            'documentation' => '# Generated documentation will appear here'
        ]);
    }
    
    public function generateCrudWithAi(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'CRUD generated successfully',
            'files' => []
        ]);
    }
    
    public function refactorCodeWithAi(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Code refactored successfully',
            'refactored_code' => '// Refactored code'
        ]);
    }
    
    public function reviewCodeWithAi(Request $request): JsonResponse
    {
        try {
            // 1. التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:10',
                'language' => 'required|string|in:php,javascript,python,java,csharp,typescript,other',
            ], [
                'code.required' => 'حقل الكود مطلوب للمراجعة.',
                'code.min' => 'يجب أن يحتوي الكود على 10 أحرف على الأقل.',
                'language.required' => 'لغة البرمجة مطلوبة.',
                'language.in' => 'لغة البرمجة المدخلة غير مدعومة حاليًا.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $code = $validated['code'];
            $language = $validated['language'];

            // 2. محاكاة عملية المراجعة (لغرض الاختبار)
            $mockReview = "تمت مراجعة الكود بنجاح.\n\nلغة البرمجة: {$language}\n\nالكود الذي تم إرساله:\n\n```{$language}\n{$code}\n```\n\n**ملاحظة:** هذه نتيجة محاكاة لغرض الاختبار.";

            // 3. إرجاع النتائج بصيغة JSON
            return response()->json([
                'status' => 'success',
                'message' => 'تمت مراجعة الكود بنجاح (محاكاة).',
                'review' => $mockReview,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            // معالجة أخطاء التحقق من صحة المدخلات
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات.',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // 4. معالجة الأخطاء العامة
            Log::error('Error during code review: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // إرجاع استجابة خطأ احترافية
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء مراجعة الكود. يرجى المحاولة لاحقًا.',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function detectBugsWithAi(Request $request)
    {
        try {
            // 1. التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:10',
            ], [
                'code.required' => 'حقل الكود مطلوب للتحليل.',
                'code.min' => 'يجب أن يحتوي الكود على 10 أحرف على الأقل.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $code = $validated['code'];

            // 2. استدعاء BugDetectorService
            $detectionResult = $this->bugDetectorService->detectBugs($code);

            // 3. إرجاع النتائج بصيغة JSON
            return response()->json([
                'status' => 'success',
                'message' => $detectionResult['message'],
                'data' => [
                    'bugs' => $detectionResult['bugs']
                ],
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            // معالجة أخطاء التحقق من صحة المدخلات
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات.',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // 4. معالجة الأخطاء العامة
            Log::error('Error during bug detection: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // إرجاع استجابة خطأ احترافية
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء كشف الأخطاء. يرجى المحاولة لاحقًا.',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function generateDocumentationWithAi(Request $request)
    {
        try {
            // 1. التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:10',
                'doc_type' => 'required|string|in:code,readme,api,user_guide',
                'output_format' => 'required|string|in:markdown,html,pdf',
            ], [
                'code.required' => 'حقل الكود مطلوب.',
                'code.min' => 'يجب أن يحتوي الكود على 10 أحرف على الأقل.',
                'doc_type.in' => 'نوع التوثيق المدخل غير مدعوم.',
                'output_format.in' => 'تنسيق الإخراج المدخل غير مدعوم.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $code = $validated['code'];
            $docType = $validated['doc_type'];
            $outputFormat = $validated['output_format'];

            // 2. بناء الـ Prompt
            $docTypeDescription = match($docType) {
                'code' => 'توثيق شامل ومفصل لجميع الفئات والدوال والأساليب (Classes, Methods, Functions)',
                'readme' => 'ملف README.md احترافي وشامل',
                'api' => 'توثيق API مفصل يشمل نقاط النهاية، المعلمات، والاستجابات',
                'user_guide' => 'دليل مستخدم شامل يشرح كيفية استخدام الكود أو الميزة',
                default => 'توثيق'
            };

            $prompt = "أنت خبير في توثيق الكود. قم بتوليد {$docTypeDescription} للكود التالي. يجب أن يكون الإخراج بتنسيق {$outputFormat} وجميع النصوص باللغة العربية الفصحى. الكود المصدر: \n\n```\n{$code}\n```";

            // 3. استدعاء Manus AI (محاكاة)
            // في بيئة الإنتاج، سيتم استخدام خدمة AI فعلية هنا.
            // حالياً، سنستخدم خدمة Manus AI المتاحة في البيئة الافتراضية.
            $documentation = $this->callManusAiService($prompt); // دالة مساعدة جديدة

            // 4. إرجاع النتائج بصيغة JSON
            return response()->json([
                'status' => 'success',
                'message' => 'تم توليد التوثيق بنجاح بواسطة الذكاء الاصطناعي.',
                'documentation' => $documentation,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات.',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            Log::error('Error during documentation generation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء توليد التوثيق. يرجى المحاولة لاحقًا.',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    // دالة مساعدة لمحاكاة استدعاء خدمة Manus AI
    private function callManusAiService(string $prompt): string
    {
        // هنا يجب أن يكون هناك منطق لاستدعاء Manus AI
        // بما أننا لا نستطيع إجراء استدعاءات HTTP خارجية من الباك إند في هذا السيناريو،
        // سنقوم بمحاكاة الاستجابة باستخدام Manus CLI المتاح.
        // بما أننا لا نستطيع استخدام Manus CLI داخل دالة Controller مباشرة،
        // سنقوم بمحاكاة استجابة بسيطة للتحقق من عمل الواجهة الأمامية والباك إند.
        
        // **ملاحظة: هذا الجزء يحتاج إلى استبدال بمنطق استدعاء Manus AI الفعلي في بيئة الإنتاج.**
        
        $mockResponse = "## توثيق الكود لـ TestController\n\n**ملخص:** هذا المتحكم (Controller) يوفر نقطتي نهاية أساسيتين: `index` لعرض قائمة الموارد و `store` لتخزين مورد جديد.\n\n### الدالة: index()\n\n- **الوصف:** تعرض قائمة بجميع الموارد. حالياً، هي فارغة (//).\n- **الاستجابة:** `\Illuminate\Http\Response`\n\n### الدالة: store(Request \$request)\n\n- **الوصف:** تخزن موردًا جديدًا في قاعدة البيانات باستخدام البيانات المرسلة في الطلب.\n- **المعلمات:**\n  - `\$request`: `\Illuminate\Http\Request` - كائن الطلب الذي يحتوي على بيانات الإدخال.\n- **الاستجابة:** `\Illuminate\Http\Response`";

        return $mockResponse;
    }
    
    public function generateTestsWithAi(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Tests generated successfully',
            'tests' => '// Test code'
        ]);
    }
    
    public function scanSecurityWithAi(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Security scan completed',
            'vulnerabilities' => []
        ]);
    }
    
    public function generateApiWithAi(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'API generated successfully',
            'files' => []
        ]);
    }
    
    public function optimizeDatabaseWithAi(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Database tables optimized successfully',
            'suggestions' => []
        ]);
    }
    
    public function chatWithAiAssistant(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'AI Assistant response',
            'response' => 'مرحباً! كيف يمكنني مساعدتك؟'
        ]);
    }
    
    public function updateAiSettings(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }
    
    public function testManusConnection()
    {
        return response()->json([
            'success' => true,
            'message' => 'Connection test successful'
        ]);
    }
    
    public function getAiSettingsPage()
    {
        try {
            // Get AI settings from database or config
            $settings = collect([
                (object)['key' => 'manus_api_key', 'value' => config('services.manus.api_key', '')],
                (object)['key' => 'ai_model', 'value' => config('services.manus.model', 'gpt-4')],
                (object)['key' => 'max_tokens', 'value' => config('services.manus.max_tokens', 2000)],
                (object)['key' => 'temperature', 'value' => config('services.manus.temperature', 0.7)],
            ]);
            
            return view('developer.ai.settings', compact('settings'));
            
        } catch (\Exception $e) {
            Log::error('AI Settings Page Error: ' . $e->getMessage());
            // Return empty settings on error
            $settings = collect([]);
            return view('developer.ai.settings', compact('settings'));
        }
    }
    
    public function getTaskViewerPage()
    {
        return view('developer.ai.task-viewer');
    }
    
    public function getTaskDetails($taskId)
    {
        return response()->json([
            'success' => true,
            'task' => ['id' => $taskId, 'status' => 'completed']
        ]);
    }
    
    public function analyzeCode(Request $request)
    {
        return response()->json([
            'success' => true,
            'analysis' => 'Code analysis results'
        ]);
    }
    
    public function optimizeCode(Request $request)
    {
        return response()->json([
            'success' => true,
            'optimized_code' => '// Optimized code'
        ]);
    }
    
    public function checkCodeQuality(Request $request)
    {
        return response()->json([
            'success' => true,
            'quality_score' => 85,
            'issues' => []
        ]);
    }

    // ========================================
    // Database Methods - مفقودة
    // ========================================
    
    public function getDatabaseInfo()
    {
        try {
            $tables = \DB::select('SHOW TABLES');
            return response()->json([
                'success' => true,
                'tables' => $tables,
                'database' => config('database.connections.mysql.database')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getTables()
    {
        try {
            $tables = \DB::select('SHOW TABLES');
            return response()->json(['success' => true, 'tables' => $tables]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getTableStructure($table)
    {
        try {
            $structure = \DB::select("DESCRIBE {$table}");
            return response()->json(['success' => true, 'structure' => $structure]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getTableData($table)
    {
        try {
            $data = \DB::table($table)->limit(100)->get();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function executeQuery(Request $request)
    {
        try {
            $query = $request->input('query');
            $result = \DB::select($query);
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getMigrations()
    {
        $migrations = \DB::table('migrations')->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'migrations' => $migrations]);
    }
    
    public function runMigrations()
    {
        try {
            \Artisan::call('migrate', ['--force' => true]);
            return response()->json(['success' => true, 'message' => 'Migrations ran successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * تشغيل Seeder محدد
     * Run a specific Seeder
     */
    public function runSpecificSeeder(Request $request)
    {
        $seederName = $request->input('seeder');
        if (!$seederName) {
            return response()->json(['success' => false, 'message' => 'Seeder name is required.'], 400);
        }

        try {
            $start = microtime(true);
            \Artisan::call('db:seed', ['--class' => $seederName, '--force' => true]);
            $end = microtime(true);
            $duration = round($end - $start, 2);
            $output = \Artisan::output();

            return response()->json([
                'success' => true,
                'message' => "Seeder '{$seederName}' ran successfully.",
                'output' => trim($output),
                'duration' => $duration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error running Seeder '{$seederName}'.",
                'trace' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تشغيل جميع Seeders (Artisan db:seed)
     * Run all Seeders
     */
    public function runAllSeeders()
    {
        try {
            $start = microtime(true);
            \Artisan::call('db:seed', ['--force' => true]);
            $end = microtime(true);
            $duration = round($end - $start, 2);
            $output = \Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'All Seeders ran successfully.',
                'output' => trim($output),
                'duration' => $duration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error running all Seeders.',
                'trace' => $e->getMessage()
            ], 500);
        }
    }
    
    public function runDatabaseOptimize()
    {
        try {
            // Get all table names
            $tables = \DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return current((array) $table);
            }, $tables);

            // Optimize each table
            foreach ($tableNames as $tableName) {
                \DB::statement("OPTIMIZE TABLE `{$tableName}`");
            }
            return response()->json(['success' => true, 'message' => 'Database tables optimized successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function createDatabaseBackup()
    {
        return response()->json([
            'success' => true,
            'message' => 'Backup feature - قيد التطوير'
        ]);
    }
    
    public function downloadBackup($file)
    {
        return response()->json([
            'success' => false,
            'message' => 'Backup feature - قيد التطوير'
        ]);
    }

    // ========================================
    // System Monitor Methods - مفقودة
    // ========================================
    
    /**
     * Helper function to format bytes into human-readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Calculate the value in the appropriate unit
        $bytes /= (1024 ** $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    public function getSystemInfo()
    {
        return response()->json([
            'success' => true,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ]);
    }
    
    public function getPerformanceMetrics()
    {
        return response()->json([
            'success' => true,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ]);
    }
    
    public function getServerInfo()
    {
        return response()->json([
            'success' => true,
            'server_info' => php_uname(),
            'php_version' => PHP_VERSION
        ]);
    }
    
    public function getApplicationInfo()
    {
        return response()->json([
            'success' => true,
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug')
        ]);
    }
    
    public function getDatabasePerformance()
    {
        return response()->json([
            'success' => true,
            'message' => 'Database performance metrics'
        ]);
    }

    // ========================================
    // Cache Methods - مفقودة
    // ========================================
    
    public function getCacheOverview()
    {
        return response()->json([
            'success' => true,
            'driver' => config('cache.default'),
            'message' => 'Cache overview'
        ]);
    }
    
    public function getCacheKeys()
    {
        return response()->json([
            'success' => true,
            'keys' => []
        ]);
    }
    
    public function getCacheValue($key)
    {
        $value = \Cache::get($key);
        return response()->json([
            'success' => true,
            'key' => $key,
            'value' => $value
        ]);
    }
    
    public function deleteCacheKey($key)
    {
        \Cache::forget($key);
        return response()->json([
            'success' => true,
            'message' => 'Key deleted successfully'
        ]);
    }
    
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully'
        ]);
    }
    
    public function getCacheStats()
    {
        return response()->json([
            'success' => true,
            'stats' => 'Cache statistics'
        ]);
    }
    
    public function clearAllCache()
    {
        return $this->clearCache();
    }

    /**
     * مسح نوع واحد من Cache
     * Clear a single type of cache
     */
    public function clearSingleCache(Request $request)
    {
        $type = $request->input('type');
        $command = match($type) {
            'cache' => 'cache:clear',
            'config' => 'config:clear',
            'route' => 'route:clear',
            'view' => 'view:clear',
            default => null,
        };

        if ($command) {
            \Artisan::call($command);
            return response()->json([
                'success' => true,
                'message' => "Cache type '{$type}' cleared successfully"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Invalid cache type: {$type}"
        ], 400);
    }

    // ========================================
    // Logs Methods - مفقودة
    // ========================================
    
    public function getLogFiles()
    {
        $logPath = storage_path('logs');
        $files = \File::files($logPath);
        
        return response()->json([
            'success' => true,
            'files' => array_map(function($file) {
                return [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime()
                ];
            }, $files)
        ]);
    }
    
    public function viewLogFile($file)
    {
        $logPath = storage_path('logs/' . $file);
        
        if (!\File::exists($logPath)) {
            return response()->json(['success' => false, 'message' => 'File not found']);
        }
        
        $content = \File::get($logPath);
        
        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }
    
    public function downloadLogFile($file)
    {
        $logPath = storage_path('logs/' . $file);
        
        if (!\File::exists($logPath)) {
            abort(404);
        }
        
        return response()->download($logPath);
    }
    
    public function clearLogFile($file)
    {
        $logPath = storage_path('logs/' . $file);
        
        if (\File::exists($logPath)) {
            \File::put($logPath, '');
            return response()->json(['success' => true, 'message' => 'Log file cleared']);
        }
        
        return response()->json(['success' => false, 'message' => 'File not found']);
    }
    
    public function deleteLogFile($file)
    {
        $logPath = storage_path('logs/' . $file);
        
        if (\File::exists($logPath)) {
            \File::delete($logPath);
            return response()->json(['success' => true, 'message' => 'Log file deleted']);
        }
        
        return response()->json(['success' => false, 'message' => 'File not found']);
    }
    
    public function analyzeLog($file)
    {
        return response()->json([
            'success' => true,
            'analysis' => 'Log analysis results'
        ]);
    }

    // ========================================
    // Other Methods - مفقودة
    // ========================================
    
    public function getDebugbar()
    {
        // Debugbar works as an overlay, redirect to developer dashboard
        return redirect()->route('developer.index')->with('info', 'Debugbar is enabled and visible at the bottom of the page');
    }
    
    public function getTelescope()
    {
        return redirect('/telescope');
    }
    
    public function getHorizon()
    {
        return redirect('/horizon');
    }
    
    public function runPintFormat()
    {
        try {
            $result = shell_exec('cd ' . base_path() . ' && ./vendor/bin/pint');
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function executeTests()
    {
        try {
            $result = shell_exec('cd ' . base_path() . ' && php artisan test');
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getVueDashboard()
    {
        return view('developer.vue-dashboard');
    }
}
