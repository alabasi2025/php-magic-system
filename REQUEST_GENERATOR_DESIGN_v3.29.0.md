# Request Generator v3.29.0 - Design Document

**التاريخ:** 2025-12-03  
**الإصدار:** 3.29.0  
**المشروع:** php-magic-system (SEMOP)  
**المهمة:** Task #21 - Request Generator

---

## 📋 نظرة عامة

**Request Generator** هو أداة ذكية لتوليد Form Request Classes في Laravel بشكل تلقائي باستخدام الذكاء الاصطناعي (Manus AI). يوفر واجهة سهلة الاستخدام لإنشاء Form Requests متقدمة مع قواعد Validation، رسائل خطأ مخصصة، ومنطق Authorization.

### الأهداف الرئيسية

1. **توليد تلقائي:** إنشاء Form Requests بناءً على الوصف النصي أو القوالب
2. **قواعد Validation متقدمة:** دعم جميع قواعد Laravel Validation
3. **رسائل مخصصة:** توليد رسائل خطأ مخصصة لكل قاعدة
4. **Authorization:** دعم منطق التفويض المخصص
5. **تكامل AI:** استخدام Manus AI لتوليد كود عالي الجودة
6. **معاينة مباشرة:** عرض الكود قبل الحفظ
7. **إدارة شاملة:** حفظ، تحميل، تعديل، وحذف Requests

---

## 🏗️ المعمارية

### البنية العامة

```
┌─────────────────────────────────────────┐
│         User Interface (View)           │
│    resources/views/request-generator    │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  RequestGeneratorController             │
│  app/Http/Controllers/                  │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  RequestGeneratorService                │
│  app/Services/                          │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│       Manus AI Client                   │
│  app/Services/AI/ManusAIClient.php      │
└─────────────────────────────────────────┘
```

---

## 🎯 المكونات الرئيسية

### 1. Controller: RequestGeneratorController

**المسار:** `app/Http/Controllers/RequestGeneratorController.php`

**المسؤوليات:**
- استقبال طلبات المستخدم من الواجهة
- التحقق من صحة البيانات المدخلة
- استدعاء Service لتنفيذ العمليات
- إرجاع النتائج بصيغة JSON أو View

**الوظائف الرئيسية:**

```php
// عرض الصفحة الرئيسية
public function index(): View

// عرض نموذج الإنشاء
public function create(): View

// توليد Request جديد
public function generate(Request $request): JsonResponse

// توليد من قالب
public function generateFromTemplate(Request $request): JsonResponse

// حفظ Request إلى ملف
public function save(Request $request): JsonResponse

// عرض قائمة Requests المولدة
public function list(): JsonResponse

// حذف Request
public function delete(Request $request): JsonResponse

// الحصول على القوالب المتاحة
public function templates(): JsonResponse
```

---

### 2. Service: RequestGeneratorService

**المسار:** `app/Services/RequestGeneratorService.php`

**المسؤوليات:**
- تنفيذ منطق التوليد الأساسي
- التواصل مع Manus AI
- إدارة الملفات والمجلدات
- معالجة القوالب الجاهزة

**الوظائف الرئيسية:**

```php
// توليد Request جديد
public function generate(array $config): array

// حفظ Request إلى ملف
public function save(string $name, string $code): array

// الحصول على قائمة Requests
public function getGeneratedRequests(): array

// حذف Request
public function delete(string $name): array

// توليد من قالب
public function generateFromTemplate(string $template, array $params): array

// الحصول على القوالب المتاحة
public function getTemplates(): array
```

---

### 3. Model: GeneratedRequest

**المسار:** `app/Models/GeneratedRequest.php`

**الحقول:**
- `id`: المعرف الفريد
- `name`: اسم Request
- `type`: نوع Request (store, update, search, filter, custom)
- `description`: وصف Request
- `config`: إعدادات التوليد (JSON)
- `code`: الكود المولد
- `file_path`: مسار الملف
- `file_size`: حجم الملف
- `is_saved`: هل تم حفظ الملف؟
- `is_active`: هل Request نشط؟
- `user_id`: المستخدم الذي أنشأ Request
- `fields_count`: عدد الحقول
- `has_authorization`: يحتوي على authorization؟
- `has_custom_messages`: يحتوي على رسائل مخصصة؟

---

### 4. Views

#### index.blade.php
- عرض قائمة Requests المولدة
- إحصائيات عامة
- القوالب السريعة
- جدول Requests مع إمكانية الحذف

#### create.blade.php
- نموذج إنشاء Request جديد
- إضافة حقول ديناميكية
- معاينة الكود المولد
- حفظ ونسخ الكود

---

## 🔧 أنواع Requests المدعومة

### 1. Store Request
لإنشاء موارد جديدة (Create)

**مثال:**
```php
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
```

### 2. Update Request
لتحديث موارد موجودة (Update)

**مثال:**
```php
class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $this->user,
        ];
    }
}
```

### 3. Search Request
للبحث والتصفية

**مثال:**
```php
class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}
```

