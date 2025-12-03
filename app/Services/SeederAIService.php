<?php

/**
 * 🧬 Gene: SeederAIService
 * 
 * خدمة الذكاء الاصطناعي لتوليد بيانات واقعية للـ Seeders
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */

namespace App\Services;

use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;

class SeederAIService
{
    /**
     * خدمة OpenAI
     */
    protected OpenAIService $openAIService;

    /**
     * Constructor
     */
    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * توليد بيانات واقعية بالذكاء الاصطناعي
     */
    public function generateRealisticData(
        string $tableName,
        array $columns,
        int $count = 10,
        string $locale = 'ar'
    ): array {
        try {
            $prompt = $this->buildPrompt($tableName, $columns, $count, $locale);
            
            $response = $this->openAIService->chat([
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت مساعد ذكي متخصص في توليد بيانات واقعية لقواعد البيانات.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
            ]);

            $data = json_decode($response['choices'][0]['message']['content'], true);
            
            return $data ?? [];
        } catch (\Exception $e) {
            Log::error('SeederAIService: Failed to generate data', [
                'error' => $e->getMessage(),
                'table' => $tableName,
            ]);
            
            return [];
        }
    }

    /**
     * بناء الـ Prompt للذكاء الاصطناعي
     */
    protected function buildPrompt(
        string $tableName,
        array $columns,
        int $count,
        string $locale
    ): string {
        $language = $locale === 'ar' ? 'العربية' : 'الإنجليزية';
        
        $prompt = "أنشئ {$count} سجل واقعي ومنطقي لجدول '{$tableName}' باللغة {$language}.\n\n";
        $prompt .= "الأعمدة المطلوبة:\n";
        
        foreach ($columns as $column => $config) {
            $type = $config['type'] ?? 'text';
            $prompt .= "- {$column}: نوع {$type}\n";
        }
        
        $prompt .= "\nيجب أن تكون البيانات:\n";
        $prompt .= "1. واقعية ومنطقية\n";
        $prompt .= "2. متنوعة وغير متكررة\n";
        $prompt .= "3. مناسبة للسياق\n";
        $prompt .= "4. بصيغة JSON Array\n\n";
        $prompt .= "مثال على الصيغة المطلوبة:\n";
        $prompt .= "[\n";
        $prompt .= "  {\n";
        $prompt .= "    \"column1\": \"value1\",\n";
        $prompt .= "    \"column2\": \"value2\"\n";
        $prompt .= "  }\n";
        $prompt .= "]\n";
        
        return $prompt;
    }

    /**
     * اقتراح بنية بيانات ذكية
     */
    public function suggestDataStructure(string $description, string $locale = 'ar'): array
    {
        try {
            $language = $locale === 'ar' ? 'العربية' : 'الإنجليزية';
            
            $prompt = "بناءً على الوصف التالي، اقترح بنية بيانات مناسبة (JSON Schema) لجدول قاعدة بيانات:\n\n";
            $prompt .= "{$description}\n\n";
            $prompt .= "يجب أن يكون الرد بصيغة JSON يحتوي على:\n";
            $prompt .= "- table_name: اسم الجدول المقترح\n";
            $prompt .= "- model_name: اسم الـ Model المقترح\n";
            $prompt .= "- columns: قائمة بالأعمدة المقترحة مع أنواعها\n";
            
            $response = $this->openAIService->chat([
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت مساعد ذكي متخصص في تصميم قواعد البيانات.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.5,
            ]);

            $suggestions = json_decode($response['choices'][0]['message']['content'], true);
            
            return $suggestions ?? [];
        } catch (\Exception $e) {
            Log::error('SeederAIService: Failed to suggest structure', [
                'error' => $e->getMessage(),
                'description' => $description,
            ]);
            
            return [];
        }
    }

    /**
     * تحسين Schema موجود
     */
    public function enhanceSchema(array $schema): array
    {
        try {
            $prompt = "حسّن بنية البيانات التالية بإضافة أعمدة مفيدة أو تحسين الأنواع:\n\n";
            $prompt .= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $prompt .= "\n\nيجب أن يكون الرد بصيغة JSON محسّنة.";
            
            $response = $this->openAIService->chat([
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت مساعد ذكي متخصص في تحسين بنية قواعد البيانات.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
            ]);

            $enhanced = json_decode($response['choices'][0]['message']['content'], true);
            
            return $enhanced ?? $schema;
        } catch (\Exception $e) {
            Log::error('SeederAIService: Failed to enhance schema', [
                'error' => $e->getMessage(),
            ]);
            
            return $schema;
        }
    }
}
