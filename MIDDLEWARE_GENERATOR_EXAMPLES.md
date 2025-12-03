# 🛡️ Middleware Generator v3.28.0 - أمثلة الاستخدام

**التاريخ:** 2025-12-03  
**الإصدار:** 3.28.0  
**المشروع:** php-magic-system (SEMOP)

---

## 📋 نظرة عامة

هذا الملف يحتوي على أمثلة عملية لاستخدام Middleware Generator v3.28.0.

---

## 🎯 الأمثلة الأساسية

### 1. توليد Authentication Middleware

```bash
php artisan generate:middleware \
  --text="middleware للتحقق من المصادقة عبر API Token" \
  --type=auth \
  --name=CustomAuthMiddleware \
  --save
```

**النتيجة:**
- ملف: `app/Http/Middleware/CustomAuthMiddleware.php`
- النوع: Authentication
- يتضمن: فحص Token، تسجيل الطلبات، معالجة الأخطاء

---

### 2. توليد Permission Middleware

```bash
php artisan generate:middleware \
  --text="middleware للتحقق من صلاحيات المستخدم" \
  --type=permission \
  --name=RolePermissionMiddleware \
  --save
```

**النتيجة:**
- ملف: `app/Http/Middleware/RolePermissionMiddleware.php`
- النوع: Permission/Authorization
- يتضمن: فحص الصلاحيات، دعم الأدوار، معالجة الرفض

---

### 3. توليد Rate Limiting Middleware

```bash
php artisan generate:middleware \
  --text="middleware لتحديد معدل الطلبات" \
  --type=rate_limit \
  --name=ApiRateLimiterMiddleware \
  --save
```

**النتيجة:**
- ملف: `app/Http/Middleware/ApiRateLimiterMiddleware.php`
- النوع: Rate Limiting
- يتضمن: تحديد الطلبات، Cache، Headers

---

### 4. توليد Logging Middleware

```bash
php artisan generate:middleware \
  --text="middleware لتسجيل جميع طلبات API" \
  --type=logging \
  --name=RequestLoggerMiddleware \
  --save
```

**النتيجة:**
- ملف: `app/Http/Middleware/RequestLoggerMiddleware.php`
- النوع: Request Logging
- يتضمن: تسجيل الطلبات، قياس الأداء، تفاصيل الاستجابة

---

## 🔧 أمثلة متقدمة

### 5. توليد من JSON Schema

**إنشاء ملف JSON:**
```json
{
  "name": "AdvancedSecurityMiddleware",
  "type": "security",
  "description": "Advanced security middleware with multiple protection layers",
  "options": {
    "author": "Security Team",
    "version": "2.0.0"
  }
}
```

**التوليد:**
```bash
php artisan generate:middleware \
  --json=path/to/schema.json \
  --save
```

---

### 6. توليد من قالب مخصص

```bash
php artisan generate:middleware \
  --template=api-versioning \
  --name=ApiVersionMiddleware \
  --save
```

**النتيجة:**
- Middleware لإدارة إصدارات API
- دعم v1, v2, v3
- قراءة الإصدار من Header أو URL

---

### 7. توليد CORS Middleware

```bash
php artisan generate:middleware \
  --text="middleware لإدارة CORS" \
  --type=cors \
  --name=CustomCorsMiddleware \
  --save
```

**النتيجة:**
- معالجة Preflight Requests
- إضافة CORS Headers
- دعم جميع HTTP Methods

---

### 8. توليد Validation Middleware

```bash
php artisan generate:middleware \
  --text="middleware للتحقق من صحة البيانات" \
  --type=validation \
  --name=RequestValidatorMiddleware \
  --save
```

**النتيجة:**
- التحقق من البيانات قبل الوصول للـ Controller
- دعم Laravel Validation Rules
- رسائل خطأ مفصلة

---

### 9. توليد Cache Middleware

```bash
php artisan generate:middleware \
  --text="middleware لتخزين الاستجابات مؤقتاً" \
  --type=cache \
  --name=ResponseCacheMiddleware \
  --save
```

**النتيجة:**
- تخزين استجابات GET
- مدة تخزين قابلة للتخصيص
- مفاتيح Cache ذكية

---

### 10. توليد Transform Middleware

```bash
php artisan generate:middleware \
  --text="middleware لتحويل البيانات" \
  --type=transform \
  --name=DataTransformMiddleware \
  --save
```

**النتيجة:**
- تحويل Request Data
- تحويل Response Data
- دعم التنسيقات المختلفة

---

## 📦 أمثلة الاستخدام التفاعلي

### استخدام القائمة التفاعلية

```bash
php artisan generate:middleware
```

**الخطوات:**
1. اختيار طريقة التوليد
2. إدخال الوصف أو المعلومات المطلوبة
3. اختيار النوع (اختياري)
4. إدخال الاسم (اختياري)
5. معاينة النتيجة
6. الحفظ (اختياري)

---

## 🎨 أمثلة التخصيص

### مثال 1: Middleware مخصص للتحقق من IP

```bash
php artisan generate:middleware \
  --text="middleware للتحقق من IP المسموح به" \
  --type=custom \
  --name=IpWhitelistMiddleware \
  --save
```

**التخصيص بعد التوليد:**
```php
protected array $allowedIps = [
    '192.168.1.1',
    '10.0.0.1',
];

protected function isAllowedIp(Request $request): bool
{
    return in_array($request->ip(), $this->allowedIps);
}
```

