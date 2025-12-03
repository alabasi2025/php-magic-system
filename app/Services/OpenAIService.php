<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * فئة خدمة OpenAI
 *
 * هذه الفئة مسؤولة عن التفاعل مع واجهة برمجة تطبيقات OpenAI (API)
 * وتغليف منطق الأعمال المتعلق بالذكاء الاصطناعي.
 * تستخدم أفضل ممارسات Laravel من خلال الاعتماد على واجهة Http Facade.
 */
class OpenAIService
{
    /**
     * @var string مفتاح API الخاص بـ OpenAI.
     */
    protected string $apiKey;

    /**
     * @var string الرابط الأساسي لواجهة API لـ OpenAI.
     */
    protected string $baseUrl = 'https://api.openai.com/v1/';

    /**
     * الدالة البانية (Constructor)
     *
     * تقوم بتحميل مفتاح API من إعدادات Laravel.
     *
     * @throws Exception إذا لم يتم العثور على مفتاح API.
     */
    public function __construct()
    {
        // يتم افتراض أن المفتاح مخزن في ملف config/services.php تحت 'openai.key'
        $this->apiKey = config('services.openai.key');

        if (empty($this->apiKey)) {
            // تسجيل خطأ وإلقاء استثناء لضمان عدم استمرار الخدمة بدون مفتاح
            Log::error('OpenAI API key is missing in configuration.');
            throw new Exception('OpenAI API key is not configured. Please check your services configuration.');
        }
    }

    /**
     * إعداد عميل HTTP مع الرؤوس المطلوبة.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function client()
    {
        // إعداد الرؤوس (Headers) المطلوبة للمصادقة ونوع المحتوى
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * توليد نص باستخدام نموذج الدردشة (Chat Completions).
     *
     * @param string $prompt النص أو السؤال المطلوب توليد استجابة له.
     * @param string $model النموذج المستخدم (افتراضياً gpt-3.5-turbo).
     * @param float $temperature درجة الحرارة للتحكم في عشوائية الاستجابة.
     * @return string|null النص الناتج من النموذج أو null في حالة الفشل.
     */
    public function generateText(string $prompt, string $model = 'gpt-3.5-turbo', float $temperature = 0.7): ?string
    {
        // بناء مصفوفة الرسائل (Messages) المطلوبة لواجهة Chat Completions API
        $messages = [
            ['role' => 'user', 'content' => $prompt],
        ];

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
        ];

        try {
            // إرسال طلب POST إلى نقطة النهاية الخاصة بالدردشة
            $response = $this->client()->post('chat/completions', $payload);

            // التحقق من حالة الاستجابة
            if ($response->successful()) {
                $data = $response->json();
                // التحقق من وجود استجابة صالحة
                if (isset($data['choices'][0]['message']['content'])) {
                    return trim($data['choices'][0]['message']['content']);
                }
            }

            // تسجيل خطأ في حالة فشل الطلب أو عدم نجاحه
            Log::error('OpenAI API Text Generation Failed.', [
                'status' => $response->status(),
                'response' => $response->body(),
                'prompt' => $prompt,
            ]);

            return null;

        } catch (Exception $e) {
            // تسجيل أي استثناءات تحدث أثناء الاتصال
            Log::error('OpenAI Service Exception: ' . $e->getMessage(), ['prompt' => $prompt]);
            return null;
        }
    }

    /**
     * توليد صورة باستخدام نموذج DALL-E.
     *
     * @param string $prompt وصف الصورة المطلوب توليدها.
     * @param int $n عدد الصور المطلوب توليدها (بحد أقصى 10).
     * @param string $size حجم الصورة المطلوب (مثل '1024x1024').
     * @return array|null مصفوفة بروابط الصور أو null في حالة الفشل.
     */
    public function generateImage(string $prompt, int $n = 1, string $size = '1024x1024'): ?array
    {
        $payload = [
            'prompt' => $prompt,
            'n' => $n,
            'size' => $size,
            'response_format' => 'url', // طلب رابط الصورة
        ];

        try {
            // إرسال طلب POST إلى نقطة النهاية الخاصة بالصور
            $response = $this->client()->post('images/generations', $payload);

            if ($response->successful()) {
                $data = $response->json();
                // استخراج روابط الصور من الاستجابة
                if (isset($data['data']) && is_array($data['data'])) {
                    return array_column($data['data'], 'url');
                }
            }

            // تسجيل خطأ في حالة فشل الطلب
            Log::error('OpenAI API Image Generation Failed.', [
                'status' => $response->status(),
                'response' => $response->body(),
                'prompt' => $prompt,
            ]);

            return null;

        } catch (Exception $e) {
            // تسجيل أي استثناءات تحدث أثناء الاتصال
            Log::error('OpenAI Service Exception: ' . $e->getMessage(), ['prompt' => $prompt]);
            return null;
        }
    }
}
