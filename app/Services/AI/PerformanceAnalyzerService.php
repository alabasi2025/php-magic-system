<?php

namespace App\Services\AI;

use Exception;
use JsonException;
use Psr\Log\LoggerInterface;

/**
 * @class PerformanceAnalyzerService
 * @brief خدمة تحليل أداء الكود البرمجي باستخدام واجهة Manus AI.
 *
 * توفر هذه الخدمة مجموعة من الأدوات لتحليل جوانب مختلفة من أداء الكود
 * مثل السرعة، استهلاك الذاكرة، نقاط الاختناق، وتحليل استعلامات قواعد البيانات.
 * يتم استخدام Manus AI لتحليل الكود وإصدار تقارير مفصلة.
 *
 * ملاحظة: تم افتراض وجود فئة ManusAIClient للاتصال بواجهة Manus AI
 * وواجهة LoggerInterface لتسجيل الأحداث.
 */
class PerformanceAnalyzerService
{
    private ManusAIClient $aiClient;
    private LoggerInterface $logger;

    // تعريف ثابت لـ Prompt يحدد الهيكل المطلوب لنتائج الذكاء الاصطناعي
    private const JSON_SCHEMA_PROMPT = 'يجب أن تكون الإجابة بصيغة JSON فقط، وتتبع الهيكل التالي: {"score": int, "summary": string, "details": array, "recommendations": array}.';

    /**
     * الدالة البانية للخدمة.
     *
     * @param ManusAIClient $aiClient عميل الاتصال بواجهة Manus AI.
     * @param LoggerInterface $logger واجهة تسجيل الأحداث (Logging).
     */
    public function __construct(ManusAIClient $aiClient, LoggerInterface $logger)
    {
        $this->aiClient = $aiClient;
        $this->logger = $logger;
    }

    /**
     * دالة مساعدة للاتصال بواجهة Manus AI وتحليل الكود.
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @param string $prompt التعليمات الموجهة لنموذج الذكاء الاصطناعي.
     * @param string $analysisType نوع التحليل (مثل: bottlenecks, speed, memory).
     * @return array نتيجة التحليل بصيغة مصفوفة PHP.
     */
    private function callAIAnalysis(string $code, string $prompt, string $analysisType): array
    {
        $fullPrompt = "أنت محلل أداء كود برمجي خبير. قم بتحليل الكود التالي: \n\n```\n{$code}\n```\n\n بناءً على التعليمات: {$prompt} \n\n" . self::JSON_SCHEMA_PROMPT;

        try {
            // افتراض أن aiClient->analyze() ترجع سلسلة JSON
            $jsonResponse = $this->aiClient->analyze($fullPrompt);
            $result = json_decode($jsonResponse, true, 512, JSON_THROW_ON_ERROR);

            // التحقق من وجود Performance Score
            if (!isset($result['score']) || !is_int($result['score'])) {
                $this->logger->warning("AI response for {$analysisType} missing valid score.", ['response' => $result]);
                $result['score'] = 0; // تعيين قيمة افتراضية
            }

            return $result;

        } catch (JsonException $e) {
            $this->logger->error("Failed to decode AI JSON response for {$analysisType}: " . $e->getMessage(), ['response' => $jsonResponse ?? 'N/A']);
            return $this->handleError("فشل في تحليل استجابة الذكاء الاصطناعي.", $analysisType);
        } catch (Exception $e) {
            $this->logger->error("AI client error during {$analysisType} analysis: " . $e->getMessage());
            return $this->handleError("خطأ في الاتصال بواجهة Manus AI.", $analysisType);
        }
    }

    /**
     * دالة مساعدة لمعالجة الأخطاء وإرجاع هيكل موحد.
     *
     * @param string $message رسالة الخطأ.
     * @param string $type نوع التحليل الذي فشل.
     * @return array هيكل نتيجة الخطأ.
     */
    private function handleError(string $message, string $type): array
    {
        return [
            'score' => 0,
            'summary' => "فشل التحليل: {$message}",
            'details' => ["النوع الفاشل: {$type}"],
            'recommendations' => ["الرجاء مراجعة سجلات الأخطاء (Logs) لمزيد من التفاصيل."],
            'status' => 'error'
        ];
    }

