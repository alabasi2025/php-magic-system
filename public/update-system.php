<?php
/**
 * SEMOP System Update Script
 * Version: 2.7.0
 * 
 * This script pulls latest changes from GitHub and clears caches
 * URL: https://mediumblue-albatross-218540.hostingersite.com/update-system.php?key=semop_secure_2024
 */

// Security key
$secret_key = 'semop_secure_2024';
$provided_key = $_GET['key'] ?? '';

// Check authentication
if ($provided_key !== $secret_key) {
    http_response_code(403);
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid security key'
    ]));
}

// Get current directory
$project_dir = dirname(__DIR__);
chdir($project_dir);

$output = [];
$errors = [];

// Execute git pull
exec('git pull origin main 2>&1', $git_output, $git_return);
$output['git_pull'] = $git_output;
$output['git_status'] = $git_return === 0 ? 'success' : 'error';

// Clear Laravel caches
$cache_commands = [
    'cache:clear' => 'Application Cache',
    'config:clear' => 'Configuration Cache',
    'route:clear' => 'Route Cache',
    'view:clear' => 'View Cache'
];

foreach ($cache_commands as $command => $description) {
    $cmd_output = [];
    exec("php artisan {$command} 2>&1", $cmd_output, $return_var);
    $output['caches'][$command] = [
        'description' => $description,
        'status' => $return_var === 0 ? 'success' : 'error',
        'output' => $cmd_output
    ];
}

// Get current version from README
$readme_content = file_exists('README.md') ? file_get_contents('README.md') : '';
preg_match('/Version:\s*([0-9.]+)/', $readme_content, $version_match);
$current_version = $version_match[1] ?? 'unknown';

// Prepare response
$response = [
    'status' => 'success',
    'message' => 'System updated successfully',
    'version' => $current_version,
    'timestamp' => date('Y-m-d H:i:s'),
    'details' => $output
];

// Return JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
