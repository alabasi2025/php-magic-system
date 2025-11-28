<?php
/**
 * سكريبت إصلاح نظام المطور
 * يقوم بتحديث cache وإصلاح المشاكل
 */

// مفتاح الأمان
$secret_key = $_GET['key'] ?? '';
if ($secret_key !== 'semop_fix_2024') {
    die(json_encode(['error' => 'Unauthorized']));
}

$results = [];

try {
    // 1. تحديث Composer Autoload
    $results[] = ['step' => 'Composer Autoload', 'status' => 'skipped', 'message' => 'يجب تشغيل يدوياً: composer dump-autoload'];
    
    // 2. مسح جميع أنواع Cache
    $cacheCommands = [
        'config:clear' => 'Config Cache',
        'route:clear' => 'Route Cache',
        'view:clear' => 'View Cache',
        'cache:clear' => 'Application Cache',
        'optimize:clear' => 'Optimize Cache'
    ];
    
    foreach ($cacheCommands as $command => $name) {
        try {
            \Illuminate\Support\Facades\Artisan::call($command);
            $results[] = ['step' => $name, 'status' => 'success', 'output' => \Illuminate\Support\Facades\Artisan::output()];
        } catch (Exception $e) {
            $results[] = ['step' => $name, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }
    
    // 3. التحقق من وجود الملفات
    $filesToCheck = [
        'app/Http/Controllers/DeveloperController.php',
        'routes/developer.php',
        'resources/views/developer/dashboard.blade.php'
    ];
    
    foreach ($filesToCheck as $file) {
        $fullPath = base_path($file);
        $exists = file_exists($fullPath);
        $results[] = [
            'step' => "Check File: $file",
            'status' => $exists ? 'success' : 'error',
            'exists' => $exists,
            'path' => $fullPath
        ];
    }
    
    // 4. التحقق من Routes
    try {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $developerRoutes = [];
        foreach ($routes as $route) {
            if (str_starts_with($route->uri(), 'developer')) {
                $developerRoutes[] = $route->uri();
            }
        }
        $results[] = [
            'step' => 'Developer Routes',
            'status' => 'success',
            'count' => count($developerRoutes),
            'routes' => array_slice($developerRoutes, 0, 10)
        ];
    } catch (Exception $e) {
        $results[] = ['step' => 'Developer Routes', 'status' => 'error', 'error' => $e->getMessage()];
    }
    
    // 5. معلومات النظام
    $results[] = [
        'step' => 'System Info',
        'status' => 'success',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'environment' => app()->environment()
    ];
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'تم تنفيذ جميع الخطوات',
        'results' => $results,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
