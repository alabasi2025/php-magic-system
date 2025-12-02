<?php

namespace App\Genes\PARTNER_ACCOUNTING\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\PARTNER_ACCOUNTING\Services\PartnerReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PartnerReportController extends Controller
{
    /**
     * @var PartnerReportService
     */
    protected $partnerReportService;

    /**
     * PartnerReportController constructor.
     *
     * @param PartnerReportService $partnerReportService
     */
    public function __construct(PartnerReportService $partnerReportService)
    {
        $this->partnerReportService = $partnerReportService;
    }

    /**
     * عرض كشف حساب الشريك.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statement(Request $request)
    {
        // منطق استدعاء الخدمة للحصول على كشف الحساب
        $data = $this->partnerReportService->getStatement($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'تم استرجاع كشف الحساب بنجاح.'
        ]);
    }

    /**
     * عرض تقرير توزيع الأرباح.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profitDistribution(Request $request)
    {
        // منطق استدعاء الخدمة للحصول على توزيع الأرباح
        $data = $this->partnerReportService->getProfitDistribution($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'تم استرجاع تقرير توزيع الأرباح بنجاح.'
        ]);
    }

    /**
     * عرض تقرير التسويات.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function settlements(Request $request)
    {
        // منطق استدعاء الخدمة للحصول على التسويات
        $data = $this->partnerReportService->getSettlements($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'تم استرجاع تقرير التسويات بنجاح.'
        ]);
    }

    /**
     * عرض ملخص تقارير الشركاء.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        // منطق استدعاء الخدمة للحصول على الملخص
        $data = $this->partnerReportService->getSummary($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'تم استرجاع ملخص تقارير الشركاء بنجاح.'
        ]);
    }

    /**
     * تصدير التقرير المطلوب (كشف حساب، توزيع أرباح، تسويات، ملخص) إلى ملف (Excel/PDF).
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Request $request)
    {
        // تحديد نوع التقرير المطلوب تصديره من الـ request
        $reportType = $request->input('report_type');
        $format = $request->input('format', 'xlsx'); // الافتراضي Excel
        
        // منطق استدعاء الخدمة لتوليد الملف
        return $this->partnerReportService->exportReport($reportType, $format, $request->all());
    }
}
