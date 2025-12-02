# 🧪 سيناريوهات اختبار الـ APIs العملية

**الإصدار:** 1.0  
**التاريخ:** 2025-12-02  
**الحالة:** جاهز للاختبار

---

## 📋 جدول المحتويات

1. [سيناريوهات Code Generator](#سيناريوهات-code-generator)
2. [سيناريوهات Helper Tools](#سيناريوهات-helper-tools)
3. [سيناريوهات النظام](#سيناريوهات-النظام)
4. [حالات الخطأ والاستثناءات](#حالات-الخطأ-والاستثناءات)
5. [اختبارات الأداء](#اختبارات-الأداء)

---

## 🎯 سيناريوهات Code Generator

### السيناريو 1: توليد CRUD كامل للمنتجات

**الوصف:** اختبار توليد CRUD كامل لنموذج المنتجات مع جميع الحقول

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/code-generator \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "description": "نموذج المنتجات يحتوي على اسم، وصف، سعر، فئة، صور، وحالة النشر. يجب أن يكون لديه علاقة مع الفئات والمستخدمين",
    "model_name": "Product",
    "fields": ["name", "description", "price", "category_id", "images", "is_published"],
    "auto_save": false
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `model_name`: "Product"
     - `code`: كود CRUD كامل
     - `components`: Model, Migration, Controller, Request, Resource
   - ✅ الأكواد تتبع معايير Laravel
   - ✅ وقت الاستجابة: < 10 ثواني

3. **التحقق:**
   ```php
   // تحقق من وجود جميع المكونات
   - Model class
   - Migration file
   - Controller with CRUD methods
   - Form Request validation
   - API Resource
   ```

---

### السيناريو 2: توليد Migration فقط

**الوصف:** اختبار توليد Migration لجدول معين

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/migration \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "table_name": "products",
    "description": "جدول المنتجات يحتوي على اسم، وصف، سعر، فئة، صور، وحالة النشر",
    "fields": ["name", "description", "price", "category_id", "images", "is_published"]
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `migration_code`: كود Migration صحيح
   - ✅ يحتوي على جميع الحقول المطلوبة
   - ✅ يحتوي على timestamps و soft deletes (إن أمكن)

3. **التحقق:**
   ```php
   // تحقق من الـ Migration
   - Schema::create('products', ...)
   - $table->id()
   - $table->string('name')
   - $table->text('description')
   - $table->decimal('price')
   - $table->foreignId('category_id')
   - $table->json('images')
   - $table->boolean('is_published')
   - $table->timestamps()
   ```

---

### السيناريو 3: توليد API Resource

**الوصف:** اختبار توليد API Resource للمنتجات

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/api-resource \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "model_name": "Product",
    "fields": ["id", "name", "description", "price", "category_id", "images", "is_published", "created_at", "updated_at"]
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `resource_code`: كود API Resource
   - ✅ يحتوي على جميع الحقول
   - ✅ يحتوي على relationships (إن أمكن)

3. **التحقق:**
   ```php
   // تحقق من الـ Resource
   - extends JsonResource
   - toArray() method
   - جميع الحقول موجودة
   ```

---

### السيناريو 4: توليد Unit Tests

**الوصف:** اختبار توليد Unit Tests للمنتجات

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/tests \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "model_name": "Product",
    "description": "اختبر جميع عمليات CRUD للمنتجات والتحقق من الـ Validation والعلاقات"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `tests_code`: كود الاختبارات
   - ✅ يحتوي على اختبارات Create, Read, Update, Delete
   - ✅ يحتوي على اختبارات Validation

3. **التحقق:**
   ```php
   // تحقق من الاختبارات
   - extends TestCase
   - test_create_product()
   - test_read_product()
   - test_update_product()
   - test_delete_product()
   - test_validation()
   ```

---

## 🛠️ سيناريوهات Helper Tools

### السيناريو 5: مراجعة الأكواد

**الوصف:** اختبار مراجعة أكواد PHP

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/code-review \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "code": "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass Product extends Model\n{\n    protected $fillable = [\"name\", \"description\", \"price\"];\n    \n    public function getDiscountedPrice()\n    {\n        return $this->price * 0.9;\n    }\n    \n    public function category()\n    {\n        return $this->belongsTo(Category::class);\n    }\n}",
    "language": "php"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `review`: تقرير شامل
   - ✅ التقرير يحتوي على:
     - نقاط القوة
     - نقاط الضعف
     - الاقتراحات
     - درجة الجودة

3. **التحقق:**
   ```
   - تقرير مفصل
   - نقاط قابلة للتحسين
   - أمثلة على التحسينات
   ```

---

### السيناريو 6: إصلاح الأخطاء

**الوصف:** اختبار إصلاح خطأ في الأكواد

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/bug-fixer \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "code": "<?php\n\npublic function getUserById($id)\n{\n    $user = User::find($id);\n    return $user->name;\n}",
    "error_message": "Call to a member function name() on null",
    "language": "php"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `fixed_code`: الكود المصحح
     - `explanation`: شرح الخطأ والحل
   - ✅ الكود المصحح يعالج الخطأ

3. **التحقق:**
   ```php
   // الكود المصحح يجب أن يحتوي على:
   - null check
   - try-catch
   - default value
   - أو أي حل آخر مناسب
   ```

---

### السيناريو 7: توليد الاختبارات

**الوصف:** اختبار توليد اختبارات لخدمة الدفع

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/test-generator \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "code": "<?php\n\nnamespace App\\Services;\n\nclass PaymentService\n{\n    public function processPayment($amount, $card)\n    {\n        if ($amount <= 0) {\n            throw new Exception(\"Invalid amount\");\n        }\n        // Process payment logic\n        return true;\n    }\n}",
    "description": "اختبر معالجة الدفع مع حالات مختلفة من المبالغ والبطاقات"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `tests`: كود الاختبارات
   - ✅ يحتوي على اختبارات:
     - حالات صحيحة
     - حالات خطأ
     - حدود القيم

3. **التحقق:**
   ```php
   // الاختبارات يجب أن تغطي:
   - test_process_payment_with_valid_amount()
   - test_process_payment_with_invalid_amount()
   - test_process_payment_with_zero_amount()
   - test_process_payment_with_negative_amount()
   ```

---

### السيناريو 8: توليد التوثيق

**الوصف:** اختبار توليد توثيق للدالة

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/documentation \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "code": "<?php\n\npublic function calculateDiscount($price, $percentage)\n{\n    if ($percentage < 0 || $percentage > 100) {\n        throw new InvalidArgumentException(\"Percentage must be between 0 and 100\");\n    }\n    return $price * (1 - $percentage / 100);\n}",
    "language": "php"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ Response يحتوي على:
     - `success`: true
     - `documentation`: التوثيق الكامل
   - ✅ يحتوي على:
     - وصف الدالة
     - المعاملات
     - القيمة المرجعة
     - الاستثناءات
     - أمثلة

3. **التحقق:**
   ```
   - توثيق واضح ومفصل
   - أمثلة على الاستخدام
   - شرح المعاملات
   - شرح القيمة المرجعة
   ```

---

## 🖥️ سيناريوهات النظام

### السيناريو 9: عرض حالة النظام

**الوصف:** اختبار عرض صفحة حالة النظام

**الخطوات:**

1. **الطلب:**
```bash
curl -X GET http://localhost:8000/system-status
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ الصفحة تحتوي على:
     - حالة النظام الإجمالية
     - المقاييس الرئيسية (Uptime, Response Time, etc.)
     - حالة الخدمات
     - الرسوم البيانية
     - التنبيهات الأخيرة

3. **التحقق:**
   ```
   - الصفحة تحميل بنجاح
   - جميع البيانات موجودة
   - الرسوم البيانية تظهر بشكل صحيح
   - التنبيهات محدثة
   ```

---

### السيناريو 10: عرض الإحصائيات

**الوصف:** اختبار عرض صفحة الإحصائيات والتقارير

**الخطوات:**

1. **الطلب:**
```bash
curl -X GET http://localhost:8000/system-analytics
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 200
   - ✅ الصفحة تحتوي على:
     - مرشحات التاريخ
     - الإحصائيات الرئيسية
     - الرسوم البيانية
     - قائمة الصفحات الأكثر زيارة
     - خيارات التصدير

3. **التحقق:**
   ```
   - الصفحة تحميل بنجاح
   - المرشحات تعمل بشكل صحيح
   - الرسوم البيانية تحدث عند تغيير المرشح
   - خيارات التصدير متاحة
   ```

---

## ❌ حالات الخطأ والاستثناءات

### السيناريو 11: طلب بدون CSRF Token

**الوصف:** اختبار الطلب بدون CSRF Token

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/code-generator \
  -H "Content-Type: application/json" \
  -d '{
    "description": "نموذج المنتجات",
    "model_name": "Product"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 419 (Token Mismatch) أو 200 (إذا كان API)
   - ✅ رسالة خطأ واضحة

---

### السيناريو 12: طلب بدون بيانات مطلوبة

**الوصف:** اختبار الطلب بدون البيانات المطلوبة

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/code-generator \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "model_name": "Product"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 422 (Unprocessable Entity)
   - ✅ رسالة خطأ تحدد الحقول المفقودة

---

### السيناريو 13: طلب ببيانات غير صحيحة

**الوصف:** اختبار الطلب ببيانات غير صحيحة

**الخطوات:**

1. **الطلب:**
```bash
curl -X POST http://localhost:8000/developer/ai/code-generator \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "description": "نموذج المنتجات",
    "model_name": "product"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 422
   - ✅ رسالة خطأ تحدد الحقل الخاطئ

---

### السيناريو 14: خطأ API OpenAI

**الوصف:** اختبار التعامل مع خطأ API OpenAI

**الخطوات:**

1. **الطلب:**
```bash
# استخدم API Key غير صحيح
curl -X POST http://localhost:8000/developer/ai/code-generator \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{
    "description": "نموذج المنتجات",
    "model_name": "Product"
  }'
```

2. **النتائج المتوقعة:**
   - ✅ Status Code: 500
   - ✅ رسالة خطأ واضحة
   - ✅ الخطأ مسجل في الـ Logs

---

## ⚡ اختبارات الأداء

### السيناريو 15: اختبار الأداء - وقت الاستجابة

**الوصف:** اختبار وقت استجابة الـ APIs

**الخطوات:**

1. **قياس الأداء:**
```bash
# استخدم Apache Bench
ab -n 10 -c 1 http://localhost:8000/developer/ai/code-generator

# أو استخدم curl مع قياس الوقت
time curl -X POST http://localhost:8000/developer/ai/code-generator \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {{csrf_token}}" \
  -d '{...}'
```

2. **النتائج المتوقعة:**
   - ✅ وقت الاستجابة: < 10 ثواني
   - ✅ معدل النجاح: > 95%
   - ✅ استهلاك الذاكرة: معقول

---

### السيناريو 16: اختبار الحمل - طلبات متزامنة

**الوصف:** اختبار الـ APIs تحت حمل عالي

**الخطوات:**

1. **قياس الحمل:**
```bash
# استخدم Apache Bench مع طلبات متزامنة
ab -n 100 -c 10 http://localhost:8000/system-status

# أو استخدم wrk
wrk -t4 -c100 -d30s http://localhost:8000/system-status
```

2. **النتائج المتوقعة:**
   - ✅ معدل النجاح: > 90%
   - ✅ وقت الاستجابة: معقول
   - ✅ لا توجد أخطاء 500

---

## 📊 جدول ملخص الاختبارات

| # | السيناريو | الـ API | الحالة | النتيجة المتوقعة |
|---|---------|--------|--------|------------------|
| 1 | توليد CRUD | POST /code-generator | ✅ | 200 + Code |
| 2 | توليد Migration | POST /migration | ✅ | 200 + Migration |
| 3 | توليد API Resource | POST /api-resource | ✅ | 200 + Resource |
| 4 | توليد Tests | POST /tests | ✅ | 200 + Tests |
| 5 | مراجعة الأكواد | POST /code-review | ✅ | 200 + Review |
| 6 | إصلاح الأخطاء | POST /bug-fixer | ✅ | 200 + Fixed Code |
| 7 | توليد الاختبارات | POST /test-generator | ✅ | 200 + Tests |
| 8 | توليد التوثيق | POST /documentation | ✅ | 200 + Docs |
| 9 | حالة النظام | GET /system-status | ✅ | 200 + Page |
| 10 | الإحصائيات | GET /system-analytics | ✅ | 200 + Page |
| 11 | بدون CSRF | POST /code-generator | ✅ | 419/200 |
| 12 | بدون بيانات | POST /code-generator | ✅ | 422 |
| 13 | بيانات خاطئة | POST /code-generator | ✅ | 422 |
| 14 | خطأ API | POST /code-generator | ✅ | 500 |
| 15 | الأداء | All APIs | ✅ | < 10s |
| 16 | الحمل | All APIs | ✅ | > 90% |

---

## ✅ قائمة التحقق

- [ ] تم اختبار جميع السيناريوهات
- [ ] جميع النتائج مطابقة للمتوقع
- [ ] تم توثيق أي مشاكل
- [ ] تم إصلاح جميع المشاكل
- [ ] تم اختبار الأداء
- [ ] تم اختبار الحمل
- [ ] تم اختبار معالجة الأخطاء
- [ ] تم توثيق النتائج

---

**تاريخ الاختبار:** _______________  
**المختبر:** _______________  
**الملاحظات:** _______________

---

*شكراً لاستخدامك نظام المطور المتكامل!* 🚀
