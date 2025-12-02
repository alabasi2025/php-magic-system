<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Controllers;

use App\Genes\PARTNERSHIP_ACCOUNTING\Services\RevenueService;
use App\Http\Controllers\Controller; // افتراض استخدام Laravel
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * RevenueController
 *
 * مسؤول عن استقبال طلبات HTTP ومعالجة عمليات CRUD للإيرادات.
 * يعتمد على RevenueService لتنفيذ منطق الأعمال ومعاملات قاعدة البيانات.
 */
class RevenueController extends Controller
{
    protected $revenueService;

    public function __construct(RevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }

    /**
     * عرض قائمة بجميع الإيرادات.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $revenues = $this->revenueService->getAll();
            return response()->json(['data' => $revenues], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'فشل في استرداد الإيرادات', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * تخزين سجل إيراد جديد.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // 1. التحقق من الصحة (Validation)
            $validatedData = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:255',
                'date' => 'required|date',
                // يمكن إضافة المزيد من قواعد التحقق هنا
            ]);

            // 2. تنفيذ منطق الأعمال عبر الخدمة
            $revenue = $this->revenueService->create($validatedData);

            // 3. الاستجابة
            return response()->json(['message' => 'تم إنشاء الإيراد بنجاح', 'data' => $revenue], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'فشل التحقق من البيانات', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'فشل في إنشاء الإيراد', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * عرض سجل إيراد محدد.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $revenue = $this->revenueService->getById($id);

            if (!$revenue) {
                return response()->json(['message' => 'لم يتم العثور على الإيراد'], 404);
            }

            return response()->json(['data' => $revenue], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'فشل في استرداد الإيراد', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * تحديث سجل إيراد محدد.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            // 1. التحقق من الصحة (Validation)
            $validatedData = $request->validate([
                'amount' => 'sometimes|numeric|min:0.01',
                'description' => 'sometimes|string|max:255',
                'date' => 'sometimes|date',
                // يمكن إضافة المزيد من قواعد التحقق هنا
            ]);

            // 2. تنفيذ منطق الأعمال عبر الخدمة
            $revenue = $this->revenueService->update($id, $validatedData);

            // 3. الاستجابة
            return response()->json(['message' => 'تم تحديث الإيراد بنجاح', 'data' => $revenue], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'فشل التحقق من البيانات', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'فشل في تحديث الإيراد', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف سجل إيراد محدد.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->revenueService->delete($id);
            return response()->json(['message' => 'تم حذف الإيراد بنجاح'], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'فشل في حذف الإيراد', 'error' => $e->getMessage()], 500);
        }
    }
}
