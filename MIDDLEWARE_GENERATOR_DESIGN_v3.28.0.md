# 🛡️ Middleware Generator Design v3.28.0

**التاريخ:** 2025-12-03  
**الإصدار:** 3.28.0  
**المشروع:** php-magic-system (SEMOP)  
**المهمة:** Task #20 - Middleware Generator

---

## 📋 نظرة عامة

**Middleware Generator** هو أداة ذكية لتوليد Middleware في Laravel بشكل تلقائي باستخدام الذكاء الاصطناعي (Manus AI). يوفر واجهة سهلة الاستخدام لإنشاء Middleware متقدمة مع دعم أنماط مختلفة ووظائف متعددة.

### الأهداف الرئيسية

1. **توليد تلقائي:** إنشاء Middleware بناءً على الوصف النصي أو القوالب
2. **أنماط متعددة:** دعم Authentication, Authorization, Logging, Rate Limiting, CORS, وغيرها
3. **تكامل AI:** استخدام Manus AI لتوليد كود عالي الجودة
4. **معاينة مباشرة:** عرض الكود قبل الحفظ
5. **إدارة شاملة:** حفظ، تحميل، تعديل، وحذف Middleware

---

## 🏗️ المعمارية

### البنية العامة

```
┌─────────────────────────────────────────┐
│         User Interface (View)           │
│    resources/views/middleware-generator │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  MiddlewareGeneratorController          │
│  app/Http/Controllers/                  │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  MiddlewareGeneratorService             │
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

### 1. Controller: MiddlewareGeneratorController

**المسار:** `app/Http/Controllers/MiddlewareGeneratorController.php`

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

// توليد Middleware جديد
public function generate(Request $request): JsonResponse

// معاينة الكود قبل الحفظ
public function preview(Request $request): JsonResponse

// حفظ Middleware إلى ملف
public function save(Request $request): JsonResponse

// تحميل Middleware
public function download(Request $request): BinaryFileResponse

// عرض قائمة Middleware المولدة
public function list(): JsonResponse

// حذف Middleware
public function delete(Request $request): JsonResponse
```

---

### 2. Service: MiddlewareGeneratorService

**المسار:** `app/Services/MiddlewareGeneratorService.php`

**المسؤوليات:**
- التواصل مع Manus AI لتوليد الكود
- بناء Prompts ذكية للحصول على أفضل نتائج
- معالجة وتنسيق الكود المولد
- حفظ الملفات في المسارات الصحيحة
- إدارة قوالب Middleware المختلفة

**الوظائف الرئيسية:**

```php
// توليد Middleware بناءً على النوع
public function generateMiddleware(string $name, string $type, array $options = []): string

// توليد Authentication Middleware
protected function generateAuthMiddleware(string $name, array $options): string

// توليد Authorization Middleware
protected function generateAuthorizationMiddleware(string $name, array $options): string

// توليد Logging Middleware
protected function generateLoggingMiddleware(string $name, array $options): string

// توليد Rate Limiting Middleware
protected function generateRateLimitMiddleware(string $name, array $options): string

// توليد CORS Middleware
protected function generateCorsMiddleware(string $name, array $options): string

// توليد Custom Middleware من وصف نصي
protected function generateCustomMiddleware(string $name, string $description, array $options): string

// معاينة الكود
public function previewMiddleware(string $name, string $type, array $options = []): string

// حفظ الملف
public function saveMiddleware(string $name, string $content): string

// الحصول على قائمة Middleware المولدة
public function getGeneratedMiddlewares(): array
```

---

### 3. View: Middleware Generator Interface

**المسار:** `resources/views/middleware-generator/`

**الملفات:**
- `index.blade.php` - الصفحة الرئيسية
- `create.blade.php` - نموذج الإنشاء
- `preview.blade.php` - معاينة الكود
- `list.blade.php` - قائمة Middleware المولدة

**المكونات الرئيسية:**

