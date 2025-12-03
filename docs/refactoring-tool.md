# Refactoring Tool - أداة إعادة الهيكلة الذكية

## نظرة عامة

أداة إعادة الهيكلة الذكية (Refactoring Tool) هي أداة متقدمة مدعومة بالذكاء الاصطناعي لإعادة هيكلة الكود البرمجي تلقائياً. تقوم الأداة بتحليل الكود، اكتشاف المشاكل الهيكلية، اقتراح تحسينات، وتطبيقها بشكل آمن.

## المميزات الرئيسية

### 1. تحليل البنية (Structure Analysis)
- فحص شامل لبنية الكود
- تحديد المشاكل الهيكلية
- اكتشاف Anti-patterns
- تقييم التعقيد والصيانة

### 2. كشف Code Smells
- Long Method
- Large Class
- Long Parameter List
- Duplicate Code
- Dead Code
- Feature Envy
- Data Clumps
- Primitive Obsession
- Switch Statements
- وأكثر من 14 نوع من Code Smells

### 3. اقتراحات إعادة الهيكلة
- Extract Method
- Extract Class
- Rename Variable/Method/Class
- Move Method
- Inline Method
- Replace Conditional with Polymorphism
- Remove Dead Code
- Simplify Conditional Expressions

### 4. تطبيق التحسينات
- تطبيق تلقائي للتحسينات
- معاينة التغييرات قبل التطبيق
- حفظ نسخة احتياطية
- إمكانية التراجع

## دليل الاستخدام

### الوصول إلى الأداة

```
URL: /ai-tools/refactoring-tool
```

### واجهة المستخدم

#### 1. محرر الكود
- محرر كبير لإدخال الكود
- دعم متعدد اللغات (PHP, JavaScript, Python, Java, TypeScript, Go, Rust, Ruby)
- إمكانية نسخ ولصق الكود

#### 2. أدوات التحكم
- **تحليل البنية**: تحليل شامل لبنية الكود
- **اقتراح التحسينات**: الحصول على اقتراحات للتحسين
- **كشف Code Smells**: اكتشاف المشاكل الشائعة
- **حذف الكود الميت**: إزالة الكود غير المستخدم
- **تبسيط الشروط**: تبسيط الشروط المعقدة
- **معاينة التغييرات**: عرض التغييرات قبل التطبيق

#### 3. منطقة النتائج
- عرض نتائج التحليل
- عرض الاقتراحات
- عرض Code Smells المكتشفة
- عرض الكود المحسّن

## API Reference

### 1. تحليل البنية

**Endpoint:** `POST /api/developer/ai/refactoring-tool/analyze`

**Request Body:**
```json
{
  "code": "string",
  "language": "php|javascript|python|java|typescript|go|rust|ruby"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحليل البنية بنجاح",
  "data": {
    "analysis": {
      "structure_issues": [...],
      "code_smells": [...],
      "anti_patterns": [...],
      "suggestions": [...],
      "complexity_score": 7,
      "maintainability_score": 6,
      "overall_health": "fair"
    },
    "task_id": "xxx"
  }
}
```

### 2. اقتراح التحسينات

**Endpoint:** `POST /api/developer/ai/refactoring-tool/suggest`

**Request Body:**
```json
{
  "code": "string",
  "language": "php"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم اقتراح التحسينات بنجاح",
  "data": {
    "suggestions": {
      "refactorings": [
        {
          "id": "ref_001",
          "type": "extract_method",
          "title": "استخراج دالة calculateTotal",
          "description": "...",
          "location": {"start_line": 10, "end_line": 25},
          "impact": "high",
          "effort": "medium",
          "benefits": [...],
          "risks": [...]
        }
      ],
      "priority_order": ["ref_001", "ref_002"],
      "estimated_improvement": "30%"
    }
  }
}
```

### 3. تطبيق التحسين

**Endpoint:** `POST /api/developer/ai/refactoring-tool/apply`

**Request Body:**
```json
{
  "code": "string",
  "refactoring": {
    "id": "ref_001",
    "type": "extract_method",
    ...
  },
  "language": "php"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تطبيق التحسين بنجاح",
  "data": {
    "refactored_code": "...",
    "explanation": "...",
    "task_id": "xxx"
  }
}
```

### 4. معاينة التغييرات

**Endpoint:** `POST /api/developer/ai/refactoring-tool/preview`

**Request Body:**
```json
{
  "code": "string",
  "refactoring": {...},
  "language": "php"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم معاينة التغييرات بنجاح",
  "data": {
    "preview": "Before/After comparison...",
    "task_id": "xxx"
  }
}
```

### 5. كشف Code Smells

**Endpoint:** `POST /api/developer/ai/refactoring-tool/detect-smells`

**Request Body:**
```json
{
  "code": "string",
  "language": "php"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم كشف Code Smells بنجاح",
  "data": {
    "smells": {
      "code_smells": [
        {
          "smell": "Long Method",
          "severity": "high",
          "location": {"start_line": 10, "end_line": 50},
          "description": "...",
          "impact": "...",
          "refactoring": "Extract Method",
          "example": "..."
        }
      ],
      "total_smells": 5,
      "critical_smells": 2,
      "code_health": "fair"
    }
  }
}
```

### 6. حذف الكود الميت

**Endpoint:** `POST /api/developer/ai/refactoring-tool/remove-dead-code`

**Request Body:**
```json
{
  "code": "string"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم حذف الكود الميت بنجاح",
  "data": {
    "refactored_code": "...",
    "explanation": "...",
    "task_id": "xxx"
  }
}
```

### 7. تبسيط الشروط

