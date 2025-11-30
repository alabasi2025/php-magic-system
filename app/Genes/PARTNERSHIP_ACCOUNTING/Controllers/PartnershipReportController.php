<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\PARTNERSHIP_ACCOUNTING\Services\PartnershipReportService;
use App\Genes\PARTNERSHIP_ACCOUNTING\Requests\StorePartnershipReportRequest;
use App\Genes\PARTNERSHIP_ACCOUNTING\Requests\UpdatePartnershipReportRequest;
use Illuminate\Http\JsonResponse;

class PartnershipReportController extends Controller
{
    protected $service;

    public function __construct(PartnershipReportService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض قائمة بجميع تقارير الشراكات. (Read - All)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $reports = $this->service->getAll();
            return response()->json(['status' => 'success', 'data' => $reports]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * تخزين تقرير شراكة جديد. (Create)
     *
     * @param StorePartnershipReportRequest $request
     * @return JsonResponse
     */
    public function store(StorePartnershipReportRequest $request): JsonResponse
    {
        try {
            $report = $this->service->create($request->validated());
            return response()->json(['status' => 'success', 'message' => 'تم إنشاء التقرير بنجاح.', 'data' => $report], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * عرض تقرير شراكة محدد. (Read - Single)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $report = $this->service->getById($id);
            return response()->json(['status' => 'success', 'data' => $report]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * تحديث تقرير شراكة محدد. (Update)
     *
     * @param UpdatePartnershipReportRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdatePartnershipReportRequest $request, int $id): JsonResponse
    {
        try {
            $report = $this->service->update($id, $request->validated());
            return response()->json(['status' => 'success', 'message' => 'تم تحديث التقرير بنجاح.', 'data' => $report]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف تقرير شراكة محدد. (Delete)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'تم حذف التقرير بنجاح.'], 204);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
