<?php

namespace App\Genes\Cashiers\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Genes\Cashiers\Models\Cashier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @class CashierController
 * @package App\Genes\Cashiers\Http\Controllers\Api
 *
 * @brief المتحكم الخاص بواجهة برمجة تطبيقات الصرافين (Cashier API Controller).
 *
 * يوفر عمليات CRUD الأساسية لإدارة موارد الصرافين.
 */
class CashierController extends Controller
{
    /**
     * عرض قائمة بالصرافين.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cashiers = Cashier::with(['user', 'branch'])->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب قائمة الصرافين بنجاح.',
            'data' => $cashiers,
        ]);
    }

    /**
     * تخزين صراف جديد.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'username' => 'required|string|max:255|unique:cashiers,username',
            'password' => 'required|string|min:6',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحقق من صحة البيانات.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cashier = Cashier::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الصراف بنجاح.',
            'data' => $cashier,
        ], 201);
    }

    /**
     * عرض صراف محدد.
     *
     * @param Cashier $cashier
     * @return JsonResponse
     */
    public function show(Cashier $cashier): JsonResponse
    {
        $cashier->load(['user', 'branch']);

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب بيانات الصراف بنجاح.',
            'data' => $cashier,
        ]);
    }

    /**
     * تحديث صراف محدد.
     *
     * @param Request $request
     * @param Cashier $cashier
     * @return JsonResponse
     */
    public function update(Request $request, Cashier $cashier): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'username' => 'sometimes|required|string|max:255|unique:cashiers,username,' . $cashier->id,
            'password' => 'nullable|string|min:6',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحقق من صحة البيانات.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except('password');
        if ($request->filled('password')) {
            // الموديل سيتولى تشفير كلمة المرور عبر setPasswordAttribute
            $data['password'] = $request->input('password');
        }

        $cashier->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث بيانات الصراف بنجاح.',
            'data' => $cashier,
        ]);
    }

    /**
     * حذف صراف محدد.
     *
     * @param Cashier $cashier
     * @return JsonResponse
     */
    public function destroy(Cashier $cashier): JsonResponse
    {
        $cashier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الصراف بنجاح.',
            'data' => null,
        ]);
    }
}