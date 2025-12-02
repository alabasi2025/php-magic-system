<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use OpenAI\Client;
use Exception;

/**
 * AI Code Generator Service
 * 
 * خدمة توليد الأكواد بالذكاء الاصطناعي
 * تحويل الأوصاف الطبيعية إلى أكواد Laravel كاملة
 * 
 * @package App\Services
 * @version 1.0.0
 */
class AiCodeGeneratorService
{
    private $client;
    private $model = 'gpt-4.1-mini';
    private $temperature = 0.7;

    public function __construct()
    {
        $this->client = \OpenAI::client(env('OPENAI_API_KEY'));
    }

    /**
     * توليد CRUD كامل من وصف طبيعي
     * 
     * @param string $description وصف الميزة المطلوبة
     * @param string $modelName اسم الـ Model
     * @param array $fields الحقول المطلوبة
     * @return array النتيجة تحتوي على الأكواد المولدة
     */
    public function generateCRUD(string $description, string $modelName, array $fields = []): array
    {
        try {
            $prompt = $this->buildCRUDPrompt($description, $modelName, $fields);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getCRUDSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 4000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'model_name' => $modelName,
                'code' => $content,
                'components' => $this->parseGeneratedCode($content),
                'message' => 'تم توليد الكود بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في توليد الكود: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * توليد Migration من وصف الجدول
     * 
     * @param string $tableName اسم الجدول
     * @param string $description وصف الجدول
     * @param array $fields الحقول المطلوبة
     * @return array النتيجة
     */
    public function generateMigration(string $tableName, string $description, array $fields = []): array
    {
        try {
            $prompt = $this->buildMigrationPrompt($tableName, $description, $fields);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getMigrationSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 2000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'table_name' => $tableName,
                'code' => $content,
                'message' => 'تم توليد Migration بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في توليد Migration: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * توليد API Resource
     * 
     * @param string $resourceName اسم الـ Resource
     * @param array $fields الحقول المطلوبة
     * @return array النتيجة
     */
    public function generateApiResource(string $resourceName, array $fields = []): array
    {
        try {
            $prompt = $this->buildApiResourcePrompt($resourceName, $fields);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getApiResourceSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 2000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'resource_name' => $resourceName,
                'code' => $content,
                'message' => 'تم توليد API Resource بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في توليد API Resource: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * توليد Unit Tests
     * 
     * @param string $modelName اسم الـ Model
     * @param string $description وصف الاختبارات المطلوبة
     * @return array النتيجة
     */
    public function generateTests(string $modelName, string $description): array
    {
        try {
            $prompt = $this->buildTestPrompt($modelName, $description);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getTestSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 3000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'model_name' => $modelName,
                'code' => $content,
                'message' => 'تم توليد الاختبارات بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في توليد الاختبارات: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * بناء Prompt لتوليد CRUD
     */
    private function buildCRUDPrompt(string $description, string $modelName, array $fields): string
    {
        $fieldsText = !empty($fields) ? implode(', ', $fields) : 'auto-detect from description';
        
        return <<<PROMPT
أنت مطور Laravel محترف. قم بتوليد CRUD كامل بناءً على الوصف التالي:

**الوصف:** $description
**اسم الـ Model:** $modelName
**الحقول:** $fieldsText

يجب أن تتضمن النتيجة:
1. Model Class مع العلاقات والـ Fillable
2. Migration للجدول
3. Controller مع جميع الـ CRUD Methods
4. Request Classes للـ Validation
5. API Resource للـ API Response

استخدم أفضل الممارسات في Laravel 12 وتأكد من:
- استخدام Eloquent ORM بشكل صحيح
- إضافة Validation Rules مناسبة
- استخدام Soft Deletes إذا لزم الأمر
- إضافة Timestamps
- استخدام Relationships الصحيحة

قدم الكود بصيغة نظيفة وجاهزة للاستخدام مباشرة.
PROMPT;
    }

    /**
     * بناء Prompt لتوليد Migration
     */
    private function buildMigrationPrompt(string $tableName, string $description, array $fields): string
    {
        $fieldsText = !empty($fields) ? implode(', ', $fields) : 'auto-detect from description';
        
        return <<<PROMPT
أنت مطور Laravel محترف. قم بتوليد Migration بناءً على الوصف التالي:

**اسم الجدول:** $tableName
**الوصف:** $description
**الحقول المطلوبة:** $fieldsText

يجب أن تتضمن Migration:
1. جميع الحقول المطلوبة مع الأنواع الصحيحة
2. Primary Key و Foreign Keys
3. Indexes المناسبة
4. Timestamps إذا لزم الأمر
5. Soft Deletes إذا كان مناسباً

استخدم Laravel 12 Migration Syntax.
PROMPT;
    }

    /**
     * بناء Prompt لتوليد API Resource
     */
    private function buildApiResourcePrompt(string $resourceName, array $fields): string
    {
        $fieldsText = !empty($fields) ? implode(', ', $fields) : 'auto-detect from model';
        
        return <<<PROMPT
أنت مطور Laravel محترف. قم بتوليد API Resource بناءً على المعلومات التالية:

**اسم الـ Resource:** $resourceName
**الحقول:** $fieldsText

يجب أن يتضمن Resource:
1. تحويل البيانات بشكل صحيح
2. إخفاء الحقول الحساسة
3. تضمين العلاقات المطلوبة
4. تنسيق التواريخ بشكل صحيح

استخدم Laravel 12 Resource Syntax.
PROMPT;
    }

    /**
     * بناء Prompt لتوليد Tests
     */
    private function buildTestPrompt(string $modelName, string $description): string
    {
        return <<<PROMPT
أنت مطور Laravel محترف متخصص في الاختبارات. قم بتوليد Unit Tests بناءً على المعلومات التالية:

**اسم الـ Model:** $modelName
**الوصف:** $description

يجب أن تتضمن الاختبارات:
1. اختبارات CRUD Operations
2. اختبارات Validation
3. اختبارات العلاقات
4. اختبارات الأذونات إذا وجدت
5. اختبارات الـ Edge Cases

استخدم Laravel 12 Testing Syntax مع PHPUnit.
PROMPT;
    }

    /**
     * System Prompt لتوليد CRUD
     */
    private function getCRUDSystemPrompt(): string
    {
        return <<<PROMPT
أنت مساعد ذكي متخصص في توليد أكواد Laravel احترافية وعالية الجودة.

المتطلبات:
- استخدم أفضل الممارسات في Laravel 12
- اكتب أكواد نظيفة وقابلة للصيانة
- أضف التعليقات العربية حيث لزم الأمر
- استخدم Type Hints الصحيحة
- تأكد من الأمان والـ Validation

الناتج يجب أن يكون:
- جاهز للاستخدام مباشرة
- بدون أخطاء
- متبع معايير PSR-12
- مع جميع الـ CRUD Operations
PROMPT;
    }

    /**
     * System Prompt لتوليد Migration
     */
    private function getMigrationSystemPrompt(): string
    {
        return <<<PROMPT
أنت متخصص في كتابة Laravel Migrations احترافية.

المتطلبات:
- استخدم Laravel 12 Migration Syntax
- أضف الـ Indexes المناسبة
- استخدم الأنواع الصحيحة للحقول
- أضف Foreign Keys مع Cascade Delete إذا لزم
- اكتب Rollback Methods صحيحة

الناتج يجب أن يكون:
- جاهز للتشغيل مباشرة
- بدون أخطاء
- متوافق مع MySQL 8.0
PROMPT;
    }

    /**
     * System Prompt لتوليد API Resource
     */
    private function getApiResourceSystemPrompt(): string
    {
        return <<<PROMPT
أنت متخصص في كتابة Laravel API Resources احترافية.

المتطلبات:
- استخدم Laravel 12 Resource Syntax
- قم بتحويل البيانات بشكل صحيح
- أخفِ الحقول الحساسة
- أضف العلاقات المطلوبة
- نسّق التواريخ بشكل صحيح

الناتج يجب أن يكون:
- جاهز للاستخدام مباشرة
- يتبع REST API Best Practices
PROMPT;
    }

    /**
     * System Prompt لتوليد Tests
     */
    private function getTestSystemPrompt(): string
    {
        return <<<PROMPT
أنت متخصص في كتابة Laravel Unit Tests احترافية.

المتطلبات:
- استخدم Laravel 12 Testing Syntax
- اكتب اختبارات شاملة وفعالة
- استخدم Factories و Seeders
- أضف التعليقات الواضحة
- اتبع AAA Pattern (Arrange, Act, Assert)

الناتج يجب أن يكون:
- جاهز للتشغيل مباشرة
- يغطي جميع الحالات الممكنة
- يتبع PHPUnit Best Practices
PROMPT;
    }

    /**
     * تحليل الكود المولد واستخراج المكونات
     */
    private function parseGeneratedCode(string $code): array
    {
        $components = [
            'model' => $this->extractSection($code, 'class.*Model'),
            'migration' => $this->extractSection($code, 'class.*Migration'),
            'controller' => $this->extractSection($code, 'class.*Controller'),
            'requests' => $this->extractSection($code, 'class.*Request'),
            'resource' => $this->extractSection($code, 'class.*Resource'),
        ];

        return array_filter($components);
    }

    /**
     * استخراج قسم من الكود
     */
    private function extractSection(string $code, string $pattern): ?string
    {
        if (preg_match("/$pattern.*?(?=class|\Z)/s", $code, $matches)) {
            return $matches[0];
        }
        return null;
    }

    /**
     * حفظ الأكواد المولدة في الملفات
     */
    public function saveGeneratedCode(array $components, string $modelName): array
    {
        $results = [];

        try {
            // حفظ Model
            if (isset($components['model'])) {
                $path = app_path("Models/{$modelName}.php");
                File::put($path, $components['model']);
                $results['model'] = [
                    'success' => true,
                    'path' => $path,
                    'message' => 'تم حفظ Model بنجاح'
                ];
            }

            // حفظ Controller
            if (isset($components['controller'])) {
                $path = app_path("Http/Controllers/{$modelName}Controller.php");
                File::put($path, $components['controller']);
                $results['controller'] = [
                    'success' => true,
                    'path' => $path,
                    'message' => 'تم حفظ Controller بنجاح'
                ];
            }

            // حفظ Requests
            if (isset($components['requests'])) {
                $path = app_path("Http/Requests/{$modelName}Request.php");
                File::put($path, $components['requests']);
                $results['requests'] = [
                    'success' => true,
                    'path' => $path,
                    'message' => 'تم حفظ Requests بنجاح'
                ];
            }

            // حفظ Resource
            if (isset($components['resource'])) {
                $path = app_path("Http/Resources/{$modelName}Resource.php");
                File::put($path, $components['resource']);
                $results['resource'] = [
                    'success' => true,
                    'path' => $path,
                    'message' => 'تم حفظ Resource بنجاح'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في حفظ الملفات: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'تم حفظ جميع الملفات بنجاح',
            'results' => $results
        ];
    }
}
