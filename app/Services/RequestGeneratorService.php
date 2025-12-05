<?php

namespace App\Services;

use App\Services\AI\ManusAIClient;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;

/**
 * @class RequestGeneratorService
 * @package App\Services
 *
 * @brief خدمة توليد Form Requests في Laravel باستخدام الذكاء الاصطناعي.
 *
 * توفر هذه الخدمة إمكانيات متقدمة لتوليد Form Request Classes
 * مع قواعد Validation، رسائل خطأ مخصصة، وتفويض الصلاحيات.
 *
 * Service for generating Laravel Form Requests using Artificial Intelligence.
 *
 * This service provides advanced capabilities for generating Form Request Classes
 * with validation rules, custom error messages, and authorization logic.
 *
 * @version 3.29.0
 * @author Manus AI
 */
class RequestGeneratorService
{
    /**
     * @var ManusAIClient $aiClient عميل الذكاء الاصطناعي.
     * The AI client instance.
     */
    protected ManusAIClient $aiClient;

    /**
     * @var string $requestsPath المسار الأساسي لحفظ Requests.
     * The base path for saving Request files.
     */
    protected string $requestsPath;

    // أنواع Requests المدعومة
    const TYPE_STORE = 'store';
    const TYPE_UPDATE = 'update';
    const TYPE_SEARCH = 'search';
    const TYPE_FILTER = 'filter';
    const TYPE_CUSTOM = 'custom';

    // أنواع قواعد Validation الشائعة
    const VALIDATION_REQUIRED = 'required';
    const VALIDATION_UNIQUE = 'unique';
    const VALIDATION_EMAIL = 'email';
    const VALIDATION_NUMERIC = 'numeric';
    const VALIDATION_STRING = 'string';
    const VALIDATION_ARRAY = 'array';
    const VALIDATION_DATE = 'date';
    const VALIDATION_FILE = 'file';
    const VALIDATION_IMAGE = 'image';

    /**
     * RequestGeneratorService constructor.
     *
     * @param ManusAIClient $aiClient عميل الذكاء الاصطناعي.
     * The AI client instance.
     */
    public function __construct(ManusAIClient $aiClient)
    {
        $this->aiClient = $aiClient;
        $this->requestsPath = app_path('Http/Requests');
        
        // إنشاء المجلد إذا لم يكن موجوداً
        if (!File::exists($this->requestsPath)) {
            File::makeDirectory($this->requestsPath, 0755, true);
        }
    }

    /**
     * @brief توليد Form Request جديد.
     *
     * Generates a new Form Request class.
     *
     * @param array $config إعدادات التوليد.
     * The generation configuration.
     * @return array نتيجة التوليد. The generation result.
     * @throws Exception في حالة فشل التوليد. On generation failure.
     */
    public function generate(array $config): array
    {
        $this->validateConfig($config);

        $prompt = $this->buildPrompt($config);
        $code = $this->aiClient->generateCode($prompt);

        return [
            'success' => true,
            'code' => $code,
            'name' => $config['name'],
            'type' => $config['type'] ?? self::TYPE_CUSTOM,
            'path' => $this->getRequestPath($config['name'])
        ];
    }

    /**
     * @brief حفظ Request إلى ملف.
     *
     * Saves the Request to a file.
     *
     * @param string $name اسم Request.
     * The Request name.
     * @param string $code كود Request.
     * The Request code.
     * @return array نتيجة الحفظ. The save result.
     * @throws Exception في حالة فشل الحفظ. On save failure.
     */
    public function save(string $name, string $code): array
    {
        $path = $this->getRequestPath($name);
        
        if (File::exists($path)) {
            throw new Exception("Request already exists: {$name}");
        }

        File::put($path, $code);

        return [
            'success' => true,
            'message' => 'Request saved successfully',
            'path' => $path,
            'name' => $name
        ];
    }

