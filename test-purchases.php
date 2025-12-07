<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = [
    // Dashboard
    '/purchases' => 'Dashboard (Redirect)',
    '/purchases/dashboard' => 'Dashboard',
    
    // Suppliers
    '/purchases/suppliers' => 'Suppliers Index',
    '/purchases/suppliers/create' => 'Suppliers Create',
    
    // Orders
    '/purchases/orders' => 'Orders Index',
    '/purchases/orders/create' => 'Orders Create',
    
    // Receipts
    '/purchases/receipts' => 'Receipts Index',
    '/purchases/receipts/create' => 'Receipts Create',
    
    // Invoices
    '/purchases/invoices' => 'Invoices Index',
    '/purchases/invoices/create' => 'Invoices Create',
    
    // Reports
    '/purchases/reports/orders' => 'Report: Orders',
    '/purchases/reports/by-supplier' => 'Report: By Supplier',
    '/purchases/reports/by-item' => 'Report: By Item',
    '/purchases/reports/due-invoices' => 'Report: Due Invoices',
    '/purchases/reports/supplier-performance' => 'Report: Supplier Performance',
];

echo "=== 🧪 اختبار نظام المشتريات v4.1.0 ===\n\n";

$passed = 0;
$failed = 0;
$errors = [];

foreach ($routes as $route => $description) {
    try {
        $request = Illuminate\Http\Request::create($route, 'GET');
        $response = $kernel->handle($request);
        $statusCode = $response->getStatusCode();
        
        // 200 = OK, 302 = Redirect (OK for dashboard)
        if ($statusCode == 200 || $statusCode == 302) {
            echo "✅ [$statusCode] $description\n";
            echo "   URL: $route\n\n";
            $passed++;
        } else {
            echo "❌ [$statusCode] $description\n";
            echo "   URL: $route\n\n";
            $failed++;
            $errors[] = [
                'route' => $route,
                'description' => $description,
                'status' => $statusCode,
                'content' => substr($response->getContent(), 0, 500)
            ];
        }
        
        $kernel->terminate($request, $response);
        
    } catch (\Exception $e) {
        echo "❌ [ERROR] $description\n";
        echo "   URL: $route\n";
        echo "   Error: " . $e->getMessage() . "\n\n";
        $failed++;
        $errors[] = [
            'route' => $route,
            'description' => $description,
            'error' => $e->getMessage()
        ];
    }
}

echo "\n=== 📊 النتائج ===\n";
echo "✅ نجح: $passed\n";
echo "❌ فشل: $failed\n";
echo "📈 النسبة: " . round(($passed / count($routes)) * 100, 2) . "%\n\n";

if (!empty($errors)) {
    echo "=== ❌ الأخطاء ===\n";
    foreach ($errors as $error) {
        echo "\nRoute: {$error['route']}\n";
        echo "Description: {$error['description']}\n";
        if (isset($error['status'])) {
            echo "Status: {$error['status']}\n";
            echo "Content Preview: " . substr($error['content'], 0, 200) . "...\n";
        }
        if (isset($error['error'])) {
            echo "Error: {$error['error']}\n";
        }
        echo "---\n";
    }
}

echo "\n=== ✅ الاختبار مكتمل ===\n";