---

### مثال 2: Middleware للتحقق من الوقت

```bash
php artisan generate:middleware \
  --text="middleware للتحقق من وقت الوصول" \
  --type=custom \
  --name=TimeBasedAccessMiddleware \
  --save
```

**التخصيص:**
```php
protected function isAccessAllowed(): bool
{
    $hour = now()->hour;
    return $hour >= 8 && $hour <= 18; // 8 AM to 6 PM
}
```

---

### مثال 3: Middleware للتحقق من الاشتراك

```bash
php artisan generate:middleware \
  --text="middleware للتحقق من صلاحية الاشتراك" \
  --type=custom \
  --name=SubscriptionCheckMiddleware \
  --save
```

**التخصيص:**
```php
protected function hasActiveSubscription($user): bool
{
    return $user->subscription && 
           $user->subscription->isActive() && 
           !$user->subscription->isExpired();
}
```

---

## 🔗 التسجيل في Kernel

بعد توليد أي Middleware، يجب تسجيله في `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... existing middleware
    
    // Generated Middleware
    'custom.auth' => \App\Http\Middleware\CustomAuthMiddleware::class,
    'role.permission' => \App\Http\Middleware\RolePermissionMiddleware::class,
    'api.rate_limiter' => \App\Http\Middleware\ApiRateLimiterMiddleware::class,
    'request.logger' => \App\Http\Middleware\RequestLoggerMiddleware::class,
    'advanced.security' => \App\Http\Middleware\AdvancedSecurityMiddleware::class,
    'api.version' => \App\Http\Middleware\ApiVersionMiddleware::class,
    'custom.cors' => \App\Http\Middleware\CustomCorsMiddleware::class,
    'request.validator' => \App\Http\Middleware\RequestValidatorMiddleware::class,
    'response.cache' => \App\Http\Middleware\ResponseCacheMiddleware::class,
    'data.transform' => \App\Http\Middleware\DataTransformMiddleware::class,
    'ip.whitelist' => \App\Http\Middleware\IpWhitelistMiddleware::class,
    'time.access' => \App\Http\Middleware\TimeBasedAccessMiddleware::class,
    'subscription.check' => \App\Http\Middleware\SubscriptionCheckMiddleware::class,
];
```

---

## 🚀 الاستخدام في Routes

### مثال 1: API محمي بالكامل

```php
Route::prefix('api/v1')->middleware([
    'request.logger',
    'api.rate_limiter',
    'custom.auth',
])->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role.permission:users.view');
    
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('role.permission:users.create');
});
```

---

### مثال 2: استخدام متعدد

```php
Route::middleware([
    'advanced.security',
    'custom.cors',
    'api.version',
])->group(function () {
    Route::get('/public-data', [DataController::class, 'index'])
        ->middleware('response.cache');
});
```

---

### مثال 3: Middleware مشروط

```php
Route::get('/premium-content', [ContentController::class, 'show'])
    ->middleware([
        'custom.auth',
        'subscription.check',
        'time.access',
    ]);
```

---

## 📊 أمثلة الاختبار

### اختبار Middleware مولد

```bash
# توليد مع التحقق
php artisan generate:middleware \
  --text="test middleware" \
  --type=custom \
  --name=TestMiddleware \
  --validate

# عرض الأنواع المدعومة
php artisan generate:middleware --list-types
```

---

## 💡 نصائح وأفضل الممارسات

### 1. التسمية
- استخدم أسماء واضحة ومعبرة
- اتبع نمط PascalCase
- أضف "Middleware" في نهاية الاسم

### 2. التوثيق
- أضف تعليقات واضحة
- وثق المعاملات والقيم المرجعة
- اشرح المنطق المعقد

### 3. الأداء
- تجنب العمليات الثقيلة في Middleware
- استخدم Cache عند الحاجة
- قلل من استعلامات قاعدة البيانات

### 4. الأمان
- تحقق من جميع المدخلات
- استخدم HTTPS في الإنتاج
- سجل محاولات الوصول المشبوهة

### 5. الاختبار
- اختبر جميع السيناريوهات
- استخدم Unit Tests
- اختبر الأداء تحت الضغط

---

## 🔍 استكشاف الأخطاء

### خطأ: "Middleware not found"
**الحل:** تأكد من تسجيل Middleware في Kernel.php

### خطأ: "Too many requests"
**الحل:** اضبط إعدادات Rate Limiting

### خطأ: "Permission denied"
**الحل:** تحقق من صلاحيات المستخدم

---

## 📚 موارد إضافية

- [Laravel Middleware Documentation](https://laravel.com/docs/middleware)
- [Middleware Generator Design Document](MIDDLEWARE_GENERATOR_DESIGN.md)
- [API Documentation](API_DOCUMENTATION.md)

---

## 🎯 الخلاصة

Middleware Generator v3.28.0 يوفر:

✅ **10 أنواع مدعومة** من Middleware  
✅ **توليد ذكي** من وصف نصي  
✅ **قوالب جاهزة** للاستخدام  
✅ **تخصيص كامل** حسب الحاجة  
✅ **تحقق تلقائي** من الصحة  
✅ **توثيق شامل** لكل ميزة  

---

**Generated by Middleware Generator v3.28.0**  
**SEMOP Team © 2025**
