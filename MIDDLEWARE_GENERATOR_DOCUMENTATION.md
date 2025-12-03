# 🛡️ Middleware Generator Documentation v3.28.0

**التاريخ:** 2025-12-03  
**الإصدار:** 3.28.0  
**المشروع:** php-magic-system (SEMOP)

---

## 📋 نظرة عامة

**Middleware Generator** هو أداة ذكية مدعومة بالذكاء الاصطناعي (Manus AI) لتوليد Middleware في Laravel بشكل تلقائي. يوفر واجهة سهلة الاستخدام لإنشاء Middleware متقدمة مع دعم أنماط مختلفة ووظائف متعددة.

### المميزات الرئيسية

✅ **توليد تلقائي:** إنشاء Middleware بناءً على الوصف النصي أو القوالب  
✅ **أنماط متعددة:** دعم 6 أنواع مختلفة من Middleware  
✅ **تكامل AI:** استخدام Manus AI لتوليد كود عالي الجودة  
✅ **معاينة مباشرة:** عرض الكود قبل الحفظ  
✅ **إدارة شاملة:** حفظ، تحميل، تعديل، وحذف Middleware  
✅ **نسخ احتياطي:** حفظ تلقائي لنسخ احتياطية من جميع الملفات المولدة  

---

## 🎯 أنواع Middleware المدعومة

### 1. Authentication Middleware 🔐

**الوصف:** التحقق من هوية المستخدم

**الخيارات المتاحة:**
- `guard`: اسم الـ Guard (web, api, admin)
- `token_type`: نوع Token (Bearer, API-Key, Custom)
- `redirect_route`: المسار عند الفشل (للـ web)

**مثال الاستخدام:**

```php
// في routes/web.php
Route::middleware(['check.api.auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

**مثال الكود المولد:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        return $next($request);
    }
}
```

---

### 2. Authorization Middleware ✅

**الوصف:** التحقق من صلاحيات المستخدم

**الخيارات المتاحة:**
- `permission`: الصلاحية المطلوبة
- `role`: الدور المطلوب
- `ability`: القدرة المطلوبة

**مثال الاستخدام:**

```php
// في routes/web.php
Route::middleware(['check.admin.role'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

**مثال الكود المولد:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRoleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}
```

---

### 3. Logging Middleware 📝

**الوصف:** تسجيل الطلبات والاستجابات

**الخيارات المتاحة:**
- `log_channel`: قناة التسجيل (daily, single, stack)
- `log_level`: مستوى التسجيل (info, debug, error)
- `include_request`: تسجيل بيانات الطلب (true/false)
- `include_response`: تسجيل بيانات الاستجابة (true/false)

**مثال الاستخدام:**

```php
Route::middleware(['api.request.logger'])->group(function () {
    Route::apiResource('posts', PostController::class);
});
```

**البيانات المسجلة:**
- Method (GET, POST, PUT, DELETE)
- URL الكامل
- IP Address
- User Agent
- Request Headers (مع إخفاء البيانات الحساسة)
- Request Body (مع إخفاء كلمات المرور)
- Response Status
- Response Body
- مدة التنفيذ (بالميلي ثانية)

---

### 4. Rate Limiting Middleware ⏱️

**الوصف:** تحديد معدل الطلبات لمنع إساءة الاستخدام

**الخيارات المتاحة:**
- `max_attempts`: عدد المحاولات الأقصى (افتراضي: 60)
- `decay_minutes`: مدة الانتظار بالدقائق (افتراضي: 1)
- `key`: مفتاح التعريف (ip, user_id)

**مثال الاستخدام:**

```php
Route::middleware(['api.rate.limiter'])->group(function () {
    Route::post('/api/search', [SearchController::class, 'search']);
});
```

**Response Headers:**

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1638360000
```

**Response عند تجاوز الحد:**

```json
{
    "success": false,
    "message": "Too many requests",
    "retry_after": 60
}
```

---

### 5. CORS Middleware 🌐

**الوصف:** إدارة Cross-Origin Resource Sharing

**الخيارات المتاحة:**
- `allowed_origins`: النطاقات المسموحة (افتراضي: *)
- `allowed_methods`: الطرق المسموحة (GET, POST, PUT, DELETE, OPTIONS)
- `allowed_headers`: الرؤوس المسموحة (Content-Type, Authorization)

**مثال الاستخدام:**

```php
Route::middleware(['custom.cors'])->group(function () {
    Route::apiResource('api/products', ProductController::class);
});
```

**Headers المضافة:**

```http
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

---

### 6. Custom Middleware ⚙️

**الوصف:** Middleware مخصص بناءً على وصف نصي

**المدخلات:**
- `name`: اسم Middleware
- `description`: وصف تفصيلي للوظيفة المطلوبة

**مثال الوصف:**

```
Check if the user has an active subscription.
If not, redirect to subscription page.
Allow access to free routes (/home, /about, /contact).
```

