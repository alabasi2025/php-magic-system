<?php

namespace App\Services;

use App\Models\ModelGeneration;
use Illuminate\Support\Facades\Http;

/**
 * 🤖 Service: ModelAIService
 * 
 * خدمة التكامل مع الذكاء الاصطناعي لتحسين توليد الـ Models
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */
class ModelAIService
{
    /**
     * OpenAI API Key
     */
    protected ?string $apiKey;

    /**
     * OpenAI Model
     */
    protected string $model = 'gpt-4.1-mini';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    /**
     * تحسين وصف Model باستخدام AI
     * 
     * @param string $description
     * @return array
     */
    public function enhanceDescription(string $description): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key غير متوفر');
        }

        $prompt = $this->buildEnhancementPrompt($description);

        $response = $this->callOpenAI($prompt);

        return $this->parseAIResponse($response);
    }

    /**
     * اقتراح علاقات للـ Model
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    public function suggestRelations(ModelGeneration $generation): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key غير متوفر');
        }

        $prompt = $this->buildRelationsSuggestionPrompt($generation);

        $response = $this->callOpenAI($prompt);

        return $this->parseRelationsSuggestions($response);
    }

    /**
     * اقتراح Scopes للـ Model
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    public function suggestScopes(ModelGeneration $generation): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key غير متوفر');
        }

        $prompt = $this->buildScopesSuggestionPrompt($generation);

        $response = $this->callOpenAI($prompt);

        return $this->parseScopesSuggestions($response);
    }

    /**
     * اقتراح Accessors & Mutators
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    public function suggestAccessorsMutators(ModelGeneration $generation): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key غير متوفر');
        }

        $prompt = $this->buildAccessorsMutatorsPrompt($generation);

        $response = $this->callOpenAI($prompt);

        return $this->parseAccessorsMutatorsSuggestions($response);
    }

    /**
     * توليد PHPDoc شامل
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    public function generatePhpDoc(ModelGeneration $generation): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key غير متوفر');
        }

        $prompt = $this->buildPhpDocPrompt($generation);

        $response = $this->callOpenAI($prompt);

        return $response['content'] ?? '';
    }

    /**
     * تحليل Model وتقديم اقتراحات تحسين
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    public function analyzeAndSuggest(ModelGeneration $generation): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key غير متوفر');
        }

        $prompt = $this->buildAnalysisPrompt($generation);

        $response = $this->callOpenAI($prompt);

        return $this->parseAnalysisSuggestions($response);
    }

    /**
     * بناء Prompt لتحسين الوصف
     * 
     * @param string $description
     * @return string
     */
    protected function buildEnhancementPrompt(string $description): string
    {
        return <<<PROMPT
أنت خبير في Laravel و Eloquent Models. قم بتحليل الوصف التالي واستخرج منه:

1. اسم الـ Model (بالإنجليزية، PascalCase)
2. اسم الجدول (snake_case, plural)
3. قائمة الخصائص (attributes) مع أنواعها
4. الحقول التي يجب أن تكون في fillable
5. الحقول التي يجب أن تكون في hidden
6. الـ casts المناسبة
7. العلاقات المحتملة
8. الـ Scopes المفيدة
9. الـ Traits المناسبة

الوصف:
{$description}

أرجع النتيجة بصيغة JSON بالشكل التالي:
{
  "name": "ModelName",
  "table_name": "table_name",
  "attributes": [
    {"name": "field_name", "type": "string", "nullable": false}
  ],
  "fillable": ["field1", "field2"],
  "hidden": ["password"],
  "casts": {"field": "type"},
  "relations": [
    {"type": "hasMany", "model": "RelatedModel", "method": "relatedModels"}
  ],
  "scopes": [
    {"name": "active", "condition": "is_active = true"}
  ],
  "traits": ["HasFactory", "SoftDeletes"]
}
PROMPT;
    }

    /**
     * بناء Prompt لاقتراح العلاقات
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    protected function buildRelationsSuggestionPrompt(ModelGeneration $generation): string
    {
        $attributes = json_encode($generation->attributes ?? []);
        
        return <<<PROMPT
أنت خبير في Laravel Eloquent Relations. بناءً على الـ Model التالي، اقترح العلاقات المناسبة:

Model Name: {$generation->name}
Table Name: {$generation->table_name}
Attributes: {$attributes}

اقترح العلاقات المناسبة (hasOne, hasMany, belongsTo, belongsToMany, etc.) مع شرح لكل علاقة.

أرجع النتيجة بصيغة JSON:
{
  "relations": [
    {
      "type": "belongsTo",
      "model": "User",
      "method": "user",
      "foreign_key": "user_id",
      "explanation": "كل سجل ينتمي لمستخدم واحد"
    }
  ]
}
PROMPT;
    }

    /**
     * بناء Prompt لاقتراح Scopes
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    protected function buildScopesSuggestionPrompt(ModelGeneration $generation): string
    {
        $attributes = json_encode($generation->attributes ?? []);
        
        return <<<PROMPT
أنت خبير في Laravel Query Scopes. بناءً على الـ Model التالي، اقترح Scopes مفيدة:

Model Name: {$generation->name}
Table Name: {$generation->table_name}
Attributes: {$attributes}

اقترح Scopes مفيدة للاستعلامات الشائعة.

أرجع النتيجة بصيغة JSON:
{
  "scopes": [
    {
      "name": "active",
      "condition": "is_active = true",
      "description": "للحصول على السجلات النشطة فقط"
    }
  ]
}
PROMPT;
    }

    /**
     * بناء Prompt لاقتراح Accessors & Mutators
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    protected function buildAccessorsMutatorsPrompt(ModelGeneration $generation): string
    {
        $attributes = json_encode($generation->attributes ?? []);
        
        return <<<PROMPT
أنت خبير في Laravel Accessors & Mutators. بناءً على الـ Model التالي، اقترح Accessors و Mutators مفيدة:

Model Name: {$generation->name}
Attributes: {$attributes}

اقترح Accessors و Mutators مفيدة.

أرجع النتيجة بصيغة JSON:
{
  "accessors": [
    {
      "name": "full_name",
      "return_type": "string",
      "description": "دمج first_name و last_name"
    }
  ],
  "mutators": [
    {
      "name": "password",
      "description": "تشفير كلمة المرور"
    }
  ]
}
PROMPT;
    }

    /**
     * بناء Prompt لتوليد PHPDoc
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    protected function buildPhpDocPrompt(ModelGeneration $generation): string
    {
        return <<<PROMPT
أنت خبير في توثيق PHP. قم بإنشاء PHPDoc شامل للـ Model التالي:

Model Name: {$generation->name}
Description: {$generation->description}
Table Name: {$generation->table_name}

أنشئ PHPDoc يتضمن:
- وصف الـ Model
- @package
- @version
- @since
- @property لكل خاصية مع نوعها

أرجع PHPDoc فقط بدون أي نص إضافي.
PROMPT;
    }

    /**
     * بناء Prompt لتحليل Model
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    protected function buildAnalysisPrompt(ModelGeneration $generation): string
    {
        $content = $generation->generated_content;
        
        return <<<PROMPT
أنت خبير في Laravel Best Practices. قم بتحليل الـ Model التالي وقدم اقتراحات للتحسين:

{$content}

قدم اقتراحات حول:
1. البنية والتنظيم
2. الأمان (Security)
3. الأداء (Performance)
4. Best Practices
5. Missing Features

أرجع النتيجة بصيغة JSON:
{
  "score": 85,
  "suggestions": [
    {
      "category": "Security",
      "priority": "high",
      "suggestion": "أضف $hidden للحقول الحساسة"
    }
  ],
  "missing_features": ["Observer", "Policy"],
  "best_practices": ["استخدم SoftDeletes"]
}
PROMPT;
    }

    /**
     * استدعاء OpenAI API
     * 
     * @param string $prompt
     * @return array
     */
    protected function callOpenAI(string $prompt): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت خبير في Laravel و Eloquent Models. تقدم إجابات دقيقة ومفصلة بصيغة JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            
            return [
                'content' => $data['choices'][0]['message']['content'] ?? '',
                'usage' => $data['usage'] ?? [],
            ];
        } catch (\Exception $e) {
            throw new \Exception('فشل الاتصال بـ OpenAI: ' . $e->getMessage());
        }
    }

    /**
     * تحليل استجابة AI
     * 
     * @param array $response
     * @return array
     */
    protected function parseAIResponse(array $response): array
    {
        $content = $response['content'] ?? '';
        
        // استخراج JSON من الاستجابة
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('فشل تحليل استجابة AI: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * تحليل اقتراحات العلاقات
     * 
     * @param array $response
     * @return array
     */
    protected function parseRelationsSuggestions(array $response): array
    {
        return $this->parseAIResponse($response);
    }

    /**
     * تحليل اقتراحات Scopes
     * 
     * @param array $response
     * @return array
     */
    protected function parseScopesSuggestions(array $response): array
    {
        return $this->parseAIResponse($response);
    }

    /**
     * تحليل اقتراحات Accessors & Mutators
     * 
     * @param array $response
     * @return array
     */
    protected function parseAccessorsMutatorsSuggestions(array $response): array
    {
        return $this->parseAIResponse($response);
    }

    /**
     * تحليل اقتراحات التحليل
     * 
     * @param array $response
     * @return array
     */
    protected function parseAnalysisSuggestions(array $response): array
    {
        return $this->parseAIResponse($response);
    }

    /**
     * التحقق من توفر AI
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }
}
