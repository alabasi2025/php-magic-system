# 🔐 API Middleware Documentation v3.16.0

**التاريخ:** 2025-12-03  
**الإصدار:** 3.16.0  
**المشروع:** php-magic-system (SEMOP)

---

## 📋 نظرة عامة

تم توليد 4 Middleware متقدمة لحماية وإدارة API endpoints:

1. **ApiAuthMiddleware** - المصادقة والتحقق من API Token
2. **ApiRateLimitMiddleware** - التحكم في معدل الطلبات
3. **ApiPermissionMiddleware** - التحقق من صلاحيات الوصول
4. **ApiLoggingMiddleware** - تسجيل جميع طلبات API

---

## 1️⃣ ApiAuthMiddleware

### الوصف
يتحقق من وجود وصحة API Token في كل طلب.

### الاستخدام

```php
// في routes/api.php
Route::middleware(['api.auth'])->group(function () {
    Route::get('/users', [UserApiController::class, 'index']);
});
```

### Headers المطلوبة

```http
X-API-Token: your-api-token-here
```

### Response عند الفشل

```json
{
    "success": false,
    "message": "API token is required",
    "error": "Missing X-API-Token header"
}
```

### التخصيص

يمكنك تخصيص منطق التحقق من Token:

```php
// في ApiAuthMiddleware.php
$user = User::where('api_token', $apiToken)->first();
if (!$user) {
    return response()->json([
        'success' => false,
        'message' => 'Invalid API token'
    ], 401);
}
```

---

## 2️⃣ ApiRateLimitMiddleware

### الوصف
يحد من عدد الطلبات لكل مستخدم/IP لمنع إساءة الاستخدام.

### الإعدادات الافتراضية

- **الحد الأقصى:** 60 طلب
- **الفترة الزمنية:** 1 دقيقة

### الاستخدام

```php
Route::middleware(['api.rate_limit'])->group(function () {
    Route::get('/users', [UserApiController::class, 'index']);
});
```

### Response Headers

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
```

### Response عند تجاوز الحد

```json
{
    "success": false,
    "message": "Too many requests",
    "error": "Rate limit exceeded. Please try again later.",
    "retry_after": 60
}
```

### التخصيص

```php
// تغيير الحد الأقصى
protected int $maxAttempts = 100;

// تغيير الفترة الزمنية
protected int $decayMinutes = 5;
```

---

## 3️⃣ ApiPermissionMiddleware

### الوصف
يتحقق من صلاحيات المستخدم للوصول إلى موارد محددة.

### الاستخدام

```php
// التحقق من صلاحية محددة
Route::middleware(['api.auth', 'api.permission:users.view'])->group(function () {
    Route::get('/users', [UserApiController::class, 'index']);
});

// صلاحيات مختلفة لكل endpoint
Route::middleware(['api.auth'])->group(function () {
    Route::get('/users', [UserApiController::class, 'index'])
        ->middleware('api.permission:users.view');
    
    Route::post('/users', [UserApiController::class, 'store'])
        ->middleware('api.permission:users.create');
    
    Route::put('/users/{id}', [UserApiController::class, 'update'])
        ->middleware('api.permission:users.edit');
    
    Route::delete('/users/{id}', [UserApiController::class, 'destroy'])
        ->middleware('api.permission:users.delete');
});
```

### Response عند عدم وجود صلاحية

```json
{
    "success": false,
    "message": "Permission denied",
    "error": "You don't have permission to access this resource",
    "required_permission": "users.edit"
}
```

### التكامل مع Spatie Laravel Permission

```php
protected function hasPermission($user, string $permission): bool
{
    return $user->hasPermissionTo($permission);
}
```

---

## 4️⃣ ApiLoggingMiddleware

### الوصف
يسجل جميع طلبات API للمراجعة والتدقيق.

### البيانات المسجلة

- Method (GET, POST, PUT, DELETE)
- URL الكامل
- IP Address
- User Agent
- Request Headers (مع إخفاء البيانات الحساسة)
- Request Body (مع إخفاء كلمات المرور)
- Response Status
- Response Body
- مدة التنفيذ (بالميلي ثانية)

### الاستخدام

```php
Route::middleware(['api.logging'])->group(function () {
    Route::get('/users', [UserApiController::class, 'index']);
});
```

### جدول قاعدة البيانات

يجب إنشاء جدول `api_logs`:

```sql
CREATE TABLE api_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    method VARCHAR(10),
    url TEXT,
    path VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_headers TEXT,
    request_body TEXT,
    response_status INT,
    response_body TEXT,
    duration_ms DECIMAL(10, 2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_method (method),
    INDEX idx_ip_address (ip_address),
    INDEX idx_created_at (created_at)
);
```

### البيانات الحساسة

يتم إخفاء البيانات الحساسة تلقائياً:

- Headers: `authorization`, `x-api-token`, `cookie`
- Body Fields: `password`, `password_confirmation`, `token`, `secret`, `api_key`

---

## 🔧 التسجيل في Kernel

أضف Middleware إلى `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... existing middleware
    'api.auth' => \App\Http\Middleware\ApiAuthMiddleware::class,
    'api.rate_limit' => \App\Http\Middleware\ApiRateLimitMiddleware::class,
    'api.permission' => \App\Http\Middleware\ApiPermissionMiddleware::class,
    'api.logging' => \App\Http\Middleware\ApiLoggingMiddleware::class,
];
```

---

## 📦 مثال شامل

```php
// routes/api.php