**مثال الكود المولد:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionMiddleware
{
    protected $freeRoutes = ['/home', '/about', '/contact'];
    
    public function handle(Request $request, Closure $next)
    {
        // Allow free routes
        if (in_array($request->path(), $this->freeRoutes)) {
            return $next($request);
        }
        
        // Check subscription
        if (!$request->user() || !$request->user()->hasActiveSubscription()) {
            return redirect()->route('subscription.plans');
        }
        
        return $next($request);
    }
}
```

---

## 🚀 دليل الاستخدام

### الطريقة 1: استخدام الواجهة الرسومية

1. **الوصول إلى الصفحة الرئيسية:**
   ```
   http://your-domain.com/middleware-generator
   ```

2. **اختيار نوع Middleware:**
   - انقر على أحد الأزرار السريعة (Authentication, Authorization, إلخ)
   - أو انقر على "إنشاء Middleware جديد"

3. **ملء البيانات:**
   - أدخل اسم Middleware
   - اختر النوع
   - املأ الخيارات المطلوبة

4. **المعاينة:**
   - انقر على "معاينة" لرؤية الكود المولد
   - راجع الكود وتأكد من صحته

5. **الحفظ:**
   - انقر على "توليد وحفظ" لحفظ الملف
   - أو انقر على "حفظ" من صفحة المعاينة

6. **التحميل:**
   - يمكنك تحميل الملف مباشرة من صفحة القائمة
   - أو من صفحة المعاينة

---

### الطريقة 2: استخدام API

#### 1. معاينة Middleware

**Endpoint:** `POST /api/middleware-generator/preview`

**Request Body:**

```json
{
    "name": "CheckApiAuth",
    "type": "authentication",
    "options": {
        "guard": "api",
        "token_type": "Bearer"
    }
}
```

**Response:**

```json
{
    "status": "success",
    "message": "Code preview generated successfully.",
    "code": "<?php\n\nnamespace App\\Http\\Middleware;\n\n...",
    "name": "CheckApiAuthMiddleware"
}
```

---

#### 2. توليد وحفظ Middleware

**Endpoint:** `POST /api/middleware-generator/generate`

**Request Body:**

```json
{
    "name": "CheckAdminRole",
    "type": "authorization",
    "options": {
        "role": "admin"
    }
}
```

**Response:**

```json
{
    "status": "success",
    "message": "Middleware generated and saved successfully.",
    "file_path": "/path/to/app/Http/Middleware/CheckAdminRoleMiddleware.php",
    "name": "CheckAdminRoleMiddleware"
}
```

---

#### 3. الحصول على قائمة Middleware

**Endpoint:** `GET /api/middleware-generator/list`

**Response:**

```json
{
    "status": "success",
    "data": [
        {
            "name": "CheckApiAuthMiddleware",
            "path": "/path/to/app/Http/Middleware/CheckApiAuthMiddleware.php",
            "size": 2048,
            "modified": "2025-12-03 15:30:00"
        }
    ],
    "count": 1
}
```

---

## 🔧 التسجيل في Kernel

بعد توليد Middleware، يجب تسجيله في `app/Http/Kernel.php`:

### للـ Global Middleware:

```php
protected $middleware = [
    // ...
    \App\Http\Middleware\YourMiddleware::class,
];
```

### للـ Route Middleware:

```php
protected $middlewareAliases = [
    // ...
    'check.auth' => \App\Http\Middleware\CheckApiAuthMiddleware::class,
    'check.admin' => \App\Http\Middleware\CheckAdminRoleMiddleware::class,
    'api.logger' => \App\Http\Middleware\ApiRequestLoggerMiddleware::class,
];
```

### للـ Middleware Groups:

```php
protected $middlewareGroups = [
    'api' => [
        'throttle:api',
        \App\Http\Middleware\CheckApiAuthMiddleware::class,
        \App\Http\Middleware\ApiRequestLoggerMiddleware::class,
    ],
];
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
│   │       ├── [Generated Middlewares]
│   │       ├── CheckApiAuthMiddleware.php
│   │       ├── CheckAdminRoleMiddleware.php
│   │       └── ...
│   ├── Services/
│   │   └── MiddlewareGeneratorService.php
│   └── Exceptions/
│       └── MiddlewareGenerationException.php
├── resources/
│   └── views/
│       └── middleware-generator/
│           ├── index.blade.php
│           └── create.blade.php
├── routes/
│   └── middleware_generator.php
└── storage/
    └── app/
        └── generated/
            └── middlewares/
                └── [Backup files]
