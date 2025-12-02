<?php

namespace App\Genes\CASH_BOXES\Controllers;

use App\Genes\CASH_BOXES\Services\CashBoxReportService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // افتراض وجود Controller أساسي

class CashBoxReportController extends Controller
{
    protected $cashBoxReportService;

    public function __construct(CashBoxReportService $cashBoxReportService)
    {
        $this->cashBoxReportService = $cashBoxReportService;
    }

    /**
     * الحصول على التقرير اليومي للصندوق.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function daily(Request $request)
    {
        $data = $this->cashBoxReportService->getDailyReport($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * الحصول على التقرير الشهري للصندوق.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthly(Request $request)
    {
        $data = $this->cashBoxReportService->getMonthlyReport($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * الحصول على ملخص تقرير الصندوق.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        $data = $this->cashBoxReportService->getSummaryReport($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * تصدير تقرير الصندوق إلى ملف Excel.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        // افتراض أن الخدمة ترجع مسار الملف الذي تم إنشاؤه
        $filePath = $this->cashBoxReportService->exportReport($request->all());

        // يجب أن يتم تعديل هذا الجزء ليتناسب مع مكتبة التصدير المستخدمة في المشروع
        // هذا مثال افتراضي لإرجاع ملف
        return response()->download($filePath, 'cashbox_report.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
