<?php

namespace App\Services\AI;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة محسن الكود - تحسين وتحليل الكود تلقائياً
 * 
 * تستخدم Manus AI لتحليل الكود وتقديم اقتراحات للتحسين
 */
class CodeOptimizerService
{
    private $apiKey;
    private $apiUrl = 'https://api.manus.ai/v1/tasks';
    
    public function __construct()
    {
        $this->apiKey = AiSetting::where('key', 'manus_api_key')->value('value');
    }
    
    /**
     * تحليل الكود وتقديم اقتراحات للتحسين
     */
    public function analyzeCode(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildAnalysisPrompt($code, $language);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'prompt' => $prompt,
                'model' => 'manus-1.5-lite',
                'mode' => 'chat',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseAnalysisResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            Log::error('Code Optimizer Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * تحسين الكود تلقائياً
     */
    public function optimizeCode(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildOptimizationPrompt($code, $language);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'prompt' => $prompt,
                'model' => 'manus-1.5-lite',
                'mode' => 'chat',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseOptimizationResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            Log::error('Code Optimizer Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * فحص الأداء
     */
    public function checkPerformance(string $code): array
    {
        try {
            $prompt = "قم بتحليل أداء هذا الكود وحدد المشاكل المحتملة:\n\n```php\n{$code}\n```\n\nركز على:\n1. N+1 queries\n2. استخدام الذاكرة\n3. التعقيد الزمني\n4. الاستعلامات البطيئة\n5. التحميل الزائد";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'prompt' => $prompt,
                'model' => 'manus-1.5-lite',
                'mode' => 'chat',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'analysis' => $this->extractTextFromResponse($data),
                    'task_id' => $data['id'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * فحص جودة الكود
     */
    public function checkQuality(string $code): array
    {
        try {
            $prompt = "قم بفحص جودة هذا الكود:\n\n```php\n{$code}\n```\n\nافحص:\n1. التزام بمعايير PSR-12\n2. Clean Code principles\n3. SOLID principles\n4. Design Patterns\n5. Best Practices\n\nأعط تقييم من 10 مع شرح مفصل.";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'prompt' => $prompt,
                'model' => 'manus-1.5-lite',
                'mode' => 'chat',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'analysis' => $this->extractTextFromResponse($data),
                    'task_id' => $data['id'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * بناء prompt للتحليل
     */
    private function buildAnalysisPrompt(string $code, string $language): string
    {
        return "أنت خبير في تحليل وتحسين الكود. قم بتحليل هذا الكود:\n\n```{$language}\n{$code}\n```\n\n**المطلوب:**\n1. تحديد المشاكل والأخطاء المحتملة\n2. اقتراحات للتحسين\n3. تحسينات الأداء\n4. تحسينات الأمان\n5. تحسينات القراءة\n\n**الرد بصيغة JSON:**\n```json\n{\n  \"issues\": [\n    {\"type\": \"نوع المشكلة\", \"line\": رقم السطر, \"description\": \"الوصف\", \"severity\": \"high|medium|low\"}\n  ],\n  \"suggestions\": [\n    {\"category\": \"الفئة\", \"description\": \"الاقتراح\", \"impact\": \"التأثير\"}\n  ],\n  \"score\": {\"performance\": 0-10, \"security\": 0-10, \"readability\": 0-10, \"overall\": 0-10}\n}\n```";
    }
    
    /**
     * بناء prompt للتحسين
     */
    private function buildOptimizationPrompt(string $code, string $language): string
    {
        return "أنت خبير في تحسين الكود. قم بتحسين هذا الكود:\n\n```{$language}\n{$code}\n```\n\n**المطلوب:**\n1. تحسين الأداء\n2. تحسين الأمان\n3. تحسين القراءة\n4. تطبيق Best Practices\n5. إضافة تعليقات مفيدة\n\n**الرد:**\nالكود المحسّن كاملاً مع شرح التحسينات.";
    }
    
    /**
     * تحليل رد التحليل
     */
    private function parseAnalysisResponse(array $data): array
    {
        $text = $this->extractTextFromResponse($data);
        
        // محاولة استخراج JSON من الرد
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json) {
                return [
                    'success' => true,
                    'analysis' => $json,
                    'task_id' => $data['id'] ?? null
                ];
            }
        }
        
        // إذا لم يكن JSON، أرجع النص كما هو
        return [
            'success' => true,
            'analysis' => $text,
            'task_id' => $data['id'] ?? null
        ];
    }
    
    /**
     * تحليل رد التحسين
     */
    private function parseOptimizationResponse(array $data): array
    {
        $text = $this->extractTextFromResponse($data);
        
        // استخراج الكود المحسّن
        if (preg_match('/```(?:php)?\s*(.*?)\s*```/s', $text, $matches)) {
            return [
                'success' => true,
                'optimized_code' => trim($matches[1]),
                'explanation' => $text,
                'task_id' => $data['id'] ?? null
            ];
        }
        
        return [
            'success' => true,
            'optimized_code' => $text,
            'explanation' => $text,
            'task_id' => $data['id'] ?? null
        ];
    }
    
    /**
     * استخراج النص من رد Manus
     */
    private function extractTextFromResponse(array $data): string
    {
        if (isset($data['output']) && is_array($data['output'])) {
            $texts = [];
            foreach ($data['output'] as $item) {
                if (isset($item['type']) && $item['type'] === 'text' && isset($item['text'])) {
                    $texts[] = $item['text'];
                }
            }
            return implode("\n\n", $texts);
        }
        
        return '';
    }
}
