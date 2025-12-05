<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\AI\AiAssistantPlusService;

/**
 * AiAssistantPlusController
 * 
 * المساعد الذكي المتقدم Plus v3.18.0
 * 
 * @package App\Http\Controllers
 */
class AiAssistantPlusController extends Controller
{
    protected AiAssistantPlusService $aiService;

    public function __construct(AiAssistantPlusService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * عرض صفحة المساعد الذكي المتقدم
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('developer.ai.assistant-plus', [
            'title' => 'المساعد الذكي المتقدم Plus',
            'version' => 'v3.18.0',
        ]);
    }

    /**
     * محادثة عامة مع المساعد الذكي
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chat(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:5000',
                'conversation_id' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $message = $request->input('message');
            $conversationId = $request->input('conversation_id', 'default');

            $result = $this->aiService->chat($message, $conversationId);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحليل كود متقدم
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzeCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->analyzeCodeAdvanced($code, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * توليد كود متقدم
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required|string|max:2000',
                'language' => 'nullable|string|max:50',
                'requirements' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $description = $request->input('description');
            $language = $request->input('language', 'PHP');
            $requirements = $request->input('requirements', []);

            $result = $this->aiService->generateCodeAdvanced($description, $language, $requirements);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * إصلاح أخطاء متقدم
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fixBug(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'error' => 'required|string|max:2000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $error = $request->input('error');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->fixBugAdvanced($code, $error, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * إعادة هيكلة الكود
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refactorCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->refactorCode($code, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * توليد اختبارات
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateTests(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->generateTests($code, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * توليد توثيق
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateDocumentation(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->generateDocumentation($code, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * فحص الأمان
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function securityScan(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->securityScan($code, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحسين الأداء
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function optimizePerformance(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'language' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $language = $request->input('language', 'PHP');

            $result = $this->aiService->optimizePerformance($code, $language);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ترجمة الكود
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function translateCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10000',
                'from_language' => 'required|string|max:50',
                'to_language' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = $request->input('code');
            $fromLanguage = $request->input('from_language');
            $toLanguage = $request->input('to_language');

            $result = $this->aiService->translateCode($code, $fromLanguage, $toLanguage);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على اقتراحات ذكية
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'context' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $context = $request->input('context');

            $result = $this->aiService->getSuggestions($context);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * مسح محادثة
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clearConversation(Request $request): JsonResponse
    {
        try {
            $conversationId = $request->input('conversation_id', 'default');
            
            $this->aiService->clearConversation($conversationId);

            return response()->json([
                'success' => true,
                'message' => 'تم مسح المحادثة بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات الاستخدام
     *
     * @return JsonResponse
     */
    public function getUsageStats(): JsonResponse
    {
        try {
            $stats = $this->aiService->getUsageStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }
}
