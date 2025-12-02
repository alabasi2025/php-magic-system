<?php

namespace App\Genes\ACCOUNTING_REPORTS\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\ACCOUNTING_REPORTS\Models\ReportTemplate;
use App\Genes\ACCOUNTING_REPORTS\Models\GeneratedReport;
use App\Genes\ACCOUNTING_REPORTS\Services\ReportGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * كونترولر التقارير المحاسبية
 */
class AccountingReportController extends Controller
{
    protected ReportGeneratorService $reportService;

    public function __construct(ReportGeneratorService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * عرض قائمة التقارير المولدة
     */
    public function index()
    {
        $reports = GeneratedReport::with(['template', 'generator'])
            ->latest()
            ->paginate(20);

        $templates = ReportTemplate::active()->get();

        return view('ACCOUNTING_REPORTS::reports.index', compact('reports', 'templates'));
    }

    /**
     * عرض نموذج إنشاء تقرير جديد
     */
    public function create()
    {
        $templates = ReportTemplate::active()->get();

        return view('ACCOUNTING_REPORTS::reports.create', compact('templates'));
    }

    /**
     * حفظ قالب تقرير جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|string',
            'structure' => 'required|array',
            'parameters' => 'nullable|array',
        ]);

        $template = ReportTemplate::create([
            ...$validated,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('accounting-reports.index')
            ->with('success', 'تم إنشاء قالب التقرير بنجاح');
    }

    /**
     * توليد تقرير من قالب
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:report_templates,id',
            'period_from' => 'nullable|date',
            'period_to' => 'nullable|date|after_or_equal:period_from',
            'parameters' => 'nullable|array',
        ]);

        try {
            $report = $this->reportService->generateReport(
                $validated['template_id'],
                $validated,
                Auth::id()
            );

            return redirect()
                ->route('accounting-reports.show', $report)
                ->with('success', 'تم توليد التقرير بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * عرض تقرير محدد
     */
    public function show(GeneratedReport $report)
    {
        $report->load(['template', 'generator']);

        return view('ACCOUNTING_REPORTS::reports.show', compact('report'));
    }

    /**
     * تحميل التقرير
     */
    public function download(GeneratedReport $report, Request $request)
    {
        $format = $request->input('format', 'pdf');

        try {
            $path = match ($format) {
                'pdf' => $this->reportService->exportToPDF($report),
                'excel' => $this->reportService->exportToExcel($report),
                default => throw new \Exception('صيغة غير مدعومة'),
            };

            return response()->download(storage_path('app/' . $path));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * حذف تقرير
     */
    public function destroy(GeneratedReport $report)
    {
        // حذف الملف إذا كان موجوداً
        if ($report->file_path && \Storage::exists($report->file_path)) {
            \Storage::delete($report->file_path);
        }

        $report->delete();

        return redirect()
            ->route('accounting-reports.index')
            ->with('success', 'تم حذف التقرير بنجاح');
    }

    /**
     * عرض قوالب التقارير
     */
    public function templates()
    {
        $templates = ReportTemplate::withCount('generatedReports')
            ->latest()
            ->paginate(20);

        return view('ACCOUNTING_REPORTS::reports.templates', compact('templates'));
    }

    /**
     * تعطيل/تفعيل قالب
     */
    public function toggleTemplate(ReportTemplate $template)
    {
        $template->is_active = !$template->is_active;
        $template->save();

        $status = $template->is_active ? 'تفعيل' : 'تعطيل';

        return back()->with('success', "تم {$status} القالب بنجاح");
    }

    /**
     * حذف قالب تقرير
     */
    public function destroyTemplate(ReportTemplate $template)
    {
        // التحقق من عدم وجود تقارير مرتبطة
        if ($template->generatedReports()->count() > 0) {
            return back()->withErrors([
                'error' => 'لا يمكن حذف القالب لوجود تقارير مرتبطة به'
            ]);
        }

        $template->delete();

        return redirect()
            ->route('accounting-reports.templates')
            ->with('success', 'تم حذف القالب بنجاح');
    }
}
