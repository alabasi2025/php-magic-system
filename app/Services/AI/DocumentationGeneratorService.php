<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DocumentationGeneratorService
{
    protected $apiKey;
    protected $apiUrl = 'https://open.manus.ai/v1/chat/completions';
    protected $model = 'gpt-4.1-mini';

    public function __construct()
    {
        $this->apiKey = env('MANUS_API_KEY');
    }

    /**
     * توليد توثيق شامل للكود
     */
    public function generateDocumentation(string $code, string $type = 'code'): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'MANUS_API_KEY غير مُعرّف في ملف .env'
            ];
        }

        $prompt = $this->buildPrompt($code, $type);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد خبير في توليد التوثيق التقني باللغة العربية. استخدم تنسيق Markdown في استجابتك.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return [
                    'success' => true,
                    'documentation' => $content,
                    'type' => $type
                ];
            }

            Log::error('Manus AI API Error: ' . $response->body());
            return [
                'success' => false,
                'error' => 'حدث خطأ في الاتصال بواجهة Manus AI: ' . $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Manus AI Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'حدث خطأ غير متوقع: ' . $e->getMessage()
            ];
        }
    }

    /**
     * بناء Prompt حسب نوع التوثيق
     */
    protected function buildPrompt(string $code, string $type): string
    {
        switch ($type) {
            case 'code':
                return "قم بتوليد توثيق شامل للكود التالي باللغة العربية، مع شرح كل فئة ودالة بالتفصيل:\n\n```php\n{$code}\n```";
            
            case 'readme':
                return "قم بتوليد محتوى كامل لملف README.md باللغة العربية للمشروع التالي: '{$code}'. يجب أن يشمل: نظرة عامة، متطلبات النظام، تعليمات التثبيت، والاستخدام.";
            
            case 'api':
                return "قم بتوليد توثيق API شامل باللغة العربية لنقاط النهاية التالية. يجب أن يشمل: المسار، الطريقة، المعلمات، ومثال للاستجابة:\n\n{$code}";
            
            case 'user_guide':
                return "قم بتوليد دليل مستخدم مفصل باللغة العربية للميزة التالية: '{$code}'. يجب أن يكون سهل الفهم ويشمل خطوات واضحة وأمثلة عملية.";
            
            default:
                return "قم بتوليد توثيق شامل باللغة العربية للمحتوى التالي:\n\n{$code}";
        }
    }
}
