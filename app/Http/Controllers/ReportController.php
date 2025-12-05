<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    protected $reportService;

    /**
     * تهيئة المتحكم مع خدمة التقارير.
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        // تطبيق سياسة الأمان (Authorization) على جميع الدوال
        $this->middleware('can:view-reports');
    }

    /**
     * عرض صفحة اختيار التقارير.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // التحقق من الأمان مرة أخرى (على الرغم من وجودها في الـ middleware)
        if (Gate::denies('view-reports')) {
            abort(403, 'غير مصرح لك بعرض التقارير.');
        }

        // قائمة بأنواع التقارير المتاحة للعرض في الواجهة
        $reportTypes = [
            'balance' => 'رصيد المخزون',
            'movement' => 'حركة الأصناف',
            'valuation' => 'تقييم المخزون',
            'min_stock' => 'الأصناف تحت الحد الأدنى',
            'slow_moving' => 'الأصناف الراكدة',
            'active' => 'الأصناف الأكثر حركة',
            'purchases' => 'تقرير المشتريات',
            'sales' => 'تقرير المبيعات',
        ];

        return view('reports.index', compact('reportTypes'));
    }

    /**
     * توليد وعرض التقرير المطلوب.
     *
     * @param ReportRequest $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function generate(ReportRequest $request)
    {
        // التحقق من الأمان (تم بالفعل في ReportRequest::authorize)
        // إذا فشل التحقق، سيتم إرجاع استجابة 403 تلقائياً.

        $reportType = $request->validated('report_type');
        $data = [];
        $reportTitle = '';

        try {
            switch ($reportType) {
                case 'balance':
                    $data = $this->reportService->getInventoryBalance();
                    $reportTitle = 'تقرير رصيد المخزون الحالي';
                    break;
                case 'movement':
                    $data = $this->reportService->getItemMovement(
                        $request->validated('start_date'),
                        $request->validated('end_date'),
                        $request->validated('item_id')
                    );
                    $reportTitle = 'تقرير حركة الأصناف';
                    break;
                case 'valuation':
                    $data['total_value'] = $this->reportService->getInventoryValuation();
                    $reportTitle = 'تقرير تقييم المخزون';
                    break;
                case 'min_stock':
                    $data = $this->reportService->getBelowMinimumStock();
                    $reportTitle = 'تقرير الأصناف تحت الحد الأدنى';
                    break;
                case 'slow_moving':
                    $data = $this->reportService->getSlowMovingItems($request->validated('period_days', 90));
                    $reportTitle = 'تقرير الأصناف الراكدة';
                    break;
                case 'active':
                    $data = $this->reportService->getMostActiveItems(
                        $request->validated('start_date'),
                        $request->validated('end_date'),
                        $request->validated('limit', 10)
                    );
                    $reportTitle = 'تقرير الأصناف الأكثر حركة';
                    break;
                case 'purchases':
                    $data = $this->reportService->getPurchasesReport(
                        $request->validated('start_date'),
                        $request->validated('end_date')
                    );
                    $reportTitle = 'تقرير المشتريات';
                    break;
                case 'sales':
                    $data = $this->reportService->getSalesReport(
                        $request->validated('start_date'),
                        $request->validated('end_date')
                    );
                    $reportTitle = 'تقرير المبيعات';
                    break;
                default:
                    return back()->withErrors(['report_type' => 'نوع التقرير غير مدعوم.']);
            }

            // عرض صفحة النتائج
            return view('reports.results', [
                'reportType' => $reportType,
                'reportTitle' => $reportTitle,
                'data' => $data,
                'filters' => $request->validated(),
            ]);

        } catch (\Exception $e) {
            // معالجة الأخطاء: تسجيل الخطأ وإرجاع رسالة للمستخدم
            \Log::error("Report generation failed: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'حدث خطأ أثناء توليد التقرير. يرجى المحاولة لاحقاً.']);
        }
    }
}