    /**
     * @brief الحصول على قائمة Requests المولدة.
     *
     * Gets the list of generated Requests.
     *
     * @return array قائمة Requests. The list of Requests.
     */
    public function getGeneratedRequests(): array
    {
        if (!File::exists($this->requestsPath)) {
            return [];
        }

        $files = File::files($this->requestsPath);
        $requests = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $requests[] = [
                    'name' => $file->getFilenameWithoutExtension(),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime())
                ];
            }
        }

        return $requests;
    }

    /**
     * @brief حذف Request.
     *
     * Deletes a Request.
     *
     * @param string $name اسم Request.
     * The Request name.
     * @return array نتيجة الحذف. The deletion result.
     * @throws Exception في حالة فشل الحذف. On deletion failure.
     */
    public function delete(string $name): array
    {
        $path = $this->getRequestPath($name);

        if (!File::exists($path)) {
            throw new Exception("Request not found: {$name}");
        }

        File::delete($path);

        return [
            'success' => true,
            'message' => 'Request deleted successfully'
        ];
    }

    /**
     * @brief بناء Prompt للذكاء الاصطناعي.
     *
     * Builds the AI prompt.
     *
     * @param array $config إعدادات التوليد.
     * The generation configuration.
     * @return string Prompt للذكاء الاصطناعي. The AI prompt.
     */
    protected function buildPrompt(array $config): string
    {
        $name = $config['name'];
        $type = $config['type'] ?? self::TYPE_CUSTOM;
        $description = $config['description'] ?? '';
        $fields = $config['fields'] ?? [];
        $authorization = $config['authorization'] ?? false;
        $customMessages = $config['custom_messages'] ?? false;

        $prompt = "Generate a Laravel Form Request class with the following specifications:\n\n";
        $prompt .= "Class Name: {$name}\n";
        $prompt .= "Type: {$type}\n";
        
        if ($description) {
            $prompt .= "Description: {$description}\n";
        }

        $prompt .= "\nValidation Rules:\n";
        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $rules = is_array($field['rules']) ? implode('|', $field['rules']) : $field['rules'];
            $prompt .= "- {$fieldName}: {$rules}\n";
        }

        if ($authorization) {
            $prompt .= "\nInclude authorization logic:\n";
            $prompt .= "- Check if user is authenticated\n";
            if (isset($config['authorization_logic'])) {
                $prompt .= "- {$config['authorization_logic']}\n";
            }
        }

        if ($customMessages) {
            $prompt .= "\nInclude custom error messages for all validation rules.\n";
        }

        $prompt .= "\nRequirements:\n";
        $prompt .= "- Use proper namespace: App\\Http\\Requests\n";
        $prompt .= "- Extend Illuminate\\Foundation\\Http\\FormRequest\n";
        $prompt .= "- Include proper PHPDoc comments in both Arabic and English\n";
        $prompt .= "- Follow Laravel best practices\n";
        $prompt .= "- Use type hints for all methods\n";
        $prompt .= "- Return proper types (bool for authorize, array for rules)\n";

        return $prompt;
    }

    /**
     * @brief التحقق من صحة إعدادات التوليد.
     *
     * Validates the generation configuration.
     *
     * @param array $config إعدادات التوليد.
     * The generation configuration.
     * @throws Exception في حالة وجود خطأ في الإعدادات. On invalid configuration.
     */
    protected function validateConfig(array $config): void
    {
        if (!isset($config['name']) || empty($config['name'])) {
            throw new Exception('Request name is required');
        }

        if (!Str::endsWith($config['name'], 'Request')) {
            $config['name'] .= 'Request';
        }

        if (!isset($config['fields']) || !is_array($config['fields']) || empty($config['fields'])) {
            throw new Exception('At least one field is required');
        }

        foreach ($config['fields'] as $field) {
            if (!isset($field['name']) || empty($field['name'])) {
                throw new Exception('Field name is required');
            }
            if (!isset($field['rules']) || empty($field['rules'])) {
                throw new Exception("Rules are required for field: {$field['name']}");
            }
        }
    }

    /**
     * @brief الحصول على المسار الكامل لملف Request.
     *
     * Gets the full path for a Request file.
     *
     * @param string $name اسم Request.
     * The Request name.
     * @return string المسار الكامل. The full path.
     */
    protected function getRequestPath(string $name): string
    {
        if (!Str::endsWith($name, '.php')) {
            $name .= '.php';
        }

        return $this->requestsPath . '/' . $name;
    }

    /**
     * @brief توليد Request من قالب جاهز.
     *
     * Generates a Request from a template.
     *
     * @param string $template اسم القالب.
     * The template name.
     * @param array $params معاملات القالب.
     * The template parameters.
     * @return array نتيجة التوليد. The generation result.
     */
    public function generateFromTemplate(string $template, array $params): array
    {
        $templates = $this->getTemplates();

        if (!isset($templates[$template])) {
            throw new Exception("Template not found: {$template}");
        }

        $config = array_merge($templates[$template], $params);
        return $this->generate($config);
    }

    /**
     * @brief الحصول على القوالب الجاهزة.
     *
     * Gets the available templates.
     *
     * @return array القوالب المتاحة. The available templates.
     */
    public function getTemplates(): array
    {
        return [
            'user_store' => [
                'name' => 'StoreUserRequest',
                'type' => self::TYPE_STORE,
                'description' => 'Validate user creation data',
                'fields' => [
                    ['name' => 'name', 'rules' => 'required|string|max:255'],
                    ['name' => 'email', 'rules' => 'required|email|unique:users,email'],
                    ['name' => 'password', 'rules' => 'required|string|min:8|confirmed'],
                ],
                'authorization' => true,
                'custom_messages' => true
            ],
            'user_update' => [
                'name' => 'UpdateUserRequest',
                'type' => self::TYPE_UPDATE,
                'description' => 'Validate user update data',
                'fields' => [
                    ['name' => 'name', 'rules' => 'sometimes|string|max:255'],
                    ['name' => 'email', 'rules' => 'sometimes|email|unique:users,email'],
                ],
                'authorization' => true,
                'custom_messages' => true
            ],
            'search' => [
                'name' => 'SearchRequest',
                'type' => self::TYPE_SEARCH,
                'description' => 'Validate search parameters',
                'fields' => [
                    ['name' => 'query', 'rules' => 'required|string|min:2'],
                    ['name' => 'page', 'rules' => 'sometimes|integer|min:1'],
                    ['name' => 'per_page', 'rules' => 'sometimes|integer|min:1|max:100'],
                ],
                'authorization' => false,
                'custom_messages' => true
            ],
        ];
    }
}