1. **نموذج الإدخال:**
   - اسم Middleware
   - نوع Middleware (قائمة منسدلة)
   - خيارات إضافية (حسب النوع)
   - وصف نصي (للـ Custom Middleware)

2. **منطقة المعاينة:**
   - عرض الكود المولد مع Syntax Highlighting
   - أزرار النسخ والتحميل

3. **قائمة Middleware:**
   - جدول بجميع Middleware المولدة
   - خيارات التحميل والحذف

---

### 4. Routes: middleware_generator.php

**المسار:** `routes/middleware_generator.php`

**المسارات:**

```php
// Web Routes
Route::prefix('middleware-generator')->name('middleware-generator.')->group(function () {
    // Index & Create
    Route::get('/', [MiddlewareGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [MiddlewareGeneratorController::class, 'create'])->name('create');
    
    // Generate & Preview
    Route::post('/generate', [MiddlewareGeneratorController::class, 'generate'])->name('generate');
    Route::post('/preview', [MiddlewareGeneratorController::class, 'preview'])->name('preview');
    
    // Save & Download
    Route::post('/save', [MiddlewareGeneratorController::class, 'save'])->name('save');
    Route::get('/download', [MiddlewareGeneratorController::class, 'download'])->name('download');
    
    // List & Delete
    Route::get('/list', [MiddlewareGeneratorController::class, 'list'])->name('list');
    Route::delete('/{name}', [MiddlewareGeneratorController::class, 'delete'])->name('delete');
});

// API Routes
Route::prefix('api/middleware-generator')->name('api.middleware-generator.')->group(function () {
    Route::post('/generate', [MiddlewareGeneratorController::class, 'apiGenerate'])->name('generate');
    Route::post('/preview', [MiddlewareGeneratorController::class, 'apiPreview'])->name('preview');
    Route::get('/list', [MiddlewareGeneratorController::class, 'apiList'])->name('list');
});
```

---

## 📊 أنواع Middleware المدعومة

### 1. Authentication Middleware

**الوصف:** التحقق من هوية المستخدم

**الخيارات:**
- `guard`: اسم الـ Guard (web, api, admin)
- `redirect_route`: المسار عند الفشل
- `token_type`: نوع Token (Bearer, API-Key, Custom)

**مثال:**
```php
// Input
name: CheckApiAuth
type: authentication
options: {
  guard: 'api',
  token_type: 'Bearer'
}

// Output
class CheckApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
```

---

### 2. Authorization Middleware

**الوصف:** التحقق من صلاحيات المستخدم

**الخيارات:**
- `permission`: الصلاحية المطلوبة
- `role`: الدور المطلوب
- `ability`: القدرة المطلوبة

**مثال:**
```php
// Input
name: CheckAdminRole
type: authorization
options: {
  role: 'admin'
}

// Output
class CheckAdminRoleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
```

---

### 3. Logging Middleware

**الوصف:** تسجيل الطلبات والاستجابات

**الخيارات:**
- `log_channel`: قناة التسجيل
- `log_level`: مستوى التسجيل (info, debug, error)
- `include_request`: تسجيل بيانات الطلب
- `include_response`: تسجيل بيانات الاستجابة

---

### 4. Rate Limiting Middleware

**الوصف:** تحديد معدل الطلبات

**الخيارات:**
- `max_attempts`: عدد المحاولات الأقصى
- `decay_minutes`: مدة الانتظار بالدقائق
- `key`: مفتاح التعريف (IP, User ID)

---

### 5. CORS Middleware

**الوصف:** إدارة Cross-Origin Resource Sharing

**الخيارات:**
- `allowed_origins`: النطاقات المسموحة
- `allowed_methods`: الطرق المسموحة
- `allowed_headers`: الرؤوس المسموحة

---

### 6. Custom Middleware

**الوصف:** Middleware مخصص بناءً على وصف نصي

**المدخلات:**
- `name`: اسم Middleware
- `description`: وصف تفصيلي للوظيفة المطلوبة

