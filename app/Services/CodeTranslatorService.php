<?php

namespace App\Services\AI;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * خدمة مترجم الأكواد - ترجمة الأكواد بين اللغات المختلفة
 * 
 * تستخدم Manus AI لترجمة الأكواد بذكاء بين PHP و Python/JavaScript/Java/C#
 * 
 * @version 3.15.0
 * @author PHP Magic System
 */
class CodeTranslatorService
{
    private $apiKey;
    private $apiUrl = 'https://api.manus.ai/v1/tasks';
    
    /**
     * اللغات المدعومة
     */
    private const SUPPORTED_LANGUAGES = [
        'php' => 'PHP',
        'python' => 'Python',
        'javascript' => 'JavaScript',
        'java' => 'Java',
        'csharp' => 'C#',
        'typescript' => 'TypeScript',
    ];
    
    /**
     * أنماط اللغات للكشف التلقائي
     */
    private const LANGUAGE_PATTERNS = [
        'php' => [
            '/<\?php/',
            '/namespace\s+[\w\\\\]+;/',
            '/use\s+[\w\\\\]+;/',
            '/\$\w+\s*=/',
            '/function\s+\w+\s*\(/',
            '/class\s+\w+/',
            '/->\w+/',
        ],
        'python' => [
            '/def\s+\w+\s*\(/',
            '/class\s+\w+\s*:/',
            '/import\s+\w+/',
            '/from\s+\w+\s+import/',
            '/__init__\s*\(/',
            '/self\.\w+/',
        ],
        'javascript' => [
            '/function\s+\w+\s*\(/',
            '/const\s+\w+\s*=/',
            '/let\s+\w+\s*=/',
            '/var\s+\w+\s*=/',
            '/=>\s*{/',
            '/class\s+\w+\s*{/',
            '/require\s*\(/',
            '/import\s+.*from/',
        ],
        'java' => [
            '/public\s+class\s+\w+/',
            '/private\s+\w+\s+\w+;/',
            '/public\s+static\s+void\s+main/',
            '/import\s+[\w.]+;/',
            '/@Override/',
            '/System\.out\.println/',
        ],
        'csharp' => [
            '/using\s+[\w.]+;/',
            '/namespace\s+[\w.]+/',
            '/public\s+class\s+\w+/',
            '/private\s+\w+\s+\w+;/',
            '/Console\.WriteLine/',
            '/\[.*\]/',
        ],
    ];
    
    public function __construct()
    {
        $this->apiKey = AiSetting::where('key', 'manus_api_key')->value('value');
    }
    
