<?php

namespace App\Services\AI;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة أداة إعادة الهيكلة - إعادة هيكلة الكود تلقائياً
 * 
 * تستخدم Manus AI لتحليل الكود واقتراح تحسينات هيكلية وتطبيقها بشكل آمن
 */
class RefactoringToolService
{
    private $apiKey;
    private $apiUrl = 'https://api.manus.ai/v1/tasks';
    
    public function __construct()
    {
        $this->apiKey = AiSetting::where('key', 'manus_api_key')->value('value');
    }
    
    /**
     * تحليل بنية الكود
     */
    public function analyzeStructure(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildStructureAnalysisPrompt($code, $language);
            
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
                return $this->parseStructureAnalysisResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * اقتراح تحسينات إعادة الهيكلة
     */
    public function suggestRefactorings(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildRefactoringSuggestionsPrompt($code, $language);
            
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
                return $this->parseRefactoringSuggestionsResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * تطبيق إعادة الهيكلة
     */
    public function applyRefactoring(string $code, array $refactoring, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildApplyRefactoringPrompt($code, $refactoring, $language);
            
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
                return $this->parseApplyRefactoringResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * معاينة التغييرات
     */
    public function previewChanges(string $code, array $refactoring, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildPreviewChangesPrompt($code, $refactoring, $language);
            
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
                return [
                    'success' => true,
                    'preview' => $this->extractTextFromResponse($data),
                    'task_id' => $data['id'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * كشف Code Smells
     */
    public function detectCodeSmells(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildCodeSmellsPrompt($code, $language);
            
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
                return $this->parseCodeSmellsResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * استخراج Method
     */
    public function extractMethod(string $code, array $params): array
    {
        try {
            $prompt = "أنت خبير في إعادة هيكلة الكود. قم بتطبيق Extract Method refactoring:\n\n**الكود الأصلي:**\n```php\n{$code}\n```\n\n**المعلمات:**\n" . json_encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n**المطلوب:**\n1. استخراج الكود المحدد إلى method منفصلة\n2. إعطاء اسم مناسب للـ method\n3. تحديد المعاملات المطلوبة\n4. استبدال الكود الأصلي باستدعاء الـ method الجديدة\n\n**الرد:**\nالكود المحسّن كاملاً.";
            
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
                return $this->parseApplyRefactoringResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * استخراج Class
     */
    public function extractClass(string $code, array $params): array
    {
        try {
            $prompt = "أنت خبير في إعادة هيكلة الكود. قم بتطبيق Extract Class refactoring:\n\n**الكود الأصلي:**\n```php\n{$code}\n```\n\n**المعلمات:**\n" . json_encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n**المطلوب:**\n1. استخراج الوظائف المحددة إلى class منفصلة\n2. إعطاء اسم مناسب للـ class\n3. تحديد المسؤوليات\n4. تحديث الكود الأصلي لاستخدام الـ class الجديدة\n\n**الرد:**\nالكود المحسّن كاملاً مع الـ class الجديدة.";
            
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
                return $this->parseApplyRefactoringResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * إعادة تسمية Symbol
     */
    public function renameSymbol(string $code, string $oldName, string $newName, string $type = 'variable'): array
    {
        try {
            $prompt = "أنت خبير في إعادة هيكلة الكود. قم بإعادة تسمية {$type}:\n\n**الكود الأصلي:**\n```php\n{$code}\n```\n\n**المطلوب:**\nإعادة تسمية {$type} من '{$oldName}' إلى '{$newName}' في جميع الأماكن.\n\n**الرد:**\nالكود المحسّن كاملاً.";
            
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
                return $this->parseApplyRefactoringResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * حذف الكود الميت
     */
    public function removeDeadCode(string $code): array
    {
        try {
            $prompt = "أنت خبير في إعادة هيكلة الكود. قم بحذف الكود الميت (Dead Code):\n\n**الكود الأصلي:**\n```php\n{$code}\n```\n\n**المطلوب:**\n1. تحديد الكود غير المستخدم\n2. حذف المتغيرات غير المستخدمة\n3. حذف الدوال غير المستدعاة\n4. حذف الـ imports غير المستخدمة\n5. حذف التعليقات القديمة\n\n**الرد:**\nالكود المحسّن كاملاً بدون كود ميت.";
            
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
                return $this->parseApplyRefactoringResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * تبسيط الشروط
     */
    public function simplifyConditionals(string $code): array
    {
        try {
            $prompt = "أنت خبير في إعادة هيكلة الكود. قم بتبسيط الشروط (Simplify Conditionals):\n\n**الكود الأصلي:**\n```php\n{$code}\n```\n\n**المطلوب:**\n1. تبسيط الشروط المعقدة\n2. دمج الشروط المتشابهة\n3. استخدام Early Return\n4. استبدال الشروط المتداخلة\n5. استخدام Guard Clauses\n\n**الرد:**\nالكود المحسّن كاملاً مع شروط مبسطة.";
            
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
                return $this->parseApplyRefactoringResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * بناء prompt لتحليل البنية
     */
    private function buildStructureAnalysisPrompt(string $code, string $language): string
    {
        return "أنت خبير في تحليل بنية الكود. قم بتحليل هذا الكود:\n\n```{$language}\n{$code}\n```\n\n**المطلوب:**\n1. تحديد المشاكل الهيكلية\n2. اكتشاف Code Smells\n3. تحديد Anti-patterns\n4. اقتراح تحسينات هيكلية\n5. تقييم التعقيد والصيانة\n\n**الرد بصيغة JSON:**\n```json\n{\n  \"structure_issues\": [\n    {\"type\": \"نوع المشكلة\", \"line\": رقم السطر, \"description\": \"الوصف\", \"severity\": \"high|medium|low\"}\n  ],\n  \"code_smells\": [\n    {\"smell\": \"اسم الـ Smell\", \"location\": \"الموقع\", \"description\": \"الوصف\", \"suggestion\": \"الاقتراح\"}\n  ],\n  \"anti_patterns\": [\n    {\"pattern\": \"اسم الـ Pattern\", \"location\": \"الموقع\", \"description\": \"الوصف\", \"solution\": \"الحل\"}\n  ],\n  \"suggestions\": [\n    {\"category\": \"الفئة\", \"description\": \"الاقتراح\", \"impact\": \"high|medium|low\", \"effort\": \"high|medium|low\"}\n  ],\n  \"complexity_score\": 0-10,\n  \"maintainability_score\": 0-10,\n  \"overall_health\": \"excellent|good|fair|poor\"\n}\n```";
    }
    
    /**
     * بناء prompt لاقتراحات إعادة الهيكلة
     */
    private function buildRefactoringSuggestionsPrompt(string $code, string $language): string
    {
        return "أنت خبير في إعادة هيكلة الكود. قم باقتراح تحسينات لهذا الكود:\n\n```{$language}\n{$code}\n```\n\n**اقترح:**\n1. Extract Method opportunities\n2. Extract Class opportunities\n3. Rename suggestions (variables, methods, classes)\n4. Dead code removal\n5. Conditional simplification\n6. Move Method opportunities\n7. Inline Method opportunities\n8. Replace Conditional with Polymorphism\n\n**الرد بصيغة JSON:**\n```json\n{\n  \"refactorings\": [\n    {\n      \"id\": \"unique_id\",\n      \"type\": \"extract_method|extract_class|rename|remove_dead_code|simplify_conditional|move_method|inline_method\",\n      \"title\": \"عنوان التحسين\",\n      \"description\": \"وصف مفصل\",\n      \"location\": {\"start_line\": رقم, \"end_line\": رقم},\n      \"suggested_name\": \"الاسم المقترح (إن وجد)\",\n      \"impact\": \"high|medium|low\",\n      \"effort\": \"high|medium|low\",\n      \"benefits\": [\"فائدة 1\", \"فائدة 2\"],\n      \"risks\": [\"خطر 1\", \"خطر 2\"]\n    }\n  ],\n  \"priority_order\": [\"id1\", \"id2\", \"id3\"],\n  \"estimated_improvement\": \"نسبة التحسين المتوقعة\"\n}\n```";
    }
    
    /**
     * بناء prompt لتطبيق إعادة الهيكلة
     */
    private function buildApplyRefactoringPrompt(string $code, array $refactoring, string $language): string
    {
        $refactoringDetails = json_encode($refactoring, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return "أنت خبير في تطبيق إعادة الهيكلة. قم بتطبيق هذا التحسين:\n\n**الكود الأصلي:**\n```{$language}\n{$code}\n```\n\n**التحسين المطلوب:**\n```json\n{$refactoringDetails}\n```\n\n**المطلوب:**\n1. تطبيق التحسين بدقة\n2. التأكد من عدم كسر الوظائف\n3. الحفاظ على السلوك الأصلي\n4. تحسين القراءة والصيانة\n5. إضافة تعليقات إذا لزم الأمر\n\n**الرد:**\nالكود المحسّن كاملاً مع شرح التغييرات.";
    }
    
    /**
     * بناء prompt لمعاينة التغييرات
     */
    private function buildPreviewChangesPrompt(string $code, array $refactoring, string $language): string
    {
        $refactoringDetails = json_encode($refactoring, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return "أنت خبير في إعادة هيكلة الكود. قم بمعاينة التغييرات لهذا التحسين:\n\n**الكود الأصلي:**\n```{$language}\n{$code}\n```\n\n**التحسين المطلوب:**\n```json\n{$refactoringDetails}\n```\n\n**المطلوب:**\n1. عرض الكود قبل وبعد التحسين\n2. تحديد الأسطر المتغيرة\n3. شرح التغييرات\n4. تحديد الفوائد والمخاطر\n5. اقتراح اختبارات للتحقق\n\n**الرد:**\nمعاينة مفصلة للتغييرات مع مقارنة Before/After.";
    }
    
    /**
     * بناء prompt لكشف Code Smells
     */
    private function buildCodeSmellsPrompt(string $code, string $language): string
    {
        return "أنت خبير في كشف Code Smells. قم بفحص هذا الكود:\n\n```{$language}\n{$code}\n```\n\n**ابحث عن:**\n1. Long Method\n2. Large Class\n3. Long Parameter List\n4. Duplicate Code\n5. Dead Code\n6. Speculative Generality\n7. Feature Envy\n8. Data Clumps\n9. Primitive Obsession\n10. Switch Statements\n11. Lazy Class\n12. Shotgun Surgery\n13. Divergent Change\n14. Parallel Inheritance Hierarchies\n\n**الرد بصيغة JSON:**\n```json\n{\n  \"code_smells\": [\n    {\n      \"smell\": \"اسم الـ Smell\",\n      \"severity\": \"high|medium|low\",\n      \"location\": {\"start_line\": رقم, \"end_line\": رقم},\n      \"description\": \"وصف المشكلة\",\n      \"impact\": \"التأثير على الكود\",\n      \"refactoring\": \"التحسين المقترح\",\n      \"example\": \"مثال على الحل\"\n    }\n  ],\n  \"total_smells\": عدد,\n  \"critical_smells\": عدد,\n  \"code_health\": \"excellent|good|fair|poor\"\n}\n```";
    }
    
    /**
     * تحليل رد تحليل البنية
     */
    private function parseStructureAnalysisResponse(array $data): array
    {
        $text = $this->extractTextFromResponse($data);
        
        // محاولة استخراج JSON من الرد
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json) {
                return [
                    'success' => true,
                    'analysis' => $json,
                    'task_id' => $data['id'] ?? null
                ];
            }
        }
        
        // إذا لم يكن JSON، أرجع النص كما هو
        return [
            'success' => true,
            'analysis' => $text,
            'task_id' => $data['id'] ?? null
        ];
    }
    
    /**
     * تحليل رد اقتراحات إعادة الهيكلة
     */
    private function parseRefactoringSuggestionsResponse(array $data): array
    {
        $text = $this->extractTextFromResponse($data);
        
        // محاولة استخراج JSON من الرد
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json) {
                return [
                    'success' => true,
                    'suggestions' => $json,
                    'task_id' => $data['id'] ?? null
                ];
            }
        }
        
        return [
            'success' => true,
            'suggestions' => $text,
            'task_id' => $data['id'] ?? null
        ];
    }
    
    /**
     * تحليل رد تطبيق إعادة الهيكلة
     */
    private function parseApplyRefactoringResponse(array $data): array
    {
        $text = $this->extractTextFromResponse($data);
        
        // استخراج الكود المحسّن
        if (preg_match('/```(?:php|javascript|python|java)?\s*(.*?)\s*```/s', $text, $matches)) {
            return [
                'success' => true,
                'refactored_code' => trim($matches[1]),
                'explanation' => $text,
                'task_id' => $data['id'] ?? null
            ];
        }
        
        return [
            'success' => true,
            'refactored_code' => $text,
            'explanation' => $text,
            'task_id' => $data['id'] ?? null
        ];
    }
    
    /**
     * تحليل رد كشف Code Smells
     */
    private function parseCodeSmellsResponse(array $data): array
    {
        $text = $this->extractTextFromResponse($data);
        
        // محاولة استخراج JSON من الرد
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json) {
                return [
                    'success' => true,
                    'smells' => $json,
                    'task_id' => $data['id'] ?? null
                ];
            }
        }
        
        return [
            'success' => true,
            'smells' => $text,
            'task_id' => $data['id'] ?? null
        ];
    }
    
    /**
     * استخراج النص من رد Manus
     */
    private function extractTextFromResponse(array $data): string
    {
        if (isset($data['output']) && is_array($data['output'])) {
            $texts = [];
            foreach ($data['output'] as $item) {
                if (isset($item['type']) && $item['type'] === 'text' && isset($item['text'])) {
                    $texts[] = $item['text'];
                }
            }
            return implode("\n\n", $texts);
        }
        
        return '';
    }
}
