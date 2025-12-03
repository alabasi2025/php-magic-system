<?php

namespace App\Http\Controllers;

use App\Services\AI\PerformanceAnalyzerService;
use App\Services\AI\CodeTranslatorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
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
     * خدمة ترجمة الأكواد.
     * The code translator service instance.
     *
     * @var CodeTranslatorService
     */
    protected $translatorService;

    /**
     * إنشاء مثيل جديد للمتحكم.
     * Create a new controller instance.
     *
     * @param PerformanceAnalyzerService $analyzerService
     * @return void
     */
    public function __construct(
        PerformanceAnalyzerService $analyzerService,
        CodeTranslatorService $translatorService
    ) {
        $this->analyzerService = $analyzerService;
        $this->translatorService = $translatorService;
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

    // ========================================
    // Code Translator Methods - v3.15.0
    // ========================================

    /**
     * عرض صفحة مترجم الأكواد
     * Display the code translator page
     *
     * @return \Illuminate\View\View
     */
    public function getAiCodeTranslatorPage()
    {
        return view('developer.ai.code-translator', [
            'title' => 'Code Translator - مترجم الأكواد',
            'version' => 'v3.15.0',
            'supported_languages' => $this->translatorService->getSupportedLanguages(),
        ]);
    }

    /**
     * ترجمة الكود باستخدام الذكاء الاصطناعي
     * Translate code using AI
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function translateCodeWithAi(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة المدخلات
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:5',
                'from_language' => 'required|string|in:php,python,javascript,java,csharp,typescript',
                'to_language' => 'required|string|in:php,python,javascript,java,csharp,typescript',
                'action' => 'required|string|in:translate,detect,validate,compare',
            ], [
                'code.required' => 'حقل الكود مطلوب',
                'code.min' => 'يجب أن يحتوي الكود على 5 أحرف على الأقل',
                'from_language.required' => 'لغة المصدر مطلوبة',
                'from_language.in' => 'لغة المصدر غير مدعومة',
                'to_language.required' => 'لغة الهدف مطلوبة',
                'to_language.in' => 'لغة الهدف غير مدعومة',
                'action.required' => 'الإجراء مطلوب',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $action = $validated['action'];

            // تنفيذ الإجراء المطلوب
            $result = match($action) {
                'translate' => $this->translatorService->translateCode(
                    $validated['code'],
                    $validated['from_language'],
                    $validated['to_language']
                ),
                'detect' => $this->translatorService->detectLanguage($validated['code']),
                'validate' => $this->translatorService->validateSyntax(
                    $validated['code'],
                    $validated['from_language']
                ),
                'compare' => $this->translatorService->compareTranslations(
                    $validated['code'],
                    $request->input('translated_code', ''),
                    $validated['from_language'],
                    $validated['to_language']
                ),
                default => ['success' => false, 'error' => 'إجراء غير معروف']
            };

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تمت العملية بنجاح',
                    'data' => $result,
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'حدث خطأ أثناء العملية',
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من صحة المدخلات',
                'errors' => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            Log::error('Code Translation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء الترجمة',
                'details' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
