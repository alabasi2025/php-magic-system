<?php
// Auto Deploy Script for SEMOP
// This script pulls latest changes from GitHub

// Security: Only allow from specific IPs or with secret key
$secret_key = 'semop_deploy_2024';
$provided_key = $_GET['key'] ?? '';

if ($provided_key !== $secret_key) {
    http_response_code(403);
    die('Forbidden');
}

// Change to project directory
chdir('/home/u306850950/domains/mediumblue-albatross-218540.hostingersite.com/public_html');

// Execute git pull
$output = [];
$return_var = 0;

exec('git pull origin main 2>&1', $output, $return_var);

// Clear Laravel caches
exec('php artisan cache:clear 2>&1', $output);
exec('php artisan config:clear 2>&1', $output);
exec('php artisan route:clear 2>&1', $output);
exec('php artisan view:clear 2>&1', $output);

// Return response
header('Content-Type: application/json');
echo json_encode([
    'status' => $return_var === 0 ? 'success' : 'error',
    'output' => $output,
    'timestamp' => date('Y-m-d H:i:s')
]);
