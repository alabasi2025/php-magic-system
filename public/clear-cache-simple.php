<?php
// مفتاح الأمان
if (($_GET['key'] ?? '') !== 'semop_clear_2024') {
    die('Unauthorized');
}

echo "<h1>تحديث Cache</h1>";
echo "<pre>";

// تنفيذ الأوامر
$commands = [
    'php artisan route:clear',
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear'
];

foreach ($commands as $cmd) {
    echo "\n=== تنفيذ: $cmd ===\n";
    $output = shell_exec("cd " . dirname(__DIR__) . " && $cmd 2>&1");
    echo $output;
}

echo "\n\n✅ تم تحديث جميع أنواع Cache!";
echo "</pre>";
