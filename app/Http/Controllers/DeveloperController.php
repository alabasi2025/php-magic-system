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
     * عرض لوحة تحكم المطور
     */
    public function getDashboard()
    {
        try {
            $data = [
                'system_overview' => $this->getSystemOverview(),
                'quick_stats' => $this->getQuickStats(),
                'recent_activity' => $this->getRecentActivity(),
                'version' => 'v2.8.1'
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
}