    /**
     * ترجمة الكود من لغة إلى أخرى
     */
    public function translateCode(string $code, string $fromLang, string $toLang): array
    {
        try {
            // التحقق من اللغات المدعومة
            if (!$this->isLanguageSupported($fromLang) || !$this->isLanguageSupported($toLang)) {
                return [
                    'success' => false,
                    'error' => 'اللغة المحددة غير مدعومة'
                ];
            }
            
            // التحقق من أن اللغتين مختلفتين
            if ($fromLang === $toLang) {
                return [
                    'success' => false,
                    'error' => 'اللغة المصدر والهدف متطابقتان'
                ];
            }
            
            // التحقق من وجود API Key
            if (!$this->apiKey) {
                return [
                    'success' => false,
                    'error' => 'لم يتم تكوين Manus API Key'
                ];
            }
            
            // محاولة الحصول على الترجمة من الذاكرة المؤقتة
            $cacheKey = 'code_translation_' . md5($code . $fromLang . $toLang);
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return [
                    'success' => true,
                    'translated_code' => $cached['translated_code'],
                    'notes' => $cached['notes'],
                    'from_cache' => true,
                ];
            }
            
            // بناء Prompt للترجمة
            $prompt = $this->buildTranslationPrompt($code, $fromLang, $toLang);
            
            // إرسال الطلب إلى Manus AI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'prompt' => $prompt,
                'model' => 'manus-1.5-lite',
                'mode' => 'chat',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $result = $this->parseTranslationResponse($data);
                
                // حفظ في الذاكرة المؤقتة لمدة 24 ساعة
                Cache::put($cacheKey, [
                    'translated_code' => $result['translated_code'],
                    'notes' => $result['notes'],
                ], now()->addHours(24));
                
                return [
                    'success' => true,
                    'translated_code' => $result['translated_code'],
                    'notes' => $result['notes'],
                    'from_cache' => false,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            Log::error('Code Translation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'حدث خطأ أثناء الترجمة: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * كشف لغة البرمجة تلقائياً
     */
    public function detectLanguage(string $code): array
    {
        try {
            $scores = [];
            
            foreach (self::LANGUAGE_PATTERNS as $lang => $patterns) {
                $score = 0;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $code)) {
                        $score++;
                    }
                }
                $scores[$lang] = $score;
            }
            
            // ترتيب حسب النتيجة
            arsort($scores);
            
            $detectedLang = array_key_first($scores);
            $confidence = $scores[$detectedLang] > 0 ? 
                min(100, ($scores[$detectedLang] / count(self::LANGUAGE_PATTERNS[$detectedLang])) * 100) : 0;
            
            return [
                'success' => true,
                'language' => $detectedLang,
                'language_name' => self::SUPPORTED_LANGUAGES[$detectedLang] ?? 'Unknown',
                'confidence' => round($confidence, 2),
                'all_scores' => $scores,
            ];
            
        } catch (\Exception $e) {
            Log::error('Language Detection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'فشل كشف اللغة: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * التحقق من صحة الكود
     */
    public function validateSyntax(string $code, string $language): array
    {
        try {
            // التحقق الأساسي من وجود الكود
            if (empty(trim($code))) {
                return [
                    'success' => false,
                    'valid' => false,
                    'error' => 'الكود فارغ'
                ];
            }
            
            // التحقق حسب اللغة
            $validationResult = match($language) {
                'php' => $this->validatePhpSyntax($code),
                'python' => $this->validatePythonSyntax($code),
                'javascript' => $this->validateJavaScriptSyntax($code),
                'java' => $this->validateJavaSyntax($code),
                'csharp' => $this->validateCSharpSyntax($code),
                default => ['valid' => true, 'message' => 'لا يمكن التحقق من هذه اللغة']
            };
            
            return [
                'success' => true,
                'valid' => $validationResult['valid'],
                'message' => $validationResult['message'] ?? '',
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'فشل التحقق: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * مقارنة الكود الأصلي والمترجم
     */
    public function compareTranslations(string $original, string $translated, string $fromLang, string $toLang): array
    {
        try {
            $comparison = [
                'original_lines' => substr_count($original, "\n") + 1,
                'translated_lines' => substr_count($translated, "\n") + 1,
                'original_size' => strlen($original),
                'translated_size' => strlen($translated),
                'from_language' => self::SUPPORTED_LANGUAGES[$fromLang] ?? $fromLang,
                'to_language' => self::SUPPORTED_LANGUAGES[$toLang] ?? $toLang,
            ];
            
            // حساب نسبة التغيير
            $comparison['size_change_percent'] = round(
                (($comparison['translated_size'] - $comparison['original_size']) / $comparison['original_size']) * 100,
                2
            );
            
            $comparison['lines_change_percent'] = round(
                (($comparison['translated_lines'] - $comparison['original_lines']) / $comparison['original_lines']) * 100,
                2
            );
            
            return [
                'success' => true,
                'comparison' => $comparison,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'فشلت المقارنة: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * الحصول على اللغات المدعومة
     */
    public function getSupportedLanguages(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }
    
    /**
     * التحقق من دعم اللغة
     */
    private function isLanguageSupported(string $language): bool
    {
        return array_key_exists($language, self::SUPPORTED_LANGUAGES);
    }
    
    /**
     * بناء Prompt للترجمة
     */
    private function buildTranslationPrompt(string $code, string $fromLang, string $toLang): string
    {
        $fromLangName = self::SUPPORTED_LANGUAGES[$fromLang];
        $toLangName = self::SUPPORTED_LANGUAGES[$toLang];
        
        $specificInstructions = $this->getSpecificInstructions($fromLang, $toLang);
        
        return <<<PROMPT
أنت خبير في ترجمة الأكواد البرمجية بين اللغات المختلفة. قم بترجمة الكود التالي من {$fromLangName} إلى {$toLangName}.

**متطلبات الترجمة:**
1. الحفاظ على المنطق والوظيفة الأصلية بشكل كامل
2. استخدام أفضل الممارسات (Best Practices) للغة {$toLangName}
3. إضافة تعليقات توضيحية للأجزاء المعقدة أو المهمة
4. تحسين الأداء والكفاءة حيثما أمكن
5. الحفاظ على قابلية القراءة (Readability) والصيانة
6. استخدام المكتبات والأطر الشائعة في {$toLangName}

{$specificInstructions}

**الكود الأصلي ({$fromLangName}):**
```{$fromLang}
{$code}
```

**المطلوب:**
قم بتوفير الكود المترجم إلى {$toLangName} مع ملاحظات حول التغييرات المهمة.

**تنسيق الإجابة:**
```{$toLang}
[الكود المترجم هنا]
```

**ملاحظات الترجمة:**
- [ملاحظة 1]
- [ملاحظة 2]
- [...]
PROMPT;
    }
    
    /**
     * الحصول على تعليمات محددة حسب اللغات
     */
    private function getSpecificInstructions(string $fromLang, string $toLang): string
    {
        $instructions = [
            'php_to_python' => "- تحويل Eloquent ORM إلى SQLAlchemy\n- تحويل Blade Templates إلى Jinja2\n- تحويل Middleware إلى Decorators\n- استخدام Type Hints في Python 3.10+",
            'php_to_javascript' => "- تحويل Classes إلى ES6 Classes\n- تحويل Eloquent إلى Sequelize أو Mongoose\n- استخدام async/await للعمليات غير المتزامنة\n- تحويل Blade إلى React JSX أو Vue Templates",
            'php_to_java' => "- تحويل Classes إلى Spring Components\n- تحويل Eloquent إلى Hibernate/JPA\n- استخدام Annotations المناسبة\n- تحويل Routes إلى Spring Controllers",
            'php_to_csharp' => "- تحويل Classes إلى C# Classes مع Properties\n- تحويل Eloquent إلى Entity Framework\n- استخدام LINQ للاستعلامات\n- تحويل Blade إلى Razor Views",
            'python_to_php' => "- تحويل SQLAlchemy إلى Eloquent ORM\n- تحويل Decorators إلى Middleware\n- استخدام Type Declarations في PHP 8+\n- تحويل Jinja2 إلى Blade",
            'javascript_to_php' => "- تحويل Promises/async-await إلى Synchronous Code أو Promises في PHP\n- تحويل Sequelize/Mongoose إلى Eloquent\n- تحويل Express Routes إلى Laravel Routes\n- استخدام PHP 8+ Features",
            'java_to_php' => "- تحويل Spring Annotations إلى Laravel Attributes\n- تحويل Hibernate إلى Eloquent\n- تبسيط الكود باستخدام PHP's Dynamic Typing\n- استخدام Laravel's Service Container",
            'csharp_to_php' => "- تحويل Entity Framework إلى Eloquent\n- تحويل LINQ إلى Eloquent Query Builder\n- تحويل Properties إلى PHP Properties\n- تحويل Razor إلى Blade",
        ];
        
        $key = $fromLang . '_to_' . $toLang;
        return $instructions[$key] ?? "- اتبع أفضل الممارسات للغة الهدف";
    }
    
    /**
     * تحليل استجابة الترجمة
     */
    private function parseTranslationResponse(array $data): array
    {
        $content = $data['result'] ?? $data['response'] ?? '';
        
        // استخراج الكود المترجم
        preg_match('/```[\w]*\n(.*?)\n```/s', $content, $codeMatches);
        $translatedCode = $codeMatches[1] ?? $content;
        
        // استخراج الملاحظات
        preg_match('/\*\*ملاحظات الترجمة:\*\*(.*?)(?=\n\n|$)/s', $content, $notesMatches);
        $notes = $notesMatches[1] ?? 'لا توجد ملاحظات';
        
        // تنظيف الملاحظات
        $notes = trim($notes);
        $notes = preg_replace('/^-\s*/m', '• ', $notes);
        
        return [
            'translated_code' => trim($translatedCode),
            'notes' => $notes ?: 'تمت الترجمة بنجاح',
        ];
    }
    
    /**
     * التحقق من صحة PHP
     */
    private function validatePhpSyntax(string $code): array
    {
        // التحقق من وجود <?php
        if (!str_contains($code, '<?php') && !str_starts_with(trim($code), '<?')) {
            return [
                'valid' => false,
                'message' => 'الكود يجب أن يبدأ بـ <?php'
            ];
        }
        
        // التحقق من الأقواس المتوازنة
        $openBraces = substr_count($code, '{');
        $closeBraces = substr_count($code, '}');
        
        if ($openBraces !== $closeBraces) {
            return [
                'valid' => false,
                'message' => 'الأقواس غير متوازنة'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'الكود يبدو صحيحاً'
        ];
    }
    
    /**
     * التحقق من صحة Python
     */
    private function validatePythonSyntax(string $code): array
    {
        // التحقق من المسافات البادئة
        $lines = explode("\n", $code);
        $indentationValid = true;
        
        foreach ($lines as $line) {
            if (preg_match('/^\s+/', $line, $matches)) {
                $indent = strlen($matches[0]);
                if ($indent % 4 !== 0) {
                    $indentationValid = false;
                    break;
                }
            }
        }
        
        if (!$indentationValid) {
            return [
                'valid' => false,
                'message' => 'المسافات البادئة غير صحيحة (يجب أن تكون مضاعفات 4)'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'الكود يبدو صحيحاً'
        ];
    }
    
    /**
     * التحقق من صحة JavaScript
     */
    private function validateJavaScriptSyntax(string $code): array
    {
        // التحقق من الأقواس المتوازنة
        $openBraces = substr_count($code, '{');
        $closeBraces = substr_count($code, '}');
        $openParens = substr_count($code, '(');
        $closeParens = substr_count($code, ')');
        
        if ($openBraces !== $closeBraces || $openParens !== $closeParens) {
            return [
                'valid' => false,
                'message' => 'الأقواس غير متوازنة'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'الكود يبدو صحيحاً'
        ];
    }
    
    /**
     * التحقق من صحة Java
     */
    private function validateJavaSyntax(string $code): array
    {
        // التحقق من وجود class
        if (!preg_match('/class\s+\w+/', $code)) {
            return [
                'valid' => false,
                'message' => 'يجب أن يحتوي الكود على class'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'الكود يبدو صحيحاً'
        ];
    }
    
    /**
     * التحقق من صحة C#
     */
    private function validateCSharpSyntax(string $code): array
    {
        // التحقق من وجود namespace أو class
        if (!preg_match('/namespace\s+[\w.]+|class\s+\w+/', $code)) {
            return [
                'valid' => false,
                'message' => 'يجب أن يحتوي الكود على namespace أو class'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'الكود يبدو صحيحاً'
        ];
    }
}
