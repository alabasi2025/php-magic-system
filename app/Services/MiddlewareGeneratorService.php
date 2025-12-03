<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * 🛡️ Middleware Generator Service v3.28.0
 * 
 * خدمة توليد Middleware بشكل ذكي ومتقدم
 * 
 * @version 3.28.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 * @author Manus AI
 */
class MiddlewareGeneratorService
{
    /**
     * أنواع Middleware المدعومة
     */
    const TYPES = [
        'auth' => 'Authentication Middleware',
        'permission' => 'Permission/Authorization Middleware',
        'rate_limit' => 'Rate Limiting Middleware',
        'logging' => 'Request Logging Middleware',
        'cors' => 'CORS Middleware',
        'validation' => 'Request Validation Middleware',
        'cache' => 'Response Cache Middleware',
        'transform' => 'Request/Response Transformation Middleware',
        'security' => 'Security Headers Middleware',
        'custom' => 'Custom Middleware',
    ];

    /**
     * مسار حفظ Middleware
     */
    protected string $middlewarePath = 'app/Http/Middleware';

    /**
     * مسار حفظ القوالب
     */
    protected string $templatesPath = 'app/Templates/Middleware';

    /**
     * توليد Middleware من وصف نصي
     *
     * @param string $description
     * @param array $options
     * @return array
     */
    public function generateFromText(string $description, array $options = []): array
    {
        // تحليل الوصف لاستخراج المعلومات
        $analysis = $this->analyzeDescription($description);

        // تحديد نوع Middleware
        $type = $options['type'] ?? $analysis['type'] ?? 'custom';

        // توليد اسم Middleware
        $name = $options['name'] ?? $analysis['name'] ?? $this->generateName($description);

        // توليد المحتوى
        $content = $this->generateContent($name, $type, $description, $options);

        return [
            'name' => $name,
            'type' => $type,
            'description' => $description,
            'content' => $content,
            'path' => $this->getFilePath($name),
            'namespace' => 'App\\Http\\Middleware',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * توليد Middleware من JSON Schema
     *
     * @param array $schema
     * @return array
     */
    public function generateFromJson(array $schema): array
    {
        $name = $schema['name'] ?? 'CustomMiddleware';
        $type = $schema['type'] ?? 'custom';
        $description = $schema['description'] ?? 'Custom middleware';
        $options = $schema['options'] ?? [];

        $content = $this->generateContent($name, $type, $description, $options);

        return [
            'name' => $name,
            'type' => $type,
            'description' => $description,
            'content' => $content,
            'path' => $this->getFilePath($name),
            'namespace' => 'App\\Http\\Middleware',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * توليد Middleware من قالب
     *
     * @param string $templateName
     * @param array $variables
     * @return array
     */
    public function generateFromTemplate(string $templateName, array $variables = []): array
    {
        $templatePath = base_path("{$this->templatesPath}/{$templateName}.php");

        if (!File::exists($templatePath)) {
            throw new \Exception("Template not found: {$templateName}");
        }

        $template = File::get($templatePath);
        $content = $this->replaceVariables($template, $variables);

        $name = $variables['name'] ?? Str::studly($templateName) . 'Middleware';

        return [
            'name' => $name,
            'type' => $variables['type'] ?? 'custom',
            'description' => $variables['description'] ?? "Generated from template: {$templateName}",
            'content' => $content,
            'path' => $this->getFilePath($name),
            'namespace' => 'App\\Http\\Middleware',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * توليد محتوى Middleware
     *
     * @param string $name
     * @param string $type
     * @param string $description
     * @param array $options
     * @return string
     */
    protected function generateContent(string $name, string $type, string $description, array $options = []): string
    {
        // تحديد القالب المناسب
        $template = $this->getTemplate($type);

        // استبدال المتغيرات
        $variables = array_merge([
            'name' => $name,
            'description' => $description,
            'namespace' => 'App\\Http\\Middleware',
            'version' => '3.28.0',
            'date' => now()->toDateString(),
            'author' => 'Manus AI',
        ], $options);

        return $this->replaceVariables($template, $variables);
    }

    /**
     * الحصول على القالب المناسب
     *
     * @param string $type
     * @return string
     */
    protected function getTemplate(string $type): string
    {
        return match ($type) {
            'auth' => $this->getAuthTemplate(),
            'permission' => $this->getPermissionTemplate(),
            'rate_limit' => $this->getRateLimitTemplate(),
            'logging' => $this->getLoggingTemplate(),
            'cors' => $this->getCorsTemplate(),
            'validation' => $this->getValidationTemplate(),
            'cache' => $this->getCacheTemplate(),
            'transform' => $this->getTransformTemplate(),
            'security' => $this->getSecurityTemplate(),
            default => $this->getCustomTemplate(),
        };
    }

    /**
     * قالب Authentication Middleware
     */
    protected function getAuthTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log authentication attempt
        Log::info('Authentication check', [
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ]);

        // Check authentication
        if (!$this->isAuthenticated($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'Please provide valid credentials'
            ], 401);
        }

        return $next($request);
    }

    /**
     * Check if request is authenticated
     *
     * @param Request $request
     * @return bool
     */
    protected function isAuthenticated(Request $request): bool
    {
        // TODO: Implement your authentication logic
        // Example: Check for token in header
        $token = $request->header('Authorization');
        
        return !empty($token);
    }
}
PHP;
    }

    /**
     * قالب Permission Middleware
     */
    protected function getPermissionTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        // Check if user has permission
        if ($permission && !$this->hasPermission($request, $permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied',
                'error' => "You don't have permission: {$permission}",
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }

    /**
     * Check if user has permission
     *
     * @param Request $request
     * @param string $permission
     * @return bool
     */
    protected function hasPermission(Request $request, string $permission): bool
    {
        // TODO: Implement your permission check logic
        // Example: Check user permissions from database
        // $user = $request->user();
        // return $user && $user->hasPermissionTo($permission);
        
        return true;
    }
}
PHP;
    }

    /**
     * قالب Rate Limit Middleware
     */
    protected function getRateLimitTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Maximum number of attempts
     */
    protected int $maxAttempts = 60;

    /**
     * Decay time in minutes
     */
    protected int $decayMinutes = 1;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $this->resolveRequestSignature($request);
        
        $attempts = Cache::get($key, 0);

        if ($attempts >= $this->maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests',
                'error' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $this->decayMinutes * 60
            ], 429);
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($this->decayMinutes));

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $this->maxAttempts,
            'X-RateLimit-Remaining' => max(0, $this->maxAttempts - $attempts - 1),
        ]);
    }

    /**
     * Resolve request signature
     *
     * @param Request $request
     * @return string
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return 'rate_limit:' . $request->ip();
    }
}
PHP;
    }

    /**
     * قالب Logging Middleware
     */
    protected function getLoggingTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        // Log request
        Log::info('Request received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Log response
        Log::info('Response sent', [
            'status' => $response->status(),
            'duration_ms' => $duration,
        ]);

        return $response;
    }
}
PHP;
    }

    /**
     * قالب CORS Middleware
     */
    protected function getCorsTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle preflight request
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->header('Access-Control-Max-Age', '86400');
        }

        $response = $next($request);

        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
}
PHP;
    }

    /**
     * قالب Validation Middleware
     */
    protected function getValidationTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $rules = $this->getRules($request);

        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        return $next($request);
    }

    /**
     * Get validation rules
     *
     * @param Request $request
     * @return array
     */
    protected function getRules(Request $request): array
    {
        // TODO: Define your validation rules
        return [];
    }
}
PHP;
    }

    /**
     * قالب Cache Middleware
     */
    protected function getCacheTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Cache duration in minutes
     */
    protected int $cacheDuration = 60;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $key = $this->getCacheKey($request);

        // Check if response is cached
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $next($request);

        // Cache the response
        if ($response->isSuccessful()) {
            Cache::put($key, $response, now()->addMinutes($this->cacheDuration));
        }

        return $response;
    }

    /**
     * Get cache key for request
     *
     * @param Request $request
     * @return string
     */
    protected function getCacheKey(Request $request): string
    {
        return 'response_cache:' . md5($request->fullUrl());
    }
}
PHP;
    }

    /**
     * قالب Transform Middleware
     */
    protected function getTransformTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Transform request
        $this->transformRequest($request);

        $response = $next($request);

        // Transform response
        return $this->transformResponse($response);
    }

    /**
     * Transform request data
     *
     * @param Request $request
     * @return void
     */
    protected function transformRequest(Request $request): void
    {
        // TODO: Implement request transformation logic
    }

    /**
     * Transform response data
     *
     * @param mixed $response
     * @return mixed
     */
    protected function transformResponse($response)
    {
        // TODO: Implement response transformation logic
        return $response;
    }
}
PHP;
    }

    /**
     * قالب Security Middleware
     */
    protected function getSecurityTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add security headers
        return $response
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-XSS-Protection', '1; mode=block')
            ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->header('Content-Security-Policy', "default-src 'self'");
    }
}
PHP;
    }

    /**
     * قالب Custom Middleware
     */
    protected function getCustomTemplate(): string
    {
        return <<<'PHP'
<?php

namespace {{namespace}};

use Closure;
use Illuminate\Http\Request;

/**
 * {{name}}
 * 
 * {{description}}
 * Auto-generated by Middleware Generator v{{version}}
 * 
 * @version {{version}}
 * @date {{date}}
 * @author {{author}}
 */
class {{name}}
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // TODO: Implement your middleware logic here
        
        // Before request processing
        // ...

        $response = $next($request);

        // After request processing
        // ...

        return $response;
    }
}
PHP;
    }

    /**
     * استبدال المتغيرات في القالب
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    protected function replaceVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }

        return $template;
    }

    /**
     * تحليل الوصف النصي
     *
     * @param string $description
     * @return array
     */
    protected function analyzeDescription(string $description): array
    {
        $description = strtolower($description);
        $type = 'custom';
        $name = null;

        // تحديد النوع من الوصف
        if (str_contains($description, 'auth') || str_contains($description, 'مصادقة')) {
            $type = 'auth';
        } elseif (str_contains($description, 'permission') || str_contains($description, 'صلاحية')) {
            $type = 'permission';
        } elseif (str_contains($description, 'rate') || str_contains($description, 'limit') || str_contains($description, 'معدل')) {
            $type = 'rate_limit';
        } elseif (str_contains($description, 'log') || str_contains($description, 'تسجيل')) {
            $type = 'logging';
        } elseif (str_contains($description, 'cors')) {
            $type = 'cors';
        } elseif (str_contains($description, 'valid') || str_contains($description, 'تحقق')) {
            $type = 'validation';
        } elseif (str_contains($description, 'cache') || str_contains($description, 'تخزين')) {
            $type = 'cache';
        } elseif (str_contains($description, 'transform') || str_contains($description, 'تحويل')) {
            $type = 'transform';
        } elseif (str_contains($description, 'security') || str_contains($description, 'أمان')) {
            $type = 'security';
        }

        return [
            'type' => $type,
            'name' => $name,
        ];
    }

    /**
     * توليد اسم Middleware
     *
     * @param string $description
     * @return string
     */
    protected function generateName(string $description): string
    {
        // استخراج الكلمات المهمة
        $words = preg_split('/\s+/', $description);
        $words = array_filter($words, fn($word) => strlen($word) > 3);
        $words = array_slice($words, 0, 3);

        $name = implode('', array_map('ucfirst', $words));

        return $name . 'Middleware';
    }

    /**
     * الحصول على مسار الملف
     *
     * @param string $name
     * @return string
     */
    protected function getFilePath(string $name): string
    {
        return base_path("{$this->middlewarePath}/{$name}.php");
    }

    /**
     * حفظ Middleware إلى الملف
     *
     * @param array $middleware
     * @return bool
     */
    public function save(array $middleware): bool
    {
        $path = $middleware['path'];
        $content = $middleware['content'];

        // إنشاء المجلد إذا لم يكن موجوداً
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return File::put($path, $content) !== false;
    }

    /**
     * التحقق من صحة Middleware
     *
     * @param array $middleware
     * @return array
     */
    public function validate(array $middleware): array
    {
        $errors = [];
        $warnings = [];

        // التحقق من الاسم
        if (empty($middleware['name'])) {
            $errors[] = 'Middleware name is required';
        }

        // التحقق من المحتوى
        if (empty($middleware['content'])) {
            $errors[] = 'Middleware content is empty';
        }

        // التحقق من صحة PHP Syntax
        if (!empty($middleware['content'])) {
            $tempFile = tempnam(sys_get_temp_dir(), 'middleware_');
            file_put_contents($tempFile, $middleware['content']);
            
            exec("php -l {$tempFile} 2>&1", $output, $returnCode);
            
            if ($returnCode !== 0) {
                $errors[] = 'PHP syntax error: ' . implode("\n", $output);
            }
            
            unlink($tempFile);
        }

        // التحقق من وجود handle method
        if (!str_contains($middleware['content'], 'public function handle')) {
            $errors[] = 'Middleware must have a handle() method';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * الحصول على قائمة الأنواع المدعومة
     *
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return self::TYPES;
    }
}
