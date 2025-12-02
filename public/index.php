<?php

// Check PHP Version
if (version_compare(PHP_VERSION, '8.2', '<')) {
    die('<h1>❌ خطأ في إصدار PHP</h1><p>هذا المشروع يتطلب PHP 8.2 أو أعلى</p><p>الإصدار الحالي: ' . PHP_VERSION . '</p><p>يرجى تحديث PHP على الخادم</p>');
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
