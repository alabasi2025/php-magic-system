# تحليل متطلبات Policy Generator v3.31.0

## نظرة عامة

**المهمة:** 23 من 100
**الإصدار:** v3.31.0
**التاريخ:** 2025-12-03
**الوقت المقدر:** 30 دقيقة

## الهدف

تطوير مولد Policies ذكي مدعوم بالذكاء الاصطناعي (Manus AI) لإطار عمل Laravel، يوفر واجهة سهلة الاستخدام لتوليد ملفات Policy احترافية مع دعم أنماط متعددة.

---

## فهم Laravel Policies

### ما هي Policies؟

Policies هي فئات (Classes) تنظم منطق التفويض (Authorization Logic) حول نموذج (Model) أو مورد (Resource) معين. تستخدم لتحديد من يمكنه القيام بإجراءات معينة على الموارد.

### البنية الأساسية

```php
<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine if the given post can be viewed by the user.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine if the given post can be updated by the user.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine if the given post can be deleted by the user.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
```

### الأساليب (Methods) القياسية في Policy

1. **viewAny** - عرض قائمة الموارد
2. **view** - عرض مورد واحد
3. **create** - إنشاء مورد جديد
4. **update** - تحديث مورد موجود
5. **delete** - حذف مورد
6. **restore** - استعادة مورد محذوف (Soft Delete)
7. **forceDelete** - حذف نهائي للمورد

### أنماط Policy المدعومة

#### 1. **Model-Based Policy**
Policy مرتبط بنموذج Eloquent محدد (مثل: PostPolicy للـ Post Model)

#### 2. **Resource Policy**
Policy شامل مع جميع الأساليب القياسية (viewAny, view, create, update, delete, restore, forceDelete)

#### 3. **Custom Policy**
Policy مخصص بأساليب محددة حسب الحاجة

#### 4. **Role-Based Policy**
Policy يعتمد على الأدوار (Roles) والصلاحيات (Permissions)

#### 5. **Ownership Policy**
Policy يتحقق من ملكية المستخدم للمورد

---

## المكونات المطلوبة

### 1. PolicyGeneratorService

**المسار:** `app/Services/PolicyGeneratorService.php`

**الوظائف الرئيسية:**
- `generatePolicy(string $name, string $model, array $options)` - توليد Policy جديد
- `previewPolicy(string $name, string $model, array $options)` - معاينة Policy قبل الحفظ
- `generateResourcePolicy()` - توليد Policy شامل
- `generateCustomPolicy()` - توليد Policy مخصص
- `generateRoleBasedPolicy()` - توليد Policy قائم على الأدوار
- `generateOwnershipPolicy()` - توليد Policy قائم على الملكية

**الثوابت:**
```php
public const TYPE_RESOURCE = 'resource';
public const TYPE_CUSTOM = 'custom';
public const TYPE_ROLE_BASED = 'role_based';
public const TYPE_OWNERSHIP = 'ownership';
```

**الخيارات (Options):**
- `model` - اسم النموذج المرتبط
- `methods` - قائمة الأساليب المطلوبة
- `roles` - الأدوار المسموح بها (للـ Role-Based)
- `permissions` - الصلاحيات المطلوبة
- `ownership_field` - حقل الملكية (مثل: user_id)
- `use_responses` - استخدام Response objects بدلاً من boolean
- `include_filters` - تضمين before/after filters
- `guest_support` - دعم المستخدمين الضيوف

---

### 2. PolicyGeneratorController

**المسار:** `app/Http/Controllers/PolicyGeneratorController.php`

**الأساليب (Methods):**
- `index()` - عرض الصفحة الرئيسية
- `create()` - عرض نموذج الإنشاء
- `store(PolicyGeneratorRequest $request)` - حفظ Policy جديد
- `preview(Request $request)` - معاينة Policy
- `download($id)` - تحميل ملف Policy
- `list()` - قائمة Policies المولدة

---

### 3. PolicyGeneratorRequest

**المسار:** `app/Http/Requests/PolicyGeneratorRequest.php`

**قواعد التحقق (Validation Rules):**
```php
'name' => 'required|string|max:255',
'model' => 'required|string|max:255',
'type' => 'required|in:resource,custom,role_based,ownership',
'methods' => 'nullable|array',
'methods.*' => 'in:viewAny,view,create,update,delete,restore,forceDelete',
'roles' => 'nullable|array',
'permissions' => 'nullable|array',
'ownership_field' => 'nullable|string',
'use_responses' => 'nullable|boolean',
'include_filters' => 'nullable|boolean',
'guest_support' => 'nullable|boolean',
```

---

### 4. Views

**المسار:** `resources/views/policy-generator/`

#### index.blade.php
- عرض قائمة Policies المولدة
- إحصائيات (عدد Policies، الأنواع، إلخ)
- إجراءات سريعة (Quick Actions)

#### create.blade.php
- نموذج إنشاء Policy جديد
- اختيار النوع (Resource, Custom, Role-Based, Ownership)
- خيارات متقدمة
- معاينة مباشرة للكود