    /**
     * تحليل شامل لأداء الكود.
     *
     * يقوم بتجميع نتائج التحليلات الفرعية (نقاط الاختناق، السرعة، الذاكرة، الاستعلامات).
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @param string $type نوع الكود (مثل: php, javascript, sql).
     * @return string نتيجة التحليل الشامل بصيغة JSON.
     */
    public function analyzePerformance(string $code, string $type): string
    {
        $this->logger->info("Starting comprehensive performance analysis for type: {$type}.");

        $bottlenecks = $this->detectBottlenecks($code, false);
        $speed = $this->analyzeSpeed($code, false);
        $memory = $this->analyzeMemory($code, false);
        $queries = $this->analyzeQueries($code, false);

        $analysis = [
            'type' => $type,
            'bottlenecks' => json_decode($bottlenecks, true),
            'speed' => json_decode($speed, true),
            'memory' => json_decode($memory, true),
            'queries' => json_decode($queries, true),
        ];

        // حساب Performance Score الإجمالي
        $totalScore = 0;
        $count = 0;
        foreach (['bottlenecks', 'speed', 'memory', 'queries'] as $key) {
            if (isset($analysis[$key]['score'])) {
                $totalScore += $analysis[$key]['score'];
                $count++;
            }
        }
        $overallScore = $count > 0 ? (int) round($totalScore / $count) : 0;
        $analysis['overall_score'] = $overallScore;

        return $this->generateReport($analysis);
    }

    /**
     * كشف نقاط الاختناق (Bottlenecks) في الكود.
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @param bool $returnJson هل يجب إرجاع النتيجة كـ JSON مباشرة؟ (افتراضي: true).
     * @return string|array نتيجة التحليل بصيغة JSON أو مصفوفة PHP.
     */
    public function detectBottlenecks(string $code, bool $returnJson = true): string|array
    {
        $prompt = "قم بتحديد وتحليل جميع نقاط الاختناق المحتملة (Bottlenecks) في الكود. ركز على الحلقات المتداخلة، العمليات ذات التعقيد الزمني العالي (مثل O(n^2))، والعمليات التي تستهلك موارد بشكل غير فعال. اقترح تحسينات محددة.";
        $result = $this->callAIAnalysis($code, $prompt, 'bottlenecks');
        $result['analysis_type'] = 'Bottlenecks Detection';

        return $returnJson ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $result;
    }

    /**
     * تحليل السرعة (Speed Analysis) للكود.
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @param bool $returnJson هل يجب إرجاع النتيجة كـ JSON مباشرة؟ (افتراضي: true).
     * @return string|array نتيجة التحليل بصيغة JSON أو مصفوفة PHP.
     */
    public function analyzeSpeed(string $code, bool $returnJson = true): string|array
    {
        $prompt = "قم بتحليل كفاءة الكود من حيث السرعة والتعقيد الزمني (Time Complexity). حدد الأجزاء التي يمكن تسريعها باستخدام خوارزميات أو هياكل بيانات أكثر كفاءة. أعطِ تقديراً للتعقيد الزمني العام (Big O Notation).";
        $result = $this->callAIAnalysis($code, $prompt, 'speed');
        $result['analysis_type'] = 'Speed Analysis';

        return $returnJson ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $result;
    }

    /**
     * تحليل الذاكرة (Memory Analysis) للكود.
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @param bool $returnJson هل يجب إرجاع النتيجة كـ JSON مباشرة؟ (افتراضي: true).
     * @return string|array نتيجة التحليل بصيغة JSON أو مصفوفة PHP.
     */
    public function analyzeMemory(string $code, bool $returnJson = true): string|array
    {
        $prompt = "قم بتحليل استهلاك الذاكرة (Memory Consumption) للكود. حدد أي تسريبات محتملة للذاكرة (Memory Leaks) أو استخدام غير فعال للمتغيرات والمصفوفات الكبيرة. اقترح طرقاً لتقليل البصمة الذاكرية (Memory Footprint).";
        $result = $this->callAIAnalysis($code, $prompt, 'memory');
        $result['analysis_type'] = 'Memory Analysis';

        return $returnJson ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $result;
    }

