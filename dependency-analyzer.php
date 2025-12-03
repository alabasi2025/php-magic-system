#!/usr/bin/env php
<?php
/**
 * Dependency Analyzer v3.20.0
 * 
 * أداة متقدمة لتحليل التبعيات في مشاريع PHP باستخدام Composer
 * تقوم بتحليل شامل لملفات composer.json و composer.lock
 * وتوليد تقارير تفصيلية عن الحزم والتبعيات
 * 
 * @version 3.20.0
 * @author PHP Magic System
 * @date 2025-12-03
 */

class DependencyAnalyzer
{
    private string $projectPath;
    private array $composerJson = [];
    private array $composerLock = [];
    private array $analysis = [];
    
    private const VERSION = '3.20.0';
    private const COLORS = [
        'reset' => "\033[0m",
        'bold' => "\033[1m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'cyan' => "\033[36m",
        'red' => "\033[31m",
        'magenta' => "\033[35m",
    ];

    public function __construct(string $projectPath = '.')
    {
        $this->projectPath = realpath($projectPath);
        if (!$this->projectPath) {
            throw new Exception("المسار المحدد غير صالح: {$projectPath}");
        }
    }

    /**
     * تشغيل التحليل الكامل
     */
    public function run(): void
    {
        $this->printHeader();
        $this->loadComposerFiles();
        $this->analyzeProject();
        $this->printAnalysis();
        $this->generateReports();
    }

    /**
     * طباعة رأس البرنامج
     */
    private function printHeader(): void
    {
        $this->colorPrint("╔════════════════════════════════════════════════════════════╗", 'cyan', true);
        $this->colorPrint("║         Dependency Analyzer v" . self::VERSION . "                    ║", 'cyan', true);
        $this->colorPrint("║         أداة تحليل التبعيات المتقدمة                       ║", 'cyan', true);
        $this->colorPrint("╚════════════════════════════════════════════════════════════╝", 'cyan', true);
        echo PHP_EOL;
    }

    /**
     * تحميل ملفات Composer
     */
    private function loadComposerFiles(): void
    {
        $this->colorPrint("📂 جاري تحميل ملفات Composer...", 'blue', true);
        
        // تحميل composer.json
        $composerJsonPath = $this->projectPath . '/composer.json';
        if (!file_exists($composerJsonPath)) {
            throw new Exception("ملف composer.json غير موجود في: {$composerJsonPath}");
        }
        
        $this->composerJson = json_decode(file_get_contents($composerJsonPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("خطأ في قراءة composer.json: " . json_last_error_msg());
        }
        $this->colorPrint("   ✓ تم تحميل composer.json", 'green');
        
        // تحميل composer.lock
        $composerLockPath = $this->projectPath . '/composer.lock';
        if (file_exists($composerLockPath)) {
            $this->composerLock = json_decode(file_get_contents($composerLockPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("خطأ في قراءة composer.lock: " . json_last_error_msg());
            }
            $this->colorPrint("   ✓ تم تحميل composer.lock", 'green');
        } else {
            $this->colorPrint("   ⚠ ملف composer.lock غير موجود", 'yellow');
        }
        
        echo PHP_EOL;
    }

    /**
     * تحليل المشروع
     */
    private function analyzeProject(): void
    {
        $this->colorPrint("🔍 جاري تحليل المشروع...", 'blue', true);
        
        // معلومات المشروع الأساسية
        $this->analysis['project'] = [
            'name' => $this->composerJson['name'] ?? 'غير محدد',
            'description' => $this->composerJson['description'] ?? 'غير محدد',
            'type' => $this->composerJson['type'] ?? 'library',
            'license' => $this->composerJson['license'] ?? 'غير محدد',
            'php_version' => $this->composerJson['require']['php'] ?? 'غير محدد',
        ];

        // تحليل التبعيات الإنتاجية
        $this->analysis['production_dependencies'] = $this->analyzeDependencies(
            $this->composerJson['require'] ?? []
        );

        // تحليل تبعيات التطوير
        $this->analysis['dev_dependencies'] = $this->analyzeDependencies(
            $this->composerJson['require-dev'] ?? []
        );

        // تحليل الحزم المثبتة من composer.lock
        if (!empty($this->composerLock['packages'])) {
            $this->analysis['installed_packages'] = $this->analyzeInstalledPackages(
                $this->composerLock['packages']
            );
        }

        // تحليل حسب الفئات
        $this->analysis['categories'] = $this->categorizePackages();

        // إحصائيات عامة
        $this->analysis['statistics'] = $this->calculateStatistics();

        $this->colorPrint("   ✓ تم إكمال التحليل بنجاح", 'green');
        echo PHP_EOL;
    }

    /**
     * تحليل التبعيات
     */
    private function analyzeDependencies(array $dependencies): array
    {
        $analyzed = [];
        
        foreach ($dependencies as $package => $version) {
            if ($package === 'php') {
                continue;
            }
            
            $analyzed[] = [
                'name' => $package,
                'version_constraint' => $version,
                'vendor' => explode('/', $package)[0] ?? 'unknown',
                'package_name' => explode('/', $package)[1] ?? $package,
            ];
        }
        
        return $analyzed;
    }

    /**
     * تحليل الحزم المثبتة
     */
    private function analyzeInstalledPackages(array $packages): array
    {
        $analyzed = [];
        
        foreach ($packages as $package) {
            $analyzed[] = [
                'name' => $package['name'] ?? 'unknown',
                'version' => $package['version'] ?? 'unknown',
                'type' => $package['type'] ?? 'library',
                'description' => $package['description'] ?? '',
                'license' => is_array($package['license'] ?? null) 
                    ? implode(', ', $package['license']) 
                    : ($package['license'] ?? 'غير محدد'),
                'vendor' => explode('/', $package['name'] ?? 'unknown/unknown')[0],
                'homepage' => $package['homepage'] ?? '',
                'keywords' => $package['keywords'] ?? [],
            ];
        }
        
        return $analyzed;
    }

    /**
     * تصنيف الحزم حسب الفئات
     */
    private function categorizePackages(): array
    {
        $categories = [];
        
        if (!empty($this->composerLock['packages'])) {
            foreach ($this->composerLock['packages'] as $package) {
                $vendor = explode('/', $package['name'] ?? 'unknown/unknown')[0];
                
                if (!isset($categories[$vendor])) {
                    $categories[$vendor] = [
                        'count' => 0,
                        'packages' => [],
                    ];
                }
                
                $categories[$vendor]['count']++;
                $categories[$vendor]['packages'][] = [
                    'name' => $package['name'],
                    'version' => $package['version'] ?? 'unknown',
                ];
            }
        }
        
        // ترتيب حسب العدد
        uasort($categories, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        return $categories;
    }

    /**
     * حساب الإحصائيات
     */
    private function calculateStatistics(): array
    {
        $stats = [
            'total_production_deps' => count($this->analysis['production_dependencies']),
            'total_dev_deps' => count($this->analysis['dev_dependencies']),
            'total_installed_packages' => count($this->analysis['installed_packages'] ?? []),
            'total_vendors' => count($this->analysis['categories']),
            'top_vendors' => [],
        ];

        // أكثر 10 موردين استخداماً
        $topVendors = array_slice($this->analysis['categories'], 0, 10, true);
        foreach ($topVendors as $vendor => $data) {
            $stats['top_vendors'][$vendor] = $data['count'];
        }

        return $stats;
    }

    /**
     * طباعة نتائج التحليل
     */
    private function printAnalysis(): void
    {
        $this->colorPrint("📊 نتائج التحليل", 'magenta', true);
        $this->colorPrint(str_repeat("=", 60), 'cyan');
        echo PHP_EOL;

        // معلومات المشروع
        $this->printSection("معلومات المشروع", [
            'الاسم' => $this->analysis['project']['name'],
            'الوصف' => $this->analysis['project']['description'],
            'النوع' => $this->analysis['project']['type'],
            'الترخيص' => $this->analysis['project']['license'],
            'إصدار PHP' => $this->analysis['project']['php_version'],
        ]);

        // الإحصائيات العامة
        $this->printSection("الإحصائيات العامة", [
            'التبعيات الإنتاجية' => $this->analysis['statistics']['total_production_deps'],
            'تبعيات التطوير' => $this->analysis['statistics']['total_dev_deps'],
            'إجمالي الحزم المثبتة' => $this->analysis['statistics']['total_installed_packages'],
            'عدد الموردين' => $this->analysis['statistics']['total_vendors'],
        ]);

        // أكثر الموردين استخداماً
        $this->colorPrint("🏆 أكثر 10 موردين استخداماً:", 'yellow', true);
        foreach ($this->analysis['statistics']['top_vendors'] as $vendor => $count) {
            $this->colorPrint(sprintf("   %-30s %3d حزمة", $vendor, $count), 'cyan');
        }
        echo PHP_EOL;

        // تفاصيل الفئات الرئيسية
        $this->printMainCategories();
    }

    /**
     * طباعة قسم من المعلومات
     */
    private function printSection(string $title, array $data): void
    {
        $this->colorPrint("📌 {$title}:", 'yellow', true);
        foreach ($data as $key => $value) {
            $this->colorPrint(sprintf("   %-25s: %s", $key, $value), 'cyan');
        }
        echo PHP_EOL;
    }

    /**
     * طباعة الفئات الرئيسية
     */
    private function printMainCategories(): void
    {
        $mainCategories = ['laravel', 'filament', 'spatie', 'symfony', 'livewire'];
        
        foreach ($mainCategories as $category) {
            if (isset($this->analysis['categories'][$category])) {
                $data = $this->analysis['categories'][$category];
                $this->colorPrint("📦 حزم {$category} ({$data['count']} حزمة):", 'yellow', true);
                
                foreach ($data['packages'] as $package) {
                    $this->colorPrint(
                        sprintf("   %-45s %s", $package['name'], $package['version']), 
                        'cyan'
                    );
                }
                echo PHP_EOL;
            }
        }
    }

    /**
     * توليد التقارير
     */
    private function generateReports(): void
    {
        $this->colorPrint("📝 جاري توليد التقارير...", 'blue', true);
        
        // تقرير JSON
        $this->generateJsonReport();
        
        // تقرير Markdown
        $this->generateMarkdownReport();
        
        // تقرير CSV
        $this->generateCsvReport();
        
        $this->colorPrint("   ✓ تم توليد جميع التقارير بنجاح", 'green');
        echo PHP_EOL;
    }

    /**
     * توليد تقرير JSON
     */
    private function generateJsonReport(): void
    {
        $reportPath = $this->projectPath . '/dependency-analysis.json';
        $report = [
            'analyzer_version' => self::VERSION,
            'analysis_date' => date('Y-m-d H:i:s'),
            'project_path' => $this->projectPath,
            'analysis' => $this->analysis,
        ];
        
        file_put_contents(
            $reportPath, 
            json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        $this->colorPrint("   ✓ تقرير JSON: dependency-analysis.json", 'green');
    }

    /**
     * توليد تقرير Markdown
     */
    private function generateMarkdownReport(): void
    {
        $reportPath = $this->projectPath . '/dependency-analysis.md';
        
        $md = "# تقرير تحليل التبعيات\n\n";
        $md .= "**تم التوليد بواسطة:** Dependency Analyzer v" . self::VERSION . "\n";
        $md .= "**التاريخ:** " . date('Y-m-d H:i:s') . "\n\n";
        
        $md .= "## معلومات المشروع\n\n";
        $md .= "| المعلومة | القيمة |\n";
        $md .= "|----------|--------|\n";
        foreach ($this->analysis['project'] as $key => $value) {
            $md .= "| {$key} | {$value} |\n";
        }
        $md .= "\n";
        
        $md .= "## الإحصائيات العامة\n\n";
        $md .= "| الإحصائية | العدد |\n";
        $md .= "|-----------|-------|\n";
        $md .= "| التبعيات الإنتاجية | {$this->analysis['statistics']['total_production_deps']} |\n";
        $md .= "| تبعيات التطوير | {$this->analysis['statistics']['total_dev_deps']} |\n";
        $md .= "| إجمالي الحزم المثبتة | {$this->analysis['statistics']['total_installed_packages']} |\n";
        $md .= "| عدد الموردين | {$this->analysis['statistics']['total_vendors']} |\n";
        $md .= "\n";
        
        $md .= "## أكثر الموردين استخداماً\n\n";
        $md .= "| المورد | عدد الحزم |\n";
        $md .= "|--------|----------|\n";
        foreach ($this->analysis['statistics']['top_vendors'] as $vendor => $count) {
            $md .= "| {$vendor} | {$count} |\n";
        }
        $md .= "\n";
        
        $md .= "## التبعيات الإنتاجية\n\n";
        $md .= "| الحزمة | القيد الإصداري |\n";
        $md .= "|--------|---------------|\n";
        foreach ($this->analysis['production_dependencies'] as $dep) {
            $md .= "| {$dep['name']} | {$dep['version_constraint']} |\n";
        }
        $md .= "\n";
        
        $md .= "## تبعيات التطوير\n\n";
        $md .= "| الحزمة | القيد الإصداري |\n";
        $md .= "|--------|---------------|\n";
        foreach ($this->analysis['dev_dependencies'] as $dep) {
            $md .= "| {$dep['name']} | {$dep['version_constraint']} |\n";
        }
        $md .= "\n";
        
        // إضافة تفاصيل الفئات الرئيسية
        $mainCategories = ['laravel', 'filament', 'spatie', 'symfony'];
        foreach ($mainCategories as $category) {
            if (isset($this->analysis['categories'][$category])) {
                $data = $this->analysis['categories'][$category];
                $md .= "## حزم {$category}\n\n";
                $md .= "| الحزمة | الإصدار |\n";
                $md .= "|--------|----------|\n";
                foreach ($data['packages'] as $package) {
                    $md .= "| {$package['name']} | {$package['version']} |\n";
                }
                $md .= "\n";
            }
        }
        
        file_put_contents($reportPath, $md);
        $this->colorPrint("   ✓ تقرير Markdown: dependency-analysis.md", 'green');
    }

    /**
     * توليد تقرير CSV
     */
    private function generateCsvReport(): void
    {
        $reportPath = $this->projectPath . '/dependency-analysis.csv';
        
        $csv = fopen($reportPath, 'w');
        
        // كتابة الترويسة
        fputcsv($csv, ['Package Name', 'Version', 'Type', 'Vendor', 'License', 'Description']);
        
        // كتابة البيانات
        if (!empty($this->analysis['installed_packages'])) {
            foreach ($this->analysis['installed_packages'] as $package) {
                fputcsv($csv, [
                    $package['name'],
                    $package['version'],
                    $package['type'],
                    $package['vendor'],
                    $package['license'],
                    $package['description'],
                ]);
            }
        }
        
        fclose($csv);
        $this->colorPrint("   ✓ تقرير CSV: dependency-analysis.csv", 'green');
    }

    /**
     * طباعة نص ملون
     */
    private function colorPrint(string $text, string $color = 'reset', bool $bold = false): void
    {
        $output = '';
        
        if ($bold && isset(self::COLORS['bold'])) {
            $output .= self::COLORS['bold'];
        }
        
        if (isset(self::COLORS[$color])) {
            $output .= self::COLORS[$color];
        }
        
        $output .= $text . self::COLORS['reset'];
        
        echo $output . PHP_EOL;
    }
}

// تشغيل البرنامج
try {
    $projectPath = $argv[1] ?? '.';
    $analyzer = new DependencyAnalyzer($projectPath);
    $analyzer->run();
    
    echo PHP_EOL;
    echo "✅ تم إكمال التحليل بنجاح!" . PHP_EOL;
    echo "📁 التقارير متوفرة في مجلد المشروع" . PHP_EOL;
    echo PHP_EOL;
    
} catch (Exception $e) {
    echo "\033[31m❌ خطأ: " . $e->getMessage() . "\033[0m" . PHP_EOL;
    exit(1);
}
