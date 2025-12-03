<?php

namespace App\Services;

use App\Analyzers\ComplexityAnalyzer;
use App\Analyzers\SecurityAnalyzer;
use App\Models\CodeMetric;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CodeMetricsService
{
    private ComplexityAnalyzer $complexityAnalyzer;
    private SecurityAnalyzer $securityAnalyzer;
    private string $projectPath;
    private array $excludedDirs = ['vendor', 'node_modules', 'storage', 'bootstrap/cache'];

    public function __construct()
    {
        $this->complexityAnalyzer = new ComplexityAnalyzer();
        $this->securityAnalyzer = new SecurityAnalyzer();
        $this->projectPath = base_path();
    }

    /**
     * Run complete code analysis
     */
    public function runCompleteAnalysis(): CodeMetric
    {
        $startTime = time();
        
        Log::info('بدء تحليل جودة الكود...');

        // Analyze complexity
        $complexityResults = $this->analyzeComplexity();
        
        // Analyze security
        $securityResults = $this->analyzeSecurity();
        
        // Analyze maintainability
        $maintainabilityResults = $this->analyzeMaintainability($complexityResults);
        
        // Analyze performance patterns
        $performanceResults = $this->analyzePerformance();
        
        // Analyze reliability
        $reliabilityResults = $this->analyzeReliability();
        
        // Calculate overall score
        $overallScore = $this->calculateOverallScore([
            'security' => $securityResults['score'],
            'reliability' => $reliabilityResults['score'],
            'performance' => $performanceResults['score'],
            'maintainability' => $maintainabilityResults['score'],
        ]);

        // Collect all issues
        $allIssues = array_merge(
            $securityResults['issues'] ?? [],
            $maintainabilityResults['issues'] ?? [],
            $performanceResults['issues'] ?? [],
            $reliabilityResults['issues'] ?? []
        );

        // Generate recommendations
        $recommendations = $this->generateRecommendations($allIssues, [
            'security' => $securityResults['score'],
            'reliability' => $reliabilityResults['score'],
            'performance' => $performanceResults['score'],
            'maintainability' => $maintainabilityResults['score'],
        ]);

        // Get top complex files
        $topComplexFiles = $this->getTopComplexFiles($complexityResults);

        $duration = time() - $startTime;

        // Save to database
        $metric = CodeMetric::create([
            'version' => $this->getCurrentVersion(),
            'analyzed_at' => now(),
            
            // General metrics
            'total_files' => $complexityResults['total_files'] ?? 0,
            'total_lines' => $complexityResults['total_lines'] ?? 0,
            'logical_lines' => $complexityResults['logical_lines'] ?? 0,
            'comment_lines' => $complexityResults['comment_lines'] ?? 0,
            'blank_lines' => $complexityResults['blank_lines'] ?? 0,
            
            // Complexity metrics
            'avg_cyclomatic_complexity' => $complexityResults['avg_cyclomatic_complexity'] ?? 0,
            'max_cyclomatic_complexity' => $complexityResults['max_cyclomatic_complexity'] ?? 0,
            'avg_cognitive_complexity' => $complexityResults['avg_cognitive_complexity'] ?? 0,
            'max_cognitive_complexity' => $complexityResults['max_cognitive_complexity'] ?? 0,
            
            // Size metrics
            'avg_function_size' => $complexityResults['avg_function_size'] ?? 0,
            'max_function_size' => $complexityResults['max_function_size'] ?? 0,
            'avg_class_size' => $complexityResults['avg_class_size'] ?? 0,
            'max_class_size' => $complexityResults['max_class_size'] ?? 0,
            
            // ISO 5055 scores
            'security_score' => $securityResults['score'],
            'security_issues' => $securityResults['total_issues'],
            'reliability_score' => $reliabilityResults['score'],
            'reliability_issues' => $reliabilityResults['total_issues'],
            'performance_score' => $performanceResults['score'],
            'performance_issues' => $performanceResults['total_issues'],
            'maintainability_score' => $maintainabilityResults['score'],
            'maintainability_issues' => $maintainabilityResults['total_issues'],
            
            // Documentation
            'documentation_percentage' => $complexityResults['documentation_percentage'] ?? 0,
            'documented_functions' => 0, // TODO: Implement
            'total_functions' => $complexityResults['total_functions'] ?? 0,
            'documented_classes' => 0, // TODO: Implement
            'total_classes' => $complexityResults['total_classes'] ?? 0,
            
            // Overall
            'overall_score' => $overallScore,
            'grade' => $this->calculateGrade($overallScore),
            
            // Detailed data
            'detailed_analysis' => [
                'complexity' => $complexityResults,
                'security' => $securityResults,
                'reliability' => $reliabilityResults,
                'performance' => $performanceResults,
                'maintainability' => $maintainabilityResults,
            ],
            'issues' => $allIssues,
            'recommendations' => $recommendations,
            'top_complex_files' => $topComplexFiles,
            'top_security_issues' => $securityResults['top_issues'] ?? [],
            
            'analysis_duration_seconds' => $duration,
            'analyzer_version' => '1.0.0',
        ]);

        Log::info('انتهى تحليل جودة الكود', [
            'duration' => $duration,
            'score' => $overallScore,
            'grade' => $metric->grade,
        ]);

        return $metric;
    }

    /**
     * Analyze code complexity
     */
    private function analyzeComplexity(): array
    {
        $appPath = $this->projectPath . '/app';
        $results = $this->complexityAnalyzer->analyzeDirectory($appPath);
        
        // Add cognitive complexity estimation
        $results['avg_cognitive_complexity'] = round($results['avg_cyclomatic_complexity'] * 0.8, 2);
        $results['max_cognitive_complexity'] = round($results['max_cyclomatic_complexity'] * 0.8);
        
        return $results;
    }

    /**
     * Analyze security
     */
    private function analyzeSecurity(): array
    {
        $appPath = $this->projectPath . '/app';
        $results = $this->securityAnalyzer->analyzeDirectory($appPath);
        
        // Add Laravel-specific checks
        $laravelIssues = $this->securityAnalyzer->checkLaravelSecurity($this->projectPath);
        $results['issues'] = array_merge($results['issues'], $laravelIssues);
        $results['total_issues'] = count($results['issues']);
        
        // Recalculate score
        $results['score'] = $this->calculateSecurityScore($results);
        
        return $results;
    }

    /**
     * Analyze maintainability
     */
    private function analyzeMaintainability(array $complexityResults): array
    {
        $issues = [];
        $score = 100;

        // Check average complexity
        $avgComplexity = $complexityResults['avg_cyclomatic_complexity'] ?? 0;
        if ($avgComplexity > 10) {
            $issues[] = [
                'type' => 'high_average_complexity',
                'severity' => 'high',
                'message' => "متوسط التعقيد الدوري مرتفع: {$avgComplexity}",
                'recommendation' => 'قم بتقسيم الدوال المعقدة إلى دوال أصغر',
            ];
            $score -= 15;
        }

        // Check max complexity
        $maxComplexity = $complexityResults['max_cyclomatic_complexity'] ?? 0;
        if ($maxComplexity > 20) {
            $issues[] = [
                'type' => 'very_high_complexity',
                'severity' => 'critical',
                'message' => "أعلى تعقيد دوري: {$maxComplexity}",
                'recommendation' => 'أعد هيكلة الدوال ذات التعقيد العالي فوراً',
            ];
            $score -= 20;
        }

        // Check function size
        $avgFunctionSize = $complexityResults['avg_function_size'] ?? 0;
        if ($avgFunctionSize > 50) {
            $issues[] = [
                'type' => 'large_functions',
                'severity' => 'medium',
                'message' => "متوسط حجم الدوال كبير: {$avgFunctionSize} سطر",
                'recommendation' => 'حافظ على الدوال أقل من 50 سطر',
            ];
            $score -= 10;
        }

        // Check documentation
        $docPercentage = $complexityResults['documentation_percentage'] ?? 0;
        if ($docPercentage < 15) {
            $issues[] = [
                'type' => 'low_documentation',
                'severity' => 'medium',
                'message' => "نسبة التوثيق منخفضة: {$docPercentage}%",
                'recommendation' => 'أضف تعليقات وتوثيق للكود',
            ];
            $score -= 10;
        }

        return [
            'score' => max(0, $score),
            'total_issues' => count($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Analyze performance patterns
     */
    private function analyzePerformance(): array
    {
        $issues = [];
        $score = 100;

        // Check for N+1 query problems
        $appPath = $this->projectPath . '/app';
        $files = $this->getPhpFiles($appPath);

        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for potential N+1 queries
            if (preg_match_all('/foreach.*->get\(\).*\{.*\$.*->/', $content, $matches)) {
                $issues[] = [
                    'type' => 'potential_n_plus_one',
                    'severity' => 'high',
                    'message' => 'احتمالية مشكلة N+1 في الاستعلامات',
                    'file' => $file,
                    'recommendation' => 'استخدم Eager Loading مع with()',
                ];
                $score -= 10;
            }

            // Check for missing indexes
            if (preg_match('/where\(["\'](?!id)[^"\']+["\']\)/', $content)) {
                // This is a simplified check
                $score -= 2;
            }
        }

        return [
            'score' => max(0, $score),
            'total_issues' => count($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Analyze reliability
     */
    private function analyzeReliability(): array
    {
        $issues = [];
        $score = 100;

        $appPath = $this->projectPath . '/app';
        $files = $this->getPhpFiles($appPath);

        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for proper exception handling
            $tryCount = substr_count($content, 'try {');
            $catchCount = substr_count($content, 'catch (');
            
            if ($tryCount > 0 && $catchCount === 0) {
                $issues[] = [
                    'type' => 'missing_exception_handling',
                    'severity' => 'high',
                    'message' => 'معالجة استثناءات غير كاملة',
                    'file' => $file,
                    'recommendation' => 'أضف معالجة للاستثناءات في كتل try',
                ];
                $score -= 5;
            }

            // Check for resource cleanup
            if (preg_match('/fopen\(/', $content) && !preg_match('/fclose\(/', $content)) {
                $issues[] = [
                    'type' => 'resource_leak',
                    'severity' => 'medium',
                    'message' => 'احتمالية تسريب موارد - ملفات غير مغلقة',
                    'file' => $file,
                    'recommendation' => 'تأكد من إغلاق جميع الموارد المفتوحة',
                ];
                $score -= 5;
            }
        }

        return [
            'score' => max(0, $score),
            'total_issues' => count($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Calculate security score
     */
    private function calculateSecurityScore(array $results): float
    {
        $score = 100;
        $score -= ($results['critical'] ?? 0) * 20;
        $score -= ($results['high'] ?? 0) * 10;
        $score -= ($results['medium'] ?? 0) * 5;
        $score -= ($results['low'] ?? 0) * 2;
        return max(0, $score);
    }

    /**
     * Calculate overall score based on ISO 5055 weights
     */
    private function calculateOverallScore(array $scores): float
    {
        return round(
            ($scores['security'] * 0.30) +
            ($scores['reliability'] * 0.25) +
            ($scores['performance'] * 0.20) +
            ($scores['maintainability'] * 0.25),
            2
        );
    }

    /**
     * Calculate grade from score
     */
    private function calculateGrade(float $score): string
    {
        if ($score >= 95) return 'A+';
        if ($score >= 90) return 'A';
        if ($score >= 85) return 'B+';
        if ($score >= 80) return 'B';
        if ($score >= 75) return 'C+';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Generate AI-powered recommendations
     */
    private function generateRecommendations(array $issues, array $scores): array
    {
        $recommendations = [];

        // Priority recommendations based on scores
        if ($scores['security'] < 80) {
            $recommendations[] = [
                'priority' => 'critical',
                'category' => 'security',
                'title' => 'تحسين الأمان بشكل عاجل',
                'description' => 'النتيجة الأمنية منخفضة. يجب معالجة الثغرات الأمنية فوراً.',
                'actions' => [
                    'راجع جميع المشاكل الأمنية الحرجة',
                    'نفذ Prepared Statements لجميع استعلامات SQL',
                    'أضف التحقق من المدخلات والتنظيف',
                    'فعّل CSRF protection لجميع النماذج',
                ],
            ];
        }

        if ($scores['maintainability'] < 70) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'maintainability',
                'title' => 'تحسين قابلية الصيانة',
                'description' => 'الكود معقد ويصعب صيانته.',
                'actions' => [
                    'قسّم الدوال الكبيرة إلى دوال أصغر',
                    'قلل التعقيد الدوري للدوال',
                    'أضف توثيق للكود',
                    'طبق مبادئ SOLID',
                ],
            ];
        }

        if ($scores['performance'] < 80) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'performance',
                'title' => 'تحسين الأداء',
                'description' => 'هناك فرص لتحسين أداء التطبيق.',
                'actions' => [
                    'استخدم Eager Loading لتجنب N+1',
                    'أضف Indexes لقاعدة البيانات',
                    'فعّل Caching للبيانات المتكررة',
                    'قلل عدد الاستعلامات',
                ],
            ];
        }

        return $recommendations;
    }

    /**
     * Get top complex files
     */
    private function getTopComplexFiles(array $complexityResults): array
    {
        // This would need detailed file-level analysis
        // Simplified version for now
        return [];
    }

    /**
     * Get current version
     */
    private function getCurrentVersion(): string
    {
        $versionFile = $this->projectPath . '/VERSION';
        if (file_exists($versionFile)) {
            return trim(file_get_contents($versionFile));
        }
        return 'unknown';
    }

    /**
     * Get PHP files from directory
     */
    private function getPhpFiles(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Get latest analysis
     */
    public function getLatestAnalysis(): ?CodeMetric
    {
        return CodeMetric::orderBy('analyzed_at', 'desc')->first();
    }

    /**
     * Get analysis history
     */
    public function getAnalysisHistory(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return CodeMetric::orderBy('analyzed_at', 'desc')->limit($limit)->get();
    }

    /**
     * Compare two analyses
     */
    public function compareAnalyses(int $id1, int $id2): array
    {
        $analysis1 = CodeMetric::findOrFail($id1);
        $analysis2 = CodeMetric::findOrFail($id2);

        return [
            'analysis1' => $analysis1,
            'analysis2' => $analysis2,
            'comparison' => [
                'overall_score' => $analysis2->overall_score - $analysis1->overall_score,
                'security_score' => $analysis2->security_score - $analysis1->security_score,
                'reliability_score' => $analysis2->reliability_score - $analysis1->reliability_score,
                'performance_score' => $analysis2->performance_score - $analysis1->performance_score,
                'maintainability_score' => $analysis2->maintainability_score - $analysis1->maintainability_score,
            ],
        ];
    }
}
