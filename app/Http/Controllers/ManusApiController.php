<?php

namespace App\Http\Controllers;

use App\Services\ManusApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * المتحكم الخاص بواجهة Manus API.
 * يتولى هذا المتحكم معالجة طلبات المستخدمين وتوجيهها إلى خدمة ManusApiService
 * وجلب البيانات المطلوبة، مع تطبيق معالجة الأخطاء المناسبة.
 */
class ManusApiController extends Controller
{
    /**
     * @var ManusApiService
     */
    protected ManusApiService $manusService;

    /**
     * تهيئة المتحكم وحقن خدمة ManusApiService.
     *
     * @param ManusApiService $manusService
     */
    public function __construct(ManusApiService $manusService)
    {
        $this->manusService = $manusService;
    }

    /**
     * جلب بيانات لوحة التحكم (Dashboard).
     *
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        try {
            $data = $this->manusService->getDashboardData();
            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب بيانات لوحة التحكم بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in dashboard method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب بيانات لوحة التحكم.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب سجل المعاملات (Transactions).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            // يمكن تمرير فلاتر من الطلب إلى الخدمة
            $filters = $request->only(['type', 'date_from', 'date_to']);
            $data = $this->manusService->getTransactions($filters);
            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب سجل المعاملات بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in transactions method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب سجل المعاملات.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب الإحصائيات (Stats) العامة.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'monthly');
            $data = $this->manusService->getStats($period);
            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الإحصائيات بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in stats method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب الإحصائيات.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب التقارير (Reports) المخصصة.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reports(Request $request): JsonResponse
    {
        try {
            $reportType = $request->input('report_type');
            if (!$reportType) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تحديد نوع التقرير (report_type).',
                ], 400);
            }

            $options = $request->except('report_type');
            $data = $this->manusService->getReports($reportType, $options);
            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب التقرير بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in reports method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب التقارير.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال رسالة إلى خدمة الدردشة (Chat).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chat(Request $request): JsonResponse
    {
        try {
            $message = $request->input('message');
            $sessionId = $request->input('session_id', uniqid('chat_'));

            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب إرسال محتوى الرسالة (message).',
                ], 400);
            }

            $data = $this->manusService->sendChatMessage($message, $sessionId);
            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال رسالة الدردشة بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in chat method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في إرسال رسالة الدردشة.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * طلب إكمال نص (Completion).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function completion(Request $request): JsonResponse
    {
        try {
            $prompt = $request->input('prompt');
            if (!$prompt) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب إرسال النص التمهيدي (prompt).',
                ], 400);
            }

            $options = $request->except('prompt');
            $data = $this->manusService->requestCompletion($prompt, $options);
            return response()->json([
                'status' => 'success',
                'message' => 'تم طلب إكمال النص بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in completion method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في طلب إكمال النص.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب بيانات الاستخدام (Usage) التفصيلية.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function usage(Request $request): JsonResponse
    {
        try {
            // افتراض أن معرف المستخدم يتم جلبه من الطلب أو سياق المصادقة
            $userId = $request->user() ? $request->user()->id : 'default_user_id';
            $period = $request->input('period', 'current_month');

            $data = $this->manusService->getUsageData($userId, $period);
            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب بيانات الاستخدام بنجاح.',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("Error in usage method: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب بيانات الاستخدام.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}