**Endpoint:** `POST /api/developer/ai/refactoring-tool/simplify-conditionals`

**Request Body:**
```json
{
  "code": "string"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تبسيط الشروط بنجاح",
  "data": {
    "refactored_code": "...",
    "explanation": "...",
    "task_id": "xxx"
  }
}
```

## أمثلة عملية

### مثال 1: تحليل كود PHP

**الكود الأصلي:**
```php
class UserManager {
    public function processUser($userId) {
        $user = DB::table('users')->where('id', $userId)->first();
        
        if ($user) {
            if ($user->status == 'active') {
                if ($user->email_verified) {
                    $orders = DB::table('orders')->where('user_id', $userId)->get();
                    $total = 0;
                    foreach ($orders as $order) {
                        $total += $order->amount;
                    }
                    
                    if ($total > 1000) {
                        $discount = $total * 0.1;
                        $finalAmount = $total - $discount;
                        return $finalAmount;
                    } else {
                        return $total;
                    }
                }
            }
        }
        return 0;
    }
}
```

**المشاكل المكتشفة:**
1. Long Method
2. Nested Conditionals (3 مستويات)
3. Magic Numbers (1000, 0.1)
4. Missing Early Returns
5. No separation of concerns

**الكود المحسّن:**
```php
class UserManager {
    private const DISCOUNT_THRESHOLD = 1000;
    private const DISCOUNT_RATE = 0.1;
    
    public function processUser($userId) {
        $user = $this->getActiveVerifiedUser($userId);
        
        if (!$user) {
            return 0;
        }
        
        $total = $this->calculateUserOrdersTotal($userId);
        return $this->applyDiscount($total);
    }
    
    private function getActiveVerifiedUser($userId) {
        $user = DB::table('users')->where('id', $userId)->first();
        
        if (!$user || $user->status !== 'active' || !$user->email_verified) {
            return null;
        }
        
        return $user;
    }
    
    private function calculateUserOrdersTotal($userId) {
        return DB::table('orders')
            ->where('user_id', $userId)
            ->sum('amount');
    }
    
    private function applyDiscount($total) {
        if ($total <= self::DISCOUNT_THRESHOLD) {
            return $total;
        }
        
        $discount = $total * self::DISCOUNT_RATE;
        return $total - $discount;
    }
}
```

**التحسينات المطبقة:**
1. ✅ Extract Method (3 methods)
2. ✅ Early Return Pattern
3. ✅ Replace Magic Numbers with Constants
4. ✅ Simplified Conditionals
5. ✅ Single Responsibility Principle

### مثال 2: كشف Code Smells

**الكود:**
```php
class ReportGenerator {
    public function generateReport($data, $type, $format, $filters, $options) {
        // Long Parameter List
        // ...
    }
    
    private $unusedVariable; // Dead Code
    
    public function oldMethod() {
        // Dead Code - never called
    }
}
```

**Code Smells المكتشفة:**
1. **Long Parameter List** (5 parameters)
   - Severity: Medium
   - Suggestion: Use Parameter Object pattern

2. **Dead Code** (unusedVariable)
   - Severity: Low
   - Suggestion: Remove unused variable

3. **Dead Code** (oldMethod)
   - Severity: Low
   - Suggestion: Remove unused method

## أفضل الممارسات

### 1. قبل إعادة الهيكلة
- ✅ تأكد من وجود اختبارات (Tests)
- ✅ احفظ نسخة احتياطية من الكود
- ✅ افهم الكود جيداً قبل التعديل
- ✅ راجع التبعيات (Dependencies)

### 2. أثناء إعادة الهيكلة
- ✅ طبق تحسين واحد في كل مرة
- ✅ اختبر بعد كل تحسين
- ✅ استخدم معاينة التغييرات
- ✅ راجع الكود المحسّن

### 3. بعد إعادة الهيكلة
- ✅ شغل جميع الاختبارات
- ✅ راجع الأداء
- ✅ وثق التغييرات
- ✅ راجع مع الفريق

## الأسئلة الشائعة

### س: هل الأداة تدعم جميع لغات البرمجة؟
ج: حالياً تدعم الأداة: PHP, JavaScript, Python, Java, TypeScript, Go, Rust, Ruby. يمكن إضافة لغات أخرى مستقبلاً.

### س: هل التحسينات آمنة؟
ج: الأداة تستخدم الذكاء الاصطناعي لضمان عدم كسر الوظائف، لكن يُنصح دائماً بمراجعة التغييرات واختبارها.

### س: هل يمكن التراجع عن التحسينات؟
ج: نعم، يمكنك دائماً الرجوع إلى الكود الأصلي. يُنصح بحفظ نسخة احتياطية.

### س: كم من الوقت يستغرق التحليل؟
ج: عادة أقل من 5 ثواني للملفات الصغيرة، وحتى 30 ثانية للملفات الكبيرة (10,000+ سطر).

### س: هل الأداة تحتاج إلى API Key؟
ج: نعم، تحتاج إلى Manus AI API Key. يمكن إعدادها في إعدادات النظام.

## الدعم الفني

للمساعدة أو الإبلاغ عن مشاكل:
- البريد الإلكتروني: support@semop.com
- التوثيق: https://docs.semop.com/refactoring-tool
- GitHub: https://github.com/alabasi2025/php-magic-system

## الإصدار

**الإصدار الحالي:** v3.19.0  
**تاريخ الإصدار:** 2025-12-03  
**المهمة:** 11/100

---

**ملاحظة:** هذه الأداة مدعومة بالذكاء الاصطناعي (Manus AI) وتتحسن باستمرار.
