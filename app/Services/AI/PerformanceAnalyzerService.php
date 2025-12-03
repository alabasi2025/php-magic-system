<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * @class PerformanceAnalyzerService
 * @package App\Services\AI
 *
 * خدمة متكاملة لتحليل أداء الكود باستخدام OpenAI API.
 * تتضمن تحليل سرعة الكود، كشف الاختناقات (Bottlenecks)، واقتراحات التحسين.
 */
class PerformanceAnalyzerService
{
    /**
     * تحليل أداء جزء من الكود وتقديم تقرير شامل.
     *
     * @param string $code الكود المراد تحليله.
     * @param string $language لغة البرمجة للكود (مثل 'PHP', 'JavaScript').
     * @param string $locale اللغة المطلوبة للتقرير ('ar' أو 'en').
     * @return array تقرير التحليل الشامل.
     * @throws Throwable
     */
    public function analyze(string $code, string $language = 'PHP', string $locale = 'ar'): array
    {
        try {
            // إعداد الـ prompt للذكاء الاصطناعي
            $prompt = $this->buildPrompt($code, $language, $locale);

            // استدعاء OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $locale === 'ar' 
                            ? 'أنت خبير في تحليل أداء الكود وتحسينه. قدم تحليلاً شاملاً ومفصلاً.'
                            : 'You are an expert in code performance analysis and optimization. Provide comprehensive and detailed analysis.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if (!$response->successful()) {
                throw new \Exception('فشل الاتصال بـ OpenAI API: ' . $response->body());
            }

            $result = $response->json();
            $analysisText = $result['choices'][0]['message']['content'] ?? '';

            // تحليل النص وتحويله إلى بيانات منظمة
            return $this->parseAnalysis($analysisText, $locale);

        } catch (Throwable $e) {
            // معالجة الأخطاء
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'speed' => $locale === 'ar' ? 'غير متاح' : 'N/A',
                'bottlenecks' => [],
                'suggestions' => [],
                'complexity' => $locale === 'ar' ? 'غير محدد' : 'Unknown',
            ];
        }
    }

    /**
     * بناء الـ prompt المناسب للذكاء الاصطناعي
     *
     * @param string $code
     * @param string $language
     * @param string $locale
     * @return string
     */
    protected function buildPrompt(string $code, string $language, string $locale): string
    {
        if ($locale === 'ar') {
            return <<<PROMPT
قم بتحليل الكود التالي المكتوب بلغة {$language} وقدم تقريراً شاملاً يتضمن:

1. **السرعة المقدرة**: تقييم سرعة تنفيذ الكود (سريع، متوسط، بطيء)
2. **التعقيد الزمني**: Time Complexity (مثل O(n), O(n²))
3. **نقاط الاختناق (Bottlenecks)**: قائمة بالمشاكل التي تؤثر على الأداء
4. **اقتراحات التحسين**: توصيات محددة لتحسين الأداء

الكود:
```{$language}
{$code}
```

قدم الإجابة بتنسيق واضح ومنظم.
PROMPT;
        } else {
            return <<<PROMPT
Analyze the following {$language} code and provide a comprehensive report including:

1. **Estimated Speed**: Performance rating (Fast, Medium, Slow)
2. **Time Complexity**: Big O notation (e.g., O(n), O(n²))
3. **Bottlenecks**: List of performance issues
4. **Optimization Suggestions**: Specific recommendations to improve performance

Code:
```{$language}
{$code}
```

Provide the answer in a clear and organized format.
PROMPT;
        }
    }

    /**
     * تحليل نص الإجابة وتحويله إلى بيانات منظمة
     *
     * @param string $text
     * @param string $locale
     * @return array
     */
    protected function parseAnalysis(string $text, string $locale): array
    {
        // استخراج المعلومات من النص
        $speed = $this->extractSpeed($text, $locale);
        $complexity = $this->extractComplexity($text);
        $bottlenecks = $this->extractBottlenecks($text, $locale);
        $suggestions = $this->extractSuggestions($text, $locale);

        return [
            'success' => true,
            'speed' => $speed,
            'complexity' => $complexity,
            'bottlenecks' => $bottlenecks,
            'suggestions' => $suggestions,
            'raw_analysis' => $text,
        ];
    }

    /**
     * استخراج السرعة من النص
     */
    protected function extractSpeed(string $text, string $locale): string
    {
        if ($locale === 'ar') {
            if (preg_match('/(سريع|سريعة)/i', $text)) return 'سريع';
            if (preg_match('/(بطيء|بطيئة)/i', $text)) return 'بطيء';
            return 'متوسط';
        } else {
            if (preg_match('/\b(fast|quick|efficient)\b/i', $text)) return 'Fast';
            if (preg_match('/\b(slow|inefficient|poor)\b/i', $text)) return 'Slow';
            return 'Medium';
        }
    }

    /**
     * استخراج التعقيد الزمني
     */
    protected function extractComplexity(string $text): string
    {
        if (preg_match('/O\([^)]+\)/i', $text, $matches)) {
            return $matches[0];
        }
        return 'O(n)';
    }

    /**
     * استخراج نقاط الاختناق
     */
    protected function extractBottlenecks(string $text, string $locale): array
    {
        $bottlenecks = [];
        
        // البحث عن القوائم والنقاط
        if (preg_match_all('/[-•*]\s*(.+?)(?=\n|$)/u', $text, $matches)) {
            foreach ($matches[1] as $match) {
                $cleaned = trim($match);
                if (strlen($cleaned) > 10 && strlen($cleaned) < 200) {
                    $bottlenecks[] = $cleaned;
                }
            }
        }

        // إذا لم نجد شيء، نضيف رسالة افتراضية
        if (empty($bottlenecks)) {
            $bottlenecks[] = $locale === 'ar' 
                ? 'لم يتم اكتشاف مشاكل أداء واضحة'
                : 'No obvious performance issues detected';
        }

        return array_slice($bottlenecks, 0, 5); // أول 5 فقط
    }

    /**
     * استخراج الاقتراحات
     */
    protected function extractSuggestions(string $text, string $locale): array
    {
        $suggestions = [];
        
        // البحث عن الاقتراحات
        if (preg_match_all('/[-•*]\s*(.+?)(?=\n|$)/u', $text, $matches)) {
            foreach ($matches[1] as $match) {
                $cleaned = trim($match);
                if (strlen($cleaned) > 10 && strlen($cleaned) < 200) {
                    $suggestions[] = $cleaned;
                }
            }
        }

        // إذا لم نجد شيء، نضيف اقتراح افتراضي
        if (empty($suggestions)) {
            $suggestions[] = $locale === 'ar'
                ? 'الكود يبدو جيداً، استمر في المراجعة الدورية'
                : 'Code looks good, continue with regular reviews';
        }

        return array_slice($suggestions, 0, 7); // أول 7 فقط
    }
}
