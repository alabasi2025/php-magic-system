<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Genes\PARTNERSHIP_ACCOUNTING\Services\ProfitService;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * ProfitController
 *
 * مسؤول عن معالجة طلبات HTTP والتحقق من صحة البيانات (Validation)
 * وتوجيه الطلبات إلى ProfitService.
 */
class ProfitController extends Controller
{
    protected $profitService;

    public function __construct(ProfitService $profitService)
    {
        $this->profitService = $profitService;
    }

    /**
     * عرض قائمة بجميع سجلات الأرباح. (Read - All)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->all();
            $profits = $this->profitService->getAll($filters);
            return response()->json(['success' => true, 'data' => $profits]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * تخزين سجل أرباح جديد. (Create)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|integer|exists:accounting_periods,id',
            'net_profit_amount' => 'required|numeric|min:0',
            // إضافة قواعد التحقق الأخرى
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['period_id', 'net_profit_amount']);
            $newProfit = $this->profitService->create($data);
            return response()->json(['success' => true, 'message' => 'Profit record created.', 'data' => $newProfit], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * عرض سجل أرباح محدد. (Read - Single)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $profit = $this->profitService->getById($id);
            return response()->json(['success' => true, 'data' => $profit]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * تحديث سجل أرباح محدد. (Update)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'net_profit_amount' => 'sometimes|required|numeric|min:0',
            // إضافة قواعد التحقق الأخرى
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['net_profit_amount']);
            $updatedProfit = $this->profitService->update($id, $data);
            return response()->json(['success' => true, 'message' => 'Profit record updated.', 'data' => $updatedProfit]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف سجل أرباح محدد. (Delete)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->profitService->delete($id);
            return response()->json(['success' => true, 'message' => 'Profit record deleted successfully.'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * وظيفة إضافية: حساب وتوزيع الأرباح.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateAndDistribute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|integer|exists:accounting_periods,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $periodId = $request->input('period_id');
            $result = $this->profitService->calculateAndDistribute($periodId);
            return response()->json(['success' => true, 'message' => $result['message']]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