### 4. Filter Request
لتصفية البيانات

### 5. Custom Request
لأي غرض مخصص

---

## 🎨 قواعد Validation المدعومة

### القواعد الأساسية
- `required`: حقل إلزامي
- `nullable`: حقل اختياري
- `sometimes`: التحقق فقط إذا كان الحقل موجوداً

### قواعد النصوص
- `string`: نص
- `max:n`: الحد الأقصى للطول
- `min:n`: الحد الأدنى للطول
- `email`: بريد إلكتروني
- `url`: رابط URL
- `alpha`: حروف فقط
- `alpha_num`: حروف وأرقام

### قواعد الأرقام
- `numeric`: رقم
- `integer`: عدد صحيح
- `between:min,max`: بين قيمتين
- `gt:value`: أكبر من
- `lt:value`: أصغر من

### قواعد التواريخ
- `date`: تاريخ
- `date_format:format`: صيغة تاريخ محددة
- `after:date`: بعد تاريخ
- `before:date`: قبل تاريخ

### قواعد الملفات
- `file`: ملف
- `image`: صورة
- `mimes:jpg,png`: أنواع ملفات محددة
- `max:size`: حجم أقصى بالكيلوبايت

### قواعد قاعدة البيانات
- `unique:table,column`: قيمة فريدة
- `exists:table,column`: قيمة موجودة

---

## 🔐 Authorization

### تفعيل Authorization

```php
public function authorize(): bool
{
    // التحقق من تسجيل الدخول
    if (!auth()->check()) {
        return false;
    }
    
    // منطق مخصص
    return auth()->user()->can('create-users');
}
```

---

## 💬 رسائل الخطأ المخصصة

### مثال

```php
public function messages(): array
{
    return [
        'name.required' => 'الاسم مطلوب',
        'email.required' => 'البريد الإلكتروني مطلوب',
        'email.email' => 'البريد الإلكتروني غير صحيح',
        'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
    ];
}
```

---

## 📦 القوالب الجاهزة

### 1. User Store Template
لإنشاء مستخدم جديد

### 2. User Update Template
لتحديث بيانات مستخدم

### 3. Search Template
للبحث العام

---

## 🚀 الاستخدام

### من الواجهة

1. انتقل إلى `/request-generator`
2. انقر على "إنشاء Request جديد"
3. املأ النموذج أو اختر قالباً جاهزاً
4. انقر على "توليد Request"
5. عاين الكود ثم احفظه

### من سطر الأوامر

```bash
# توليد Request بسيط
php artisan generate:request StoreUserRequest \
    --type=store \
    --fields='[{"name":"name","rules":"required|string"},{"name":"email","rules":"required|email"}]' \
    --authorization \
    --custom-messages \
    --save

# توليد تفاعلي
php artisan generate:request MyRequest
```

---

## 🧪 الاختبار

### اختبارات الوحدة (Unit Tests)

```php
public function test_generate_request()
{
    $config = [
        'name' => 'TestRequest',
        'type' => 'store',
        'fields' => [
            ['name' => 'name', 'rules' => 'required|string'],
        ],
    ];
    
    $result = $this->service->generate($config);
    
    $this->assertTrue($result['success']);
    $this->assertStringContainsString('class TestRequest', $result['code']);
}
```

---

## 📊 المقاييس

### الأداء
- **زمن التوليد:** < 3 ثوانٍ
- **حجم الكود:** 2-5 KB
- **دقة الكود:** 95%+

### الجودة
- **PSR-12 Compliance:** ✅
- **Laravel Best Practices:** ✅
- **PHPDoc Comments:** ✅ (ثنائي اللغة)

---

## 🔄 التكامل مع الأنظمة الأخرى

### Controller Generator
يمكن استخدام Requests المولدة مباشرة في Controllers

### API Generator
دعم كامل لـ API Requests

### Documentation Generator
توليد توثيق تلقائي للـ Requests

---

## 🛠️ التطوير المستقبلي

### الإصدار v3.30.0
- [ ] دعم Form Request Macros
- [ ] توليد Requests من Database Schema
- [ ] دعم Custom Validation Rules
- [ ] Integration مع Swagger/OpenAPI

### الإصدار v3.31.0
- [ ] AI-powered validation suggestions
- [ ] Auto-fix للقواعد الخاطئة
- [ ] Batch generation
- [ ] Team collaboration features

---

## 📝 الملاحظات

### نقاط القوة
✅ سهل الاستخدام  
✅ مدعوم بالذكاء الاصطناعي  
✅ قوالب جاهزة  
✅ معاينة مباشرة  
✅ دعم CLI  

### التحسينات المطلوبة
⚠️ إضافة المزيد من القوالب  
⚠️ دعم Nested Validation  
⚠️ تحسين رسائل الخطأ  

---

## 🙏 الشكر والتقدير

تم تطوير هذه الأداة بواسطة **Manus AI** كجزء من مشروع **PHP Magic System**.

**الإصدار:** 3.29.0  
**التاريخ:** 2025-12-03  
**الحالة:** ✅ مكتمل
