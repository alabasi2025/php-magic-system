<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestGeneratorService
{
    protected $apiKey;
    protected $apiUrl = 'https://open.manus.ai/v1/chat/completions';
    protected $model = 'gpt-4.1-mini';

    public function __construct()
    {
        $this->apiKey = env('MANUS_API_KEY');
    }

    /**
     * توليد اختبارات تلقائياً
     */
    public function generateTests(string $code, string $testType = 'unit', string $framework = 'phpunit'): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'MANUS_API_KEY غير مُعرّف في ملف .env'
            ];
        }

        $prompt = $this->buildPrompt($code, $testType, $framework);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد خبير في كتابة الاختبارات البرمجية. قم بتوليد اختبارات شاملة باللغة العربية.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $tests = $response->json('choices.0.message.content');
                return [
                    'success' => true,
                    'tests' => $tests,
                    'type' => $testType,
                    'framework' => $framework
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
     * بناء Prompt حسب نوع الاختبار
     */
    protected function buildPrompt(string $code, string $testType, string $framework): string
    {
        $frameworkName = $framework === 'pest' ? 'Pest' : 'PHPUnit';
        
        switch ($testType) {
            case 'unit':
                return "قم بتوليد Unit Tests شاملة للكود التالي باستخدام {$frameworkName}. يجب أن تغطي جميع الحالات الممكنة:\n\n```php\n{$code}\n```";
            
            case 'feature':
                return "قم بتوليد Feature Tests للكود التالي باستخدام {$frameworkName}. يجب أن تختبر التفاعل بين المكونات:\n\n```php\n{$code}\n```";
            
            case 'integration':
                return "قم بتوليد Integration Tests للكود التالي باستخدام {$frameworkName}. يجب أن تختبر التكامل مع قاعدة البيانات والخدمات الخارجية:\n\n```php\n{$code}\n```";
            
            default:
                return "قم بتوليد اختبارات شاملة للكود التالي باستخدام {$frameworkName}:\n\n```php\n{$code}\n```";
        }
    }
}
