<?php

namespace App\Services;

use App\Exceptions\PolicyGenerationException;
use App\Services\AI\ManusAIClient;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Throwable;

/**
 * PolicyGeneratorService
 *
 * الخدمة الرئيسية لتوليد Policies في Laravel.
 * تدعم أنماط متعددة (Resource, Custom, Role-Based, Ownership).
 *
 * The main service for generating Laravel Policies.
 * Supports multiple patterns (Resource, Custom, Role-Based, Ownership).
 *
 * @package App\Services
 * @version v3.31.0
 * @author Manus AI
 */
class PolicyGeneratorService
{
    // ثوابت لأنواع Policies المدعومة
    // Constants for supported policy types
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_CUSTOM = 'custom';
    public const TYPE_ROLE_BASED = 'role_based';
    public const TYPE_OWNERSHIP = 'ownership';

    // الأساليب القياسية - Standard methods
    public const STANDARD_METHODS = [
        'viewAny',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'forceDelete',
    ];

    /**
     * @var ManusAIClient عميل Manus AI للتكامل مع الذكاء الاصطناعي.
     *                    Manus AI client for AI integration.
     */
    protected ManusAIClient $aiClient;

    /**
     * المسار الأساسي لـ Policies.
     * The base path for policies.
     *
     * @var string
     */
    protected string $policyPath = 'app/Policies/';

    /**
     * المسار الاحتياطي للملفات المولدة.
     * The backup path for generated files.
     *
     * @var string
     */
    protected string $backupPath = 'storage/app/generated/policies/';

    /**
     * PolicyGeneratorService constructor.
     *
     * @param ManusAIClient $aiClient عميل Manus AI.
     */
    public function __construct(ManusAIClient $aiClient)
    {
        $this->aiClient = $aiClient;
    }

