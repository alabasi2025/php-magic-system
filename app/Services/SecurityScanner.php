<?php

namespace App\Services;

/**
 * Security Scanner Service
 * 
 * خدمة شاملة لفحص الأمان في الكود والتطبيقات
 * تتضمن فحص SQL Injection, XSS, CSRF, والأذونات
 * 
 * @version 3.14.0
 * @package App\Services
 */
class SecurityScanner
{
    /**
     * نتائج الفحص
     */
    private array $results = [];

    /**
     * مستوى الخطورة
     */
    private const SEVERITY_CRITICAL = 'critical';
    private const SEVERITY_HIGH = 'high';
    private const SEVERITY_MEDIUM = 'medium';
    private const SEVERITY_LOW = 'low';
    private const SEVERITY_INFO = 'info';

    /**
     * فحص شامل للكود
     * 
     * @param string $code الكود المراد فحصه
     * @param array $options خيارات الفحص
     * @return array نتائج الفحص
     */
    public function scan(string $code, array $options = []): array
    {
        $this->results = [];

        // تفعيل جميع الفحوصات بشكل افتراضي
        $enabledScans = $options['scans'] ?? [
            'sql_injection' => true,
            'xss' => true,
            'csrf' => true,
            'permissions' => true,
            'file_upload' => true,
            'authentication' => true,
            'encryption' => true,
            'input_validation' => true,
        ];

        if ($enabledScans['sql_injection'] ?? true) {
            $this->scanSQLInjection($code);
        }

        if ($enabledScans['xss'] ?? true) {
            $this->scanXSS($code);
        }

        if ($enabledScans['csrf'] ?? true) {
            $this->scanCSRF($code);
        }

        if ($enabledScans['permissions'] ?? true) {
            $this->scanPermissions($code);
        }

        if ($enabledScans['file_upload'] ?? true) {
            $this->scanFileUpload($code);
        }

        if ($enabledScans['authentication'] ?? true) {
            $this->scanAuthentication($code);
        }

        if ($enabledScans['encryption'] ?? true) {
            $this->scanEncryption($code);
        }

        if ($enabledScans['input_validation'] ?? true) {
            $this->scanInputValidation($code);
        }

        return [
            'total_issues' => count($this->results),
            'critical_count' => $this->countBySeverity(self::SEVERITY_CRITICAL),
            'high_count' => $this->countBySeverity(self::SEVERITY_HIGH),
            'medium_count' => $this->countBySeverity(self::SEVERITY_MEDIUM),
            'low_count' => $this->countBySeverity(self::SEVERITY_LOW),
            'info_count' => $this->countBySeverity(self::SEVERITY_INFO),
            'issues' => $this->results,
            'score' => $this->calculateSecurityScore(),
        ];
    }

