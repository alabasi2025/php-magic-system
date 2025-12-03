<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * @class ClaudeService
 * @brief خدمة للتفاعل مع واجهة برمجة تطبيقات Claude AI من Anthropic.
 *
 * هذه الخدمة مسؤولة عن إرسال الطلبات إلى نموذج Claude وتلقي الردود.
 * تتبع أفضل ممارسات Laravel باستخدام حقن التبعية (Dependency Injection) لعميل HTTP.
 */
class ClaudeService
{
    /**
     * @var Client $httpClient عميل HTTP لإرسال الطلبات الخارجية.
     */
    protected Client $httpClient;

    /**
     * @var string $apiKey مفتاح API الخاص بـ Claude.
     */
    protected string $apiKey;

    /**
     * @var string $model النموذج الافتراضي لـ Claude المراد استخدامه (مثل 'claude-3-opus-20240229').
     */
    protected string $model;

    /**
     * @var string $baseUrl نقطة النهاية الأساسية لواجهة برمجة تطبيقات Claude.
     */
    protected string $baseUrl = 'https://api.anthropic.com/v1/messages';

    /**
     * @brief مُنشئ الخدمة.
     *
     * يقوم بتهيئة عميل HTTP وإعداد مفتاح API والنموذج من ملفات الإعداد.
     *
     * @param Client $httpClient عميل Guzzle HTTP.
     */
    public function __construct(Client $httpClient)
    {
        // تهيئة عميل HTTP
        $this->httpClient = $httpClient;

        // جلب الإعدادات من ملف config/services.php (يجب إضافة إعدادات claude هناك)
        // مثال: 'claude' => ['key' => env('CLAUDE_API_KEY'), 'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229')]
        $this->apiKey = config('services.claude.key');
        $this->model = config('services.claude.model', 'claude-3-opus-20240229');
    }

    /**
     * @brief إرسال موجه (Prompt) إلى نموذج Claude والحصول على الرد.
     *
     * تستخدم هذه الدالة واجهة Messages API الجديدة من Anthropic.
     *
     * @param string $prompt النص الموجه الذي سيتم إرساله إلى النموذج.
     * @param array $options خيارات إضافية للطلب (مثل درجة الحرارة، الحد الأقصى للرموز).
     * @return string|null الرد النصي من النموذج، أو null في حالة حدوث خطأ.
     */
    public function generateResponse(string $prompt, array $options = []): ?string
    {
        // التحقق من وجود مفتاح API قبل المتابعة
        if (empty($this->apiKey)) {
            Log::error('ClaudeService: مفتاح API مفقود. يرجى التحقق من إعدادات CLAUDE_API_KEY في ملف .env.');
            return null;
        }

        // بناء جسم الطلب (Payload) وفقاً لمتطلبات Anthropic Messages API
        $payload = array_merge([
            'model' => $this->model,
            'max_tokens' => 1024, // الحد الأقصى للرموز الافتراضي
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ], $options);

        try {
            // إرسال الطلب إلى واجهة برمجة تطبيقات Claude
            $response = $this->httpClient->post($this->baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01', // إصدار API المطلوب
                ],
                'json' => $payload,
            ]);

            // تحليل الرد
            $data = json_decode($response->getBody()->getContents(), true);

            // التحقق من وجود محتوى في الرد
            if (isset($data['content'][0]['text'])) {
                return $data['content'][0]['text'];
            }

            // تسجيل خطأ في حالة عدم وجود محتوى متوقع
            Log::warning('ClaudeService: الرد من API لا يحتوي على محتوى متوقع.', ['response' => $data]);
            return null;

        } catch (GuzzleException $e) {
            // تسجيل أخطاء الاتصال أو أخطاء HTTP
            Log::error('ClaudeService: حدث خطأ في الاتصال بواجهة برمجة تطبيقات Claude.', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return null;
        } catch (\Exception $e) {
            // تسجيل أي استثناءات أخرى غير متوقعة
            Log::error('ClaudeService: حدث خطأ غير متوقع.', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * @brief تعيين النموذج (Model) للاستخدام في الطلب الحالي.
     *
     * @param string $model اسم النموذج الجديد.
     * @return self
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @brief الحصول على النموذج الحالي المستخدم.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