    /**
     * تحليل الاستعلامات (Queries Analysis) ضمن الكود.
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @param bool $returnJson هل يجب إرجاع النتيجة كـ JSON مباشرة؟ (افتراضي: true).
     * @return string|array نتيجة التحليل بصيغة JSON أو مصفوفة PHP.
     */
    public function analyzeQueries(string $code, bool $returnJson = true): string|array
    {
        $prompt = "إذا كان الكود يحتوي على استعلامات قواعد بيانات (SQL, NoSQL)، قم بتحليل كفاءتها. ابحث عن مشكلات مثل استعلامات N+1، الاستعلامات غير المفهرسة، أو الاستعلامات التي تجلب بيانات أكثر من اللازم. اقترح تحسينات على الاستعلامات.";
        $result = $this->callAIAnalysis($code, $prompt, 'queries');
        $result['analysis_type'] = 'Queries Analysis';

        return $returnJson ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $result;
    }

    /**
     * توليد تقرير مفصل بناءً على نتائج التحليل المجمعة.
     *
     * @param array $analysis مصفوفة تحتوي على نتائج التحليلات الفرعية.
     * @return string التقرير النهائي بصيغة JSON منظمة.
     */
    public function generateReport(array $analysis): string
    {
        $this->logger->info("Generating final performance report.");

        $report = [
            'component_name' => 'Performance Analyzer Service Report',
            'overall_performance_score' => $analysis['overall_score'] ?? 0,
            'code_type' => $analysis['type'] ?? 'N/A',
            'summary_report' => "تقرير شامل لأداء الكود. النتيجة الإجمالية هي {$analysis['overall_score']} من 100.",
            'detailed_analysis' => [
                'bottlenecks' => $analysis['bottlenecks'] ?? $this->handleError('No data', 'bottlenecks'),
                'speed' => $analysis['speed'] ?? $this->handleError('No data', 'speed'),
                'memory' => $analysis['memory'] ?? $this->handleError('No data', 'memory'),
                'queries' => $analysis['queries'] ?? $this->handleError('No data', 'queries'),
            ],
            'final_recommendations' => $this->compileRecommendations($analysis),
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        try {
            return json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->error("Failed to encode final report to JSON: " . $e->getMessage());
            return json_encode($this->handleError("فشل في توليد التقرير النهائي.", 'Report Generation'));
        }
    }

    /**
     * تجميع التوصيات من جميع التحليلات الفرعية.
     *
     * @param array $analysis مصفوفة تحتوي على نتائج التحليلات الفرعية.
     * @return array قائمة موحدة بالتوصيات.
     */
    private function compileRecommendations(array $analysis): array
    {
        $recommendations = [];
        foreach (['bottlenecks', 'speed', 'memory', 'queries'] as $key) {
            if (isset($analysis[$key]['recommendations']) && is_array($analysis[$key]['recommendations'])) {
                $recommendations = array_merge($recommendations, $analysis[$key]['recommendations']);
            }
        }
        // إزالة التوصيات المكررة
        return array_values(array_unique($recommendations));
    }
}

// ملاحظة: يجب تعريف فئة ManusAIClient وواجهة LoggerInterface في بيئة العمل الفعلية.
// لغرض هذا المثال، نفترض وجود تعريفات بسيطة لهما لتجنب أخطاء التحميل.

/**
 * فئة وهمية لتمثيل عميل Manus AI.
 * في بيئة العمل الحقيقية، يجب أن تحتوي على منطق الاتصال بـ API.
 */
class ManusAIClient
{
    public function analyze(string $prompt): string
    {
        // منطق وهمي: إرجاع استجابة JSON صالحة
        // في التطبيق الحقيقي، سيتم استدعاء واجهة برمجة تطبيقات Manus AI هنا
        $mockResponse = [
            'score' => rand(60, 95),
            'summary' => 'تحليل مبدئي ممتاز، الكود منظم ولكن هناك مجال للتحسين.',
            'details' => ['تم تحديد حلقة واحدة يمكن تحسينها.'],
            'recommendations' => ['استبدال حلقة foreach بـ array_map لتحسين الأداء.'],
        ];
        return json_encode($mockResponse);
    }
}

/**
 * واجهة وهمية لتمثيل Logger.
 * في بيئة العمل الحقيقية، سيتم استخدام PSR-3 Logger.
 */
interface LoggerInterface
{
    public function info(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
}
// نهاية الملف