**مثال:**
```
name: CheckSubscription
description: "Check if the user has an active subscription. 
If not, redirect to subscription page. 
Allow access to free routes."
```

---

## 🔧 التكامل مع Manus AI

### بناء Prompts ذكية

**القالب العام:**

```
Generate a Laravel Middleware class named {name}.

Type: {type}
Description: {description}

Requirements:
- Follow Laravel best practices
- Include proper PHPDoc comments
- Handle errors gracefully
- Use dependency injection where appropriate
- Add Arabic and English comments

Options:
{options_json}

Please generate complete, production-ready code.
```

**مثال Prompt لـ Authentication Middleware:**

```
Generate a Laravel Middleware class named CheckApiAuthMiddleware.

Type: Authentication
Description: Verify API authentication using Bearer token

Requirements:
- Check for Authorization header
- Validate Bearer token format
- Verify token against database
- Return 401 if unauthorized
- Include proper PHPDoc comments
- Add Arabic and English comments

Options:
{
  "guard": "api",
  "token_type": "Bearer",
  "check_database": true
}

Please generate complete, production-ready code.
```

---

## 📁 هيكل الملفات

```
php-magic-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── MiddlewareGeneratorController.php
│   │   └── Middleware/
│   │       └── [Generated Middlewares]
│   ├── Services/
│   │   └── MiddlewareGeneratorService.php
│   └── Exceptions/
│       └── MiddlewareGenerationException.php
├── resources/
│   └── views/
│       └── middleware-generator/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── preview.blade.php
│           └── list.blade.php
├── routes/
│   └── middleware_generator.php
└── storage/
    └── app/
        └── generated/
            └── middlewares/
                └── [Backup files]
```

---

## 🎨 واجهة المستخدم

### الصفحة الرئيسية

**العناصر:**
1. **Header:**
   - عنوان: "🛡️ Middleware Generator v3.28.0"
   - وصف مختصر
   - زر "إنشاء Middleware جديد"

2. **Quick Actions:**
   - Authentication Middleware
   - Authorization Middleware
   - Logging Middleware
   - Rate Limiting Middleware
   - CORS Middleware
   - Custom Middleware

3. **Recent Middlewares:**
   - قائمة بآخر 10 Middleware تم توليدها
   - خيارات سريعة (معاينة، تحميل، حذف)

---

### نموذج الإنشاء

**الحقول:**

1. **اسم Middleware** (مطلوب)
   - Input text
   - Placeholder: "CheckUserSubscription"
   - Validation: يجب أن ينتهي بـ "Middleware"

2. **نوع Middleware** (مطلوب)
   - Select dropdown
   - Options: Authentication, Authorization, Logging, Rate Limiting, CORS, Custom

3. **الخيارات** (اختياري - يتغير حسب النوع)
   - Dynamic form fields

4. **الوصف** (للـ Custom فقط)
   - Textarea
   - Placeholder: "وصف تفصيلي للوظيفة المطلوبة..."

5. **أزرار:**
   - "معاينة" (Preview)
   - "توليد وحفظ" (Generate & Save)
   - "إلغاء" (Cancel)

---

### صفحة المعاينة

**المكونات:**

1. **معلومات Middleware:**
   - الاسم
   - النوع
   - تاريخ التوليد

2. **عرض الكود:**
   - Syntax highlighting (PHP)
   - Line numbers
   - زر النسخ

3. **أزرار:**
   - "حفظ" (Save)
   - "تحميل" (Download)
   - "تعديل" (Edit)
   - "رجوع" (Back)

---

## 🧪 الاختبار

### اختبارات الوحدة (Unit Tests)

