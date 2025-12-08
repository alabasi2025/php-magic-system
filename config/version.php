<?php

/**
 * System Version Configuration
 * يقرأ رقم الإصدار تلقائياً من Git tags
 */

// محاولة قراءة آخر tag من Git
$gitVersion = null;
try {
    if (function_exists('exec') && is_dir(base_path('.git'))) {
        exec('cd ' . base_path() . ' && git describe --tags --abbrev=0 2>/dev/null', $output, $returnCode);
        if ($returnCode === 0 && !empty($output[0])) {
            $gitVersion = trim($output[0]);
        }
    }
} catch (\Exception $e) {
    // في حالة فشل قراءة Git، استخدم القيمة الافتراضية
}

// إزالة حرف v من البداية إن وجد
$versionNumber = $gitVersion ? ltrim($gitVersion, 'v') : '5.0.1';
$versionFull = $gitVersion ?: 'v' . $versionNumber;

return [
    'version' => $versionFull,
    'number' => $versionNumber,
    'release_date' => date('Y-m-d'),
    'codename' => 'Purchases & Inventory Suite',
    'git_enabled' => $gitVersion !== null,
];
