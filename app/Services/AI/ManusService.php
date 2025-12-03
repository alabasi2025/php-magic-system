<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * خدمة Manus AI
 * 
 * توفر واجهة للتواصل مع Manus API
 */
class ManusService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.manus.ai/v1/tasks';

    public function __construct()
    {
        $this->apiKey = env('MANUS_API_KEY', '');
    }

    /**
     * إرسال رسالة والحصول على رد من Manus AI
     *
     * @param string $message الرسالة المرسلة
     * @param array $context السياق السابق للمحادثة (اختياري)
     * @param string $taskId معرف المهمة للمحادثات المتعددة (اختياري)
     * @return array
     */
    public function sendMessage(string $message, array $context = [], string $taskId = null): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('Manus API Key غير موجود في ملف .env');
            }

            // بناء الرسالة مع السياق
            $prompt = $this->buildPrompt($message, $context);

            // بناء البيانات المرسلة
            $data = [
                'prompt' => $prompt,
                'agentProfile' => 'manus-1.5',
                'taskMode' => 'chat',
                'locale' => 'ar-SA',
            ];

            // إضافة taskId للمحادثات المتعددة
            if (!empty($taskId)) {
                $data['taskId'] = $taskId;
            }

            // إرسال الطلب إلى Manus API
            $response = Http::withHeaders([
                'API_KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, $data);

            if (!$response->successful()) {
                throw new Exception('Manus API Error: ' . $response->body());
            }

            $result = $response->json();

            // الحصول على نتيجة المهمة
            $taskResult = $this->getTaskResult($result['task_id']);

            return [
                'success' => true,
                'message' => $taskResult['result'] ?? 'تم إنشاء المهمة بنجاح',
                'task_id' => $result['task_id'],
                'task_url' => $result['task_url'] ?? '',
                'task_title' => $result['task_title'] ?? '',
            ];

        } catch (Exception $e) {
            Log::error('ManusService Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'حدث خطأ في الاتصال بـ Manus AI: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * الحصول على نتيجة المهمة
     *
     * @param string $taskId
     * @return array
     */
    private function getTaskResult(string $taskId): array
    {
        try {
            // الانتظار قليلاً لإتمام المهمة
            sleep(3);

            $response = Http::withHeaders([
                'API_KEY' => $this->apiKey,
            ])->timeout(60)->get("https://api.manus.ai/v1/tasks/{$taskId}");

            if ($response->successful()) {
                return $response->json();
            }

            return ['result' => 'جاري معالجة طلبك...'];

        } catch (Exception $e) {
            Log::error('Get Task Result Error: ' . $e->getMessage());
            return ['result' => 'جاري معالجة طلبك...'];
        }
    }

    /**
     * بناء الرسالة مع السياق
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    private function buildPrompt(string $message, array $context): string
    {
        $prompt = "أنت مساعد ذكي للمطورين في نظام SEMOP. تساعد في البرمجة وحل المشاكل التقنية. أجب باللغة العربية بشكل احترافي ومفصل.\n\n";

        // إضافة السياق السابق
        if (!empty($context)) {
            $prompt .= "السياق السابق:\n";
            foreach ($context as $msg) {
                $role = $msg['role'] === 'user' ? 'المستخدم' : 'المساعد';
                $prompt .= "{$role}: {$msg['content']}\n";
            }
            $prompt .= "\n";
        }

        // إضافة الرسالة الحالية
        $prompt .= "المستخدم: {$message}\n\nالمساعد:";

        return $prompt;
    }

    /**
     * تحليل كود برمجي
     *
     * @param string $code
     * @return array
     */
    public function analyzeCode(string $code): array
    {
        $message = "قم بتحليل الكود التالي وأعطني:\n1. ملخص عن وظيفة الكود\n2. نقاط القوة\n3. نقاط الضعف\n4. اقتراحات للتحسين\n\nالكود:\n```\n{$code}\n```";
        
        return $this->sendMessage($message);
    }

    /**
     * شرح كود برمجي
     *
     * @param string $code
     * @return array
     */
    public function explainCode(string $code): array
    {
        $message = "اشرح الكود التالي بالتفصيل باللغة العربية:\n\n```\n{$code}\n```";
        
        return $this->sendMessage($message);
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
        $message = "الكود التالي به خطأ. قم بإصلاحه وإعطائي الكود المصلح:\n\n";
        
        if (!empty($error)) {
            $message .= "رسالة الخطأ:\n```\n{$error}\n```\n\n";
        }
        
        $message .= "الكود:\n```\n{$code}\n```";
        
        return $this->sendMessage($message);
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
        $message = "قم بتوليد كود {$language} للمهمة التالية:\n\n{$description}\n\nأعطني الكود كاملاً مع التعليقات.";
        
        return $this->sendMessage($message);
    }
}
