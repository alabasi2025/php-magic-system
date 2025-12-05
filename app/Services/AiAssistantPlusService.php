<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * خدمة المساعد الذكي المتقدم Plus
 * 
 * نسخة متطورة من المساعد الذكي مع ميزات متقدمة:
 * - دعم المحادثات المتعددة
 * - حفظ السياق التلقائي
 * - تحليل متقدم للأكواد
 * - اقتراحات ذكية
 * - دعم اللغات المتعددة
 * - تكامل مع أدوات التطوير
 */
class AiAssistantPlusService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    private string $model = 'gpt-4.1-mini';
    private int $maxTokens = 4000;
    private float $temperature = 0.7;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
    }

    /**
     * إرسال رسالة متقدمة مع دعم السياق والذاكرة
     *
     * @param string $message الرسالة المرسلة
     * @param string $conversationId معرف المحادثة
     * @param array $options خيارات إضافية
     * @return array
     */
    public function chat(string $message, string $conversationId = 'default', array $options = []): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('OpenAI API Key غير موجود في ملف .env');
            }

            // جلب سياق المحادثة من الذاكرة المؤقتة
            $context = $this->getConversationContext($conversationId);

            // بناء الرسائل
            $messages = $this->buildAdvancedMessages($message, $context, $options);

            // إرسال الطلب
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post($this->apiUrl, [
                'model' => $options['model'] ?? $this->model,
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? $this->temperature,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            ]);

            if (!$response->successful()) {
                throw new Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? '';

            // حفظ السياق
            $this->saveConversationContext($conversationId, $message, $reply);

            return [
                'success' => true,
                'message' => $reply,
                'conversation_id' => $conversationId,
                'usage' => [
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                    'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                    'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                ],
            ];

        } catch (Exception $e) {
            Log::error('AiAssistantPlusService Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'حدث خطأ في الاتصال بالذكاء الاصطناعي: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * تحليل كود متقدم مع اقتراحات تحسين
     *
     * @param string $code الكود المراد تحليله
     * @param string $language لغة البرمجة
     * @return array
     */
    public function analyzeCodeAdvanced(string $code, string $language = 'PHP'): array
    {
        $prompt = "قم بتحليل الكود التالي ({$language}) بشكل متقدم وأعطني:\n\n";
        $prompt .= "1. **ملخص الوظيفة**: وصف مختصر لما يفعله الكود\n";
        $prompt .= "2. **التعقيد الزمني**: Big O Notation\n";
        $prompt .= "3. **نقاط القوة**: ما هو جيد في الكود\n";
        $prompt .= "4. **نقاط الضعف**: المشاكل والأخطاء المحتملة\n";
        $prompt .= "5. **مخاطر الأمان**: ثغرات أمنية محتملة\n";
        $prompt .= "6. **اقتراحات التحسين**: كيف يمكن تحسين الكود\n";
        $prompt .= "7. **الكود المحسّن**: نسخة محسنة من الكود\n\n";
        $prompt .= "الكود:\n```{$language}\n{$code}\n```";

        return $this->chat($prompt, 'code_analysis_' . md5($code), [
            'temperature' => 0.3, // دقة أعلى للتحليل
        ]);
    }

    /**
     * توليد كود متقدم مع شرح تفصيلي
     *
     * @param string $description وصف المهمة
     * @param string $language لغة البرمجة
     * @param array $requirements متطلبات إضافية
     * @return array
     */
    public function generateCodeAdvanced(string $description, string $language = 'PHP', array $requirements = []): array
    {
        $prompt = "قم بتوليد كود {$language} احترافي للمهمة التالية:\n\n";
        $prompt .= "**المهمة**: {$description}\n\n";

        if (!empty($requirements)) {
            $prompt .= "**المتطلبات الإضافية**:\n";
            foreach ($requirements as $req) {
                $prompt .= "- {$req}\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "يجب أن يتضمن الكود:\n";
        $prompt .= "1. تعليقات توضيحية شاملة\n";
        $prompt .= "2. معالجة الأخطاء (Error Handling)\n";
        $prompt .= "3. التحقق من المدخلات (Validation)\n";
        $prompt .= "4. أفضل الممارسات (Best Practices)\n";
        $prompt .= "5. كود نظيف وقابل للصيانة\n\n";
        $prompt .= "أعطني:\n";
        $prompt .= "- الكود الكامل\n";
        $prompt .= "- شرح مختصر لكل جزء\n";
        $prompt .= "- أمثلة على الاستخدام";

        return $this->chat($prompt, 'code_generation_' . md5($description), [
            'temperature' => 0.5,
            'max_tokens' => 6000,
        ]);
    }

    /**
     * إصلاح أخطاء متقدم مع شرح السبب
     *
     * @param string $code الكود الذي به خطأ
     * @param string $error رسالة الخطأ
     * @param string $language لغة البرمجة
     * @return array
     */
    public function fixBugAdvanced(string $code, string $error, string $language = 'PHP'): array
    {
        $prompt = "الكود التالي ({$language}) به خطأ. قم بتحليله وإصلاحه:\n\n";
        $prompt .= "**رسالة الخطأ**:\n```\n{$error}\n```\n\n";
        $prompt .= "**الكود**:\n```{$language}\n{$code}\n```\n\n";
        $prompt .= "أعطني:\n";
        $prompt .= "1. **تشخيص المشكلة**: ما هو سبب الخطأ بالتفصيل\n";
        $prompt .= "2. **الكود المصلح**: الكود بعد الإصلاح\n";
        $prompt .= "3. **الشرح**: شرح التغييرات التي تم إجراؤها\n";
        $prompt .= "4. **نصائح للوقاية**: كيف تتجنب هذا الخطأ مستقبلاً";

        return $this->chat($prompt, 'bug_fix_' . md5($code . $error), [
            'temperature' => 0.2,
        ]);
    }

    /**
     * إعادة هيكلة الكود (Refactoring)
     *
     * @param string $code الكود المراد إعادة هيكلته
     * @param string $language لغة البرمجة
     * @return array
     */
    public function refactorCode(string $code, string $language = 'PHP'): array
    {
        $prompt = "قم بإعادة هيكلة الكود التالي ({$language}) لجعله أفضل:\n\n";
        $prompt .= "```{$language}\n{$code}\n```\n\n";
        $prompt .= "ركز على:\n";
        $prompt .= "1. تحسين القراءة (Readability)\n";
        $prompt .= "2. تقليل التعقيد (Complexity Reduction)\n";
        $prompt .= "3. إزالة التكرار (DRY Principle)\n";
        $prompt .= "4. تطبيق Design Patterns المناسبة\n";
        $prompt .= "5. تحسين الأداء\n\n";
        $prompt .= "أعطني الكود المحسّن مع شرح التغييرات";

        return $this->chat($prompt, 'refactor_' . md5($code), [
            'temperature' => 0.4,
        ]);
    }

    /**
     * توليد اختبارات تلقائية
     *
     * @param string $code الكود المراد اختباره
     * @param string $language لغة البرمجة
     * @return array
     */
    public function generateTests(string $code, string $language = 'PHP'): array
    {
        $framework = $language === 'PHP' ? 'PHPUnit' : 'Jest';
        
        $prompt = "قم بتوليد اختبارات {$framework} شاملة للكود التالي:\n\n";
        $prompt .= "```{$language}\n{$code}\n```\n\n";
        $prompt .= "يجب أن تغطي الاختبارات:\n";
        $prompt .= "1. الحالات الطبيعية (Happy Path)\n";
        $prompt .= "2. حالات الأخطاء (Error Cases)\n";
        $prompt .= "3. الحالات الحدية (Edge Cases)\n";
        $prompt .= "4. اختبارات الأداء إن أمكن\n\n";
        $prompt .= "أعطني كود الاختبارات كاملاً";

        return $this->chat($prompt, 'test_gen_' . md5($code), [
            'temperature' => 0.3,
        ]);
    }

    /**
     * توليد توثيق تلقائي
     *
     * @param string $code الكود المراد توثيقه
     * @param string $language لغة البرمجة
     * @return array
     */
    public function generateDocumentation(string $code, string $language = 'PHP'): array
    {
        $prompt = "قم بتوليد توثيق شامل للكود التالي ({$language}):\n\n";
        $prompt .= "```{$language}\n{$code}\n```\n\n";
        $prompt .= "يجب أن يتضمن التوثيق:\n";
        $prompt .= "1. وصف عام للكود\n";
        $prompt .= "2. شرح كل دالة/method\n";
        $prompt .= "3. المعاملات (Parameters) ونوعها\n";
        $prompt .= "4. القيم المرجعة (Return Values)\n";
        $prompt .= "5. أمثلة على الاستخدام\n";
        $prompt .= "6. ملاحظات هامة\n\n";
        $prompt .= "استخدم تنسيق PHPDoc أو JSDoc حسب اللغة";

        return $this->chat($prompt, 'doc_gen_' . md5($code), [
            'temperature' => 0.4,
        ]);
    }

    /**
     * فحص الأمان
     *
     * @param string $code الكود المراد فحصه
     * @param string $language لغة البرمجة
     * @return array
     */
    public function securityScan(string $code, string $language = 'PHP'): array
    {
        $prompt = "قم بفحص الأمان للكود التالي ({$language}) وابحث عن:\n\n";
        $prompt .= "```{$language}\n{$code}\n```\n\n";
        $prompt .= "ابحث عن:\n";
        $prompt .= "1. SQL Injection\n";
        $prompt .= "2. XSS (Cross-Site Scripting)\n";
        $prompt .= "3. CSRF\n";
        $prompt .= "4. Authentication/Authorization Issues\n";
        $prompt .= "5. Sensitive Data Exposure\n";
        $prompt .= "6. أي ثغرات أمنية أخرى\n\n";
        $prompt .= "أعطني:\n";
        $prompt .= "- قائمة بالثغرات المكتشفة\n";
        $prompt .= "- مستوى الخطورة لكل ثغرة\n";
        $prompt .= "- الحلول المقترحة";

        return $this->chat($prompt, 'security_scan_' . md5($code), [
            'temperature' => 0.2,
        ]);
    }

    /**
     * تحسين الأداء
     *
     * @param string $code الكود المراد تحسينه
     * @param string $language لغة البرمجة
     * @return array
     */
    public function optimizePerformance(string $code, string $language = 'PHP'): array
    {
        $prompt = "قم بتحليل أداء الكود التالي ({$language}) واقترح تحسينات:\n\n";
        $prompt .= "```{$language}\n{$code}\n```\n\n";
        $prompt .= "ركز على:\n";
        $prompt .= "1. تحسين التعقيد الزمني\n";
        $prompt .= "2. تقليل استهلاك الذاكرة\n";
        $prompt .= "3. تحسين استعلامات قاعدة البيانات\n";
        $prompt .= "4. استخدام Caching حيث يلزم\n";
        $prompt .= "5. تحسين الخوارزميات\n\n";
        $prompt .= "أعطني الكود المحسّن مع شرح التحسينات";

        return $this->chat($prompt, 'perf_opt_' . md5($code), [
            'temperature' => 0.3,
        ]);
    }

    /**
     * ترجمة الكود بين اللغات
     *
     * @param string $code الكود المراد ترجمته
     * @param string $fromLanguage اللغة المصدر
     * @param string $toLanguage اللغة الهدف
     * @return array
     */
    public function translateCode(string $code, string $fromLanguage, string $toLanguage): array
    {
        $prompt = "قم بترجمة الكود التالي من {$fromLanguage} إلى {$toLanguage}:\n\n";
        $prompt .= "```{$fromLanguage}\n{$code}\n```\n\n";
        $prompt .= "احرص على:\n";
        $prompt .= "1. الحفاظ على نفس المنطق\n";
        $prompt .= "2. استخدام أفضل الممارسات في {$toLanguage}\n";
        $prompt .= "3. إضافة تعليقات توضيحية\n";
        $prompt .= "4. التعامل مع الاختلافات بين اللغتين\n\n";
        $prompt .= "أعطني الكود المترجم مع ملاحظات عن الاختلافات";

        return $this->chat($prompt, 'translate_' . md5($code), [
            'temperature' => 0.4,
        ]);
    }

    /**
     * اقتراحات ذكية بناءً على السياق
     *
     * @param string $context السياق الحالي
     * @return array
     */
    public function getSuggestions(string $context): array
    {
        $prompt = "بناءً على السياق التالي، اقترح أفضل الخطوات التالية:\n\n{$context}\n\n";
        $prompt .= "أعطني:\n";
        $prompt .= "1. اقتراحات للتحسين\n";
        $prompt .= "2. أدوات مفيدة\n";
        $prompt .= "3. أفضل الممارسات\n";
        $prompt .= "4. موارد تعليمية";

        return $this->chat($prompt, 'suggestions_' . md5($context), [
            'temperature' => 0.6,
        ]);
    }

    /**
     * بناء رسائل متقدمة مع سياق ذكي
     *
     * @param string $message
     * @param array $context
     * @param array $options
     * @return array
     */
    private function buildAdvancedMessages(string $message, array $context, array $options): array
    {
        $systemPrompt = $options['system_prompt'] ?? 
            'أنت مساعد ذكي متقدم للمطورين في نظام SEMOP. ' .
            'تساعد في البرمجة، تحليل الأكواد، إصلاح الأخطاء، وتحسين الأداء. ' .
            'أجب باللغة العربية بشكل احترافي ومفصل مع أمثلة عملية. ' .
            'استخدم تنسيق Markdown للردود الطويلة.';

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
        ];

        // إضافة السياق السابق (آخر 10 رسائل)
        $recentContext = array_slice($context, -10);
        foreach ($recentContext as $msg) {
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
     * جلب سياق المحادثة من الذاكرة المؤقتة
     *
     * @param string $conversationId
     * @return array
     */
    private function getConversationContext(string $conversationId): array
    {
        $cacheKey = "ai_conversation_{$conversationId}";
        return Cache::get($cacheKey, []);
    }

    /**
     * حفظ سياق المحادثة في الذاكرة المؤقتة
     *
     * @param string $conversationId
     * @param string $userMessage
     * @param string $aiReply
     * @return void
     */
    private function saveConversationContext(string $conversationId, string $userMessage, string $aiReply): void
    {
        $cacheKey = "ai_conversation_{$conversationId}";
        $context = $this->getConversationContext($conversationId);

        $context[] = ['role' => 'user', 'content' => $userMessage];
        $context[] = ['role' => 'assistant', 'content' => $aiReply];

        // حفظ آخر 20 رسالة فقط
        $context = array_slice($context, -20);

        // حفظ لمدة 24 ساعة
        Cache::put($cacheKey, $context, now()->addHours(24));
    }

    /**
     * مسح سياق محادثة معينة
     *
     * @param string $conversationId
     * @return void
     */
    public function clearConversation(string $conversationId): void
    {
        $cacheKey = "ai_conversation_{$conversationId}";
        Cache::forget($cacheKey);
    }

    /**
     * الحصول على إحصائيات الاستخدام
     *
     * @return array
     */
    public function getUsageStats(): array
    {
        // يمكن تطوير هذه الدالة لجلب إحصائيات من قاعدة البيانات
        return [
            'total_conversations' => 0,
            'total_messages' => 0,
            'total_tokens_used' => 0,
        ];
    }
}
