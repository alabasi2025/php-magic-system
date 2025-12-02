<?php

namespace App\Services;

use OpenAI\Client;
use Exception;

/**
 * AI Helper Tools Service
 * 
 * خدمة أدوات الذكاء الاصطناعي المساعدة
 * تشمل: Code Review, Bug Fixing, Test Generation, Documentation
 * 
 * @package App\Services
 * @version 1.0.0
 */
class AiHelperToolsService
{
    private $client;
    private $model = 'gpt-4.1-mini';
    private $temperature = 0.7;

    public function __construct()
    {
        $this->client = \OpenAI::client(env('OPENAI_API_KEY'));
    }

    /**
     * مراجعة الأكواد بالذكاء الاصطناعي
     * 
     * @param string $code الكود المراد مراجعته
     * @param string $language لغة البرمجة
     * @return array النتيجة تحتوي على التقرير والاقتراحات
     */
    public function reviewCode(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildCodeReviewPrompt($code, $language);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getCodeReviewSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 3000,
            ]);

            $content = $response->choices[0]->message->content;
            $review = $this->parseCodeReview($content);
            
            return [
                'success' => true,
                'review' => $review,
                'raw_response' => $content,
                'message' => 'تم مراجعة الكود بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في مراجعة الكود: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * إصلاح الأخطاء في الأكواد
     * 
     * @param string $code الكود الذي يحتوي على أخطاء
     * @param string $errorMessage رسالة الخطأ
     * @param string $language لغة البرمجة
     * @return array النتيجة تحتوي على الكود المصحح
     */
    public function fixBug(string $code, string $errorMessage, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildBugFixPrompt($code, $errorMessage, $language);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getBugFixSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 2500,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'fixed_code' => $content,
                'message' => 'تم إصلاح الخطأ بنجاح',
                'explanation' => $this->extractExplanation($content)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في إصلاح الكود: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * توليد اختبارات للأكواد
     * 
     * @param string $code الكود المراد اختباره
     * @param string $description وصف الاختبارات المطلوبة
     * @return array النتيجة تحتوي على الاختبارات
     */
    public function generateTests(string $code, string $description = ''): array
    {
        try {
            $prompt = $this->buildTestGenerationPrompt($code, $description);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getTestGenerationSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 3000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'tests' => $content,
                'message' => 'تم توليد الاختبارات بنجاح',
                'test_count' => $this->countTests($content)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في توليد الاختبارات: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * توليد التوثيق للأكواد
     * 
     * @param string $code الكود المراد توثيقه
     * @param string $language لغة البرمجة
     * @return array النتيجة تحتوي على التوثيق
     */
    public function generateDocumentation(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildDocumentationPrompt($code, $language);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getDocumentationSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 3500,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'documentation' => $content,
                'message' => 'تم توليد التوثيق بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في توليد التوثيق: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * تحسين الأداء (Performance Optimization)
     * 
     * @param string $code الكود المراد تحسينه
     * @return array النتيجة تحتوي على الكود المحسّن
     */
    public function optimizePerformance(string $code): array
    {
        try {
            $prompt = $this->buildPerformanceOptimizationPrompt($code);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getPerformanceOptimizationSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 3000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'optimized_code' => $content,
                'message' => 'تم تحسين الأداء بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في تحسين الأداء: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * تحسين الأمان (Security Enhancement)
     * 
     * @param string $code الكود المراد تحسين أمانه
     * @return array النتيجة تحتوي على الكود الآمن
     */
    public function enhanceSecurity(string $code): array
    {
        try {
            $prompt = $this->buildSecurityEnhancementPrompt($code);
            
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSecurityEnhancementSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 3000,
            ]);

            $content = $response->choices[0]->message->content;
            
            return [
                'success' => true,
                'secure_code' => $content,
                'message' => 'تم تحسين الأمان بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في تحسين الأمان: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    // ========================================
    // Prompt Builders
    // ========================================

    private function buildCodeReviewPrompt(string $code, string $language): string
    {
        return <<<PROMPT
قم بمراجعة الكود التالي بـ $language وقدم تقرير شامل:

```$language
$code
```

يجب أن يتضمن التقرير:
1. نقاط القوة
2. نقاط الضعف
3. الأخطاء المحتملة
4. اقتراحات للتحسين
5. مستوى الجودة (من 1 إلى 10)
6. أولويات التحسين

اكتب التقرير بصيغة منظمة وواضحة.
PROMPT;
    }

    private function buildBugFixPrompt(string $code, string $errorMessage, string $language): string
    {
        return <<<PROMPT
الكود التالي يحتوي على خطأ:

```$language
$code
```

رسالة الخطأ:
$errorMessage

قم بـ:
1. تحديد سبب الخطأ
2. إصلاح الكود
3. شرح الحل

قدم الكود المصحح مع التفسير.
PROMPT;
    }

    private function buildTestGenerationPrompt(string $code, string $description): string
    {
        $desc = $description ? "الوصف: $description" : '';
        
        return <<<PROMPT
قم بتوليد اختبارات شاملة للكود التالي:

```php
$code
```

$desc

يجب أن تغطي الاختبارات:
1. الحالات الطبيعية
2. حالات الخطأ
3. الحدود الحدية
4. الحالات الاستثنائية

استخدم Laravel Testing Syntax.
PROMPT;
    }

    private function buildDocumentationPrompt(string $code, string $language): string
    {
        return <<<PROMPT
قم بتوليد توثيق شامل للكود التالي بـ $language:

```$language
$code
```

يجب أن يتضمن التوثيق:
1. وصف الدالة/الفئة
2. المعاملات (Parameters)
3. القيمة المرجعة (Return Value)
4. أمثلة الاستخدام
5. الاستثناءات المحتملة
6. ملاحظات مهمة

اكتب التوثيق بصيغة احترافية.
PROMPT;
    }

    private function buildPerformanceOptimizationPrompt(string $code): string
    {
        return <<<PROMPT
قم بتحسين أداء الكود التالي:

```php
$code
```

ركز على:
1. تقليل عدد الاستعلامات (N+1 Problem)
2. استخدام Caching
3. تحسين الخوارزميات
4. استخدام الفهارس (Indexes)
5. تجنب العمليات الثقيلة

قدم الكود المحسّن مع شرح التحسينات.
PROMPT;
    }

    private function buildSecurityEnhancementPrompt(string $code): string
    {
        return <<<PROMPT
قم بتحسين أمان الكود التالي:

```php
$code
```

ركز على:
1. SQL Injection Prevention
2. XSS Prevention
3. CSRF Protection
4. Authentication & Authorization
5. Input Validation
6. Secure Password Handling

قدم الكود الآمن مع شرح التحسينات.
PROMPT;
    }

    // ========================================
    // System Prompts
    // ========================================

    private function getCodeReviewSystemPrompt(): string
    {
        return <<<PROMPT
أنت مراجع أكواد محترف وخبير في أفضل الممارسات البرمجية.

قم بـ:
- تقييم جودة الكود
- تحديد المشاكل والأخطاء
- تقديم اقتراحات بناءة
- اتباع معايير الصناعة
- الكتابة بوضوح وسهولة الفهم

كن دقيقاً وعملياً في تقييمك.
PROMPT;
    }

    private function getBugFixSystemPrompt(): string
    {
        return <<<PROMPT
أنت خبير في إصلاح الأخطاء البرمجية.

قم بـ:
- فهم سبب الخطأ بدقة
- تقديم حل صحيح وفعال
- شرح الحل بوضوح
- تجنب الحلول المؤقتة
- اتباع أفضل الممارسات

كن دقيقاً وشاملاً في الحل.
PROMPT;
    }

    private function getTestGenerationSystemPrompt(): string
    {
        return <<<PROMPT
أنت خبير في كتابة الاختبارات الشاملة والفعالة.

قم بـ:
- توليد اختبارات شاملة
- تغطية جميع الحالات
- استخدام أفضل الممارسات
- كتابة اختبارات قابلة للصيانة
- إضافة تعليقات واضحة

كن شاملاً وفعالاً في الاختبارات.
PROMPT;
    }

    private function getDocumentationSystemPrompt(): string
    {
        return <<<PROMPT
أنت خبير في كتابة التوثيق الاحترافي والواضح.

قم بـ:
- كتابة توثيق شامل وواضح
- استخدام أمثلة عملية
- شرح المعاملات والقيم المرجعة
- إضافة ملاحظات مهمة
- اتباع معايير التوثيق

كن واضحاً وشاملاً في التوثيق.
PROMPT;
    }

    private function getPerformanceOptimizationSystemPrompt(): string
    {
        return <<<PROMPT
أنت خبير في تحسين أداء التطبيقات.

ركز على:
- تقليل استهلاك الموارد
- تحسين سرعة التنفيذ
- استخدام Caching بفعالية
- تحسين الاستعلامات
- تجنب الاختناقات

قدم حلولاً عملية وفعالة.
PROMPT;
    }

    private function getSecurityEnhancementSystemPrompt(): string
    {
        return <<<PROMPT
أنت خبير في أمان التطبيقات والبرمجة الآمنة.

ركز على:
- منع الثغرات الشائعة
- تطبيق أفضل ممارسات الأمان
- حماية البيانات الحساسة
- المصادقة والتفويض
- التحقق من المدخلات

قدم حلولاً آمنة وموثوقة.
PROMPT;
    }

    // ========================================
    // Parsing Methods
    // ========================================

    private function parseCodeReview(string $response): array
    {
        return [
            'summary' => $this->extractSection($response, 'ملخص|Summary'),
            'strengths' => $this->extractSection($response, 'نقاط القوة|Strengths'),
            'weaknesses' => $this->extractSection($response, 'نقاط الضعف|Weaknesses'),
            'suggestions' => $this->extractSection($response, 'الاقتراحات|Suggestions'),
            'quality_score' => $this->extractScore($response),
            'priorities' => $this->extractSection($response, 'الأولويات|Priorities')
        ];
    }

    private function extractExplanation(string $content): string
    {
        if (preg_match('/شرح|Explanation|التفسير:?\s*(.+?)(?=```|$)/is', $content, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    private function extractSection(string $content, string $pattern): string
    {
        if (preg_match("/$pattern:?\s*(.+?)(?=\n\n|$)/is", $content, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    private function extractScore(string $content): ?int
    {
        if (preg_match('/(\d+)\s*(?:\/|out of)\s*10/i', $content, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    private function countTests(string $content): int
    {
        return substr_count($content, 'public function test') + substr_count($content, 'public function it');
    }
}
