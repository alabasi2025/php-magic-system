# تصميم Resource Generator v3.30.0 - مولد API Resources الذكي

**الإصدار:** v3.30.0  
**تاريخ الإنشاء:** 2025-12-03  
**المؤلف:** Manus AI  
**الحالة:** قيد التطوير  
**المهمة:** 22/100

---

## 📑 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [الفلسفة والرؤية](#الفلسفة-والرؤية)
3. [المعمارية العامة](#المعمارية-العامة)
4. [الميزات الأساسية](#الميزات-الأساسية)
5. [الميزات المتقدمة](#الميزات-المتقدمة)
6. [بنية الملفات](#بنية-الملفات)
7. [واجهات التوليد](#واجهات-التوليد)
8. [التكامل مع الذكاء الاصطناعي](#التكامل-مع-الذكاء-الاصطناعي)
9. [خطة التنفيذ](#خطة-التنفيذ)

---

<a name="نظرة-عامة"></a>
## 1. نظرة عامة

**Resource Generator** هو مولد ذكي ومتقدم لإنشاء API Resources في Laravel بشكل آلي ومتطور. يهدف إلى تسريع عملية تطوير APIs وتوحيد معايير تنسيق البيانات وتقليل الأخطاء البشرية من خلال توليد Resource Classes كاملة ومتكاملة.

### الأهداف الرئيسية

1. **الأتمتة الكاملة**: توليد API Resources بشكل آلي من Models أو JSON Schema
2. **الذكاء والتكيف**: استخدام AI لتحليل البيانات وتوليد Resources ذكية
3. **التوحيد والمعايير**: ضمان اتباع معايير موحدة في جميع Resources المولدة
4. **المرونة والتوسع**: دعم أنماط متعددة من Resources (Single, Collection, Nested)
5. **التوثيق التلقائي**: توليد PHPDoc كامل وشامل لكل Resource

### المدخلات المدعومة

- **Model موجود (Model-Based)**: توليد من Model موجود مع جميع العلاقات
- **JSON Schema**: مخطط JSON متكامل للـ Resource
- **وصف نصي (Text Description)**: وصف بسيط باللغة الطبيعية
- **Database Table**: توليد مباشر من جدول قاعدة البيانات
- **AI-Powered Generation**: توليد ذكي باستخدام الذكاء الاصطناعي

### المخرجات المدعومة

- **Single Resource**: تنسيق عنصر واحد (UserResource)
- **Collection Resource**: تنسيق مجموعة من العناصر (UserCollection)
- **Nested Resources**: Resources متداخلة مع العلاقات
- **Conditional Attributes**: خصائص شرطية حسب الصلاحيات
- **Paginated Resources**: دعم Pagination مع Metadata

---

<a name="الفلسفة-والرؤية"></a>
## 2. الفلسفة والرؤية

### 2.1 - Resource كوحدة تنسيق ذكية

API Resource ليس مجرد ملف PHP، بل هو **وحدة تنسيق ذكية (Smart Formatting Unit)** تتحكم في كيفية عرض البيانات. يجب أن يكون:

- **واضحاً (Clear)**: يعرض البيانات بشكل واضح ومنظم
- **آمناً (Secure)**: يخفي البيانات الحساسة (passwords, tokens)
- **مرناً (Flexible)**: يدعم تنسيقات مختلفة حسب السياق
- **موثقاً (Documented)**: كل خاصية موثقة بشكل كامل
- **قابلاً للتوسع (Extensible)**: سهل الإضافة والتعديل

### 2.2 - معايير التوليد

يجب أن يتبع كل Resource مولد المعايير التالية:

1. **PSR-12 Compliance**: اتباع معايير PHP
2. **Laravel Best Practices**: اتباع أفضل ممارسات Laravel
3. **Complete PHPDoc**: توثيق كامل لكل خاصية
4. **Type Hints**: استخدام Type Hints في كل مكان
5. **Consistent Naming**: تسمية موحدة ومتسقة (camelCase للـ JSON)
6. **RESTful Conventions**: اتباع معايير REST للـ APIs
7. **Security First**: إخفاء البيانات الحساسة تلقائياً

### 2.3 - الرؤية المستقبلية

- **AI-Powered Resources**: Resources ذكية تتعلم من الأنماط
- **Auto-Optimization**: تحسين تلقائي للأداء
- **Smart Filtering**: فلترة ذكية للبيانات حسب المستخدم
- **Dynamic Attributes**: خصائص ديناميكية تتكيف مع السياق
- **Versioning Support**: دعم إصدارات متعددة من API

---

<a name="المعمارية-العامة"></a>
## 3. المعمارية العامة

### 3.1 - المكونات الرئيسية

```
ResourceGenerator/
├── Services/
│   ├── ResourceGeneratorService.php        # الخدمة الرئيسية
│   ├── ResourceParserService.php           # محلل المدخلات
│   ├── ResourceBuilderService.php          # بناء المحتوى
│   ├── ResourceValidatorService.php        # التحقق من الصحة
│   └── ResourceAIService.php               # التكامل مع AI
├── Controllers/
│   └── ResourceGeneratorController.php     # واجهة التحكم
├── Models/
│   ├── ResourceGeneration.php              # سجل التوليد
│   └── ResourceTemplate.php                # القوالب
├── Templates/
│   ├── SingleResource.stub                 # Single Resource
│   ├── CollectionResource.stub             # Collection Resource
│   └── NestedResource.stub                 # Nested Resource
└── Traits/
    ├── HasResourceGeneration.php           # دعم التوليد
    └── HasConditionalAttributes.php        # دعم الخصائص الشرطية
```

### 3.2 - تدفق العمل (Workflow)

```
[Input] → [Parser] → [Validator] → [AI Enhancement] → [Builder] → [Generator] → [Output]
   ↓         ↓           ↓              ↓                ↓            ↓           ↓
 Model/    Analyze    Validate      Enhance          Build        Generate     Files:
 JSON/     Input      Schema        with AI         Content       Code         - Resource
 Text      Data       Rules         Suggestions     Structure     Files        - Collection
                                                                                - Tests
```

### 3.3 - الأنماط المدعومة

| النمط | الوصف | الاستخدام |
|------|-------|----------|
| **Single Resource** | تنسيق عنصر واحد | عرض تفاصيل مستخدم، منتج، إلخ |
| **Collection Resource** | تنسيق مجموعة | قوائم، Pagination |
| **Nested Resource** | Resources متداخلة | عرض العلاقات (User مع Posts) |
| **Conditional Resource** | خصائص شرطية | حسب الصلاحيات والسياق |

---

<a name="الميزات-الأساسية"></a>
## 4. الميزات الأساسية

### 4.1 - توليد Single Resource من Model

**المدخل:**
```php
Model: User
Attributes: id, name, email, created_at, updated_at
Relations: posts, comments
```

**النتيجة:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Resource
 * تنسيق بيانات المستخدم للـ API
 * 
 * @package App\Http\Resources
 * @version v3.30.0
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * تحويل المورد إلى مصفوفة
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
            
            // Relations (loaded conditionally)
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
```

### 4.2 - توليد Collection Resource

**النتيجة:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * User Collection Resource
 * تنسيق مجموعة المستخدمين للـ API
 * 
 * @package App\Http\Resources
 * @version v3.30.0
 */
class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * تحويل مجموعة الموارد إلى مصفوفة
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'perPage' => $this->perPage(),
                'currentPage' => $this->currentPage(),
                'totalPages' => $this->lastPage(),
            ],
            'links' => [
                'self' => $request->url(),
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
```

### 4.3 - Conditional Attributes (خصائص شرطية)

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        
        // Show only to authenticated users
        'phone' => $this->when($request->user(), $this->phone),
        
        // Show only to admins
        'isAdmin' => $this->when($request->user()?->isAdmin(), $this->is_admin),
        
        // Show only if exists
        'avatar' => $this->when($this->avatar, $this->avatar),
        
        // Merge additional data conditionally
        $this->mergeWhen($request->user()?->isAdmin(), [
            'createdBy' => $this->created_by,
            'lastLogin' => $this->last_login_at,
        ]),
    ];
}
```

### 4.4 - Nested Resources (Resources متداخلة)

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'content' => $this->content,
        
        // Nested resources
        'author' => new UserResource($this->whenLoaded('author')),
        'category' => new CategoryResource($this->whenLoaded('category')),
        'tags' => TagResource::collection($this->whenLoaded('tags')),
        'comments' => CommentResource::collection($this->whenLoaded('comments')),
        
        // Counts
        'commentsCount' => $this->when(
            $this->relationLoaded('comments'),
            $this->comments_count ?? $this->comments->count()
        ),
    ];
}
```

---

<a name="الميزات-المتقدمة"></a>
## 5. الميزات المتقدمة

### 5.1 - توليد ذكي بالـ AI

يستخدم النظام الذكاء الاصطناعي لـ:

- **تحليل Model**: فهم العلاقات والخصائص تلقائياً
- **اقتراح التنسيق**: اقتراح أفضل طريقة لعرض البيانات
- **إخفاء البيانات الحساسة**: كشف وإخفاء البيانات الحساسة تلقائياً
- **توليد Documentation**: توثيق شامل لكل خاصية
- **Optimization**: تحسين الأداء بتجنب N+1 queries

### 5.2 - دعم Relationships

```php
// Eager Loading Detection
'posts' => PostResource::collection($this->whenLoaded('posts')),

// Pivot Data
'role' => $this->whenPivotLoaded('role_user', function () {
    return $this->pivot->role;
}),

// Counts
'postsCount' => $this->posts_count,
```

### 5.3 - Custom Attributes

```php
// Computed attributes
'fullName' => $this->first_name . ' ' . $this->last_name,
'age' => $this->birth_date?->age,
'isActive' => $this->status === 'active',

// Formatted dates
'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
'createdAtHuman' => $this->created_at?->diffForHumans(),
```

### 5.4 - API Versioning

```php
// v1/UserResource.php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
    ];
}

// v2/UserResource.php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'firstName' => $this->first_name,
        'lastName' => $this->last_name,
        'email' => $this->email,
    ];
}
```

### 5.5 - Response Wrapping

```php
// Custom wrapper
public static $wrap = 'user';

// Result: {"user": {...}}

// Or disable wrapping
public static $wrap = null;
```

---

<a name="بنية-الملفات"></a>
## 6. بنية الملفات

### 6.1 - الملفات المطلوبة

```
app/
├── Services/
│   └── ResourceGeneratorService.php
├── Http/
│   ├── Controllers/
│   │   └── ResourceGeneratorController.php
│   └── Resources/
│       └── (Generated Resources here)
├── Models/
│   └── ResourceGeneration.php
database/
└── migrations/
    └── 2025_12_03_create_resource_generations_table.php
resources/
└── views/
    └── resource-generator/
        ├── index.blade.php
        ├── create.blade.php
        └── show.blade.php
routes/
└── resource_generator.php
tests/
└── Feature/
    └── ResourceGeneratorTest.php
```

### 6.2 - قاعدة البيانات

```php
Schema::create('resource_generations', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // UserResource
    $table->string('type'); // single, collection, nested
    $table->string('model')->nullable(); // User
    $table->json('attributes'); // ['id', 'name', 'email']
    $table->json('relations')->nullable(); // ['posts', 'comments']
    $table->json('options')->nullable(); // Additional options
    $table->text('file_path'); // app/Http/Resources/UserResource.php
    $table->text('content'); // Generated content
    $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
    $table->text('error_message')->nullable();
    $table->timestamps();
});
```

---

<a name="واجهات-التوليد"></a>
## 7. واجهات التوليد

### 7.1 - واجهة الويب

صفحة رئيسية تحتوي على:

- **نموذج التوليد**: اختيار Model أو JSON Schema
- **خيارات متقدمة**: Relations, Conditional Attributes, Pagination
- **معاينة**: معاينة الكود قبل التوليد
- **سجل التوليد**: جميع Resources المولدة سابقاً

### 7.2 - API Endpoints

```php
POST   /api/resource-generator/generate
GET    /api/resource-generator/list
GET    /api/resource-generator/{id}
DELETE /api/resource-generator/{id}
POST   /api/resource-generator/preview
```

### 7.3 - Artisan Command

```bash
# Generate from model
php artisan generate:resource User

# Generate with options
php artisan generate:resource User --type=collection --relations=posts,comments

# Generate from JSON
php artisan generate:resource User --schema=schema.json

# AI-powered generation
php artisan generate:resource User --ai
```

---

<a name="التكامل-مع-الذكاء-الاصطناعي"></a>
## 8. التكامل مع الذكاء الاصطناعي

### 8.1 - استخدامات AI

1. **تحليل Model**: فهم البنية والعلاقات
2. **اقتراح Attributes**: اقتراح أفضل الخصائص للعرض
3. **كشف البيانات الحساسة**: تحديد البيانات التي يجب إخفاؤها
4. **توليد Documentation**: توثيق شامل وواضح
5. **Optimization**: اقتراحات لتحسين الأداء

### 8.2 - AI Prompts

```
Analyze the User model and generate an API Resource that:
1. Includes all safe attributes (exclude password, tokens)
2. Formats dates to ISO 8601
3. Includes relations (posts, comments) conditionally
4. Adds computed attributes (fullName, age)
5. Implements proper PHPDoc comments
6. Follows Laravel best practices
```

---

<a name="خطة-التنفيذ"></a>
## 9. خطة التنفيذ

### المرحلة 1: البنية الأساسية (10 دقائق)

- ✅ إنشاء ResourceGeneratorService
- ✅ إنشاء ResourceGeneratorController
- ✅ إنشاء Model: ResourceGeneration
- ✅ إنشاء Migration

### المرحلة 2: المنطق الأساسي (10 دقائق)

- ✅ دالة توليد Single Resource
- ✅ دالة توليد Collection Resource
- ✅ دالة تحليل Model
- ✅ دالة كتابة الملفات

### المرحلة 3: التكامل مع AI (5 دقائق)

- ✅ دمج ManusAIClient
- ✅ Prompts للتوليد الذكي
- ✅ معالجة الأخطاء

### المرحلة 4: الواجهات (10 دقائق)

- ✅ صفحة index.blade.php
- ✅ صفحة create.blade.php
- ✅ صفحة show.blade.php
- ✅ Routes

### المرحلة 5: الاختبار والتوثيق (5 دقائق)

- ✅ اختبارات Feature
- ✅ التوثيق
- ✅ CHANGELOG

**الوقت الإجمالي:** 40 دقيقة

---

## 🎯 معايير النجاح

- ✅ توليد Single Resource بنجاح
- ✅ توليد Collection Resource بنجاح
- ✅ دعم Relations و Conditional Attributes
- ✅ تكامل كامل مع AI
- ✅ واجهة مستخدم سهلة وواضحة
- ✅ توثيق شامل
- ✅ اختبارات ناجحة 100%

---

**الإصدار:** v3.30.0  
**التاريخ:** 2025-12-03  
**المطور:** Manus AI  
**الحالة:** 📋 جاهز للتنفيذ
