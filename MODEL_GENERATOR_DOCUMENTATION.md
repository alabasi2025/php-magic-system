# 🧬 Model Generator v3.26.0 - دليل المستخدم الشامل

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [التثبيت والإعداد](#التثبيت-والإعداد)
3. [طرق التوليد](#طرق-التوليد)
4. [واجهة الويب](#واجهة-الويب)
5. [واجهة CLI](#واجهة-cli)
6. [واجهة API](#واجهة-api)
7. [التكامل مع AI](#التكامل-مع-ai)
8. [القوالب (Templates)](#القوالب-templates)
9. [الاختبار والتحقق](#الاختبار-والتحقق)
10. [أمثلة عملية](#أمثلة-عملية)
11. [الأسئلة الشائعة](#الأسئلة-الشائعة)

---

## 🎯 نظرة عامة

**Model Generator v3.26.0** هو نظام متقدم لتوليد Eloquent Models في Laravel بشكل ذكي وآلي. يدعم النظام 5 طرق مختلفة للتوليد ويتكامل مع الذكاء الاصطناعي لتحسين النتائج.

### ✨ الميزات الرئيسية

- ✅ **5 طرق توليد مختلفة**: Text, JSON, Database, Migration, AI
- ✅ **تكامل كامل مع OpenAI**: لتحسين وتحليل Models
- ✅ **دعم جميع أنواع العلاقات**: hasOne, hasMany, belongsTo, belongsToMany, Polymorphic
- ✅ **توليد تلقائي**: Scopes, Observers, Factories, Seeders, Policies
- ✅ **توثيق PHPDoc شامل**: لجميع الخصائص والدوال
- ✅ **التحقق من الصحة**: Syntax validation & Best practices check
- ✅ **3 واجهات استخدام**: Web UI, CLI Commands, REST API
- ✅ **نظام القوالب**: قوالب جاهزة وقابلة للتخصيص
- ✅ **إحصائيات متقدمة**: تتبع الاستخدام والنجاح

---

## 🔧 التثبيت والإعداد

### 1. تشغيل Migrations

```bash
php artisan migrate
```

سيتم إنشاء الجداول التالية:
- `model_generations` - سجلات التوليد
- `model_templates` - القوالب

### 2. إعداد OpenAI (اختياري)

أضف API Key في ملف `.env`:

```env
OPENAI_API_KEY=sk-your-api-key-here
```

### 3. تسجيل Routes

أضف في `routes/web.php`:

```php
require __DIR__.'/model-generator.php';
```

### 4. تسجيل Command

تأكد من تسجيل Command في `app/Console/Kernel.php`:

```php
protected $commands = [
    \App\Console\Commands\GenerateModelCommand::class,
];
```

---

## 📝 طرق التوليد

### 1️⃣ من وصف نصي (Text Description)

**الاستخدام:**

```php
use App\Services\ModelGeneratorService;

$service = new ModelGeneratorService();
$generation = $service->generateFromText("
Model للمنتج (Product)
- الاسم (name) نص مطلوب
- الوصف (description) نص اختياري
- السعر (price) decimal مطلوب
- الكمية (quantity) integer default: 0
- نشط (is_active) boolean default: true
- belongsTo مع Category
- hasMany مع OrderItem
- soft delete
- scope: active where is_active = true
");
```

**الميزات:**
- ✅ دعم اللغة العربية والإنجليزية
- ✅ استخراج تلقائي للخصائص والأنواع
- ✅ كشف العلاقات من الوصف
- ✅ كشف Traits (SoftDeletes, HasFactory)
- ✅ كشف Scopes

---

### 2️⃣ من JSON Schema

**الاستخدام:**

```php
$schema = [
    'name' => 'Product',
    'description' => 'نموذج المنتج',
    'table' => 'products',
    'namespace' => 'App\\Models',
    'attributes' => [
        ['name' => 'name', 'type' => 'string', 'nullable' => false],
        ['name' => 'description', 'type' => 'text', 'nullable' => true],
        ['name' => 'price', 'type' => 'decimal', 'nullable' => false],
        ['name' => 'quantity', 'type' => 'integer', 'nullable' => false],
        ['name' => 'is_active', 'type' => 'boolean', 'nullable' => false],
    ],
    'fillable' => ['name', 'description', 'price', 'quantity', 'is_active'],
    'hidden' => [],
    'casts' => [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ],
    'relations' => [
        ['type' => 'belongsTo', 'model' => 'Category', 'method' => 'category'],
        ['type' => 'hasMany', 'model' => 'OrderItem', 'method' => 'orderItems'],
    ],
    'scopes' => [
        ['name' => 'active', 'condition' => 'is_active = true'],
    ],
    'traits' => ['HasFactory', 'SoftDeletes'],
    'timestamps' => true,
    'soft_deletes' => true,
    'observer' => false,
    'factory' => true,
    'seeder' => false,
    'policy' => false,
];

$generation = $service->generateFromJson($schema);
```

**الميزات:**
- ✅ تحكم كامل في جميع التفاصيل
- ✅ مناسب للأتمتة
- ✅ سهل التكامل مع أنظمة خارجية

---

### 3️⃣ من قاعدة البيانات (Reverse Engineering)

**الاستخدام:**

```php
// توليد Model من جدول واحد
$generation = $service->generateFromDatabase('products');

// توليد Models لجميع الجداول
$results = $service->generateAllFromDatabase();
```

**الميزات:**
- ✅ قراءة بنية الجدول تلقائياً
- ✅ كشف أنواع البيانات
- ✅ كشف Foreign Keys
- ✅ كشف Timestamps & SoftDeletes
- ✅ توليد Casts تلقائياً

---

### 4️⃣ من ملف Migration

**الاستخدام:**

```php
$generation = $service->generateFromMigration('2025_12_03_000001_create_products_table.php');
```

**الميزات:**
- ✅ تحليل Migration file
- ✅ استخراج الأعمدة والأنواع
- ✅ كشف Indexes & Foreign Keys

---

### 5️⃣ باستخدام AI

**الاستخدام:**

```php
use App\Services\ModelAIService;

$aiService = new ModelAIService();

// تحسين وصف نصي
$enhanced = $aiService->enhanceDescription("Model للعميل مع بيانات الاتصال");

// اقتراح علاقات
$relations = $aiService->suggestRelations($generation);

// اقتراح Scopes
$scopes = $aiService->suggestScopes($generation);

// تحليل وتقديم اقتراحات
$analysis = $aiService->analyzeAndSuggest($generation);
```

**الميزات:**
- ✅ تحسين الوصف النصي
- ✅ اقتراح علاقات ذكية
- ✅ اقتراح Scopes مفيدة
- ✅ اقتراح Accessors & Mutators
- ✅ تحليل Best Practices

---

## 🌐 واجهة الويب

### الصفحة الرئيسية

```
/model-generator
```

**الميزات:**
- 📊 عرض جميع Generations
- 📈 إحصائيات شاملة
- 🔍 بحث وتصفية
- 📝 إنشاء Generation جديد

### صفحة الإنشاء

```
/model-generator/create
```

**الخطوات:**
1. اختر طريقة التوليد
2. أدخل البيانات المطلوبة
3. معاينة النتيجة
4. التحقق من الصحة
5. النشر

### صفحة التفاصيل

```
/model-generator/{id}
```

**الميزات:**
- 👁️ عرض المحتوى المولد
- ✅ التحقق من الصحة
- 🚀 النشر إلى نظام الملفات
- 📝 تعديل المحتوى
- 🗑️ حذف

---

## 💻 واجهة CLI

### الأمر الأساسي

```bash
php artisan generate:model
```

سيعرض قائمة تفاعلية لاختيار طريقة التوليد.

### من وصف نصي

```bash
php artisan generate:model --text="Model للمنتج مع اسم وسعر"
```

### من JSON

```bash
php artisan generate:model --json=/path/to/schema.json
```

### من جدول قاعدة البيانات

```bash
php artisan generate:model --table=products
```

### من Migration

```bash
php artisan generate:model --migration=2025_12_03_000001_create_products_table.php
```

### من جميع الجداول

```bash
php artisan generate:model --all
```

### مع التحقق والنشر

```bash
php artisan generate:model --table=products --validate --deploy
```

### أمثلة متقدمة

```bash
# توليد من وصف نصي مع نشر مباشر
php artisan generate:model \
  --text="Model للعميل مع الاسم والبريد والهاتف" \
  --deploy

# توليد من JSON مع التحقق
php artisan generate:model \
  --json=schema.json \
  --validate

# توليد جميع Models من قاعدة البيانات
php artisan generate:model --all
```

---

## 🔌 واجهة API

### Base URL

```
/api/model-generator
```

### Endpoints

#### 1. توليد من وصف نصي

```http
POST /api/model-generator/generate/text
Content-Type: application/json

{
  "description": "Model للمنتج مع اسم وسعر"
}
```

**Response:**

```json
{
  "success": true,
  "message": "تم توليد Model بنجاح",
  "data": {
    "generation": {
      "id": 1,
      "name": "Product",
      "table_name": "products",
      "status": "generated",
      ...
    },
    "content": "<?php\n\nnamespace App\\Models;\n..."
  }
}
```

#### 2. توليد من JSON Schema

```http
POST /api/model-generator/generate/json
Content-Type: application/json

{
  "schema": {
    "name": "Product",
    "table": "products",
    "attributes": [...]
  }
}
```

#### 3. توليد من قاعدة البيانات

```http
POST /api/model-generator/generate/database
Content-Type: application/json

{
  "table_name": "products"
}
```

#### 4. الحصول على إحصائيات

```http
GET /api/model-generator/statistics
```

**Response:**

```json
{
  "success": true,
  "data": {
    "total": 150,
    "draft": 10,
    "generated": 80,
    "validated": 40,
    "deployed": 15,
    "failed": 5,
    "with_ai": 30,
    "by_input_method": {
      "text": 50,
      "json": 40,
      "database": 30,
      "migration": 20,
      "ai": 10
    }
  }
}
```

---

## 🤖 التكامل مع AI

### إعداد OpenAI

```env
OPENAI_API_KEY=sk-your-api-key-here
```

### استخدام AI Service

```php
use App\Services\ModelAIService;

$aiService = new ModelAIService();

// التحقق من توفر AI
if ($aiService->isAvailable()) {
    // تحسين الوصف
    $enhanced = $aiService->enhanceDescription($description);
    
    // اقتراح علاقات
    $relations = $aiService->suggestRelations($generation);
    
    // اقتراح Scopes
    $scopes = $aiService->suggestScopes($generation);
    
    // تحليل Model
    $analysis = $aiService->analyzeAndSuggest($generation);
}
```

### مثال: تحسين Model باستخدام AI

```php
// 1. توليد Model أساسي
$generation = $service->generateFromText("Model للعميل");

// 2. الحصول على اقتراحات AI
$aiSuggestions = $aiService->analyzeAndSuggest($generation);

// 3. تطبيق الاقتراحات
$generation->update([
    'ai_suggestions' => $aiSuggestions,
    'relations' => $aiSuggestions['suggested_relations'] ?? [],
    'scopes' => $aiSuggestions['suggested_scopes'] ?? [],
]);

// 4. إعادة توليد المحتوى
$content = $builder->buildModelContent($generation);
$generation->update(['generated_content' => $content]);
```

---

## 📋 القوالب (Templates)

### إنشاء قالب جديد

```php
use App\Models\ModelTemplate;

$template = ModelTemplate::create([
    'name' => 'E-commerce Product',
    'slug' => 'ecommerce-product',
    'description' => 'قالب لنموذج المنتج في التجارة الإلكترونية',
    'category' => 'ecommerce',
    'icon' => '🛒',
    'template_content' => $templateContent,
    'template_variables' => ['name', 'table_name'],
    'default_traits' => ['HasFactory', 'SoftDeletes'],
    'default_casts' => [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ],
    'has_timestamps' => true,
    'has_soft_deletes' => true,
    'generate_factory' => true,
    'is_active' => true,
]);
```

### استخدام قالب

```php
$template = ModelTemplate::where('slug', 'ecommerce-product')->first();

$generation = $service->generateFromTemplate($template, [
    'name' => 'Product',
    'table_name' => 'products',
    'description' => 'نموذج المنتج',
]);
```

### القوالب الجاهزة

1. **Basic Model** - نموذج أساسي بسيط
2. **E-commerce Product** - نموذج منتج تجارة إلكترونية
3. **User Management** - نموذج إدارة المستخدمين
4. **Accounting Entry** - نموذج قيد محاسبي
5. **CRM Contact** - نموذج جهة اتصال CRM

---

## ✅ الاختبار والتحقق

### التحقق من صحة Model

```php
$results = $service->validate($generation);

if ($results['valid']) {
    echo "✅ Model صحيح";
} else {
    echo "❌ أخطاء:\n";
    foreach ($results['errors'] as $error) {
        echo "  • $error\n";
    }
}

if (!empty($results['warnings'])) {
    echo "⚠️  تحذيرات:\n";
    foreach ($results['warnings'] as $warning) {
        echo "  • $warning\n";
    }
}
```

### عمليات التحقق

1. ✅ **PHP Syntax Check** - التحقق من صحة بناء الجملة
2. ✅ **Namespace Validation** - التحقق من Namespace
3. ✅ **Class Name Validation** - التحقق من اسم الكلاس
4. ✅ **Table Name Validation** - التحقق من اسم الجدول
5. ✅ **Fillable Check** - التحقق من Fillable
6. ✅ **Relations Validation** - التحقق من العلاقات
7. ✅ **Traits Check** - التحقق من Traits
8. ✅ **Casts Validation** - التحقق من Casts

### تشغيل Tests

```bash
# تشغيل جميع الاختبارات
php artisan test

# تشغيل اختبارات Model Generator فقط
php artisan test --filter=ModelGeneratorTest

# مع تقرير تفصيلي
php artisan test --filter=ModelGeneratorTest --testdox
```

---

## 📚 أمثلة عملية

### مثال 1: نموذج منتج كامل

```php
$schema = [
    'name' => 'Product',
    'description' => 'نموذج المنتج في التجارة الإلكترونية',
    'table' => 'products',
    'attributes' => [
        ['name' => 'name', 'type' => 'string', 'nullable' => false],
        ['name' => 'slug', 'type' => 'string', 'nullable' => false],
        ['name' => 'description', 'type' => 'text', 'nullable' => true],
        ['name' => 'price', 'type' => 'decimal', 'nullable' => false],
        ['name' => 'sale_price', 'type' => 'decimal', 'nullable' => true],
        ['name' => 'quantity', 'type' => 'integer', 'nullable' => false],
        ['name' => 'sku', 'type' => 'string', 'nullable' => false],
        ['name' => 'is_active', 'type' => 'boolean', 'nullable' => false],
        ['name' => 'category_id', 'type' => 'unsignedBigInteger', 'nullable' => false],
    ],
    'fillable' => ['name', 'slug', 'description', 'price', 'sale_price', 'quantity', 'sku', 'is_active', 'category_id'],
    'hidden' => [],
    'casts' => [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ],
    'relations' => [
        ['type' => 'belongsTo', 'model' => 'Category', 'method' => 'category', 'foreign_key' => 'category_id'],
        ['type' => 'hasMany', 'model' => 'OrderItem', 'method' => 'orderItems'],
        ['type' => 'hasMany', 'model' => 'Review', 'method' => 'reviews'],
        ['type' => 'belongsToMany', 'model' => 'Tag', 'method' => 'tags'],
    ],
    'scopes' => [
        ['name' => 'active', 'condition' => 'is_active = true'],
        ['name' => 'inStock', 'condition' => 'quantity > 0'],
        ['name' => 'onSale', 'condition' => 'sale_price IS NOT NULL'],
    ],
    'traits' => ['HasFactory', 'SoftDeletes'],
    'timestamps' => true,
    'soft_deletes' => true,
    'factory' => true,
    'seeder' => true,
];

$generation = $service->generateFromJson($schema);
$service->validate($generation);
$service->deploy($generation);
```

### مثال 2: نموذج قيد محاسبي

```bash
php artisan generate:model --text="
Model للقيد المحاسبي (JournalEntry)
- رقم القيد (entry_number) نص فريد مطلوب
- التاريخ (date) تاريخ مطلوب
- الوصف (description) نص اختياري
- المبلغ الإجمالي (total_amount) decimal مطلوب
- الحالة (status) enum: draft, posted, cancelled
- belongsTo مع User (created_by)
- hasMany مع JournalEntryLine
- scope: posted where status = 'posted'
- scope: thisMonth where date >= first day of month
- soft delete
" --validate --deploy
```

---

## ❓ الأسئلة الشائعة

### س: هل يمكن تعديل Model بعد التوليد؟

نعم، يمكنك تعديل المحتوى المولد قبل النشر من خلال:
- واجهة الويب
- تحديث `generated_content` مباشرة
- إعادة التوليد مع تعديلات

### س: هل يدعم النظام Polymorphic Relations؟

نعم، يمكنك إضافة علاقات Polymorphic في JSON Schema:

```json
{
  "relations": [
    {
      "type": "morphTo",
      "method": "commentable"
    }
  ]
}
```

### س: كيف أضيف Custom Methods؟

بعد التوليد، يمكنك تعديل المحتوى وإضافة methods مخصصة قبل النشر.

### س: هل يمكن استخدام النظام بدون OpenAI؟

نعم، جميع الميزات تعمل بدون AI. AI اختياري للتحسينات الإضافية فقط.

### س: كيف أنشئ قالب مخصص؟

```php
$template = ModelTemplate::create([
    'name' => 'My Custom Template',
    'template_content' => $yourTemplateContent,
    // ... باقي الإعدادات
]);
```

---

## 📞 الدعم والمساعدة

- 📧 **البريد الإلكتروني**: support@example.com
- 💬 **Discord**: [Join our server](#)
- 📖 **Documentation**: [Full docs](#)
- 🐛 **Bug Reports**: [GitHub Issues](#)

---

## 📄 الترخيص

هذا النظام مرخص تحت MIT License.

---

**تم التطوير بواسطة:** PHP Magic System Team  
**الإصدار:** v3.26.0  
**التاريخ:** 2025-12-03
