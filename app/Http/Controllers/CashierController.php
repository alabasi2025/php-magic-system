<?php

namespace App\Http\Controllers;

use App\Genes\Cashiers\CashierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CashierException;

/**
 * @class CashierController
 * @package App\Http\Controllers
 * @brief وحدة التحكم الخاصة بإدارة الصرافين.
 *
 * هذه الوحدة مسؤولة عن معالجة طلبات HTTP المتعلقة بإدارة الصرافين،
 * وتعتمد على CashierService لتنفيذ منطق الأعمال.
 */
class CashierController extends Controller
{
    /**
     * @var CashierService
     */
    protected $cashierService;

    /**
     * CashierController constructor.
     *
     * @param CashierService $cashierService
     */
    public function __construct(CashierService $cashierService)
    {
        $this->cashierService = $cashierService;
    }

    /**
     * @brief عرض قائمة بجميع الصرافين.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $cashiers = $this->cashierService->getAllCashiers();
            return response()->json([
                'status' => 'success',
                'data' => $cashiers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب قائمة الصرافين: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief إنشاء صراف جديد.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cashier = $this->cashierService->createCashier($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الصراف بنجاح',
                'data' => $cashier
            ], 201);
        } catch (CashierException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في إنشاء الصراف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief عرض تفاصيل صراف معين.
     *
     * @param int $id معرف الصراف.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $cashier = $this->cashierService->getCashierById($id);

            if (!$cashier) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على الصراف'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $cashier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في جلب تفاصيل الصراف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief تحديث بيانات صراف موجود.
     *
     * @param Request $request
     * @param int $id معرف الصراف.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|integer|exists:users,id',
            'branch_id' => 'sometimes|integer|exists:branches,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cashier = $this->cashierService->updateCashier($id, $request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث بيانات الصراف بنجاح',
                'data' => $cashier
            ]);
        } catch (CashierException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في تحديث بيانات الصراف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief تفعيل أو إلغاء تفعيل صراف.
     *
     * @param int $id معرف الصراف.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(int $id, Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'حالة التفعيل مطلوبة ويجب أن تكون قيمة منطقية (boolean)',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cashier = $this->cashierService->toggleCashierStatus($id, $request->input('is_active'));
            $statusMessage = $cashier->is_active ? 'تفعيل' : 'إلغاء تفعيل';
            return response()->json([
                'status' => 'success',
                'message' => "تم {$statusMessage} الصراف بنجاح",
                'data' => $cashier
            ]);
        } catch (CashierException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في تغيير حالة الصراف: ' . $e->getMessage()
            ], 500);
        }
    }

    // TODO: إضافة دالة destroy لحذف الصراف إذا كان ذلك مسموحًا به في منطق الأعمال.
}