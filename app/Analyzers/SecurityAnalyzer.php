<?php

namespace App\Analyzers;

class SecurityAnalyzer
{
    private array $issues = [];
    private array $patterns = [];

    public function __construct()
    {
        $this->initializePatterns();
    }

    /**
     * Initialize security patterns to check
     */
    private function initializePatterns(): void
    {
        $this->patterns = [
            // SQL Injection
            'sql_injection' => [
                'pattern' => '/DB::raw\s*\(\s*["\'].*\$.*["\']\s*\)|->whereRaw\s*\(\s*["\'].*\$.*["\']\s*\)/i',
                'severity' => 'critical',
                'message' => 'احتمالية حقن SQL - استخدام متغيرات مباشرة في استعلامات SQL',
                'recommendation' => 'استخدم Prepared Statements أو Query Builder مع Parameter Binding',
            ],
            
            // XSS
            'xss_vulnerability' => [
                'pattern' => '/echo\s+\$|print\s+\$|<\?=\s*\$/i',
                'severity' => 'high',
                'message' => 'احتمالية XSS - طباعة متغيرات بدون تنظيف',
                'recommendation' => 'استخدم {{ $variable }} في Blade أو htmlspecialchars() في PHP',
            ],
            
            // Command Injection
            'command_injection' => [
                'pattern' => '/exec\s*\(|shell_exec\s*\(|system\s*\(|passthru\s*\(/i',
                'severity' => 'critical',
                'message' => 'استخدام دوال تنفيذ الأوامر - خطر حقن الأوامر',
                'recommendation' => 'تجنب استخدام exec() وتحقق من المدخلات بدقة',
            ],
            
            // File Upload
            'unsafe_file_upload' => [
                'pattern' => '/move_uploaded_file\s*\(/i',
                'severity' => 'high',
                'message' => 'رفع ملفات بدون التحقق من النوع',
                'recommendation' => 'تحقق من نوع الملف وحجمه واستخدم Storage facade',
            ],
            
            // Eval usage
            'eval_usage' => [
                'pattern' => '/eval\s*\(/i',
                'severity' => 'critical',
                'message' => 'استخدام eval() - خطر أمني كبير',
                'recommendation' => 'تجنب استخدام eval() تماماً',
            ],
            
            // Weak cryptography
            'weak_crypto' => [
                'pattern' => '/md5\s*\(|sha1\s*\(/i',
                'severity' => 'medium',
                'message' => 'استخدام خوارزميات تشفير ضعيفة',
                'recommendation' => 'استخدم bcrypt أو Hash::make() لكلمات المرور',
            ],
            
            // Hardcoded credentials
            'hardcoded_credentials' => [
                'pattern' => '/password\s*=\s*["\'][^"\']+["\']|api_key\s*=\s*["\'][^"\']+["\']/i',
                'severity' => 'high',
                'message' => 'بيانات اعتماد مشفرة في الكود',
                'recommendation' => 'استخدم ملف .env لتخزين البيانات الحساسة',
            ],
            
            // Unvalidated redirect
            'open_redirect' => [
                'pattern' => '/redirect\s*\(\s*\$|header\s*\(\s*["\']Location:.*\$/i',
                'severity' => 'medium',
                'message' => 'إعادة توجيه غير محققة - احتمالية Open Redirect',
                'recommendation' => 'تحقق من عناوين URL قبل إعادة التوجيه',
            ],
            
            // Insecure deserialization
            'unsafe_unserialize' => [
                'pattern' => '/unserialize\s*\(/i',
                'severity' => 'high',
                'message' => 'استخدام unserialize() - خطر تنفيذ كود عشوائي',
                'recommendation' => 'استخدم JSON بدلاً من serialize/unserialize',
            ],
            
            // Missing CSRF protection
            'missing_csrf' => [
                'pattern' => '/<form[^>]*method\s*=\s*["\']post["\'][^>]*>/i',
                'severity' => 'medium',
                'message' => 'نموذج POST بدون حماية CSRF',
                'recommendation' => 'أضف @csrf في نماذج Blade',
            ],
        ];
    }

    /**
     * Analyze file for security issues
     */
    public function analyzeFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        $issues = [];

