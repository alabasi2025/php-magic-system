<?php

namespace App\Http\Controllers;

use App\Services\SecurityScanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

/**
 * Security Scanner Controller
 * 
 * معالج طلبات فاحص الأمان
 * 
 * @version 3.14.0
 * @package App\Http\Controllers
 */
class SecurityScannerController extends Controller
{
    /**
     * خدمة فاحص الأمان
     */
    private SecurityScanner $scanner;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scanner = new SecurityScanner();
    }

    /**
     * عرض صفحة فاحص الأمان
     */
    public function index()
    {
        $recommendations = $this->scanner->getRecommendations();
        return view('developer.ai.security-scanner', compact('recommendations'));
    }

    /**
     * فحص الكود
     */
    public function scan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:10',
            'scans' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $code = $request->input('code');
            $scans = $request->input('scans', []);

            $results = $this->scanner->scan($code, ['scans' => $scans]);

            return response()->json([
                'success' => true,
                'message' => 'تم الفحص بنجاح',
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الفحص',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * فحص ملف
     */
    public function scanFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:php,blade.php,txt|max:10240',
            'scans' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $code = file_get_contents($file->getRealPath());
            $scans = $request->input('scans', []);

            $results = $this->scanner->scan($code, ['scans' => $scans]);

            return response()->json([
                'success' => true,
                'message' => 'تم فحص الملف بنجاح',
                'filename' => $file->getClientOriginalName(),
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فحص الملف',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * فحص مجلد
     */
    public function scanDirectory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'scans' => 'sometimes|array',
            'extensions' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $path = base_path($request->input('path'));
            
            if (!File::isDirectory($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'المسار المحدد غير موجود',
                ], 404);
            }

            $extensions = $request->input('extensions', ['php', 'blade.php']);
            $scans = $request->input('scans', []);
            
            $files = $this->getFilesRecursive($path, $extensions);
            $allResults = [];
            $totalIssues = 0;
            $criticalCount = 0;
            $highCount = 0;
            $mediumCount = 0;
            $lowCount = 0;
            $infoCount = 0;

            foreach ($files as $file) {
                $code = file_get_contents($file);
                $results = $this->scanner->scan($code, ['scans' => $scans]);
                
                if ($results['total_issues'] > 0) {
                    $allResults[] = [
                        'file' => str_replace(base_path(), '', $file),
                        'results' => $results,
                    ];
                    
                    $totalIssues += $results['total_issues'];
                    $criticalCount += $results['critical_count'];
                    $highCount += $results['high_count'];
                    $mediumCount += $results['medium_count'];
                    $lowCount += $results['low_count'];
                    $infoCount += $results['info_count'];
                }
            }

            $averageScore = count($allResults) > 0 
                ? array_sum(array_column(array_column($allResults, 'results'), 'score')) / count($allResults)
                : 100;

            return response()->json([
                'success' => true,
                'message' => 'تم فحص المجلد بنجاح',
                'data' => [
                    'total_files_scanned' => count($files),
                    'files_with_issues' => count($allResults),
                    'total_issues' => $totalIssues,
                    'critical_count' => $criticalCount,
                    'high_count' => $highCount,
                    'medium_count' => $mediumCount,
                    'low_count' => $lowCount,
                    'info_count' => $infoCount,
                    'average_score' => round($averageScore, 2),
                    'files' => $allResults,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فحص المجلد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على الملفات بشكل تكراري
     */
    private function getFilesRecursive(string $directory, array $extensions): array
    {
        $files = [];
        $items = File::allFiles($directory);

        foreach ($items as $item) {
            $extension = $item->getExtension();
            
            // التحقق من الامتداد
            if (in_array($extension, $extensions) || 
                (str_contains($item->getFilename(), '.blade.') && in_array('blade.php', $extensions))) {
                $files[] = $item->getRealPath();
            }
        }

        return $files;
    }

    /**
     * الحصول على التوصيات
     */
    public function recommendations()
    {
        $recommendations = $this->scanner->getRecommendations();
        
        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * تصدير التقرير
     */
    public function exportReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'results' => 'required|array',
            'format' => 'required|in:json,html,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $results = $request->input('results');
            $format = $request->input('format');

            switch ($format) {
                case 'json':
                    return $this->exportAsJson($results);
                case 'html':
                    return $this->exportAsHtml($results);
                case 'pdf':
                    return $this->exportAsPdf($results);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'صيغة غير مدعومة',
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير التقرير',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تصدير كـ JSON
     */
    private function exportAsJson(array $results)
    {
        $filename = 'security-scan-report-' . date('Y-m-d-His') . '.json';
        
        return response()->json($results)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * تصدير كـ HTML
     */
    private function exportAsHtml(array $results)
    {
        $html = view('developer.ai.security-scanner-report', compact('results'))->render();
        $filename = 'security-scan-report-' . date('Y-m-d-His') . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * تصدير كـ PDF
     */
    private function exportAsPdf(array $results)
    {
        // يمكن استخدام مكتبة مثل DomPDF أو wkhtmltopdf
        // هنا نستخدم HTML كبديل مؤقت
        return $this->exportAsHtml($results);
    }
}