// مجموعة API محمية بالكامل
Route::prefix('v1')->middleware([
    'api.logging',      // تسجيل جميع الطلبات
    'api.rate_limit',   // تحديد معدل الطلبات
    'api.auth'          // المصادقة
])->group(function () {
    
    // Users API
    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index'])
            ->middleware('api.permission:users.view');
        
        Route::post('/', [UserApiController::class, 'store'])
            ->middleware('api.permission:users.create');
        
        Route::get('/{id}', [UserApiController::class, 'show'])
            ->middleware('api.permission:users.view');
        
        Route::put('/{id}', [UserApiController::class, 'update'])
            ->middleware('api.permission:users.edit');
        
        Route::delete('/{id}', [UserApiController::class, 'destroy'])
            ->middleware('api.permission:users.delete');
    });
    
    // Organizations API
    Route::prefix('organizations')->group(function () {
        Route::get('/', [OrganizationApiController::class, 'index'])
            ->middleware('api.permission:organizations.view');
        
        Route::post('/', [OrganizationApiController::class, 'store'])
            ->middleware('api.permission:organizations.create');
    });
});
```

---

## 🔐 أفضل الممارسات الأمنية

### 1. استخدام HTTPS
تأكد من استخدام HTTPS في الإنتاج لحماية API Tokens.

### 2. تدوير API Tokens
قم بتدوير API Tokens بشكل دوري:

```php
$user->api_token = Str::random(64);
$user->save();
```

### 3. IP Whitelisting
أضف قائمة بيضاء للـ IP addresses:

```php
protected function isAllowedIp(string $ip): bool
{
    $allowedIps = config('api.allowed_ips', []);
    return in_array($ip, $allowedIps);
}
```

### 4. Request Signing
استخدم توقيع الطلبات للتحقق من صحتها:

```php
$signature = hash_hmac('sha256', $requestBody, $apiSecret);
```

### 5. CORS Configuration
قم بتكوين CORS بشكل صحيح:

```php
// في config/cors.php
'allowed_origins' => ['https://yourdomain.com'],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
'allowed_headers' => ['Content-Type', 'X-API-Token'],
```

---

## 📊 مراقبة الأداء

### تحليل Logs

```sql
-- أكثر endpoints استخداماً
SELECT path, COUNT(*) as count
FROM api_logs
GROUP BY path
ORDER BY count DESC
LIMIT 10;

-- متوسط وقت الاستجابة
SELECT path, AVG(duration_ms) as avg_duration
FROM api_logs
GROUP BY path
ORDER BY avg_duration DESC;

-- الطلبات الفاشلة
SELECT *
FROM api_logs
WHERE response_status >= 400
ORDER BY created_at DESC;
```

---

## 🎯 الخلاصة

تم توفير نظام Middleware متكامل يوفر:

✅ **الأمان:** مصادقة قوية وتحقق من الصلاحيات  
✅ **الحماية:** تحديد معدل الطلبات ومنع إساءة الاستخدام  
✅ **المراقبة:** تسجيل شامل لجميع الطلبات  
✅ **المرونة:** سهولة التخصيص والتوسع  

---

**Generated by API Generator v3.16.0**  
**SEMOP Team © 2025**