        foreach ($this->patterns as $type => $pattern) {
            if (preg_match_all($pattern['pattern'], $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $line = $this->getLineNumber($content, $match[1]);
                    $issues[] = [
                        'type' => $type,
                        'severity' => $pattern['severity'],
                        'message' => $pattern['message'],
                        'recommendation' => $pattern['recommendation'],
                        'file' => $filePath,
                        'line' => $line,
                        'code_snippet' => $this->getCodeSnippet($content, $line),
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * Analyze directory for security issues
     */
    public function analyzeDirectory(string $directory): array
    {
        $files = $this->getPhpFiles($directory);
        $allIssues = [];

        foreach ($files as $file) {
            $issues = $this->analyzeFile($file);
            $allIssues = array_merge($allIssues, $issues);
        }

        return $this->aggregateIssues($allIssues);
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
     * Get line number from offset
     */
    private function getLineNumber(string $content, int $offset): int
    {
        return substr_count(substr($content, 0, $offset), "\n") + 1;
    }

    /**
     * Get code snippet around line
     */
    private function getCodeSnippet(string $content, int $line, int $context = 2): array
    {
        $lines = explode("\n", $content);
        $start = max(0, $line - $context - 1);
        $end = min(count($lines), $line + $context);
        
        $snippet = [];
        for ($i = $start; $i < $end; $i++) {
            $snippet[$i + 1] = $lines[$i];
        }
        
        return $snippet;
    }

    /**
     * Aggregate issues and calculate score
     */
    private function aggregateIssues(array $issues): array
    {
        $critical = 0;
        $high = 0;
        $medium = 0;
        $low = 0;

        foreach ($issues as $issue) {
            switch ($issue['severity']) {
                case 'critical':
                    $critical++;
                    break;
                case 'high':
                    $high++;
                    break;
                case 'medium':
                    $medium++;
                    break;
                case 'low':
                    $low++;
                    break;
            }
        }

        // Calculate security score (0-100)
        $score = 100;
        $score -= ($critical * 20); // Each critical issue: -20 points
        $score -= ($high * 10);     // Each high issue: -10 points
        $score -= ($medium * 5);    // Each medium issue: -5 points
        $score -= ($low * 2);       // Each low issue: -2 points
        $score = max(0, $score);

        return [
            'score' => $score,
            'total_issues' => count($issues),
            'critical' => $critical,
            'high' => $high,
            'medium' => $medium,
            'low' => $low,
            'issues' => $issues,
            'top_issues' => array_slice($issues, 0, 10),
        ];
    }

    /**
     * Check for common Laravel security misconfigurations
     */
    public function checkLaravelSecurity(string $projectPath): array
    {
        $issues = [];

        // Check .env file
        $envPath = $projectPath . '/.env';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            
            // Check APP_DEBUG
            if (preg_match('/APP_DEBUG\s*=\s*true/i', $envContent)) {
                $issues[] = [
                    'type' => 'debug_mode_enabled',
                    'severity' => 'high',
                    'message' => 'وضع التصحيح مفعل في الإنتاج',
                    'recommendation' => 'اضبط APP_DEBUG=false في بيئة الإنتاج',
                    'file' => '.env',
                ];
            }

            // Check APP_KEY
            if (preg_match('/APP_KEY\s*=\s*$/m', $envContent)) {
                $issues[] = [
                    'type' => 'missing_app_key',
                    'severity' => 'critical',
                    'message' => 'APP_KEY غير محدد',
                    'recommendation' => 'قم بتشغيل php artisan key:generate',
                    'file' => '.env',
                ];
            }
        }

        // Check CORS configuration
        $corsConfig = $projectPath . '/config/cors.php';
        if (file_exists($corsConfig)) {
            $content = file_get_contents($corsConfig);
            if (preg_match('/["\']allowed_origins["\']\s*=>\s*\[\s*["\']\*["\']\s*\]/i', $content)) {
                $issues[] = [
                    'type' => 'open_cors',
                    'severity' => 'medium',
                    'message' => 'CORS مفتوح لجميع المصادر',
                    'recommendation' => 'حدد المصادر المسموح بها بدقة',
                    'file' => 'config/cors.php',
                ];
            }
        }

        return $issues;
    }
}
