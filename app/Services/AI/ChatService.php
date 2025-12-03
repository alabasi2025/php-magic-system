<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * خدمة المحادثة مع الذكاء الاصطناعي
 * 
 * توفر واجهة للتواصل مع OpenAI API للمحادثات
 */
class ChatService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
    }

    /**
     * إرسال رسالة والحصول على رد من AI
     *
     * @param string $message الرسالة المرسلة
     * @param array $context السياق السابق للمحادثة (اختياري)
     * @return array
     */
    public function sendMessage(string $message, array $context = []): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('OpenAI API Key غير موجود في ملف .env');
            }

            // بناء المحادثة مع السياق
            $messages = $this->buildMessages($message, $context);

            // إرسال الطلب إلى OpenAI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, [
                'model' => 'gpt-4.1-mini',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if (!$response->successful()) {
                throw new Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();

            // استخراج الرد
            $reply = $data['choices'][0]['message']['content'] ?? '';

            return [
                'success' => true,
                'message' => $reply,
                'usage' => [
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                    'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                    'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                ],
            ];

        } catch (Exception $e) {
            Log::error('ChatService Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'حدث خطأ في الاتصال بالذكاء الاصطناعي: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * بناء رسائل المحادثة مع السياق
     *
     * @param string $message
     * @param array $context
     * @return array
     */
    private function buildMessages(string $message, array $context): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'أنت مساعد ذكي للمطورين في نظام SEMOP. تساعد في البرمجة وحل المشاكل التقنية. أجب باللغة العربية بشكل احترافي ومفصل.',
            ],
        ];

        // إضافة السياق السابق
        foreach ($context as $msg) {
            $messages[] = $msg;
        }

        // إضافة الرسالة الحالية
        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        return $messages;
    }

    /**
     * تحليل كود برمجي
     *
     * @param string $code
     * @return array
     */
    public function analyzeCode(string $code): array
    {
        $prompt = "قم بتحليل الكود التالي وأعطني:\n1. ملخص عن وظيفة الكود\n2. نقاط القوة\n3. نقاط الضعف\n4. اقتراحات للتحسين\n\nالكود:\n```\n{$code}\n```";
        
        return $this->sendMessage($prompt);
    }

    /**
     * شرح كود برمجي
     *
     * @param string $code
     * @return array
     */
    public function explainCode(string $code): array
    {
        $prompt = "اشرح الكود التالي بالتفصيل باللغة العربية:\n\n```\n{$code}\n```";
        
        return $this->sendMessage($prompt);
    }

    /**
     * إصلاح أخطاء في الكود
     *
     * @param string $code
     * @param string $error
     * @return array
     */
    public function fixBug(string $code, string $error = ''): array
    {
        $prompt = "الكود التالي به خطأ. قم بإصلاحه وإعطائي الكود المصلح:\n\n";
        
        if (!empty($error)) {
            $prompt .= "رسالة الخطأ:\n```\n{$error}\n```\n\n";
        }
        
        $prompt .= "الكود:\n```\n{$code}\n```";
        
        return $this->sendMessage($prompt);
    }

    /**
     * توليد كود برمجي
     *
     * @param string $description
     * @param string $language
     * @return array
     */
    public function generateCode(string $description, string $language = 'PHP'): array
    {
        $prompt = "قم بتوليد كود {$language} للمهمة التالية:\n\n{$description}\n\nأعطني الكود كاملاً مع التعليقات.";
        
        return $this->sendMessage($prompt);
    }
}
