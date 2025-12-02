<?php
/**
 * سكريبت نشر الملفات من GitHub
 * الاستخدام: https://domain.com/deploy-files.php?key=YOUR_KEY
 */

$DEPLOY_KEY = '561d389fbbc41455bbb4afa6884f4b70b2c7d211e806ba84d0789a6def95b343';

// التحقق من المفتاح
if (!isset($_GET['key']) || $_GET['key'] !== $DEPLOY_KEY) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

$results = [];

// الملفات المطلوب تحديثها
$files = [
    'app/Http/Controllers/DeveloperController.php' => 'https://raw.githubusercontent.com/alabasi2025/php-magic-system/main/app/Http/Controllers/DeveloperController.php',
    'routes/developer.php' => 'https://raw.githubusercontent.com/alabasi2025/php-magic-system/main/routes/developer.php',
    'routes/web.php' => 'https://raw.githubusercontent.com/alabasi2025/php-magic-system/main/routes/web.php',
    'config/app.php' => 'https://raw.githubusercontent.com/alabasi2025/php-magic-system/main/config/app.php',
];

foreach ($files as $localPath => $githubUrl) {
    $fullPath = __DIR__ . '/../' . $localPath;
    $dir = dirname($fullPath);
    
    // إنشاء المجلد إذا لم يكن موجوداً
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // تحميل الملف من GitHub
    $content = @file_get_contents($githubUrl);
    
    if ($content !== false) {
        file_put_contents($fullPath, $content);
        $results[$localPath] = '✅ تم التحديث';
    } else {
        $results[$localPath] = '❌ فشل التحميل';
    }
}

// تحديث Cache
exec('cd ' . __DIR__ . '/.. && php artisan route:clear 2>&1', $output1);
exec('cd ' . __DIR__ . '/.. && php artisan config:clear 2>&1', $output2);
exec('cd ' . __DIR__ . '/.. && php artisan cache:clear 2>&1', $output3);

$results['cache'] = '✅ تم تحديث Cache';

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'files' => $results,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
