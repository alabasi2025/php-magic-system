<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks - SEMOP v2.8.1 Automated Maintenance
|--------------------------------------------------------------------------
|
| Laravel Task Scheduler - 8 مهام مجدولة للصيانة التلقائية
| تم التكوين: 29 نوفمبر 2025
|
*/

// ============================================================================
// المهام اليومية (Daily Tasks)
// ============================================================================

// 1. تنظيف وإعادة بناء Cache - يومياً الساعة 00:00
Schedule::command('optimize:clear')
    ->daily()
    ->at('00:00')
    ->timezone('Africa/Cairo')
    ->name('daily-cache-clear')
    ->onSuccess(function () {
        info('✅ Daily cache clear completed successfully');
    })
    ->onFailure(function () {
        info('❌ Daily cache clear failed');
    });

Schedule::command('optimize')
    ->daily()
    ->at('00:00')
    ->timezone('Africa/Cairo')
    ->name('daily-cache-rebuild')
    ->onSuccess(function () {
        info('✅ Daily cache rebuild completed successfully');
    })
    ->onFailure(function () {
        info('❌ Daily cache rebuild failed');
    });

// 2. تنظيف Sessions القديمة - يومياً الساعة 04:00
Schedule::call(function () {
    $sessionPath = storage_path('framework/sessions');
    
    if (!File::exists($sessionPath)) {
        info('⚠️ Session path does not exist');
        return;
    }
    
    $files = File::files($sessionPath);
    $deleted = 0;
    $sevenDaysAgo = now()->subDays(7)->timestamp;
    
    foreach ($files as $file) {
        if ($file->getMTime() < $sevenDaysAgo) {
            try {
                File::delete($file);
                $deleted++;
            } catch (\Exception $e) {
                info("⚠️ Failed to delete session file: {$file->getFilename()}");
            }
        }
    }
    
    info("✅ Cleaned up {$deleted} old session files (older than 7 days)");
})
    ->daily()
    ->at('04:00')
    ->timezone('Africa/Cairo')
    ->name('cleanup-old-sessions');

// 3. مراقبة قاعدة البيانات - يومياً الساعة 08:00
Schedule::call(function () {
    try {
        // عدد الجداول
        $tables = DB::select('SHOW TABLES');
        $tableCount = count($tables);
        
        // حجم قاعدة البيانات
        $dbSize = DB::select("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.TABLES 
            WHERE table_schema = DATABASE()
        ");
        
        $sizeMB = $dbSize[0]->size_mb ?? 'N/A';
        
        // اختبار الاتصال
        DB::connection()->getPdo();
        
        info("✅ Database Health Check: {$tableCount} tables, {$sizeMB} MB, Connection: OK");
        
    } catch (\Exception $e) {
        info("❌ Database monitoring failed: " . $e->getMessage());
    }
})
    ->daily()
    ->at('08:00')
    ->timezone('Africa/Cairo')
    ->name('monitor-database');

// ============================================================================
// المهام الأسبوعية (Weekly Tasks)
// ============================================================================

// 4. تنظيف ملفات Cache القديمة - أسبوعياً (الأحد) الساعة 00:00
Schedule::call(function () {
    $cachePath = storage_path('framework/cache/data');
    
    if (!File::exists($cachePath)) {
        info('⚠️ Cache data path does not exist');
        return;
    }
    
    $files = File::allFiles($cachePath);
    $deleted = 0;
    $sevenDaysAgo = now()->subDays(7)->timestamp;
    
    foreach ($files as $file) {
        if ($file->getMTime() < $sevenDaysAgo) {
            try {
                File::delete($file);
                $deleted++;
            } catch (\Exception $e) {
                info("⚠️ Failed to delete cache file: {$file->getFilename()}");
            }
        }
    }
    
    info("✅ Cleaned up {$deleted} old cache files (older than 7 days)");
})
    ->weekly()
    ->sundays()
    ->at('00:00')
    ->timezone('Africa/Cairo')
    ->name('cleanup-old-cache-files');

// 5. تحديث شامل للـ Cache - أسبوعياً (الأحد) الساعة 03:00
Schedule::call(function () {
    try {
        Artisan::call('optimize:clear');
        info('✅ Weekly cache clear completed');
        
        Artisan::call('optimize');
        info('✅ Weekly cache rebuild completed');
        
        info('✅ Weekly cache refresh completed successfully');
        
    } catch (\Exception $e) {
        info("❌ Weekly cache refresh failed: " . $e->getMessage());
    }
})
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->timezone('Africa/Cairo')
    ->name('weekly-cache-refresh');

// ============================================================================
// المهام الشهرية (Monthly Tasks)
// ============================================================================

// 6. تنظيف السجلات القديمة - شهرياً (أول يوم) الساعة 00:00
Schedule::call(function () {
    $logPath = storage_path('logs');
    
    if (!File::exists($logPath)) {
        info('⚠️ Logs path does not exist');
        return;
    }
    
    $files = File::files($logPath);
    $deleted = 0;
    $thirtyDaysAgo = now()->subDays(30)->timestamp;
    
    foreach ($files as $file) {
        // احتفظ بملف laravel.log الحالي
        if ($file->getFilename() === 'laravel.log') {
            continue;
        }
        
        if ($file->getMTime() < $thirtyDaysAgo) {
            try {
                File::delete($file);
                $deleted++;
            } catch (\Exception $e) {
                info("⚠️ Failed to delete log file: {$file->getFilename()}");
            }
        }
    }
    
    info("✅ Cleaned up {$deleted} old log files (older than 30 days)");
})
    ->monthly()
    ->timezone('Africa/Cairo')
    ->name('cleanup-old-logs');

// ============================================================================
// المهام الدورية (Hourly Tasks)
// ============================================================================

// 7. فحص صحة النظام - كل ساعة
Schedule::call(function () {
    try {
        $status = [
            'timestamp' => now()->toDateTimeString(),
            'cache_enabled' => config('cache.default') !== 'null',
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
            'disk_free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2),
            'disk_total_gb' => round(disk_total_space('/') / 1024 / 1024 / 1024, 2),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        ];
        
        info("✅ System Health Check: " . json_encode($status, JSON_UNESCAPED_UNICODE));
        
    } catch (\Exception $e) {
        info("❌ Health check failed: " . $e->getMessage());
    }
})
    ->hourly()
    ->timezone('Africa/Cairo')
    ->name('health-check');

// ============================================================================
// ملاحظات مهمة
// ============================================================================
/*
 * لتفعيل هذه المهام، أضف cron job واحد في Hostinger hPanel:
 * 
 * التوقيت: * * * * *
 * الأمر: cd /home/u306850950/domains/mediumblue-albatross-218540.hostingersite.com && php artisan schedule:run >> /dev/null 2>&1
 * 
 * للتحقق من المهام المجدولة:
 * php artisan schedule:list
 * 
 * لاختبار مهمة معينة:
 * php artisan schedule:test --name=health-check
 * 
 * لعرض السجلات:
 * tail -f storage/logs/laravel.log
 */
