<?php

namespace App\Services;

use App\Exceptions\MiddlewareGenerationException;
use App\Services\AI\ManusAIClient;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Throwable;

/**
 * MiddlewareGeneratorService
 *
 * الخدمة الرئيسية لتوليد Middleware في Laravel.
 * تدعم أنماط متعددة (Authentication, Authorization, Logging, Rate Limiting, CORS, Custom).
 *
 * The main service for generating Laravel Middleware.
 * Supports multiple patterns (Authentication, Authorization, Logging, Rate Limiting, CORS, Custom).
 *
 * @package App\Services
 * @version v3.28.0
 * @author Manus AI
 */
class MiddlewareGeneratorService
{
    // ثوابت لأنواع Middleware المدعومة
    // Constants for supported middleware types
    public const TYPE_AUTHENTICATION = 'authentication';
    public const TYPE_AUTHORIZATION = 'authorization';
    public const TYPE_LOGGING = 'logging';
    public const TYPE_RATE_LIMIT = 'rate_limit';
    public const TYPE_CORS = 'cors';
    public const TYPE_CUSTOM = 'custom';

    /**
     * @var ManusAIClient عميل Manus AI للتكامل مع الذكاء الاصطناعي.
     *                    Manus AI client for AI integration.
     */
    protected ManusAIClient $aiClient;

    /**
     * المسار الأساسي لـ Middleware.
     * The base path for middleware.
     *
     * @var string
     */
    protected string $middlewarePath = 'app/Http/Middleware/';

    /**
     * المسار الاحتياطي للملفات المولدة.
     * The backup path for generated files.
     *
     * @var string
     */
    protected string $backupPath = 'storage/app/generated/middlewares/';

    /**
     * MiddlewareGeneratorService constructor.
     *
     * @param ManusAIClient $aiClient عميل Manus AI.
     */
    public function __construct(ManusAIClient $aiClient)
    {
        $this->aiClient = $aiClient;
    }

