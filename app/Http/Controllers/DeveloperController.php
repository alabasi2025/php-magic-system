<?php

namespace App\Http\Controllers;

use App\Services\AI\PerformanceAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * DeveloperController
 *
 * المتحكم الخاص بأدوات المطورين، بما في ذلك تحليل أداء الكود.
 * This controller provides tools for developers, such as code performance analysis.
 */
class DeveloperController extends Controller
{
    /**
     * خدمة تحليل الأداء.
     * The performance analyzer service instance.
     *
     * @var PerformanceAnalyzerService
     */
    protected $analyzerService;

    /**
     * إنشاء مثيل جديد للمتحكم.
     * Create a new controller instance.
     *
     * @param PerformanceAnalyzerService $analyzerService
     * @return void
     */
    public function __construct(PerformanceAnalyzerService $analyzerService)
    {
        $this->analyzerService = $analyzerService;
    }

    /**
     * دالة لتحليل أداء الكود باستخدام الذكاء الاصطناعي.
     * Analyzes the provided code for performance using AI.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzePerformanceWithAi(Request $request): JsonResponse
    {
        try {
            // 1. التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:10',
                'language' => 'nullable|string|in:PHP,JavaScript,Python,Java,C++', // دعم لغات متعددة
            ], [
                'code.required' => 'حقل الكود مطلوب للتحليل.',
                'code.min' => 'يجب أن يحتوي الكود على 10 أحرف على الأقل.',
                'language.in' => 'لغة البرمجة المدخلة غير مدعومة حاليًا.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $code = $validated['code'];
            $language = $validated['language'] ?? 'PHP';

            // 2. استدعاء PerformanceAnalyzerService
            $analysisResult = $this->analyzerService->analyze($code, $language);

            // 3. إرجاع النتائج بصيغة JSON
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحليل الكود بنجاح بواسطة الذكاء الاصطناعي.',
                'data' => $analysisResult,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            // معالجة أخطاء التحقق من صحة المدخلات
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات.',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // 4. معالجة الأخطاء العامة (مثل فشل الاتصال بالذكاء الاصطناعي)
            // تسجيل الخطأ للمراجعة
            Log::error('Error during performance analysis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // إرجاع استجابة خطأ احترافية
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء تحليل الأداء. يرجى المحاولة لاحقًا.',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * دالة افتراضية أخرى (لضمان أن الملف يحتوي على دوال أخرى كما طلب المستخدم).
     * Another dummy function to ensure the file contains other functions.
     *
     * @return JsonResponse
     */
    public function getDeveloperTools(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'tools' => [
                'code_analysis' => 'Analyze code performance and security.',
                'db_migration' => 'Manage database migrations.',
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
