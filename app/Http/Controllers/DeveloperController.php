<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Exception;

/**
 * DeveloperController - نظام المطور الشامل v2.8.1
 * 
 * يحتوي على 8 أقسام رئيسية:
 * 1. Dashboard - لوحة التحكم
 * 2. Artisan Commands - أوامر Artisan
 * 3. Code Generator - مولد الأكواد
 * 4. Database Manager - إدارة قاعدة البيانات
 * 5. System Monitor - مراقبة النظام
 * 6. Cache Manager - إدارة الذاكرة المؤقتة
 * 7. Logs Viewer - عارض السجلات
 * 8. AI Tools - أدوات الذكاء الاصطناعي
 * 
 * @package App\Http\Controllers
 * @version 2.8.1
 */
class DeveloperController extends Controller
{
    /**
     * مفتاح OpenAI API
     */
    private $openaiApiKey;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->openaiApiKey = env('OPENAI_API_KEY');
    }

    // ========================================
    // القسم 1: Dashboard Functions
    // ========================================

    /**
     * عرض لوحة تحكم المطور (Method index)
     */
    public function index()
    {
        return $this->getDashboard();
    }

    /**
     * عرض لوحة تحكم المطور
     */
    public function getDashboard()
    {
        try {
            $data = [
                'system_overview' => $this->getSystemOverview(),
                'quick_stats' => $this->getQuickStats(),
                'recent_activity' => $this->getRecentActivity(),
                'version' => config('version.number')
            ];

            return view('developer.dashboard', $data);
        } catch (Exception $e) {
            Log::error('Developer Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ في تحميل لوحة التحكم');
        }
    }

    /**
     * نظرة عامة على النظام
     */
    private function getSystemOverview()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
    }

    /**
     * إحصائيات سريعة
     */
    private function getQuickStats()
    {
        return [
            'database_tables' => count(Schema::getAllTables()),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'session_driver' => config('session.driver'),
        ];
    }

    /**
     * النشاط الأخير
     */
    private function getRecentActivity()
    {
        $logFile = storage_path('logs/laravel.log');
        $recentLogs = [];

        if (File::exists($logFile)) {
            $lines = file($logFile);
            $recentLogs = array_slice(array_reverse($lines), 0, 10);
        }

        return $recentLogs;
    }

    // ========================================
    // القسم 2: Artisan Commands Functions
    // ========================================

    /**
     * عرض صفحة أوامر Artisan
     */
    public function getArtisanCommands()
    {
        $commands = [
            'cache' => $this->getCacheCommands(),
            'database' => $this->getDatabaseCommands(),
            'queue' => $this->getQueueCommands(),
            'make' => $this->getMakeCommands(),
            'maintenance' => $this->getMaintenanceCommands(),
        ];

        return view('developer.artisan.index', compact('commands'));
    }

    /**
     * تنفيذ أمر Artisan
     */
    public function executeArtisanCommand(Request $request)
    {
        try {
            $command = $request->input('command');
            $arguments = $request->input('arguments', []);

            Artisan::call($command, $arguments);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'تم تنفيذ الأمر بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * أوامر الذاكرة المؤقتة
     */
    private function getCacheCommands()
    {
        return [
            ['name' => 'cache:clear', 'description' => 'مسح الذاكرة المؤقتة'],
            ['name' => 'config:cache', 'description' => 'إنشاء cache للإعدادات'],
            ['name' => 'config:clear', 'description' => 'مسح cache الإعدادات'],
            ['name' => 'route:cache', 'description' => 'إنشاء cache للـ routes'],
            ['name' => 'route:clear', 'description' => 'مسح cache الـ routes'],
            ['name' => 'view:cache', 'description' => 'إنشاء cache للـ views'],
            ['name' => 'view:clear', 'description' => 'مسح cache الـ views'],
            ['name' => 'optimize', 'description' => 'تحسين الأداء'],
            ['name' => 'optimize:clear', 'description' => 'مسح جميع ملفات التحسين'],
        ];
    }

    /**
     * أوامر قاعدة البيانات
     */
    private function getDatabaseCommands()
    {
        return [
            ['name' => 'migrate', 'description' => 'تشغيل migrations'],
            ['name' => 'migrate:fresh', 'description' => 'حذف وإعادة تشغيل migrations'],
            ['name' => 'migrate:refresh', 'description' => 'rollback وإعادة تشغيل'],
            ['name' => 'migrate:rollback', 'description' => 'التراجع عن آخر migration'],
            ['name' => 'migrate:status', 'description' => 'عرض حالة migrations'],
            ['name' => 'db:seed', 'description' => 'تشغيل seeders'],
            ['name' => 'db:wipe', 'description' => 'حذف جميع الجداول'],
        ];
    }

    /**
     * أوامر الطوابير
     */
    private function getQueueCommands()
    {
        return [
            ['name' => 'queue:work', 'description' => 'تشغيل queue worker'],
            ['name' => 'queue:restart', 'description' => 'إعادة تشغيل workers'],
            ['name' => 'queue:failed', 'description' => 'عرض failed jobs'],
            ['name' => 'queue:retry', 'description' => 'إعادة محاولة failed job'],
            ['name' => 'queue:flush', 'description' => 'حذف جميع failed jobs'],
        ];
    }

    /**
     * أوامر التوليد
     */
    private function getMakeCommands()
    {
        return [
            ['name' => 'make:model', 'description' => 'إنشاء model'],
            ['name' => 'make:controller', 'description' => 'إنشاء controller'],
            ['name' => 'make:migration', 'description' => 'إنشاء migration'],
            ['name' => 'make:seeder', 'description' => 'إنشاء seeder'],
            ['name' => 'make:middleware', 'description' => 'إنشاء middleware'],
        ];
    }

    /**
     * أوامر الصيانة
     */
    private function getMaintenanceCommands()
    {
        return [
            ['name' => 'down', 'description' => 'وضع الصيانة'],
            ['name' => 'up', 'description' => 'إخراج من الصيانة'],
            ['name' => 'storage:link', 'description' => 'ربط storage'],
        ];
    }

    // ========================================
    // القسم 3: Code Generator Functions
    // ========================================

    /**
     * عرض صفحة مولد الأكواد
     */
    public function getCodeGenerator()
    {
        return view('developer.code-generator.index');
    }

    /**
     * توليد CRUD كامل
     */
    public function generateCRUD(Request $request)
    {
        try {
            $modelName = $request->input('model_name');
            $fields = $request->input('fields', []);
            
            // توليد Model
            Artisan::call('make:model', ['name' => $modelName, '-m' => true]);
            
            // توليد Controller
            Artisan::call('make:controller', [
                'name' => $modelName . 'Controller',
                '--resource' => true
            ]);
            
            // توليد Requests
            Artisan::call('make:request', ['name' => 'Store' . $modelName . 'Request']);
            Artisan::call('make:request', ['name' => 'Update' . $modelName . 'Request']);
            
            return response()->json([
                'success' => true,
                'message' => 'تم توليد CRUD بنجاح',
                'files' => [
                    'model' => "app/Models/{$modelName}.php",
                    'controller' => "app/Http/Controllers/{$modelName}Controller.php",
                    'migration' => 'database/migrations/...'
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * توليد API
     */
    public function generateAPI(Request $request)
    {
        try {
            $resourceName = $request->input('resource_name');
            
            Artisan::call('make:controller', [
                'name' => 'Api/' . $resourceName . 'Controller',
                '--api' => true
            ]);
            
            Artisan::call('make:resource', ['name' => $resourceName . 'Resource']);
            
            return response()->json([
                'success' => true,
                'message' => 'تم توليد API بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * توليد Migration
     */
    public function generateMigration(Request $request)
    {
        try {
            $tableName = $request->input('table_name');
            $action = $request->input('action', 'create');
            
            $migrationName = $action . '_' . $tableName . '_table';
            
            Artisan::call('make:migration', ['name' => $migrationName]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم توليد Migration بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * توليد Seeder
     */
    public function generateSeeder(Request $request)
    {
        try {
            $modelName = $request->input('model_name');
            
            Artisan::call('make:seeder', ['name' => $modelName . 'Seeder']);
            Artisan::call('make:factory', ['name' => $modelName . 'Factory']);
            
            return response()->json([
                'success' => true,
                'message' => 'تم توليد Seeder و Factory بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * توليد Policy
     */
    public function generatePolicy(Request $request)
    {
        try {
            $modelName = $request->input('model_name');
            
            Artisan::call('make:policy', [
                'name' => $modelName . 'Policy',
                '--model' => $modelName
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم توليد Policy بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * توليد Module كامل
     */
    public function generateCompleteModule(Request $request)
    {
        try {
            $moduleName = $request->input('module_name');
            
            // توليد جميع المكونات
            $this->generateCRUD($request);
            $this->generateAPI($request);
            $this->generateSeeder($request);
            $this->generatePolicy($request);
            
            return response()->json([
                'success' => true,
                'message' => "تم توليد Module {$moduleName} بنجاح"
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // القسم 4: Database Manager Functions
    // ========================================

    /**
     * معلومات قاعدة البيانات
     */
    public function getDatabaseInfo()
    {
        try {
            $connection = DB::connection();
            $dbName = $connection->getDatabaseName();
            
            $info = [
                'database_name' => $dbName,
                'driver' => config('database.default'),
                'connection' => config('database.connections.' . config('database.default')),
                'tables_count' => count(Schema::getAllTables()),
            ];
            
            return view('developer.database.info', compact('info'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في الاتصال بقاعدة البيانات');
        }
    }

    /**
     * قائمة الجداول
     */
    public function getTables()
    {
        try {
            $tables = Schema::getAllTables();
            $tablesInfo = [];
            
            foreach ($tables as $table) {
                $tableName = reset($table);
                $tablesInfo[] = [
                    'name' => $tableName,
                    'rows' => DB::table($tableName)->count(),
                    'columns' => count(Schema::getColumnListings($tableName))
                ];
            }
            
            return view('developer.database.tables', compact('tablesInfo'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في جلب الجداول');
        }
    }

    /**
     * هيكل الجدول
     */
    public function getTableStructure($table)
    {
        try {
            $columns = Schema::getColumnListings($table);
            $structure = [];
            
            foreach ($columns as $column) {
                $structure[] = [
                    'name' => $column,
                    'type' => Schema::getColumnType($table, $column),
                ];
            }
            
            return view('developer.database.structure', compact('table', 'structure'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في جلب هيكل الجدول');
        }
    }

    /**
     * بيانات الجدول
     */
    public function getTableData($table, Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $data = DB::table($table)->paginate($perPage);
            
            return view('developer.database.data', compact('table', 'data'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في جلب بيانات الجدول');
        }
    }

    /**
     * تنفيذ استعلام SQL
     */
    public function executeQuery(Request $request)
    {
        try {
            $query = $request->input('query');
            $results = DB::select($query);
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * قائمة Migrations
     */
    public function getMigrations()
    {
        try {
            $ran = DB::table('migrations')->pluck('migration')->toArray();
            $allMigrations = File::files(database_path('migrations'));
            
            $migrations = [];
            foreach ($allMigrations as $file) {
                $filename = $file->getFilename();
                $migrations[] = [
                    'name' => $filename,
                    'status' => in_array(str_replace('.php', '', $filename), $ran) ? 'ran' : 'pending'
                ];
            }
            
            return view('developer.database.migrations', compact('migrations'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في جلب Migrations');
        }
    }

    // ========================================
    // القسم 5: System Monitor Functions
    // ========================================

    /**
     * معلومات النظام
     */
    public function getSystemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];
        
        return view('developer.monitor.system-info', compact('info'));
    }

    /**
     * مقاييس الأداء
     */
    public function getPerformanceMetrics()
    {
        $metrics = [
            'memory_usage' => memory_get_usage(true) / 1024 / 1024,
            'memory_peak' => memory_get_peak_usage(true) / 1024 / 1024,
            'execution_time' => microtime(true) - LARAVEL_START,
        ];
        
        return response()->json($metrics);
    }

    /**
     * معلومات الخادم
     */
    public function getServerInfo()
    {
        $info = [
            'hostname' => gethostname(),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        ];
        
        return response()->json($info);
    }

    /**
     * معلومات التطبيق
     */
    public function getApplicationInfo()
    {
        $info = [
            'name' => config('app.name'),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
        
        return response()->json($info);
    }

    /**
     * أداء قاعدة البيانات
     */
    public function getDatabasePerformance()
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $queryTime = (microtime(true) - $start) * 1000;
            
            $performance = [
                'connection_time' => round($queryTime, 2) . ' ms',
                'active_connections' => DB::select('SHOW STATUS LIKE "Threads_connected"')[0]->Value ?? 'N/A',
            ];
            
            return response()->json($performance);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // القسم 6: Cache Manager Functions
    // ========================================

    /**
     * نظرة عامة على الذاكرة المؤقتة
     */
    public function getCacheOverview()
    {
        $overview = [
            'driver' => config('cache.default'),
            'stores' => config('cache.stores'),
        ];
        
        return view('developer.cache.overview', compact('overview'));
    }

    /**
     * قائمة مفاتيح الذاكرة المؤقتة
     */
    public function getCacheKeys()
    {
        // ملاحظة: هذه الوظيفة تعتمد على نوع cache driver
        return view('developer.cache.keys');
    }

    /**
     * قيمة مفتاح محدد
     */
    public function getCacheValue($key)
    {
        try {
            $value = Cache::get($key);
            return response()->json([
                'success' => true,
                'key' => $key,
                'value' => $value
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف مفتاح من الذاكرة المؤقتة
     */
    public function deleteCacheKey($key)
    {
        try {
            Cache::forget($key);
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المفتاح بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مسح الذاكرة المؤقتة
     */
    public function clearCache(Request $request)
    {
        try {
            $type = $request->input('type', 'all');
            
            switch ($type) {
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    break;
                default:
                    Artisan::call('cache:clear');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح الذاكرة المؤقتة بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * إحصائيات الذاكرة المؤقتة
     */
    public function getCacheStats()
    {
        $stats = [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];
        
        return response()->json($stats);
    }

    // ========================================
    // القسم 7: Logs Viewer Functions
    // ========================================

    /**
     * قائمة ملفات السجلات
     */
    public function getLogFiles()
    {
        try {
            $logPath = storage_path('logs');
            $files = File::files($logPath);
            
            $logs = [];
            foreach ($files as $file) {
                $logs[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime())
                ];
            }
            
            return view('developer.logs.index', compact('logs'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في جلب ملفات السجلات');
        }
    }

    /**
     * عرض ملف سجل
     */
    public function viewLogFile($file)
    {
        try {
            $logPath = storage_path('logs/' . $file);
            
            if (!File::exists($logPath)) {
                return back()->with('error', 'الملف غير موجود');
            }
            
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            $logs = array_reverse(array_slice($lines, -100));
            
            return view('developer.logs.viewer', compact('file', 'logs'));
        } catch (Exception $e) {
            return back()->with('error', 'خطأ في قراءة الملف');
        }
    }

    /**
     * تحميل ملف سجل
     */
    public function downloadLogFile($file)
    {
        $logPath = storage_path('logs/' . $file);
        
        if (!File::exists($logPath)) {
            return back()->with('error', 'الملف غير موجود');
        }
        
        return response()->download($logPath);
    }

    /**
     * مسح ملف سجل
     */
    public function clearLogFile($file)
    {
        try {
            $logPath = storage_path('logs/' . $file);
            File::put($logPath, '');
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح الملف بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف ملف سجل
     */
    public function deleteLogFile($file)
    {
        try {
            $logPath = storage_path('logs/' . $file);
            File::delete($logPath);
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الملف بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * تحليل السجلات
     */
    public function analyzeLog($file)
    {
        try {
            $logPath = storage_path('logs/' . $file);
            $content = File::get($logPath);
            
            $analysis = [
                'total_lines' => substr_count($content, "\n"),
                'errors' => substr_count($content, '[error]'),
                'warnings' => substr_count($content, '[warning]'),
                'info' => substr_count($content, '[info]'),
            ];
            
            return response()->json($analysis);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // القسم 8: AI Tools Functions
    // ========================================

    /**
     * مولد الأكواد بالذكاء الاصطناعي
     */
    public function aiCodeGenerator(Request $request)
    {
        try {
            $description = $request->input('description');
            $type = $request->input('type', 'general');
            
            $prompt = "أنت مطور Laravel محترف. قم بتوليد كود {$type} بناءً على الوصف التالي:\n\n{$description}\n\nالكود يجب أن يكون:\n- متوافق مع Laravel 12.x\n- يتبع أفضل الممارسات\n- موثق بشكل جيد\n- آمن ومحسّن";
            
            $code = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'code' => $code,
                'type' => $type
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مصمم قاعدة البيانات بالذكاء الاصطناعي
     */
    public function aiDatabaseDesigner(Request $request)
    {
        try {
            $description = $request->input('description');
            
            $prompt = "أنت مصمم قواعد بيانات محترف. قم بتصميم قاعدة بيانات كاملة بناءً على الوصف التالي:\n\n{$description}\n\nيجب أن يتضمن التصميم:\n- الجداول والعلاقات\n- أنواع البيانات المناسبة\n- المفاتيح الأساسية والأجنبية\n- الفهارس المناسبة\n- كود Migration لـ Laravel";
            
            $schema = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'schema' => $schema
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مولد الاختبارات بالذكاء الاصطناعي
     */
    public function aiTestGenerator(Request $request)
    {
        try {
            $className = $request->input('class_name');
            $methods = $request->input('methods', []);
            
            $methodsList = implode(', ', $methods);
            $prompt = "أنت مطور Laravel محترف. قم بتوليد اختبارات PHPUnit للكلاس {$className} والدوال التالية: {$methodsList}\n\nالاختبارات يجب أن تغطي:\n- الحالات الناجحة\n- الحالات الفاشلة\n- الحالات الحدية\n- استخدام Mocks عند الحاجة";
            
            $tests = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'tests' => $tests
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مراجع الكود بالذكاء الاصطناعي
     */
    public function aiCodeReview(Request $request)
    {
        try {
            $code = $request->input('code');
            $focus = $request->input('focus', 'general');
            
            $prompt = "أنت خبير في مراجعة أكواد Laravel. قم بمراجعة الكود التالي مع التركيز على {$focus}:\n\n```php\n{$code}\n```\n\nقدم:\n1. المشاكل الموجودة\n2. مستوى الخطورة\n3. التوصيات\n4. الكود المحسّن";
            
            $review = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'review' => $review
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مصلح الأخطاء بالذكاء الاصطناعي
     */
    public function aiBugFixer(Request $request)
    {
        try {
            $error = $request->input('error');
            $code = $request->input('code', '');
            $stackTrace = $request->input('stack_trace', '');
            
            $prompt = "أنت خبير في إصلاح أخطاء Laravel.\n\nالخطأ:\n{$error}\n\nالكود:\n```php\n{$code}\n```\n\nStack Trace:\n{$stackTrace}\n\nقدم:\n1. تحليل السبب الجذري\n2. الحل المقترح\n3. الكود المصحح\n4. نصائح لتجنب المشكلة مستقبلاً";
            
            $fix = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'fix' => $fix
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * مولد التوثيق بالذكاء الاصطناعي
     */
    public function aiDocumentationGenerator(Request $request)
    {
        try {
            $code = $request->input('code');
            $type = $request->input('type', 'phpdoc');
            
            $prompt = "أنت خبير في توثيق أكواد Laravel. قم بتوليد توثيق {$type} للكود التالي:\n\n```php\n{$code}\n```\n\nالتوثيق يجب أن يتضمن:\n- وصف واضح\n- المعاملات والأنواع\n- القيم المرجعة\n- الاستثناءات المحتملة\n- أمثلة الاستخدام";
            
            $documentation = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'documentation' => $documentation
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * استدعاء OpenAI API
     */
    private function callOpenAI($prompt)
    {
        if (!$this->openaiApiKey) {
            throw new Exception('OpenAI API Key غير موجود في .env');
        }

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        
        $data = [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'أنت مساعد ذكي متخصص في تطوير Laravel'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->openaiApiKey
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('خطأ في الاتصال بـ OpenAI API');
        }

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? 'لم يتم الحصول على رد';
    }

    // ========================================
    // AI Code Generator Methods
    // ========================================

    /**
     * عرض صفحة مولد الأكواد بـ AI
     */
    public function getAiCodeGeneratorPage()
    {
        try {
            return view('developer.ai-code-generator', [
                'version' => config('version.number')
            ]);
        } catch (Exception $e) {
            Log::error('AI Code Generator Page Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ في تحميل صفحة مولد الأكواد');
        }
    }

    /**
     * توليد CRUD بـ AI
     */
    public function generateCrudWithAi(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required|string|min:10|max:1000',
                'model_name' => 'required|string|regex:/^[A-Z][a-zA-Z0-9]*$/',
                'fields' => 'nullable|array',
                'auto_save' => 'nullable|boolean'
            ]);

            $service = new \App\Services\AiCodeGeneratorService();
            
            $result = $service->generateCRUD(
                $request->input('description'),
                $request->input('model_name'),
                $request->input('fields', [])
            );

            if ($result['success'] && $request->input('auto_save')) {
                $saveResult = $service->saveGeneratedCode(
                    $result['components'],
                    $request->input('model_name')
                );
                $result['save_result'] = $saveResult;
            }

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('AI CRUD Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في توليد CRUD: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توليد Migration بـ AI
     */
    public function generateMigrationWithAi(Request $request)
    {
        try {
            $request->validate([
                'table_name' => 'required|string|regex:/^[a-z_]+$/',
                'description' => 'required|string|min:10|max:1000',
                'fields' => 'nullable|array'
            ]);

            $service = new \App\Services\AiCodeGeneratorService();
            
            $result = $service->generateMigration(
                $request->input('table_name'),
                $request->input('description'),
                $request->input('fields', [])
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('AI Migration Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في توليد Migration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توليد API Resource بـ AI
     */
    public function generateApiResourceWithAi(Request $request)
    {
        try {
            $request->validate([
                'resource_name' => 'required|string|regex:/^[A-Z][a-zA-Z0-9]*$/',
                'fields' => 'required|array|min:1'
            ]);

            $service = new \App\Services\AiCodeGeneratorService();
            
            $result = $service->generateApiResource(
                $request->input('resource_name'),
                $request->input('fields')
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('AI API Resource Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في توليد API Resource: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توليد Tests بـ AI
     */
    public function generateTestsWithAi(Request $request)
    {
        try {
            $request->validate([
                'model_name' => 'required|string|regex:/^[A-Z][a-zA-Z0-9]*$/',
                'description' => 'required|string|min:10|max:1000'
            ]);

            $service = new \App\Services\AiCodeGeneratorService();
            
            $result = $service->generateTests(
                $request->input('model_name'),
                $request->input('description')
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('AI Tests Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في توليد الاختبارات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system information
     */
    public function systemInfo()
    {
        try {
            $systemInfo = [
                'php_version' => phpversion(),
                'laravel_version' => \Illuminate\Foundation\Application::VERSION,
                'server_os' => php_uname(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'extensions' => get_loaded_extensions(),
                'database' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.' . config('database.default') . '.host'),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $systemInfo
            ]);
        } catch (Exception $e) {
            Log::error('System Info Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في الحصول على معلومات النظام: ' . $e->getMessage()
            ], 500);
        }
    }

    public function databaseInfo()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function optimizeDatabase()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function backupDatabase()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function runMigrations()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function runSeeders()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function runPint()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function runTests()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function showRoutes()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function showLogs()
    {
        try {
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function getVueDashboard()
    {
        return view("developer.vue-dashboard", [
            "phpVersion" => phpversion(),
            "laravelVersion" => app()->version(),
            "database" => config("database.default"),
            "environment" => app()->environment(),
        ]);
    }

    public function getTelescope()
    {
        return view("developer.telescope");
    }

    public function getHorizon()
    {
        return view("developer.horizon");
    }

    public function getPermissions()
    {
        return view("developer.permissions");
    }

    // ========================================
    // الخانات الفرعية الإضافية - v2.8.7
    // ========================================

    /**
     * صفحة Debugbar
     */
    public function getDebugbar()
    {
        $data = [
            'debugbar_enabled' => config('app.debug'),
            'debugbar_collectors' => [
                'queries' => 'Database Queries',
                'routes' => 'Routes',
                'views' => 'Views',
                'events' => 'Events',
                'exceptions' => 'Exceptions',
                'logs' => 'Logs',
                'cache' => 'Cache',
            ]
        ];
        return view('developer.debugbar', $data);
    }

    /**
     * صفحة Migrations
     */
    public function getMigrationsPage()
    {
        try {
            $migrations = $this->getMigrations();
            $pending = [];
            $ran = [];
            
            // Get ran migrations
            $ranMigrations = DB::table('migrations')->pluck('migration')->toArray();
            
            // Get all migration files
            $migrationFiles = File::glob(database_path('migrations/*.php'));
            
            foreach ($migrationFiles as $file) {
                $migrationName = str_replace('.php', '', basename($file));
                if (in_array($migrationName, $ranMigrations)) {
                    $ran[] = [
                        'name' => $migrationName,
                        'batch' => DB::table('migrations')->where('migration', $migrationName)->value('batch'),
                        'file' => $file
                    ];
                } else {
                    $pending[] = [
                        'name' => $migrationName,
                        'file' => $file
                    ];
                }
            }
            
            return view('developer.migrations', [
                'ran' => $ran,
                'pending' => $pending,
                'total' => count($migrationFiles)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة Seeders
     */
    public function getSeedersPage()
    {
        try {
            $seederFiles = File::glob(database_path('seeders/*.php'));
            $seeders = [];
            
            foreach ($seederFiles as $file) {
                $seederName = str_replace('.php', '', basename($file));
                if ($seederName !== 'DatabaseSeeder') {
                    $seeders[] = [
                        'name' => $seederName,
                        'file' => $file,
                        'size' => File::size($file)
                    ];
                }
            }
            
            return view('developer.seeders', [
                'seeders' => $seeders,
                'total' => count($seeders)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة معلومات القاعدة
     */
    public function getDatabaseInfoPage()
    {
        return view('developer.database-info', $this->getDatabaseInfo());
    }

    /**
     * صفحة تحسين القاعدة
     */
    public function getDatabaseOptimizePage()
    {
        try {
            $tables = DB::select('SHOW TABLE STATUS');
            $totalSize = 0;
            $tableData = [];
            
            foreach ($tables as $table) {
                $size = $table->Data_length + $table->Index_length;
                $totalSize += $size;
                $tableData[] = [
                    'name' => $table->Name,
                    'engine' => $table->Engine,
                    'rows' => $table->Rows,
                    'data_size' => $this->formatBytes($table->Data_length),
                    'index_size' => $this->formatBytes($table->Index_length),
                    'total_size' => $this->formatBytes($size),
                    'collation' => $table->Collation
                ];
            }
            
            return view('developer.database-optimize', [
                'tables' => $tableData,
                'total_size' => $this->formatBytes($totalSize),
                'total_tables' => count($tables)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تنفيذ تحسين القاعدة
     */
    public function runDatabaseOptimize(Request $request)
    {
        try {
            $tables = Schema::getAllTables();
            $optimized = [];
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimized[] = $tableName;
            }
            
            return back()->with('success', 'تم تحسين ' . count($optimized) . ' جدول بنجاح');
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة النسخ الاحتياطي
     */
    public function getDatabaseBackupPage()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            $backups = [];
            $files = File::files($backupPath);
            
            foreach ($files as $file) {
                if (str_ends_with($file->getFilename(), '.sql')) {
                    $backups[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'date' => date('Y-m-d H:i:s', $file->getMTime()),
                        'path' => $file->getPathname()
                    ];
                }
            }
            
            return view('developer.database-backup', [
                'backups' => $backups,
                'total' => count($backups)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إنشاء نسخة احتياطية
     */
    public function createDatabaseBackup()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $filepath = $backupPath . '/' . $filename;
            
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');
            
            $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$filepath}";
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                return back()->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح: ' . $filename);
            } else {
                return back()->with('error', 'فشل إنشاء النسخة الاحتياطية');
            }
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تحميل نسخة احتياطية
     */
    public function downloadBackup($file)
    {
        $filepath = storage_path('app/backups/' . $file);
        if (File::exists($filepath)) {
            return response()->download($filepath);
        }
        return back()->with('error', 'الملف غير موجود');
    }

    /**
     * صفحة Cache
     */
    public function getCachePage()
    {
        try {
            $data = [
                'cache_driver' => config('cache.default'),
                'cache_stats' => $this->getCacheStats(),
                'cache_keys' => $this->getCacheKeys()
            ];
            return view('developer.cache', $data);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * مسح جميع أنواع Cache
     */
    public function clearAllCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return back()->with('success', 'تم مسح جميع أنواع الذاكرة المؤقتة بنجاح');
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة Laravel Pint
     */
    public function getPintPage()
    {
        try {
            $pintExists = File::exists(base_path('vendor/bin/pint'));
            return view('developer.pint', [
                'pint_installed' => $pintExists,
                'pint_config' => File::exists(base_path('pint.json'))
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تشغيل Laravel Pint
     */
    public function runPintFormat(Request $request)
    {
        try {
            $path = $request->input('path', 'app');
            $output = [];
            $returnVar = 0;
            
            exec('cd ' . base_path() . ' && ./vendor/bin/pint ' . $path, $output, $returnVar);
            
            return back()->with('success', 'تم تنسيق الكود بنجاح')->with('output', implode("\n", $output));
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة الاختبارات
     */
    public function getTestsPage()
    {
        try {
            $testFiles = [];
            $testPaths = [
                'Feature' => base_path('tests/Feature'),
                'Unit' => base_path('tests/Unit')
            ];
            
            foreach ($testPaths as $type => $path) {
                if (File::exists($path)) {
                    $files = File::allFiles($path);
                    foreach ($files as $file) {
                        $testFiles[] = [
                            'name' => $file->getFilename(),
                            'type' => $type,
                            'path' => $file->getPathname(),
                            'size' => $this->formatBytes($file->getSize())
                        ];
                    }
                }
            }
            
            return view('developer.tests', [
                'tests' => $testFiles,
                'total' => count($testFiles)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تشغيل الاختبارات
     */
    public function executeTests(Request $request)
    {
        try {
            $filter = $request->input('filter', '');
            $output = [];
            $returnVar = 0;
            
            $command = 'cd ' . base_path() . ' && php artisan test';
            if ($filter) {
                $command .= ' --filter=' . $filter;
            }
            
            exec($command, $output, $returnVar);
            
            return back()->with('success', 'تم تشغيل الاختبارات')->with('output', implode("\n", $output));
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة قائمة Routes
     */
    public function getRoutesListPage()
    {
        try {
            $routes = [];
            foreach (\Route::getRoutes() as $route) {
                $routes[] = [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => implode(', ', $route->middleware())
                ];
            }
            
            return view('developer.routes-list', [
                'routes' => $routes,
                'total' => count($routes)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة معلومات الخادم
     */
    public function getServerInfoPage()
    {
        try {
            $data = [
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
                'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
                'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
                'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
                'php_extensions' => get_loaded_extensions(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'disk_free_space' => $this->formatBytes(disk_free_space('/')),
                'disk_total_space' => $this->formatBytes(disk_total_space('/'))
            ];
            
            return view('developer.server-info', $data);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * صفحة عارض السجلات
     */
    public function getLogsViewerPage()
    {
        try {
            $logPath = storage_path('logs');
            $logFiles = [];
            
            if (File::exists($logPath)) {
                $files = File::files($logPath);
                foreach ($files as $file) {
                    if (str_ends_with($file->getFilename(), '.log')) {
                        $logFiles[] = [
                            'name' => $file->getFilename(),
                            'size' => $this->formatBytes($file->getSize()),
                            'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                            'path' => $file->getPathname()
                        ];
                    }
                }
            }
            
            return view('developer.logs-viewer', [
                'logs' => $logFiles,
                'total' => count($logFiles)
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * دالة مساعدة لتنسيق حجم الملفات
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
