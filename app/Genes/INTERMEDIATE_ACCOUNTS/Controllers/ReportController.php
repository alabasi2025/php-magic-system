<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\INTERMEDIATE_ACCOUNTS\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Controller: ReportController
 * 
 * 💡 الفكرة:
 * كونترولر يدير واجهات برمجة التطبيقات (APIs) للتقارير.
 * 
 * 🎯 المسؤوليات:
 * - تقرير العمليات المعلقة
 * - تقرير الأرصدة
 * - تقرير الحركة
 * - تقرير الربط
 * - تقرير الأداء
 * - تقرير الأعمار (Aging)
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class ReportController extends Controller
{
    /**
     * @var ReportService
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param ReportService $service
     */
    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    /**
     * Get unlinked transactions report.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unlinkedTransactions(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'nullable|integer|exists:intermediate_accounts,id',
                'type' => 'nullable|in:receipt,payment',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
            ]);

            $report = $this->service->getUnlinkedTransactionsReport(
                $validated['intermediate_account_id'] ?? null,
                [
                    'type' => $validated['type'] ?? null,
                    'date_from' => $validated['date_from'] ?? null,
                    'date_to' => $validated['date_to'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get balance report.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function balance(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|in:active,inactive',
            ]);

            $report = $this->service->getBalanceReport([
                'status' => $validated['status'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get movement report.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function movement(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'required|integer|exists:intermediate_accounts,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
            ]);

            $report = $this->service->getMovementReport(
                $validated['intermediate_account_id'],
                $validated['date_from'],
                $validated['date_to']
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get linking report.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function linking(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'nullable|integer|exists:intermediate_accounts,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'linked_by' => 'nullable|integer|exists:users,id',
            ]);

            $report = $this->service->getLinkingReport(
                $validated['intermediate_account_id'] ?? null,
                [
                    'date_from' => $validated['date_from'] ?? null,
                    'date_to' => $validated['date_to'] ?? null,
                    'linked_by' => $validated['linked_by'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get performance report.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function performance(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
            ]);

            $report = $this->service->getPerformanceReport([
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get aging report.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function aging(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'nullable|integer|exists:intermediate_accounts,id',
            ]);

            $report = $this->service->getAgingReport(
                $validated['intermediate_account_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Export report to PDF or Excel.
     *
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        try {
            $validated = $request->validate([
                'report_type' => 'required|in:unlinked,balance,movement,linking,performance,aging',
                'format' => 'required|in:pdf,excel',
                'intermediate_account_id' => 'nullable|integer|exists:intermediate_accounts,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
            ]);

            // Get report data based on type
            $reportData = null;
            $reportTitle = '';

            switch ($validated['report_type']) {
                case 'unlinked':
                    $reportData = $this->service->getUnlinkedTransactionsReport(
                        $validated['intermediate_account_id'] ?? null,
                        [
                            'date_from' => $validated['date_from'] ?? null,
                            'date_to' => $validated['date_to'] ?? null,
                        ]
                    );
                    $reportTitle = 'تقرير العمليات المعلقة';
                    break;

                case 'balance':
                    $reportData = $this->service->getBalanceReport();
                    $reportTitle = 'تقرير الأرصدة';
                    break;

                case 'movement':
                    if (!isset($validated['intermediate_account_id']) || !isset($validated['date_from']) || !isset($validated['date_to'])) {
                        throw new Exception('يجب تحديد الحساب الوسيط والفترة الزمنية');
                    }
                    $reportData = $this->service->getMovementReport(
                        $validated['intermediate_account_id'],
                        $validated['date_from'],
                        $validated['date_to']
                    );
                    $reportTitle = 'تقرير الحركة';
                    break;

                case 'linking':
                    $reportData = $this->service->getLinkingReport(
                        $validated['intermediate_account_id'] ?? null,
                        [
                            'date_from' => $validated['date_from'] ?? null,
                            'date_to' => $validated['date_to'] ?? null,
                        ]
                    );
                    $reportTitle = 'تقرير الربط';
                    break;

                case 'performance':
                    $reportData = $this->service->getPerformanceReport([
                        'date_from' => $validated['date_from'] ?? null,
                        'date_to' => $validated['date_to'] ?? null,
                    ]);
                    $reportTitle = 'تقرير الأداء';
                    break;

                case 'aging':
                    $reportData = $this->service->getAgingReport(
                        $validated['intermediate_account_id'] ?? null
                    );
                    $reportTitle = 'تقرير الأعمار';
                    break;
            }

            // Export based on format
            if ($validated['format'] === 'pdf') {
                // TODO: Implement PDF export
                return response()->json([
                    'success' => false,
                    'message' => 'تصدير PDF قيد التطوير',
                ], 501);
            } else {
                // TODO: Implement Excel export
                return response()->json([
                    'success' => false,
                    'message' => 'تصدير Excel قيد التطوير',
                ], 501);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
