# 🛡️ Policy Generator v3.31.0 - التوثيق الكامل

## نظرة عامة

**Policy Generator** هو مولد Policies ذكي مدعوم بالذكاء الاصطناعي (Manus AI) لإطار عمل Laravel. يوفر واجهة سهلة الاستخدام لتوليد ملفات Policy احترافية مع دعم أنماط متعددة.

**الإصدار:** v3.31.0  
**المهمة:** 23 من 100  
**التاريخ:** 2025-12-03  
**المؤلف:** Manus AI

---

## 📑 جدول المحتويات

1. [ما هي Policies؟](#ما-هي-policies)
2. [الميزات الرئيسية](#الميزات-الرئيسية)
3. [التثبيت](#التثبيت)
4. [الاستخدام](#الاستخدام)
5. [أنواع Policies المدعومة](#أنواع-policies-المدعومة)
6. [الخيارات المتقدمة](#الخيارات-المتقدمة)
7. [أمثلة عملية](#أمثلة-عملية)
8. [API Reference](#api-reference)
9. [الأسئلة الشائعة](#الأسئلة-الشائعة)

---

## ما هي Policies؟

**Policies** هي فئات (Classes) في Laravel تنظم منطق التفويض (Authorization Logic) حول نموذج (Model) أو مورد (Resource) معين. تستخدم لتحديد من يمكنه القيام بإجراءات معينة على الموارد.

### لماذا نستخدم Policies؟

- ✅ **تنظيم الكود:** فصل منطق التفويض عن Controllers
- ✅ **إعادة الاستخدام:** استخدام نفس Policy في أماكن متعددة
- ✅ **الوضوح:** كود واضح وسهل الفهم
- ✅ **الصيانة:** سهولة تحديث قواعد التفويض

---

## الميزات الرئيسية

### 1. 🤖 دعم الذكاء الاصطناعي (Manus AI)

- توليد Policy بناءً على وصف نصي
- اقتراح أساليب إضافية بناءً على السياق
- توليد تعليقات PHPDoc تلقائياً (عربي/إنجليزي)

### 2. 🎯 أنماط متعددة

- **Resource Policy:** شامل مع جميع الأساليب القياسية
- **Custom Policy:** مخصص بأساليب محددة
- **Role-Based Policy:** قائم على الأدوار والصلاحيات
- **Ownership Policy:** قائم على ملكية المستخدم للمورد

### 3. ⚙️ خيارات متقدمة

- استخدام Response objects أو boolean returns
- دعم before/after filters
- دعم المستخدمين الضيوف (Guest Users)
- دعم Soft Deletes (restore, forceDelete)

### 4. 🎨 واجهة مستخدم احترافية

- تصميم عصري بـ Tailwind CSS
- تفاعلية مع Alpine.js
- معاينة مباشرة للكود
- Syntax highlighting

---

## التثبيت

Policy Generator مدمج بالفعل في النظام ولا يحتاج إلى تثبيت إضافي.

### المتطلبات

- Laravel 10.x أو أحدث
- PHP 8.1 أو أحدث
- Manus AI Client

---

## الاستخدام

### 1. الوصول إلى Policy Generator

افتح المتصفح وانتقل إلى:

```
https://your-domain.com/policy-generator
```

### 2. إنشاء Policy جديد

1. اضغط على "➕ إنشاء Policy جديد"
2. أدخل اسم Policy (مثال: PostPolicy)
3. أدخل اسم النموذج (مثال: Post)
4. اختر نوع Policy
5. قم بتخصيص الخيارات حسب الحاجة
6. اضغط على "👁️ معاينة" لرؤية الكود
7. اضغط على "✨ توليد وحفظ" لحفظ Policy

### 3. استخدام Policy في التطبيق

بعد توليد Policy، يمكنك استخدامه في Controllers:

```php
// في Controller
public function update(Request $request, Post $post)
{
    // استخدام authorize method
    $this->authorize('update', $post);
    
    // أو استخدام can method
    if ($request->user()->cannot('update', $post)) {
        abort(403);
    }
    
    // تحديث Post...
}
```

أو في Blade Templates:

```blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">تعديل</a>
@endcan

@cannot('delete', $post)
    <p>ليس لديك صلاحية حذف هذا المنشور</p>
@endcannot
```

---

## أنواع Policies المدعومة

### 1. 📦 Resource Policy

Policy شامل مع جميع الأساليب القياسية:

- `viewAny()` - عرض قائمة الموارد
- `view()` - عرض مورد واحد
- `create()` - إنشاء مورد جديد
- `update()` - تحديث مورد موجود
- `delete()` - حذف مورد
- `restore()` - استعادة مورد محذوف (Soft Delete)
- `forceDelete()` - حذف نهائي للمورد

**مثال:**

```php
<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('author');
    }

    public function update(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
```

---

### 2. ⚙️ Custom Policy

Policy مخصص بأساليب محددة حسب احتياجاتك.

**متى تستخدمه:**
- عندما لا تحتاج جميع الأساليب القياسية
- عندما تريد أساليب مخصصة فقط

**مثال:**

```php
<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id 
            || $document->is_public;
    }

    public function share(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function download(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.download');
    }
}
```

---

### 3. 👥 Role-Based Policy

Policy يعتمد على الأدوار (Roles) والصلاحيات (Permissions).

**متى تستخدمه:**
- عندما يكون التفويض مبنياً على أدوار المستخدمين
- عندما تحتاج إلى نظام صلاحيات متقدم

**مثال:**

```php
<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'viewer']);
    }

    public function create(User $user): Response
    {
        return $user->hasRole(['admin', 'manager'])
            ? Response::allow()
            : Response::deny('Only admins and managers can create products.');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole(['admin', 'manager']) 
            && $user->hasPermission('products.edit');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }
}
```

---

### 4. 🔑 Ownership Policy

Policy يتحقق من ملكية المستخدم للمورد.

**متى تستخدمه:**
- عندما يجب أن يتمكن المستخدمون من إدارة مواردهم الخاصة فقط
- للتطبيقات التي تعتمد على الملكية الفردية

**مثال:**

```php
<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function update(User $user, Comment $comment): Response
    {
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny('You can only edit your own comments.');
    }

    public function delete(User $user, Comment $comment): Response
    {
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny('You can only delete your own comments.');
    }
}
```

---

## الخيارات المتقدمة

### 1. استخدام Response Objects

بدلاً من إرجاع `true` أو `false`، يمكنك استخدام Response objects لتوفير رسائل خطأ مفصلة:

```php
public function update(User $user, Post $post): Response
{
    return $user->id === $post->user_id
        ? Response::allow()
        : Response::deny('You do not own this post.');
}
```

### 2. Before Filters

يمكنك إضافة `before()` method لتفويض المسؤولين تلقائياً:

```php
public function before(User $user, string $ability): ?bool
{
    if ($user->isAdministrator()) {
        return true;
    }

    return null;
}
```

### 3. دعم Guest Users

يمكنك السماح للمستخدمين الضيوف بالوصول:

```php
public function view(?User $user, Post $post): bool
{
    // السماح للجميع بعرض المنشورات العامة
    if ($post->is_public) {
        return true;
    }

    // المستخدمون المسجلون فقط يمكنهم عرض المنشورات الخاصة
    return $user !== null && $user->id === $post->user_id;
}
```

### 4. Soft Deletes Support

إضافة دعم لـ restore و forceDelete:

```php
public function restore(User $user, Post $post): bool
{
    return $user->id === $post->user_id;
}

public function forceDelete(User $user, Post $post): bool
{
    return $user->isAdministrator();
}
```

---

## أمثلة عملية

### مثال 1: Blog System

```php
// PostPolicy.php
public function publish(User $user, Post $post): Response
{
    if ($user->id !== $post->user_id) {
        return Response::deny('You can only publish your own posts.');
    }

    if (!$user->hasVerifiedEmail()) {
        return Response::deny('You must verify your email before publishing.');
    }

    return Response::allow();
}
```

### مثال 2: E-commerce System

```php
// OrderPolicy.php
public function cancel(User $user, Order $order): Response
{
    if ($user->id !== $order->user_id) {
        return Response::deny('You can only cancel your own orders.');
    }

    if ($order->status === 'shipped') {
        return Response::deny('Cannot cancel shipped orders.');
    }

    return Response::allow();
}
```

### مثال 3: Document Management

```php
// DocumentPolicy.php
public function share(User $user, Document $document): Response
{
    if ($user->id !== $document->owner_id) {
        return Response::deny('Only the document owner can share it.');
    }

    if ($document->is_confidential && !$user->hasRole('manager')) {
        return Response::deny('Confidential documents can only be shared by managers.');
    }

    return Response::allow();
}
```

---

## API Reference

### PolicyGeneratorService

#### `generatePolicy()`

```php
public function generatePolicy(
    string $name,
    string $model,
    string $type,
    array $options = []
): string
```

**المعاملات:**
- `$name` - اسم Policy
- `$model` - اسم النموذج المرتبط
- `$type` - نوع Policy (resource, custom, role_based, ownership)
- `$options` - خيارات إضافية

**الإرجاع:** المسار الكامل للملف المنشأ

---

#### `previewPolicy()`

```php
public function previewPolicy(
    string $name,
    string $model,
    string $type,
    array $options = []
): string
```

**المعاملات:** نفس `generatePolicy()`

**الإرجاع:** محتوى Policy كنص

---

### PolicyGeneratorController

#### `index()`

عرض الصفحة الرئيسية مع قائمة Policies المولدة.

#### `create()`

عرض نموذج إنشاء Policy جديد.

#### `store(PolicyGeneratorRequest $request)`

توليد وحفظ Policy جديد.

#### `preview(Request $request)`

معاينة محتوى Policy قبل الحفظ.

#### `download(string $name)`

تحميل ملف Policy.

---

## الأسئلة الشائعة

### س: كيف أسجل Policy يدوياً؟

**ج:** في `AuthServiceProvider`:

```php
protected $policies = [
    Post::class => PostPolicy::class,
];
```

### س: كيف أستخدم Policy في API؟

**ج:** نفس الطريقة، لكن سيتم إرجاع JSON تلقائياً:

```php
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    // سيرجع 403 JSON إذا فشل التفويض
}
```

### س: هل يمكنني استخدام Policies مع Gates؟

**ج:** نعم، يمكنك الجمع بينهما:

```php
Gate::define('view-dashboard', function (User $user) {
    return $user->isAdministrator();
});
```

### س: كيف أختبر Policies؟

**ج:** استخدم `Gate::allows()` في الاختبارات:

```php
public function test_user_can_update_own_post()
{
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->assertTrue(Gate::forUser($user)->allows('update', $post));
}
```

---

## الدعم والمساعدة

للحصول على المساعدة أو الإبلاغ عن مشاكل:

- 📧 البريد الإلكتروني: support@manus.ai
- 🌐 الموقع: https://manus.ai
- 📚 التوثيق: https://docs.manus.ai

---

## الترخيص

Policy Generator v3.31.0 © 2025 Manus AI. جميع الحقوق محفوظة.

---

**آخر تحديث:** 2025-12-03  
**الإصدار:** v3.31.0  
**المؤلف:** Manus AI
