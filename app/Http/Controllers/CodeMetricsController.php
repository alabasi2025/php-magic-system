<?php

namespace App\Http\Controllers;

use App\Services\CodeMetricsService;
use App\Models\CodeMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CodeMetricsController extends Controller
{
    private CodeMetricsService $metricsService;

    public function __construct(CodeMetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Display the main dashboard
     */
    public function index()
    {
        $latestAnalysis = $this->metricsService->getLatestAnalysis();
        $history = $this->metricsService->getAnalysisHistory(5);

        return view('code-metrics.index', [
            'latestAnalysis' => $latestAnalysis,
            'history' => $history,
        ]);
    }

    /**
     * Run new analysis
     */
    public function analyze(Request $request)
    {
        try {
            set_time_limit(300); // 5 minutes
            
            Log::info('بدء تحليل جديد للكود من واجهة المستخدم');
            
            $metric = $this->metricsService->runCompleteAnalysis();
            
            return redirect()
                ->route('code-metrics.show', $metric->id)
                ->with('success', 'تم تحليل الكود بنجاح! النتيجة: ' . $metric->overall_score . ' (' . $metric->grade . ')');
                
        } catch (\Exception $e) {
            Log::error('خطأ في تحليل الكود', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'حدث خطأ أثناء تحليل الكود: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed analysis
     */
    public function show($id)
    {
        $metric = CodeMetric::findOrFail($id);
        $trend = $metric->getTrendComparison();

        return view('code-metrics.show', [
            'metric' => $metric,
            'trend' => $trend,
        ]);
    }

    /**
     * Show trends page
     */
    public function trends()
    {
        $history = $this->metricsService->getAnalysisHistory(20);

        return view('code-metrics.trends', [
            'history' => $history,
        ]);
    }

    /**
     * Compare two analyses
     */
    public function compare(Request $request)
    {
        $request->validate([
            'analysis1' => 'required|exists:code_metrics,id',
            'analysis2' => 'required|exists:code_metrics,id',
        ]);

        $comparison = $this->metricsService->compareAnalyses(
            $request->analysis1,
            $request->analysis2
        );

        return view('code-metrics.compare', $comparison);
    }

    /**
     * Export analysis as JSON
     */
    public function export($id)
    {
        $metric = CodeMetric::findOrFail($id);
        
        $data = [
            'version' => $metric->version,
            'analyzed_at' => $metric->analyzed_at->toIso8601String(),
            'overall_score' => $metric->overall_score,
            'grade' => $metric->grade,
            'scores' => [
                'security' => $metric->security_score,
                'reliability' => $metric->reliability_score,
                'performance' => $metric->performance_score,
                'maintainability' => $metric->maintainability_score,
            ],
            'metrics' => [
                'total_files' => $metric->total_files,
                'total_lines' => $metric->total_lines,
                'avg_complexity' => $metric->avg_cyclomatic_complexity,
                'max_complexity' => $metric->max_cyclomatic_complexity,
            ],
            'issues' => [
                'security' => $metric->security_issues,
                'reliability' => $metric->reliability_issues,
                'performance' => $metric->performance_issues,
                'maintainability' => $metric->maintainability_issues,
            ],
            'detailed_issues' => $metric->issues,
            'recommendations' => $metric->recommendations,
        ];

        return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get analysis data for API
     */
    public function api()
    {
        $latestAnalysis = $this->metricsService->getLatestAnalysis();
        
        if (!$latestAnalysis) {
            return response()->json([
                'message' => 'لا يوجد تحليل متاح',
            ], 404);
        }

        return response()->json([
            'version' => $latestAnalysis->version,
            'analyzed_at' => $latestAnalysis->analyzed_at,
            'overall_score' => $latestAnalysis->overall_score,
            'grade' => $latestAnalysis->grade,
            'scores' => [
                'security' => $latestAnalysis->security_score,
                'reliability' => $latestAnalysis->reliability_score,
                'performance' => $latestAnalysis->performance_score,
                'maintainability' => $latestAnalysis->maintainability_score,
            ],
        ]);
    }

    /**
     * Delete analysis
     */
    public function destroy($id)
    {
        $metric = CodeMetric::findOrFail($id);
        $metric->delete();

        return redirect()
            ->route('code-metrics.index')
            ->with('success', 'تم حذف التحليل بنجاح');
    }
}
