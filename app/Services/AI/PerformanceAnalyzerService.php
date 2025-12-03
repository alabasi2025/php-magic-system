<?php

namespace App\Services\AI;

use OpenAI\Client;
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
     * @var Client
     */
    protected Client $openai;

    /**
     * PerformanceAnalyzerService constructor.
     *
     * @param Client $openai عميل OpenAI API المحقون.
     */
    public function __construct(Client $openai)
    {
        $this->openai = $openai;
    }

    /**
     * تحليل أداء جزء من الكود وتقديم تقرير شامل.
     *
     * @param string $code الكود المراد تحليله.
     * @param string $language لغة البرمجة للكود (مثل 'PHP', 'JavaScript').
     * @param string $locale اللغة المطلوبة للتقرير ('ar' أو 'en').
     * @return array تقرير التحليل الشامل.
     * @throws Throwable
     */
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
        $prompt = $this->buildAnalysisPrompt($code, $language, $locale);

        try {
            $response = $this->openai->chat()->create([
                'model' => 'gpt-4.1-mini', // استخدام النموذج المطلوب
                'messages' => [
                    ['role' => 'system', 'content' => $this->getSystemPrompt($locale)],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content;
            $report = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // محاولة استخراج JSON من النص إذا لم يكن JSON خالصاً
                return $this->handleNonJsonOutput($content, $locale);
            }

            return $report;

        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
            $errorDetails = $locale === 'ar' ?
                "حدث خطأ أثناء الاتصال بخدمة الذكاء الاصطناعي أو معالجة الطلب: {$errorMessage}" :
                "An error occurred while connecting to the AI service or processing the request: {$errorMessage}";

            return [
                'status' => 'failed',
                'message' => $errorDetails,
                'error_type' => get_class($e),
                'code' => $e->getCode(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ];
        }
    }

    /**
     * بناء موجه التحليل (Prompt) لـ OpenAI.
     *
     * @param string $code
     * @param string $language
     * @param string $locale
     * @return string
     */
    protected function buildAnalysisPrompt(string $code, string $language, string $locale): string
    {
        $langText = $locale === 'ar' ? 'باللغة العربية' : 'in English';
        $requirements = $locale === 'ar' ?
            "قم بتحليل الكود التالي ($language) وقدّم تقريراً شاملاً بصيغة JSON $langText. يجب أن يتضمن التقرير:
            1. تحليل تعقيد الوقت (Time Complexity Analysis).
            2. كشف الاختناقات (Bottlenecks) والمشاكل المحتملة.
            3. اقتراحات تحسين مفصلة (Detailed Improvement Suggestions).
            4. تقرير أداء شامل (Comprehensive Performance Report) يوضح التقييم العام.
            الكود المراد تحليله:" :
            "Analyze the following code ($language) and provide a comprehensive report in JSON format $langText. The report must include:
            1. Time Complexity Analysis.
            2. Bottleneck detection and potential issues.
            3. Detailed Improvement Suggestions.
            4. A Comprehensive Performance Report showing the overall assessment.
            The code to analyze:";

        return $requirements . "\n\n```{$language}\n{$code}\n```";
    }

    /**
     * الحصول على موجه النظام (System Prompt) لتعريف دور الذكاء الاصطناعي.
     *
     * @param string $locale
     * @return string
     */
    protected function getSystemPrompt(string $locale): string
    {
        $jsonSchema = '{
            "overall_assessment": "string",
            "time_complexity_analysis": "string",
            "bottlenecks": ["string"],
            "improvement_suggestions": ["string"],
            "detailed_report": "string",
            "status": "success"
        }';

        if ($locale === 'ar') {
            return "أنت محلل أداء كود خبير ومحترف. مهمتك هي تحليل الكود بدقة وتقديم تقرير مفصل وشامل بصيغة JSON فقط. يجب أن يكون الإخراج بصيغة JSON التالية: $jsonSchema";
        }

        return "You are an expert and professional code performance analyzer. Your task is to accurately analyze the code and provide a detailed and comprehensive report in JSON format ONLY. The output must follow this JSON schema: $jsonSchema";
    }

    /**
     * معالجة حالة عدم الحصول على JSON صالح من الرد.
     *
     * @param string $content
     * @param string $locale
     * @return array
     */
    protected function handleNonJsonOutput(string $content, string $locale): array
    {
        $message = $locale === 'ar' ?
            'فشل في تحليل استجابة الذكاء الاصطناعي. الرد الخام:' :
            'Failed to parse AI response. Raw response:';

        // محاولة استخراج كتلة JSON من الرد الخام
        if (preg_match('/```json\s*(\{.*\})\s*```/s', $content, $matches)) {
            $jsonContent = $matches[1];
            $report = json_decode($jsonContent, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $report;
            }
        }

        return [
            'status' => 'failed',
            'message' => $message,
            'raw_response' => $content,
            'error_details' => $locale === 'ar' ? 'الرد لم يكن بصيغة JSON صالحة.' : 'Response was not a valid JSON object.',
        ];
    }
}
