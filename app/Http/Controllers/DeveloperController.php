<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class DeveloperController extends Controller
{
    /**
     * صفحة نظام المطور الرئيسية
     */
    public function index()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ];

        return view('developer.index', compact('systemInfo'));
    }

    /**
     * تشغيل Migrations
     */
    public function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تشغيل Migrations بنجاح',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تشغيل Migrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تشغيل Seeders
     */
    public function runSeeders()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تشغيل Seeders بنجاح',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تشغيل Seeders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * معلومات قاعدة البيانات
     */
    public function databaseInfo()
    {
        try {
            $connection = DB::connection();
            $dbName = $connection->getDatabaseName();
            
            // الحصول على جميع الجداول
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            
            // معلومات الاتصال
            $info = [
                'database_name' => $dbName,
                'connection' => config('database.default'),
                'driver' => config('database.connections.' . config('database.default') . '.driver'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'port' => config('database.connections.' . config('database.default') . '.port'),
                'table_count' => $tableCount,
                'tables' => array_map(function($table) {
                    return array_values((array)$table)[0];
                }, $tables)
            ];

            return view('developer.database-info', compact('info'));
        } catch (\Exception $e) {
            return back()->with('error', 'فشل الحصول على معلومات قاعدة البيانات: ' . $e->getMessage());
        }
    }

    /**
     * تحسين قاعدة البيانات
     */
    public function optimizeDatabase()
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحسين قاعدة البيانات والنظام بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحسين النظام',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * نسخ احتياطي لقاعدة البيانات
     */
    public function backupDatabase()
    {
        try {
            $dbName = DB::connection()->getDatabaseName();
            $backupFile = storage_path('backups/db_backup_' . date('Y-m-d_H-i-s') . '.sql');
            
            // إنشاء مجلد النسخ الاحتياطية إذا لم يكن موجوداً
            if (!File::exists(storage_path('backups'))) {
                File::makeDirectory(storage_path('backups'), 0755, true);
            }

            // تنفيذ النسخ الاحتياطي
            $command = sprintf(
                'mysqldump -u%s -p%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                $dbName,
                $backupFile
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء النسخة الاحتياطية بنجاح',
                    'file' => basename($backupFile)
                ]);
            } else {
                throw new \Exception('فشل تنفيذ أمر النسخ الاحتياطي');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء النسخة الاحتياطية',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * مسح جميع أنواع Cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح جميع أنواع Cache بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل مسح Cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تشغيل Laravel Pint
     */
    public function runPint()
    {
        try {
            exec('cd ' . base_path() . ' && ./vendor/bin/pint', $output, $returnVar);
            
            return response()->json([
                'success' => $returnVar === 0,
                'message' => $returnVar === 0 ? 'تم تنسيق الكود بنجاح' : 'فشل تنسيق الكود',
                'output' => implode("\n", $output)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تشغيل Pint',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تشغيل الاختبارات
     */
    public function runTests()
    {
        try {
            Artisan::call('test');
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تشغيل الاختبارات',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تشغيل الاختبارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض جميع Routes
     */
    public function showRoutes()
    {
        $routes = [];
        foreach (Route::getRoutes() as $route) {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        }

        return view('developer.routes', compact('routes'));
    }

    /**
     * معلومات النظام
     */
    public function systemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
            'database_version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];

        return view('developer.system-info', compact('info'));
    }

    /**
     * عرض السجلات (Logs)
     */
    public function showLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!File::exists($logFile)) {
                return view('developer.logs', ['logs' => 'لا توجد سجلات متاحة']);
            }

            // قراءة آخر 100 سطر من السجل
            $logs = collect(file($logFile))->reverse()->take(100)->reverse()->implode('');

            return view('developer.logs', compact('logs'));
        } catch (\Exception $e) {
            return back()->with('error', 'فشل قراءة السجلات: ' . $e->getMessage());
        }
    }
    
    // الدوال القديمة للتوافق
    public function telescope()
    {
        return redirect('/telescope');
    }
    
    public function horizon()
    {
        return redirect('/horizon');
    }
    
    public function debugbar()
    {
        return view('developer.debugbar');
    }
}