    /**
     * فحص SQL Injection
     */
    private function scanSQLInjection(string $code): void
    {
        $patterns = [
            // استخدام DB::raw بدون تنظيف
            [
                'pattern' => '/DB::raw\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
                'message' => 'استخدام DB::raw مع متغيرات غير محمية - خطر SQL Injection',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم Parameter Binding أو DB::raw مع تنظيف المدخلات',
            ],
            // استخدام whereRaw بدون معاملات آمنة
            [
                'pattern' => '/whereRaw\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
                'message' => 'استخدام whereRaw مع متغيرات مباشرة - خطر SQL Injection',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم whereRaw مع معاملات آمنة: whereRaw("column = ?", [$value])',
            ],
            // استخدام selectRaw بدون حماية
            [
                'pattern' => '/selectRaw\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
                'message' => 'استخدام selectRaw مع متغيرات غير محمية',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'استخدم selectRaw مع Parameter Binding',
            ],
            // استخدام orderByRaw بدون تنظيف
            [
                'pattern' => '/orderByRaw\s*\(\s*\$/',
                'message' => 'استخدام orderByRaw مع متغير مباشر',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'قم بتنظيف المدخلات أو استخدم whitelist للأعمدة المسموحة',
            ],
            // استخدام query مباشرة
            [
                'pattern' => '/DB::query\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
                'message' => 'استخدام DB::query مع استعلام غير آمن',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم Query Builder أو Eloquent ORM',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'SQL Injection');
    }

    /**
     * فحص XSS (Cross-Site Scripting)
     */
    private function scanXSS(string $code): void
    {
        $patterns = [
            // استخدام {!! !!} في Blade
            [
                'pattern' => '/\{\!\!.*?\!\!\}/',
                'message' => 'استخدام {!! !!} لعرض محتوى غير محمي - خطر XSS',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'استخدم {{ }} للتنظيف التلقائي أو استخدم htmlspecialchars()',
            ],
            // echo مباشر بدون تنظيف
            [
                'pattern' => '/echo\s+\$_(GET|POST|REQUEST|COOKIE)/',
                'message' => 'عرض مدخلات المستخدم مباشرة بدون تنظيف',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم htmlspecialchars() أو e() في Laravel',
            ],
            // استخدام innerHTML في JavaScript
            [
                'pattern' => '/\.innerHTML\s*=\s*.*?\$/',
                'message' => 'استخدام innerHTML مع محتوى ديناميكي - خطر XSS',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'استخدم textContent أو قم بتنظيف المحتوى',
            ],
            // استخدام eval في JavaScript
            [
                'pattern' => '/eval\s*\(/',
                'message' => 'استخدام eval() - خطر أمني كبير',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'تجنب استخدام eval() تماماً',
            ],
            // document.write
            [
                'pattern' => '/document\.write\s*\(/',
                'message' => 'استخدام document.write - قد يسبب XSS',
                'severity' => self::SEVERITY_MEDIUM,
                'fix' => 'استخدم طرق DOM آمنة',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'XSS');
    }

    /**
     * فحص CSRF (Cross-Site Request Forgery)
     */
    private function scanCSRF(string $code): void
    {
        $patterns = [
            // نماذج بدون @csrf
            [
                'pattern' => '/<form[^>]*method\s*=\s*["\']post["\'][^>]*>(?!.*@csrf)/',
                'message' => 'نموذج POST بدون حماية CSRF',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'أضف @csrf داخل النموذج',
            ],
            // استخدام Route::post بدون middleware
            [
                'pattern' => '/Route::post\s*\([^)]*\)\s*(?!.*->middleware)/',
                'message' => 'مسار POST بدون middleware للحماية',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'أضف middleware(\'web\') للحماية من CSRF',
            ],
            // تعطيل CSRF في VerifyCsrfToken
            [
                'pattern' => '/protected\s+\$except\s*=\s*\[/',
                'message' => 'تم العثور على استثناءات CSRF - تأكد من ضرورتها',
                'severity' => self::SEVERITY_MEDIUM,
                'fix' => 'قلل الاستثناءات إلى الحد الأدنى',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'CSRF');
    }

    /**
     * فحص الأذونات والصلاحيات
     */
    private function scanPermissions(string $code): void
    {
        $patterns = [
            // عدم التحقق من الصلاحيات
            [
                'pattern' => '/public\s+function\s+(delete|destroy|update|edit)\s*\([^)]*\)\s*\{(?!.*(?:authorize|can|Gate::))/s',
                'message' => 'دالة حساسة بدون التحقق من الصلاحيات',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'أضف $this->authorize() أو استخدم Gate::allows()',
            ],
            // استخدام Auth::user() بدون التحقق من null
            [
                'pattern' => '/Auth::user\(\)->(?!.*(?:if|&&|\|\||\?))/',
                'message' => 'استخدام Auth::user() بدون التحقق من وجود المستخدم',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'تحقق من وجود المستخدم أولاً أو استخدم auth()->user()?->',
            ],
            // عدم استخدام middleware للحماية
            [
                'pattern' => '/Route::(get|post|put|delete|patch)\s*\([^)]*\)\s*(?!.*->middleware)/',
                'message' => 'مسار بدون middleware للحماية',
                'severity' => self::SEVERITY_MEDIUM,
                'fix' => 'أضف middleware مناسب (auth, verified, etc.)',
            ],
            // استخدام whereIn مع مدخلات المستخدم
            [
                'pattern' => '/whereIn\s*\([^,]*,\s*\$_(GET|POST|REQUEST)/',
                'message' => 'استخدام whereIn مع مدخلات غير محققة',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'تحقق من المدخلات وقم بتنظيفها',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'Permissions');
    }

    /**
     * فحص رفع الملفات
     */
    private function scanFileUpload(string $code): void
    {
        $patterns = [
            // رفع ملفات بدون التحقق من النوع
            [
                'pattern' => '/\$request->file\([^)]*\)->store\((?!.*(?:mimes|mimetypes))/',
                'message' => 'رفع ملف بدون التحقق من النوع',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'أضف validation للتحقق من نوع الملف: mimes:jpg,png,pdf',
            ],
            // عدم التحقق من حجم الملف
            [
                'pattern' => '/\$request->file\([^)]*\)(?!.*(?:max:))/',
                'message' => 'رفع ملف بدون تحديد الحجم الأقصى',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'أضف validation: max:2048 (بالكيلوبايت)',
            ],
            // استخدام move_uploaded_file مباشرة
            [
                'pattern' => '/move_uploaded_file\s*\(/',
                'message' => 'استخدام move_uploaded_file مباشرة - استخدم Laravel Storage',
                'severity' => self::SEVERITY_MEDIUM,
                'fix' => 'استخدم Storage::put() أو $request->file()->store()',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'File Upload');
    }

    /**
     * فحص المصادقة
     */
    private function scanAuthentication(string $code): void
    {
        $patterns = [
            // كلمات مرور مشفرة بشكل ضعيف
            [
                'pattern' => '/(md5|sha1)\s*\(\s*\$.*?password/',
                'message' => 'استخدام تشفير ضعيف لكلمات المرور',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم Hash::make() أو bcrypt()',
            ],
            // تخزين كلمات مرور نصية
            [
                'pattern' => '/password\s*=\s*\$_(GET|POST|REQUEST)\[/',
                'message' => 'تخزين كلمة مرور بدون تشفير',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم Hash::make() قبل التخزين',
            ],
            // عدم استخدام throttle
            [
                'pattern' => '/Route::post\s*\(\s*["\']login["\'](?!.*throttle)/',
                'message' => 'صفحة تسجيل دخول بدون حماية من هجمات Brute Force',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'أضف middleware(\'throttle:5,1\') للحماية',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'Authentication');
    }

    /**
     * فحص التشفير
     */
    private function scanEncryption(string $code): void
    {
        $patterns = [
            // استخدام base64 للبيانات الحساسة
            [
                'pattern' => '/base64_encode\s*\(\s*\$.*?(password|token|secret)/',
                'message' => 'استخدام base64 ليس تشفيراً آمناً',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'استخدم Crypt::encrypt() للتشفير الآمن',
            ],
            // مفاتيح API مكشوفة
            [
                'pattern' => '/(api_key|secret_key|private_key)\s*=\s*["\'][^"\']+["\']/',
                'message' => 'مفتاح API مكشوف في الكود',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'انقل المفاتيح إلى ملف .env',
            ],
            // استخدام http بدلاً من https
            [
                'pattern' => '/http:\/\/(?!localhost)/',
                'message' => 'استخدام HTTP غير الآمن',
                'severity' => self::SEVERITY_MEDIUM,
                'fix' => 'استخدم HTTPS للاتصالات الآمنة',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'Encryption');
    }

    /**
     * فحص التحقق من المدخلات
     */
    private function scanInputValidation(string $code): void
    {
        $patterns = [
            // استخدام مدخلات بدون validation
            [
                'pattern' => '/\$request->(input|get|post)\([^)]*\)(?!.*validate)/',
                'message' => 'استخدام مدخلات بدون validation',
                'severity' => self::SEVERITY_HIGH,
                'fix' => 'أضف $request->validate() للتحقق من المدخلات',
            ],
            // عدم استخدام sanitize
            [
                'pattern' => '/\$_(GET|POST|REQUEST)\[/',
                'message' => 'استخدام مدخلات PHP مباشرة بدون تنظيف',
                'severity' => self::SEVERITY_CRITICAL,
                'fix' => 'استخدم $request->input() مع validation',
            ],
        ];

        $this->applyPatterns($code, $patterns, 'Input Validation');
    }

    /**
     * تطبيق الأنماط على الكود
     */
    private function applyPatterns(string $code, array $patterns, string $category): void
    {
        $lines = explode("\n", $code);
        
        foreach ($patterns as $pattern) {
            foreach ($lines as $lineNumber => $line) {
                if (preg_match($pattern['pattern'], $line)) {
                    $this->results[] = [
                        'category' => $category,
                        'line' => $lineNumber + 1,
                        'code' => trim($line),
                        'message' => $pattern['message'],
                        'severity' => $pattern['severity'],
                        'fix' => $pattern['fix'],
                    ];
                }
            }
        }
    }

    /**
     * حساب عدد المشاكل حسب الخطورة
     */
    private function countBySeverity(string $severity): int
    {
        return count(array_filter($this->results, fn($issue) => $issue['severity'] === $severity));
    }

    /**
     * حساب درجة الأمان
     */
    private function calculateSecurityScore(): int
    {
        $totalIssues = count($this->results);
        
        if ($totalIssues === 0) {
            return 100;
        }

        $criticalCount = $this->countBySeverity(self::SEVERITY_CRITICAL);
        $highCount = $this->countBySeverity(self::SEVERITY_HIGH);
        $mediumCount = $this->countBySeverity(self::SEVERITY_MEDIUM);
        $lowCount = $this->countBySeverity(self::SEVERITY_LOW);

        // حساب النقاط المفقودة
        $lostPoints = ($criticalCount * 20) + ($highCount * 10) + ($mediumCount * 5) + ($lowCount * 2);
        
        $score = max(0, 100 - $lostPoints);
        
        return $score;
    }

    /**
     * الحصول على توصيات الأمان
     */
    public function getRecommendations(): array
    {
        return [
            'sql_injection' => [
                'title' => 'حماية من SQL Injection',
                'tips' => [
                    'استخدم دائماً Eloquent ORM أو Query Builder',
                    'تجنب استخدام DB::raw مع متغيرات مباشرة',
                    'استخدم Parameter Binding في جميع الاستعلامات',
                    'قم بتنظيف جميع المدخلات قبل استخدامها',
                ],
            ],
            'xss' => [
                'title' => 'حماية من XSS',
                'tips' => [
                    'استخدم {{ }} في Blade بدلاً من {!! !!}',
                    'استخدم htmlspecialchars() لتنظيف المخرجات',
                    'تجنب استخدام eval() في JavaScript',
                    'استخدم Content Security Policy (CSP)',
                ],
            ],
            'csrf' => [
                'title' => 'حماية من CSRF',
                'tips' => [
                    'أضف @csrf في جميع نماذج POST',
                    'استخدم middleware(\'web\') للمسارات',
                    'قلل استثناءات CSRF إلى الحد الأدنى',
                    'استخدم SameSite cookies',
                ],
            ],
            'permissions' => [
                'title' => 'إدارة الصلاحيات',
                'tips' => [
                    'استخدم Policies و Gates للتحكم في الوصول',
                    'تحقق من الصلاحيات في جميع الدوال الحساسة',
                    'استخدم middleware للحماية',
                    'تحقق دائماً من وجود المستخدم قبل استخدام بياناته',
                ],
            ],
        ];
    }
}
