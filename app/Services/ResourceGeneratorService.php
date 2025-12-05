<?php

namespace App\Services;

use App\Models\ResourceGeneration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

/**
 * ResourceGeneratorService
 *
 * الخدمة الرئيسية لتوليد API Resources في Laravel.
 * تدعم أنماط متعددة (Single, Collection, Nested) وتكامل الذكاء الاصطناعي.
 *
 * The main service for generating Laravel API Resources.
 * Supports multiple patterns (Single, Collection, Nested) and AI integration.
 *
 * @package App\Services
 * @version v3.30.0
 * @author Manus AI
 */
class ResourceGeneratorService
{
    // ثوابت لأنواع الـ Resources المدعومة
    // Constants for supported resource types
    public const TYPE_SINGLE = 'single';
    public const TYPE_COLLECTION = 'collection';
    public const TYPE_NESTED = 'nested';

    /**
     * @var OpenAIService خدمة OpenAI للتكامل مع الذكاء الاصطناعي.
     *                    OpenAI service for AI integration.
     */
    protected OpenAIService $aiService;

    /**
     * المسار الأساسي للـ Resources.
     * The base path for resources.
     *
     * @var string
     */
    protected string $resourcePath = 'app/Http/Resources/';

    /**
     * ResourceGeneratorService constructor.
     *
     * @param OpenAIService $aiService خدمة OpenAI.
     */
    public function __construct(OpenAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * توليد API Resource جديد بناءً على النوع المحدد.
     * Generates a new API Resource based on the specified type.
     *
     * @param string $name اسم الـ Resource (مثال: UserResource).
     *                     The name of the resource (e.g., UserResource).
     * @param string $type نوع الـ Resource (single, collection, nested).
     *                     The type of the resource (single, collection, nested).
     * @param array<string, mixed> $options خيارات إضافية للتوليد.
     *                                      Additional generation options.
     * @return ResourceGeneration سجل التوليد.
     *                            The generation record.
     * @throws Exception إذا فشل التوليد.
     *                   If generation fails.
     */
    public function generateResource(string $name, string $type, array $options = []): ResourceGeneration
    {
        $name = $this->formatResourceName($name);
        $type = strtolower($type);

        // إنشاء سجل التوليد
        $generation = ResourceGeneration::create([
            'name' => $name,
            'type' => $type,
            'model' => $options['model'] ?? null,
            'attributes' => $options['attributes'] ?? [],
            'relations' => $options['relations'] ?? null,
            'conditional_attributes' => $options['conditional_attributes'] ?? null,
            'options' => $options,
            'file_path' => $this->getResourceFilePath($name),
            'content' => '',
            'status' => 'pending',
            'ai_generated' => $options['use_ai'] ?? false,
        ]);

        try {
            // توليد المحتوى حسب النوع
            $content = match ($type) {
                self::TYPE_SINGLE => $this->generateSingleResource($name, $options),
                self::TYPE_COLLECTION => $this->generateCollectionResource($name, $options),
                self::TYPE_NESTED => $this->generateNestedResource($name, $options),
                default => throw new Exception("نوع Resource غير مدعوم: {$type}"),
            };

            // حفظ المحتوى
            $generation->update(['content' => $content]);

            // كتابة الملف
            $this->writeFile($generation->file_path, $content);

            // تحديث الحالة
            $generation->markAsSuccessful();

            return $generation;
        } catch (Exception $e) {
            // تسجيل الخطأ
            Log::error("Resource Generation Failed: {$e->getMessage()}", [
                'name' => $name,
                'type' => $type,
                'options' => $options,
            ]);

            $generation->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * توليد Single Resource.
     * Generates a Single Resource.
     *
     * @param string $name اسم الـ Resource.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى الملف.
     */
    protected function generateSingleResource(string $name, array $options): string
    {
        $model = $options['model'] ?? null;
        $attributes = $options['attributes'] ?? [];
        $relations = $options['relations'] ?? [];
        $useAi = $options['use_ai'] ?? false;

        if ($useAi && $model) {
            return $this->generateWithAI($name, $model, 'single', $options);
        }

        // إذا كان هناك Model، نحلل الخصائص منه
        if ($model && empty($attributes)) {
            $attributes = $this->analyzeModel($model);
        }

        return $this->buildSingleResourceContent($name, $model, $attributes, $relations);
    }

    /**
     * توليد Collection Resource.
     * Generates a Collection Resource.
     *
     * @param string $name اسم الـ Resource.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى الملف.
     */
    protected function generateCollectionResource(string $name, array $options): string
    {
        $model = $options['model'] ?? null;
        $useAi = $options['use_ai'] ?? false;

        if ($useAi && $model) {
            return $this->generateWithAI($name, $model, 'collection', $options);
        }

        return $this->buildCollectionResourceContent($name);
    }

    /**
     * توليد Nested Resource.
     * Generates a Nested Resource.
     *
     * @param string $name اسم الـ Resource.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى الملف.
     */
    protected function generateNestedResource(string $name, array $options): string
    {
        $model = $options['model'] ?? null;
        $attributes = $options['attributes'] ?? [];
        $relations = $options['relations'] ?? [];
        $useAi = $options['use_ai'] ?? false;

        if ($useAi && $model) {
            return $this->generateWithAI($name, $model, 'nested', $options);
        }

        if ($model && empty($attributes)) {
            $attributes = $this->analyzeModel($model);
        }

        return $this->buildNestedResourceContent($name, $model, $attributes, $relations);
    }

    /**
     * توليد Resource باستخدام الذكاء الاصطناعي.
     * Generates Resource using AI.
     *
     * @param string $name اسم الـ Resource.
     * @param string $model اسم الـ Model.
     * @param string $type نوع الـ Resource.
     * @param array<string, mixed> $options الخيارات.
     * @return string محتوى الملف.
     */
    protected function generateWithAI(string $name, string $model, string $type, array $options): string
    {
        $prompt = $this->buildAIPrompt($name, $model, $type, $options);

        try {
            $content = $this->aiService->generateText($prompt, 'gpt-4.1-mini', 0.3);
            
            if ($content) {
                // تنظيف المحتوى من markdown code blocks
                $content = $this->cleanAIResponse($content);
                return $content;
            }
        } catch (Exception $e) {
            Log::warning("AI generation failed, falling back to template: {$e->getMessage()}");
        }

        // Fallback to template-based generation
        return match ($type) {
            'single' => $this->generateSingleResource($name, array_merge($options, ['use_ai' => false])),
            'collection' => $this->generateCollectionResource($name, array_merge($options, ['use_ai' => false])),
            'nested' => $this->generateNestedResource($name, array_merge($options, ['use_ai' => false])),
            default => throw new Exception("نوع Resource غير مدعوم: {$type}"),
        };
    }

    /**
     * بناء Prompt للذكاء الاصطناعي.
     * Builds AI prompt.
     *
     * @param string $name اسم الـ Resource.
     * @param string $model اسم الـ Model.
     * @param string $type نوع الـ Resource.
     * @param array<string, mixed> $options الخيارات.
     * @return string الـ Prompt.
     */
    protected function buildAIPrompt(string $name, string $model, string $type, array $options): string
    {
        $attributes = $options['attributes'] ?? [];
        $relations = $options['relations'] ?? [];

        $prompt = "Generate a Laravel API Resource class named {$name} for the {$model} model.\n\n";
        $prompt .= "Type: {$type}\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "1. Use proper namespace: App\\Http\\Resources\n";
        $prompt .= "2. Extend JsonResource for single/nested or ResourceCollection for collection\n";
        $prompt .= "3. Include complete PHPDoc comments in Arabic and English\n";
        $prompt .= "4. Use camelCase for JSON keys\n";
        $prompt .= "5. Format dates to ISO 8601 format\n";
        $prompt .= "6. Hide sensitive data (password, token, secret)\n";
        $prompt .= "7. Use whenLoaded() for relationships\n";
        $prompt .= "8. Add type hints everywhere\n\n";

        if (!empty($attributes)) {
            $prompt .= "Attributes to include: " . implode(', ', $attributes) . "\n\n";
        }

        if (!empty($relations)) {
            $prompt .= "Relations to include: " . implode(', ', $relations) . "\n\n";
        }

        if ($type === 'collection') {
            $prompt .= "Include pagination metadata (total, count, perPage, currentPage, totalPages)\n";
            $prompt .= "Include links (self, first, last, prev, next)\n\n";
        }

        $prompt .= "Return ONLY the PHP code without any markdown formatting or explanations.";

        return $prompt;
    }

    /**
     * تنظيف استجابة الذكاء الاصطناعي.
     * Cleans AI response.
     *
     * @param string $content المحتوى.
     * @return string المحتوى المنظف.
     */
    protected function cleanAIResponse(string $content): string
    {
        // إزالة markdown code blocks
        $content = preg_replace('/^```php\s*/m', '', $content);
        $content = preg_replace('/^```\s*/m', '', $content);
        $content = trim($content);

        return $content;
    }

    /**
     * تحليل Model للحصول على الخصائص.
     * Analyzes Model to get attributes.
     *
     * @param string $model اسم الـ Model.
     * @return array<int, string> قائمة الخصائص.
     */
    protected function analyzeModel(string $model): array
    {
        try {
            $modelClass = "App\\Models\\{$model}";
            
            if (!class_exists($modelClass)) {
                return [];
            }

            $instance = new $modelClass();
            $table = $instance->getTable();

            if (Schema::hasTable($table)) {
                $columns = Schema::getColumnListing($table);
                
                // استبعاد الحقول الحساسة
                $excludedFields = ['password', 'remember_token', 'api_token', 'secret'];
                
                return array_diff($columns, $excludedFields);
            }
        } catch (Exception $e) {
            Log::warning("Failed to analyze model {$model}: {$e->getMessage()}");
        }

        return [];
    }

    /**
     * بناء محتوى Single Resource.
     * Builds Single Resource content.
     *
     * @param string $name اسم الـ Resource.
     * @param string|null $model اسم الـ Model.
     * @param array<int, string> $attributes الخصائص.
     * @param array<int, string> $relations العلاقات.
     * @return string المحتوى.
     */
    protected function buildSingleResourceContent(string $name, ?string $model, array $attributes, array $relations): string
    {
        $modelName = $model ?? 'Model';
        
        $content = "<?php\n\n";
        $content .= "namespace App\\Http\\Resources;\n\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "use Illuminate\\Http\\Resources\\Json\\JsonResource;\n\n";
        $content .= "/**\n";
        $content .= " * {$name}\n";
        $content .= " * API Resource for {$modelName}\n";
        $content .= " * \n";
        $content .= " * @package App\\Http\\Resources\n";
        $content .= " * @version v3.30.0\n";
        $content .= " */\n";
        $content .= "class {$name} extends JsonResource\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Transform the resource into an array.\n";
        $content .= "     * تحويل المورد إلى مصفوفة\n";
        $content .= "     *\n";
        $content .= "     * @param Request \$request\n";
        $content .= "     * @return array<string, mixed>\n";
        $content .= "     */\n";
        $content .= "    public function toArray(Request \$request): array\n";
        $content .= "    {\n";
        $content .= "        return [\n";

        // إضافة الخصائص
        foreach ($attributes as $attribute) {
            $camelCase = Str::camel($attribute);
            
            // معالجة خاصة للتواريخ
            if (in_array($attribute, ['created_at', 'updated_at', 'deleted_at'])) {
                $content .= "            '{$camelCase}' => \$this->{$attribute}?->toISOString(),\n";
            } else {
                $content .= "            '{$camelCase}' => \$this->{$attribute},\n";
            }
        }

        // إضافة العلاقات
        if (!empty($relations)) {
            $content .= "\n            // Relations\n";
            foreach ($relations as $relation) {
                $resourceName = Str::studly(Str::singular($relation)) . 'Resource';
                $camelCase = Str::camel($relation);
                
                if (Str::plural($relation) === $relation) {
                    // Collection
                    $content .= "            '{$camelCase}' => {$resourceName}::collection(\$this->whenLoaded('{$relation}')),\n";
                } else {
                    // Single
                    $content .= "            '{$camelCase}' => new {$resourceName}(\$this->whenLoaded('{$relation}')),\n";
                }
            }
        }

        $content .= "        ];\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * بناء محتوى Collection Resource.
     * Builds Collection Resource content.
     *
     * @param string $name اسم الـ Resource.
     * @return string المحتوى.
     */
    protected function buildCollectionResourceContent(string $name): string
    {
        $content = "<?php\n\n";
        $content .= "namespace App\\Http\\Resources;\n\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "use Illuminate\\Http\\Resources\\Json\\ResourceCollection;\n\n";
        $content .= "/**\n";
        $content .= " * {$name}\n";
        $content .= " * API Resource Collection\n";
        $content .= " * \n";
        $content .= " * @package App\\Http\\Resources\n";
        $content .= " * @version v3.30.0\n";
        $content .= " */\n";
        $content .= "class {$name} extends ResourceCollection\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Transform the resource collection into an array.\n";
        $content .= "     * تحويل مجموعة الموارد إلى مصفوفة\n";
        $content .= "     *\n";
        $content .= "     * @param Request \$request\n";
        $content .= "     * @return array<string, mixed>\n";
        $content .= "     */\n";
        $content .= "    public function toArray(Request \$request): array\n";
        $content .= "    {\n";
        $content .= "        return [\n";
        $content .= "            'data' => \$this->collection,\n";
        $content .= "            'meta' => [\n";
        $content .= "                'total' => \$this->total(),\n";
        $content .= "                'count' => \$this->count(),\n";
        $content .= "                'perPage' => \$this->perPage(),\n";
        $content .= "                'currentPage' => \$this->currentPage(),\n";
        $content .= "                'totalPages' => \$this->lastPage(),\n";
        $content .= "            ],\n";
        $content .= "            'links' => [\n";
        $content .= "                'self' => \$request->url(),\n";
        $content .= "                'first' => \$this->url(1),\n";
        $content .= "                'last' => \$this->url(\$this->lastPage()),\n";
        $content .= "                'prev' => \$this->previousPageUrl(),\n";
        $content .= "                'next' => \$this->nextPageUrl(),\n";
        $content .= "            ],\n";
        $content .= "        ];\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * بناء محتوى Nested Resource.
     * Builds Nested Resource content.
     *
     * @param string $name اسم الـ Resource.
     * @param string|null $model اسم الـ Model.
     * @param array<int, string> $attributes الخصائص.
     * @param array<int, string> $relations العلاقات.
     * @return string المحتوى.
     */
    protected function buildNestedResourceContent(string $name, ?string $model, array $attributes, array $relations): string
    {
        // Nested Resource is similar to Single Resource but with more emphasis on relations
        return $this->buildSingleResourceContent($name, $model, $attributes, $relations);
    }

    /**
     * تنسيق اسم الـ Resource.
     * Formats resource name.
     *
     * @param string $name الاسم.
     * @return string الاسم المنسق.
     */
    protected function formatResourceName(string $name): string
    {
        $name = Str::studly($name);
        
        if (!Str::endsWith($name, 'Resource') && !Str::endsWith($name, 'Collection')) {
            $name .= 'Resource';
        }

        return $name;
    }

    /**
     * الحصول على مسار ملف الـ Resource.
     * Gets resource file path.
     *
     * @param string $name اسم الـ Resource.
     * @return string المسار.
     */
    protected function getResourceFilePath(string $name): string
    {
        return base_path($this->resourcePath . $name . '.php');
    }

    /**
     * كتابة المحتوى إلى ملف.
     * Writes content to file.
     *
     * @param string $path المسار.
     * @param string $content المحتوى.
     * @return void
     * @throws Exception إذا فشلت الكتابة.
     */
    protected function writeFile(string $path, string $content): void
    {
        $directory = dirname($path);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::put($path, $content) === false) {
            throw new Exception("فشل في كتابة الملف: {$path}");
        }
    }

    /**
     * الحصول على جميع التوليدات.
     * Gets all generations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGenerations()
    {
        return ResourceGeneration::latest()->get();
    }

    /**
     * الحصول على توليد محدد.
     * Gets specific generation.
     *
     * @param int $id معرف التوليد.
     * @return ResourceGeneration
     */
    public function getGeneration(int $id): ResourceGeneration
    {
        return ResourceGeneration::findOrFail($id);
    }

    /**
     * حذف توليد.
     * Deletes generation.
     *
     * @param int $id معرف التوليد.
     * @return bool
     */
    public function deleteGeneration(int $id): bool
    {
        $generation = $this->getGeneration($id);

        // حذف الملف
        if (File::exists($generation->file_path)) {
            File::delete($generation->file_path);
        }

        return $generation->delete();
    }
}