    /**
     * توليد Middleware جديد بناءً على النوع المحدد.
     * Generates a new Middleware based on the specified type.
     *
     * @param string $name اسم Middleware (مثال: CheckApiAuth).
     *                     The name of the middleware (e.g., CheckApiAuth).
     * @param string $type نوع Middleware.
     *                     The type of the middleware.
     * @param array<string, mixed> $options خيارات إضافية للتوليد.
     *                                      Additional generation options.
     * @return string المسار الكامل للملف الذي تم إنشاؤه.
     *                The full path to the created file.
     * @throws MiddlewareGenerationException إذا فشل التوليد أو كان النوع غير مدعوم.
     *                                       If generation fails or the type is unsupported.
     */
    public function generateMiddleware(string $name, string $type, array $options = []): string
    {
        $name = $this->formatMiddlewareName($name);
        $type = strtolower($type);

        try {
            $content = match ($type) {
                self::TYPE_AUTHENTICATION => $this->generateAuthMiddleware($name, $options),
                self::TYPE_AUTHORIZATION => $this->generateAuthorizationMiddleware($name, $options),
                self::TYPE_LOGGING => $this->generateLoggingMiddleware($name, $options),
                self::TYPE_RATE_LIMIT => $this->generateRateLimitMiddleware($name, $options),
                self::TYPE_CORS => $this->generateCorsMiddleware($name, $options),
                self::TYPE_CUSTOM => $this->generateCustomMiddleware($name, $options['description'] ?? '', $options),
                default => throw new InvalidArgumentException("نوع Middleware غير مدعوم: {$type}. Unsupported middleware type: {$type}."),
            };

            $filePath = $this->getMiddlewareFilePath($name);
            $this->writeFile($filePath, $content);

            // حفظ نسخة احتياطية
            // Save backup copy
            $this->saveBackup($name, $content);

            return $filePath;
        } catch (InvalidArgumentException $e) {
            throw new MiddlewareGenerationException("خطأ في المدخلات: " . $e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            throw new MiddlewareGenerationException("فشل توليد Middleware '{$name}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * معاينة محتوى Middleware دون حفظه.
     * Preview the middleware content without saving it.
     *
     * @param string $name اسم Middleware.
     * @param string $type نوع Middleware.
     * @param array<string, mixed> $options خيارات إضافية.
     * @return string محتوى Middleware.
     * @throws MiddlewareGenerationException
     */
    public function previewMiddleware(string $name, string $type, array $options = []): string
    {
        $name = $this->formatMiddlewareName($name);
        $type = strtolower($type);

        try {
            return match ($type) {
                self::TYPE_AUTHENTICATION => $this->generateAuthMiddleware($name, $options),
                self::TYPE_AUTHORIZATION => $this->generateAuthorizationMiddleware($name, $options),
                self::TYPE_LOGGING => $this->generateLoggingMiddleware($name, $options),
                self::TYPE_RATE_LIMIT => $this->generateRateLimitMiddleware($name, $options),
                self::TYPE_CORS => $this->generateCorsMiddleware($name, $options),
                self::TYPE_CUSTOM => $this->generateCustomMiddleware($name, $options['description'] ?? '', $options),
                default => throw new InvalidArgumentException("نوع Middleware غير مدعوم: {$type}"),
            };
        } catch (Throwable $e) {
            throw new MiddlewareGenerationException("فشل معاينة Middleware: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * توليد Authentication Middleware.
     * Generates an Authentication middleware.
     *
     * @param string $name اسم Middleware.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Middleware.
     */
    protected function generateAuthMiddleware(string $name, array $options): string
    {
        $guard = $options['guard'] ?? 'web';
        $tokenType = $options['token_type'] ?? 'Bearer';
        $redirectRoute = $options['redirect_route'] ?? 'login';

        $prompt = "Generate a Laravel Authentication Middleware named {$name}. " .
                  "Requirements:\n" .
                  "- Check authentication using guard: {$guard}\n" .
                  "- Token type: {$tokenType}\n" .
                  "- Redirect to '{$redirectRoute}' on failure for web, return 401 JSON for API\n" .
                  "- Include proper error handling\n" .
                  "- Add PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Http\\Middleware\n" .
                  "- Use proper type hints\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Authorization Middleware.
     * Generates an Authorization middleware.
     *
     * @param string $name اسم Middleware.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Middleware.
     */
    protected function generateAuthorizationMiddleware(string $name, array $options): string
    {
        $permission = $options['permission'] ?? null;
        $role = $options['role'] ?? null;
        $ability = $options['ability'] ?? null;

        $requirements = [];
        if ($permission) $requirements[] = "- Check permission: {$permission}";
        if ($role) $requirements[] = "- Check role: {$role}";
        if ($ability) $requirements[] = "- Check ability: {$ability}";

        $requirementsText = implode("\n", $requirements);

        $prompt = "Generate a Laravel Authorization Middleware named {$name}. " .
                  "Requirements:\n" .
                  "{$requirementsText}\n" .
                  "- Return 403 Forbidden if unauthorized\n" .
                  "- Support both web and API responses\n" .
                  "- Include proper error messages\n" .
                  "- Add PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Http\\Middleware\n" .
                  "- Use proper type hints\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Logging Middleware.
     * Generates a Logging middleware.
     *
     * @param string $name اسم Middleware.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Middleware.
     */
    protected function generateLoggingMiddleware(string $name, array $options): string
    {
        $logChannel = $options['log_channel'] ?? 'daily';
        $logLevel = $options['log_level'] ?? 'info';
        $includeRequest = $options['include_request'] ?? true;
        $includeResponse = $options['include_response'] ?? true;

        $prompt = "Generate a Laravel Logging Middleware named {$name}. " .
                  "Requirements:\n" .
                  "- Log to channel: {$logChannel}\n" .
                  "- Log level: {$logLevel}\n" .
                  "- Include request data: " . ($includeRequest ? 'yes' : 'no') . "\n" .
                  "- Include response data: " . ($includeResponse ? 'yes' : 'no') . "\n" .
                  "- Log request method, URL, IP, user agent\n" .
                  "- Log response status and duration\n" .
                  "- Mask sensitive data (passwords, tokens)\n" .
                  "- Add PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Http\\Middleware\n" .
                  "- Use proper type hints\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Rate Limiting Middleware.
     * Generates a Rate Limiting middleware.
     *
     * @param string $name اسم Middleware.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Middleware.
     */
    protected function generateRateLimitMiddleware(string $name, array $options): string
    {
        $maxAttempts = $options['max_attempts'] ?? 60;
        $decayMinutes = $options['decay_minutes'] ?? 1;
        $key = $options['key'] ?? 'ip';

        $prompt = "Generate a Laravel Rate Limiting Middleware named {$name}. " .
                  "Requirements:\n" .
                  "- Maximum attempts: {$maxAttempts}\n" .
                  "- Decay time: {$decayMinutes} minutes\n" .
                  "- Rate limit key: {$key} (IP address or User ID)\n" .
                  "- Return 429 Too Many Requests when exceeded\n" .
                  "- Include X-RateLimit headers (Limit, Remaining, Reset)\n" .
                  "- Use Laravel's RateLimiter facade\n" .
                  "- Add PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Http\\Middleware\n" .
                  "- Use proper type hints\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد CORS Middleware.
     * Generates a CORS middleware.
     *
     * @param string $name اسم Middleware.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Middleware.
     */
    protected function generateCorsMiddleware(string $name, array $options): string
    {
        $allowedOrigins = $options['allowed_origins'] ?? ['*'];
        $allowedMethods = $options['allowed_methods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        $allowedHeaders = $options['allowed_headers'] ?? ['Content-Type', 'Authorization', 'X-Requested-With'];

        $originsText = is_array($allowedOrigins) ? implode(', ', $allowedOrigins) : $allowedOrigins;
        $methodsText = is_array($allowedMethods) ? implode(', ', $allowedMethods) : $allowedMethods;
        $headersText = is_array($allowedHeaders) ? implode(', ', $allowedHeaders) : $allowedHeaders;

        $prompt = "Generate a Laravel CORS Middleware named {$name}. " .
                  "Requirements:\n" .
                  "- Allowed origins: {$originsText}\n" .
                  "- Allowed methods: {$methodsText}\n" .
                  "- Allowed headers: {$headersText}\n" .
                  "- Handle preflight OPTIONS requests\n" .
                  "- Add appropriate CORS headers to response\n" .
                  "- Support credentials if needed\n" .
                  "- Add PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Http\\Middleware\n" .
                  "- Use proper type hints\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Custom Middleware من وصف نصي.
     * Generates a Custom middleware from text description.
     *
     * @param string $name اسم Middleware.
     * @param string $description الوصف النصي.
     * @param array<string, mixed> $options خيارات إضافية.
     * @return string محتوى ملف Middleware.
     */
    protected function generateCustomMiddleware(string $name, string $description, array $options): string
    {
        if (empty($description)) {
            throw new InvalidArgumentException("الوصف مطلوب لـ Custom Middleware. Description is required for Custom Middleware.");
        }

        $prompt = "Generate a Laravel Custom Middleware named {$name}. " .
                  "Description: {$description}\n\n" .
                  "Requirements:\n" .
                  "- Implement the described functionality\n" .
                  "- Handle errors gracefully\n" .
                  "- Return appropriate HTTP responses\n" .
                  "- Add PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Http\\Middleware\n" .
                  "- Use proper type hints and dependency injection\n\n" .
                  "Additional Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * استدعاء Manus AI للحصول على محتوى الكود.
     * Calls Manus AI to get the code content.
     *
     * @param string $prompt الموجه (Prompt) المرسل للذكاء الاصطناعي.
     * @return string الكود الذي تم توليده.
     * @throws MiddlewareGenerationException إذا فشل اتصال الذكاء الاصطناعي.
     */
    protected function callAIForContent(string $prompt): string
    {
        try {
            $code = $this->aiClient->generateCode($prompt, 'php');

            if (empty($code)) {
                throw new MiddlewareGenerationException("تلقى استجابة فارغة من Manus AI. Received empty response from Manus AI.");
            }

            return $code;
        } catch (Throwable $e) {
            throw new MiddlewareGenerationException("فشل الاتصال بـ Manus AI: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * كتابة المحتوى إلى ملف.
     * Writes the content to a file.
     *
     * @param string $filePath المسار الكامل للملف.
     * @param string $content المحتوى المراد كتابته.
     * @return void
     * @throws MiddlewareGenerationException إذا فشلت عملية الكتابة.
     */
    protected function writeFile(string $filePath, string $content): void
    {
        try {
            $directory = dirname($filePath);
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