```php
// tests/Unit/MiddlewareGeneratorServiceTest.php

test('can generate authentication middleware', function () {
    $service = app(MiddlewareGeneratorService::class);
    $code = $service->generateMiddleware('CheckAuth', 'authentication');
    
    expect($code)->toContain('class CheckAuthMiddleware');
    expect($code)->toContain('public function handle');
});

test('can generate custom middleware from description', function () {
    $service = app(MiddlewareGeneratorService::class);
    $code = $service->generateCustomMiddleware(
        'CheckSubscription',
        'Check if user has active subscription'
    );
    
    expect($code)->toContain('class CheckSubscriptionMiddleware');
});
```

### اختبارات التكامل (Feature Tests)

```php
// tests/Feature/MiddlewareGeneratorTest.php

test('can access middleware generator page', function () {
    $response = $this->get('/middleware-generator');
    $response->assertStatus(200);
});

test('can generate middleware via API', function () {
    $response = $this->post('/api/middleware-generator/generate', [
        'name' => 'TestMiddleware',
        'type' => 'authentication',
        'options' => ['guard' => 'api']
    ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure(['status', 'code', 'file_path']);
});
```

---

## 📝 التوثيق

### ملف التوثيق الرئيسي

**المسار:** `MIDDLEWARE_GENERATOR_DOCUMENTATION.md`

**المحتويات:**
1. نظرة عامة
2. دليل الاستخدام
3. أنواع Middleware المدعومة
4. أمثلة عملية
5. API Reference
6. الأسئلة الشائعة
7. استكشاف الأخطاء

---

## 🚀 خطة التنفيذ

### المرحلة 1: الإعداد (10 دقائق)
- [x] إنشاء ملف التصميم
- [ ] إنشاء Exception class
- [ ] إعداد هيكل المجلدات

### المرحلة 2: Backend (20 دقيقة)
- [ ] تطوير MiddlewareGeneratorService
- [ ] تطوير MiddlewareGeneratorController
- [ ] إنشاء Routes

### المرحلة 3: Frontend (15 دقيقة)
- [ ] تطوير View الرئيسية
- [ ] تطوير نموذج الإنشاء
- [ ] تطوير صفحة المعاينة

### المرحلة 4: الاختبار (10 دقيقة)
- [ ] اختبار التوليد
- [ ] اختبار الحفظ والتحميل
- [ ] اختبار الواجهة

### المرحلة 5: التوثيق والنشر (15 دقيقة)
- [ ] كتابة التوثيق
- [ ] تحديث CHANGELOG
- [ ] تحديث VERSION
- [ ] Commit & Push

**الوقت الإجمالي:** 70 دقيقة

---

## 📊 المخرجات المتوقعة

### الملفات المولدة

1. **Controller:**
   - `app/Http/Controllers/MiddlewareGeneratorController.php`

2. **Service:**
   - `app/Services/MiddlewareGeneratorService.php`

3. **Exception:**
   - `app/Exceptions/MiddlewareGenerationException.php`

4. **Views:**
   - `resources/views/middleware-generator/index.blade.php`
   - `resources/views/middleware-generator/create.blade.php`
   - `resources/views/middleware-generator/preview.blade.php`
   - `resources/views/middleware-generator/list.blade.php`

5. **Routes:**
   - `routes/middleware_generator.php`

6. **Documentation:**
   - `MIDDLEWARE_GENERATOR_DOCUMENTATION.md`
   - `MIDDLEWARE_GENERATOR_USER_GUIDE.md`

7. **Tests:**
   - `tests/Unit/MiddlewareGeneratorServiceTest.php`
   - `tests/Feature/MiddlewareGeneratorTest.php`

---

## ✅ معايير النجاح

1. ✅ توليد Middleware بنجاح لجميع الأنواع المدعومة
2. ✅ معاينة الكود قبل الحفظ
3. ✅ حفظ الملفات في المسارات الصحيحة
4. ✅ واجهة مستخدم سهلة وبديهية
5. ✅ توثيق شامل وواضح
6. ✅ اجتياز جميع الاختبارات
7. ✅ تكامل سلس مع Manus AI

---

**Generated by Manus AI**  
**SEMOP Team © 2025**