**التصميم:**
- استخدام Tailwind CSS
- Alpine.js للتفاعلية
- أيقونات تعبيرية (🛡️ للـ Policies)
- دعم RTL للعربية

---

### 5. Routes

**المسار:** `routes/policy_generator.php`

```php
// Web Routes
Route::prefix('policy-generator')->name('policy-generator.')->group(function () {
    Route::get('/', [PolicyGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [PolicyGeneratorController::class, 'create'])->name('create');
    Route::post('/store', [PolicyGeneratorController::class, 'store'])->name('store');
    Route::post('/preview', [PolicyGeneratorController::class, 'preview'])->name('preview');
    Route::get('/download/{id}', [PolicyGeneratorController::class, 'download'])->name('download');
});

// API Routes
Route::prefix('api/policy-generator')->name('api.policy-generator.')->group(function () {
    Route::get('/list', [PolicyGeneratorController::class, 'list'])->name('list');
    Route::post('/generate', [PolicyGeneratorController::class, 'store'])->name('generate');
});
```

**تضمين في web.php:**
```php
// Policy Generator Routes (v3.31.0)
require __DIR__."/policy_generator.php";
```

---

### 6. Exception

**المسار:** `app/Exceptions/PolicyGenerationException.php`

```php
<?php

namespace App\Exceptions;

use Exception;

class PolicyGenerationException extends Exception
{
    //
}
```

---

## ميزات إضافية

### 1. دعم الذكاء الاصطناعي (Manus AI)

- توليد Policy بناءً على وصف نصي
- اقتراح أساليب إضافية بناءً على السياق
- توليد تعليقات PHPDoc تلقائياً (عربي/إنجليزي)

### 2. التكامل مع Laravel

- تسجيل تلقائي للـ Policies في `AuthServiceProvider`
- دعم Policy Discovery
- دعم `UsePolicy` Attribute

### 3. أمثلة جاهزة (Templates)

- PostPolicy (مثال للمدونة)
- ProductPolicy (مثال للتجارة الإلكترونية)
- DocumentPolicy (مثال للمستندات)
- UserPolicy (مثال لإدارة المستخدمين)

### 4. الاختبارات

- اختبار توليد Resource Policy
- اختبار توليد Custom Policy
- اختبار Role-Based Policy
- اختبار Ownership Policy
- اختبار معاينة Policy

---

## خطة التنفيذ

### المرحلة 1: إنشاء الهيكل الأساسي (10 دقائق)
1. إنشاء `PolicyGeneratorService`
2. إنشاء `PolicyGeneratorController`
3. إنشاء `PolicyGeneratorRequest`
4. إنشاء `PolicyGenerationException`

### المرحلة 2: تطوير الـ Views (10 دقائق)
1. إنشاء `index.blade.php`
2. إنشاء `create.blade.php`
3. تصميم الواجهة بـ Tailwind CSS

### المرحلة 3: إنشاء Routes (2 دقيقة)
1. إنشاء `policy_generator.php`
2. تضمينه في `web.php`

### المرحلة 4: الاختبار (5 دقائق)
1. اختبار توليد Policies
2. اختبار المعاينة
3. اختبار التحميل

### المرحلة 5: التوثيق (3 دقائق)
1. إنشاء ملف التوثيق
2. تحديث TIMELINE_100_TASKS.md

---

## معايير الجودة

### 1. الكود
- ✅ PSR-12 Coding Standards
- ✅ Type Hints كاملة
- ✅ PHPDoc Comments (عربي/إنجليزي)
- ✅ معالجة الأخطاء الشاملة

### 2. الأمان
- ✅ Validation شامل
- ✅ CSRF Protection
- ✅ Sanitization للمدخلات

### 3. الأداء
- ✅ Caching للـ Templates
- ✅ Lazy Loading للـ AI Client
- ✅ Efficient File Operations

### 4. التوثيق
- ✅ توثيق كامل للـ API
- ✅ أمثلة واضحة
- ✅ دليل المستخدم

---

## الملفات المطلوبة

1. ✅ `app/Services/PolicyGeneratorService.php`
2. ✅ `app/Http/Controllers/PolicyGeneratorController.php`
3. ✅ `app/Http/Requests/PolicyGeneratorRequest.php`
4. ✅ `app/Exceptions/PolicyGenerationException.php`
5. ✅ `resources/views/policy-generator/index.blade.php`
6. ✅ `resources/views/policy-generator/create.blade.php`
7. ✅ `routes/policy_generator.php`
8. ✅ `POLICY_GENERATOR_DOCUMENTATION.md`
9. ✅ `POLICY_GENERATOR_TEST_REPORT.md`

---

## الخلاصة

Policy Generator v3.31.0 سيكون أداة قوية ومرنة لتوليد ملفات Policy في Laravel، مع دعم كامل للذكاء الاصطناعي وواجهة مستخدم احترافية. سيتبع نفس الأنماط المستخدمة في المولدات الأخرى (Middleware, Controller, Migration) لضمان الاتساق والجودة.

**الوقت الإجمالي المقدر:** 30 دقيقة
**الحالة:** جاهز للتنفيذ ✅