```

---

## 🔐 أفضل الممارسات الأمنية

### 1. استخدام HTTPS
تأكد من استخدام HTTPS في الإنتاج لحماية البيانات الحساسة.

### 2. تدوير API Tokens
قم بتدوير API Tokens بشكل دوري:

```php
$user->api_token = Str::random(64);
$user->save();
```

### 3. IP Whitelisting
أضف قائمة بيضاء للـ IP addresses في Middleware:

```php
protected function isAllowedIp(string $ip): bool
{
    $allowedIps = config('api.allowed_ips', []);
    return in_array($ip, $allowedIps);
}
```

### 4. Rate Limiting
استخدم Rate Limiting لجميع API endpoints:

```php
Route::middleware(['throttle:60,1'])->group(function () {
    // Your routes
});
```

### 5. إخفاء البيانات الحساسة
تأكد من إخفاء البيانات الحساسة في Logs:

```php
$sensitiveFields = ['password', 'token', 'api_key', 'secret'];
foreach ($sensitiveFields as $field) {
    if (isset($data[$field])) {
        $data[$field] = '***HIDDEN***';
    }
}
```

---

## 🐛 استكشاف الأخطاء

### المشكلة: "فشل توليد Middleware"

**الحل:**
1. تحقق من اتصال Manus AI
2. تأكد من صحة البيانات المدخلة
3. راجع ملف الـ logs في `storage/logs/laravel.log`

---

### المشكلة: "Middleware غير موجود"

**الحل:**
1. تأكد من تسجيل Middleware في `Kernel.php`
2. قم بتشغيل `php artisan config:clear`
3. قم بتشغيل `php artisan route:clear`

---

### المشكلة: "خطأ في الصلاحيات"

**الحل:**
1. تأكد من صلاحيات المجلد `app/Http/Middleware`
2. قم بتشغيل: `chmod -R 755 app/Http/Middleware`

---

## 📊 الإحصائيات والمراقبة

### عرض إحصائيات Middleware

```php
// في Controller
$stats = [
    'total' => count($middlewares),
    'authentication' => $middlewares->filter(fn($m) => str_contains($m['name'], 'Auth'))->count(),
    'authorization' => $middlewares->filter(fn($m) => str_contains($m['name'], 'Role'))->count(),
];
```

### مراقبة الأداء

```php
// في Middleware
$startTime = microtime(true);

$response = $next($request);

$duration = (microtime(true) - $startTime) * 1000;
Log::info('Middleware execution time: ' . $duration . 'ms');
```

---

## 🎯 أمثلة عملية

### مثال 1: Middleware للتحقق من الاشتراك

```php
// الوصف في Custom Middleware:
"Check if user has active subscription. 
Redirect to /subscribe if not subscribed.
Allow free access to /home and /pricing pages."

// الكود المولد:
class CheckSubscriptionMiddleware
{
    protected $freeRoutes = ['/home', '/pricing'];
    
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->path(), $this->freeRoutes)) {
            return $next($request);
        }
        
        if (!$request->user()->subscription()->active()) {
            return redirect('/subscribe');
        }
        
        return $next($request);
    }
}
```

---

### مثال 2: Middleware للتحقق من IP

```php
// الوصف في Custom Middleware:
"Block requests from specific IP addresses.
Blocked IPs: 192.168.1.100, 10.0.0.50
Return 403 Forbidden for blocked IPs."

// الكود المولد:
class BlockIpMiddleware
{
    protected $blockedIps = ['192.168.1.100', '10.0.0.50'];
    
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->ip(), $this->blockedIps)) {
            abort(403, 'Your IP address is blocked.');
        }
        
        return $next($request);
    }
}
```

---

## 📝 الأسئلة الشائعة

### س1: هل يمكن تعديل الكود المولد؟

**ج:** نعم، الكود المولد هو كود PHP عادي يمكنك تعديله كما تشاء. يتم حفظ نسخة احتياطية تلقائياً في `storage/app/generated/middlewares/`.

---

### س2: هل يدعم النظام Laravel 10؟

**ج:** نعم، الكود المولد متوافق مع Laravel 8, 9, 10, و 11.

---

### س3: كيف أحذف Middleware؟

**ج:** يمكنك الحذف من الواجهة الرسومية أو باستخدام API endpoint:
```
DELETE /middleware-generator/{name}
```

---

### س4: هل يمكن توليد عدة Middleware دفعة واحدة؟

**ج:** حالياً لا، ولكن يمكنك استخدام API لتوليد عدة Middleware بشكل متتالي.

---

## 🔄 التحديثات المستقبلية

### v3.29.0 (مخطط)
- [ ] دعم Middleware Parameters
- [ ] قوالب جاهزة إضافية
- [ ] تصدير/استيراد Middleware

### v3.30.0 (مخطط)
- [ ] اختبار تلقائي للـ Middleware
- [ ] توثيق تلقائي
- [ ] تكامل مع IDE

---

## 📞 الدعم والمساعدة

للحصول على الدعم:
- 📧 Email: support@semop.com
- 💬 Discord: SEMOP Community
- 📖 Documentation: https://docs.semop.com

---

**Generated by Middleware Generator v3.28.0**  
**SEMOP Team © 2025**
