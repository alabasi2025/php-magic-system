<?php

return [
    'api_key' => env('MANUS_API_KEY'),
    'api_url' => env('MANUS_API_URL', 'https://api.manus.im/v1'),
    
    'defaults' => [
        'model' => env('MANUS_DEFAULT_MODEL', 'gpt-4.1-mini'),
        'max_tokens' => env('MANUS_MAX_TOKENS', 2000),
        'temperature' => env('MANUS_TEMPERATURE', 0.7),
        'timeout' => env('MANUS_TIMEOUT', 60),
    ],
    
    'logging' => [
        'enabled' => env('MANUS_LOGGING_ENABLED', true),
        'log_requests' => env('MANUS_LOG_REQUESTS', true),
        'log_responses' => env('MANUS_LOG_RESPONSES', true),
        'log_errors' => env('MANUS_LOG_ERRORS', true),
    ],
    
    'queue' => [
        'enabled' => env('MANUS_QUEUE_ENABLED', false),
        'connection' => env('MANUS_QUEUE_CONNECTION', 'database'),
        'queue_name' => env('MANUS_QUEUE_NAME', 'manus'),
    ],
    
    'webhooks' => [
        'enabled' => env('MANUS_WEBHOOKS_ENABLED', true),
        'secret' => env('MANUS_WEBHOOK_SECRET'),
    ],
    
    'rate_limit' => [
        'enabled' => env('MANUS_RATE_LIMIT_ENABLED', true),
        'max_requests_per_minute' => env('MANUS_MAX_REQUESTS_PER_MINUTE', 60),
    ],
    
    'cost_tracking' => [
        'enabled' => env('MANUS_COST_TRACKING_ENABLED', true),
        'currency' => env('MANUS_COST_CURRENCY', 'USD'),
    ],
];