    /**
     * توليد Policy جديد بناءً على النوع المحدد.
     * Generates a new Policy based on the specified type.
     *
     * @param string $name اسم Policy (مثال: PostPolicy).
     *                     The name of the policy (e.g., PostPolicy).
     * @param string $model اسم النموذج المرتبط (مثال: Post).
     *                      The associated model name (e.g., Post).
     * @param string $type نوع Policy.
     *                     The type of the policy.
     * @param array<string, mixed> $options خيارات إضافية للتوليد.
     *                                      Additional generation options.
     * @return string المسار الكامل للملف الذي تم إنشاؤه.
     *                The full path to the created file.
     * @throws PolicyGenerationException إذا فشل التوليد أو كان النوع غير مدعوم.
     *                                   If generation fails or the type is unsupported.
     */
    public function generatePolicy(string $name, string $model, string $type, array $options = []): string
    {
        $name = $this->formatPolicyName($name);
        $model = $this->formatModelName($model);
        $type = strtolower($type);

        try {
            $content = match ($type) {
                self::TYPE_RESOURCE => $this->generateResourcePolicy($name, $model, $options),
                self::TYPE_CUSTOM => $this->generateCustomPolicy($name, $model, $options),
                self::TYPE_ROLE_BASED => $this->generateRoleBasedPolicy($name, $model, $options),
                self::TYPE_OWNERSHIP => $this->generateOwnershipPolicy($name, $model, $options),
                default => throw new InvalidArgumentException("نوع Policy غير مدعوم: {$type}. Unsupported policy type: {$type}."),
            };

            $filePath = $this->getPolicyFilePath($name);
            $this->writeFile($filePath, $content);

            // حفظ نسخة احتياطية
            // Save backup copy
            $this->saveBackup($name, $content);

            return $filePath;
        } catch (InvalidArgumentException $e) {
            throw new PolicyGenerationException("خطأ في المدخلات: " . $e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            throw new PolicyGenerationException("فشل توليد Policy '{$name}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * معاينة محتوى Policy دون حفظه.
     * Preview the policy content without saving it.
     *
     * @param string $name اسم Policy.
     * @param string $model اسم النموذج.
     * @param string $type نوع Policy.
     * @param array<string, mixed> $options خيارات إضافية.
     * @return string محتوى Policy.
     * @throws PolicyGenerationException
     */
    public function previewPolicy(string $name, string $model, string $type, array $options = []): string
    {
        $name = $this->formatPolicyName($name);
        $model = $this->formatModelName($model);
        $type = strtolower($type);

        try {
            return match ($type) {
                self::TYPE_RESOURCE => $this->generateResourcePolicy($name, $model, $options),
                self::TYPE_CUSTOM => $this->generateCustomPolicy($name, $model, $options),
                self::TYPE_ROLE_BASED => $this->generateRoleBasedPolicy($name, $model, $options),
                self::TYPE_OWNERSHIP => $this->generateOwnershipPolicy($name, $model, $options),
                default => throw new InvalidArgumentException("نوع Policy غير مدعوم: {$type}"),
            };
        } catch (Throwable $e) {
            throw new PolicyGenerationException("فشل معاينة Policy: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * توليد Resource Policy (شامل مع جميع الأساليب القياسية).
     * Generates a Resource policy with all standard methods.
     *
     * @param string $name اسم Policy.
     * @param string $model اسم النموذج.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Policy.
     */
    protected function generateResourcePolicy(string $name, string $model, array $options): string
    {
        $useResponses = $options['use_responses'] ?? true;
        $includeFilters = $options['include_filters'] ?? false;
        $guestSupport = $options['guest_support'] ?? false;
        $softDeletes = $options['soft_deletes'] ?? false;

        $prompt = "Generate a complete Laravel Resource Policy named {$name} for model {$model}. " .
                  "Requirements:\n" .
                  "- Include all standard methods: viewAny, view, create, update, delete" .
                  ($softDeletes ? ", restore, forceDelete" : "") . "\n" .
                  "- Use " . ($useResponses ? "Response objects (Response::allow(), Response::deny())" : "boolean returns") . "\n" .
                  ($includeFilters ? "- Include before() filter method for administrators\n" : "") .
                  ($guestSupport ? "- Support guest users with nullable User type hint\n" : "") .
                  "- Add comprehensive PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Policies\n" .
                  "- Use proper type hints (User, {$model})\n" .
                  "- Import: use App\\Models\\User; use App\\Models\\{$model};\n" .
                  ($useResponses ? "- Import: use Illuminate\\Auth\\Access\\Response;\n" : "") .
                  "- Add descriptive comments for each method explaining the authorization logic\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Custom Policy (بأساليب مخصصة).
     * Generates a Custom policy with specified methods.
     *
     * @param string $name اسم Policy.
     * @param string $model اسم النموذج.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Policy.
     */
    protected function generateCustomPolicy(string $name, string $model, array $options): string
    {
        $methods = $options['methods'] ?? ['view', 'update', 'delete'];
        $useResponses = $options['use_responses'] ?? true;
        $description = $options['ai_description'] ?? '';

        $methodsList = implode(', ', $methods);

        $prompt = "Generate a Laravel Custom Policy named {$name} for model {$model}. " .
                  "Requirements:\n" .
                  "- Include only these methods: {$methodsList}\n" .
                  "- Use " . ($useResponses ? "Response objects" : "boolean returns") . "\n" .
                  ($description ? "- Context: {$description}\n" : "") .
                  "- Add comprehensive PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Policies\n" .
                  "- Use proper type hints\n" .
                  "- Import necessary classes\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Role-Based Policy (قائم على الأدوار).
     * Generates a Role-Based policy.
     *
     * @param string $name اسم Policy.
     * @param string $model اسم النموذج.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Policy.
     */
    protected function generateRoleBasedPolicy(string $name, string $model, array $options): string
    {
        $roles = $options['roles'] ?? ['admin', 'editor', 'viewer'];
        $permissions = $options['permissions'] ?? [];
        $useResponses = $options['use_responses'] ?? true;

        $rolesList = implode(', ', $roles);
        $permissionsList = !empty($permissions) ? implode(', ', $permissions) : 'none specified';

        $prompt = "Generate a Laravel Role-Based Policy named {$name} for model {$model}. " .
                  "Requirements:\n" .
                  "- Check user roles: {$rolesList}\n" .
                  "- Check permissions: {$permissionsList}\n" .
                  "- Include standard CRUD methods with role-based authorization\n" .
                  "- Use " . ($useResponses ? "Response objects" : "boolean returns") . "\n" .
                  "- Assume User model has hasRole() and hasPermission() methods\n" .
                  "- Add comprehensive PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Policies\n" .
                  "- Use proper type hints\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * توليد Ownership Policy (قائم على الملكية).
     * Generates an Ownership policy.
     *
     * @param string $name اسم Policy.
     * @param string $model اسم النموذج.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى ملف Policy.
     */
    protected function generateOwnershipPolicy(string $name, string $model, array $options): string
    {
        $ownershipField = $options['ownership_field'] ?? 'user_id';
        $useResponses = $options['use_responses'] ?? true;
        $softDeletes = $options['soft_deletes'] ?? false;

        $prompt = "Generate a Laravel Ownership Policy named {$name} for model {$model}. " .
                  "Requirements:\n" .
                  "- Check ownership using field: {$ownershipField}\n" .
                  "- Allow users to only manage their own resources\n" .
                  "- Include standard CRUD methods\n" .
                  ($softDeletes ? "- Include restore and forceDelete methods\n" : "") .
                  "- Use " . ($useResponses ? "Response objects with descriptive messages" : "boolean returns") . "\n" .
                  "- Add comprehensive PHPDoc comments in Arabic and English\n" .
                  "- Follow Laravel best practices\n" .
                  "- Include namespace: App\\Policies\n" .
                  "- Use proper type hints\n" .
                  "- Compare user->id with model->{$ownershipField}\n\n" .
                  "Options: " . json_encode($options, JSON_UNESCAPED_UNICODE) . "\n\n" .
                  "Generate complete, production-ready code.";

        return $this->callAIForContent($prompt);
    }

    /**
     * استدعاء Manus AI لتوليد محتوى Policy.
     * Call Manus AI to generate policy content.
     *
     * @param string $prompt موجه الذكاء الاصطناعي.
     * @return string محتوى Policy المولد.
     * @throws PolicyGenerationException
     */
    protected function callAIForContent(string $prompt): string
    {
        try {
            $response = $this->aiClient->generateCode($prompt);
            
            if (empty($response)) {
                throw new PolicyGenerationException("فشل الذكاء الاصطناعي في توليد المحتوى. AI failed to generate content.");
            }

            // استخراج الكود من الاستجابة
            // Extract code from response
            $content = $this->extractCodeFromResponse($response);

            return $content;
        } catch (Throwable $e) {
            throw new PolicyGenerationException("خطأ في استدعاء الذكاء الاصطناعي: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * استخراج الكود من استجابة الذكاء الاصطناعي.
     * Extract code from AI response.
     *
     * @param string $response استجابة الذكاء الاصطناعي.
     * @return string الكود المستخرج.
     */
    protected function extractCodeFromResponse(string $response): string
    {
        // إزالة markdown code blocks إذا وجدت
        // Remove markdown code blocks if present
        $response = preg_replace('/```php\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        $response = trim($response);

        // التأكد من وجود <?php في البداية
        // Ensure <?php tag at the beginning
        if (!str_starts_with($response, '<?php')) {
            $response = "<?php\n\n" . $response;
        }

        return $response;
    }

    /**
     * تنسيق اسم Policy.
     * Format policy name.
     *
     * @param string $name الاسم الأصلي.
     * @return string الاسم المنسق.
     */
    protected function formatPolicyName(string $name): string
    {
        $name = trim($name);
        
        // إضافة "Policy" إذا لم يكن موجوداً
        // Add "Policy" suffix if not present
        if (!str_ends_with($name, 'Policy')) {
            $name .= 'Policy';
        }

        // التأكد من أن الاسم يبدأ بحرف كبير
        // Ensure name starts with uppercase
        $name = ucfirst($name);

        return $name;
    }

    /**
     * تنسيق اسم النموذج.
     * Format model name.
     *
     * @param string $model الاسم الأصلي.
     * @return string الاسم المنسق.
     */
    protected function formatModelName(string $model): string
    {
        $model = trim($model);
        
        // التأكد من أن الاسم يبدأ بحرف كبير
        // Ensure name starts with uppercase
        $model = ucfirst($model);

        return $model;
    }

    /**
     * الحصول على المسار الكامل لملف Policy.
     * Get the full file path for a policy.
     *
     * @param string $name اسم Policy.
     * @return string المسار الكامل.
     */
    protected function getPolicyFilePath(string $name): string
    {
        return base_path($this->policyPath . $name . '.php');
    }

    /**
     * كتابة محتوى إلى ملف.
     * Write content to a file.
     *
     * @param string $filePath المسار الكامل للملف.
     * @param string $content المحتوى.
     * @return void
     * @throws PolicyGenerationException
     */
    protected function writeFile(string $filePath, string $content): void
    {
        try {
            $directory = dirname($filePath);
            
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            File::put($filePath, $content);
        } catch (Throwable $e) {
            throw new PolicyGenerationException("فشل كتابة الملف: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * حفظ نسخة احتياطية من Policy.
     * Save a backup copy of the policy.
     *
     * @param string $name اسم Policy.
     * @param string $content المحتوى.
     * @return void
     */
    protected function saveBackup(string $name, string $content): void
    {
        try {
            $backupDir = base_path($this->backupPath);
            
            if (!File::isDirectory($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupDir . $name . '_' . $timestamp . '.php';
            
            File::put($backupFile, $content);
        } catch (Throwable $e) {
            // فشل النسخ الاحتياطي لا يجب أن يوقف العملية
            // Backup failure should not stop the process
            \Log::warning("فشل حفظ النسخة الاحتياطية لـ Policy: " . $e->getMessage());
        }
    }

    /**
     * الحصول على قائمة Policies المولدة.
     * Get list of generated policies.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listGeneratedPolicies(): array
    {
        $policyDir = base_path($this->policyPath);
        
        if (!File::isDirectory($policyDir)) {
            return [];
        }

        $files = File::files($policyDir);
        $policies = [];

        foreach ($files as $file) {
            $policies[] = [
                'name' => $file->getFilenameWithoutExtension(),
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
            ];
        }

        return $policies;
    }
}
