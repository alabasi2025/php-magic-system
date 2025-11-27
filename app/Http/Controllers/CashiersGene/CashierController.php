<?php

namespace App\Http\Controllers\CashiersGene;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashiersGene\CashierStoreRequest;
use App\Http\Requests\CashiersGene\CashierUpdateRequest;
use App\Models\CashiersGene\Cashier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @class CashierController
 * @package App\Http\Controllers\CashiersGene
 * @brief وحدة التحكم (Controller) الخاصة بإدارة بيانات الصرافين (Cashiers) ضمن معمارية الجينات (Cashiers Gene).
 *
 * تتولى هذه الوحدة معالجة طلبات HTTP الواردة وتوجيهها إلى الخدمات المناسبة.
 * Task 2018: Backend - نظام الصرافين (Cashiers) - Backend - Task 3 (إنشاء وحدة التحكم).
 */
class CashierController extends Controller
{
    /**
     * @brief عرض قائمة بجميع الصرافين.
     *
     * @param Request $request طلب HTTP الوارد.
     * @return JsonResponse استجابة JSON تحتوي على قائمة الصرافين.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // استخدام Query Builder أو Eloquent لجلبالبيانات
            $cashiers = Cashier::query()
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('search') . '%')
                          ->orWhere('code', $request->input('search'));
                })
                ->paginate(15);

            return response()->json([
                'status' => 'success',
                'message' => 'Cashiers list retrieved successfully.',
                'data' => $cashiers,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to retrieve cashiers list: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving the cashiers list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @brief تخزين صراف جديد في قاعدة البيانات.
     *
     * @param CashierStoreRequest $request طلب التحقق من صحة البيانات وتخزينها.
     * @return JsonResponse استجابة JSON تؤكد نجاح العملية.
     */
    public function store(CashierStoreRequest $request): JsonResponse
    {
        try {
            // إنشاء الصراف باستخدام البيانات المتحقق منها
            $cashier = Cashier::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Cashier created successfully.',
                'data' => $cashier,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Failed to create cashier: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the cashier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @brief عرض تفاصيل صراف محدد.
     *
     * @param Cashier $cashier نموذج الصراف المطلوب.
     * @return JsonResponse استجابة JSON تحتوي على بيانات الصراف.
     */
    public function show(Cashier $cashier): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Cashier details retrieved successfully.',
            'data' => $cashier,
        ]);
    }

    /**
     * @brief تحديث بيانات صراف موجود.
     *
     * @param CashierUpdateRequest $request طلب التحقق من صحة البيانات وتحديثها.
     * @param Cashier $cashier نموذج الصراف المطلوب تحديثه.
     * @return JsonResponse استجابة JSON تؤكد نجاح العملية.
     */
    public function update(CashierUpdateRequest $request, Cashier $cashier): JsonResponse
    {
        try {
            // تحديث بيانات الصراف باستخدام البيانات المتحقق منها
            $cashier->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Cashier updated successfully.',
                'data' => $cashier,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update cashier ID {$cashier->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the cashier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @brief حذف صراف من قاعدة البيانات.
     *
     * @param Cashier $cashier نموذج الصراف المطلوب حذفه.
     * @return JsonResponse استجابة JSON تؤكد نجاح العملية.
     */
    public function destroy(Cashier $cashier): JsonResponse
    {
        try {
            $cashier->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Cashier deleted successfully.',
                'data' => null,
            ], 204); // 204 No Content for successful deletion
        } catch (\Exception $e) {
            Log::error("Failed to delete cashier ID {$cashier->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the cashier.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}