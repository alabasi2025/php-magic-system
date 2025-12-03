# 📖 Controller Generator v3.27.0 - دليل المستخدم

**الإصدار:** v3.27.0  
**تاريخ الإنشاء:** 2025-12-03  
**المهمة:** 19/100  
**الحالة:** ✅ مكتمل

---

## 📑 جدول المحتويات

1. [مقدمة](#مقدمة)
2. [البدء السريع](#البدء-السريع)
3. [واجهة الويب](#واجهة-الويب)
4. [أمثلة عملية](#أمثلة-عملية)
5. [الميزات المتقدمة](#الميزات-المتقدمة)
6. [الأسئلة الشائعة](#الأسئلة-الشائعة)

---

<a name="مقدمة"></a>
## 1. مقدمة

### ما هو Controller Generator؟

**Controller Generator** هو أداة ذكية ومتقدمة لتوليد Controllers في Laravel بشكل آلي. يوفر لك الوقت والجهد من خلال توليد:

- ✨ **Resource Controllers** - CRUD كامل
- 🌐 **API Controllers** - RESTful APIs
- 📝 **Form Requests** - التحقق من البيانات
- 🎨 **API Resources** - تنسيق البيانات
- 🛣️ **Routes** - المسارات تلقائياً

### لماذا تستخدم Controller Generator؟

- ⚡ **سرعة فائقة**: توليد Controller كامل في ثوانٍ
- 🎯 **جودة عالية**: كود يتبع أفضل الممارسات
- 🤖 **ذكاء اصطناعي**: تحليل ذكي للمتطلبات
- 📚 **توثيق شامل**: PHPDoc كامل بالعربية والإنجليزية
- 🔄 **تكامل كامل**: مع Laravel و Manus API

---

<a name="البدء-السريع"></a>
## 2. البدء السريع

### الخطوة 1: الوصول للأداة

افتح المتصفح وانتقل إلى:

```
https://your-domain.com/developer/controller-generator
```

### الخطوة 2: اختر نوع Controller

اختر من الأنواع التالية:

- **Resource Controller** - للصفحات والعمليات الكاملة
- **API Controller** - للـ APIs
- **Invokable Controller** - لعملية واحدة
- **Custom Controller** - مخصص حسب الحاجة

### الخطوة 3: أدخل المعلومات

أدخل المعلومات الأساسية:

```
اسم Controller: ProductController
اسم Model: Product
Namespace: App\Http\Controllers
```

### الخطوة 4: اختر الخيارات

حدد ما تريد توليده:

- ☑️ Form Requests (Store & Update)
- ☑️ API Resources
- ☑️ Routes
- ☑️ Views (اختياري)

### الخطوة 5: توليد

اضغط على زر **"توليد"** وانتظر ثوانٍ معدودة!

---

<a name="واجهة-الويب"></a>
## 3. واجهة الويب

### 3.1 الصفحة الرئيسية

عند فتح الأداة، ستجد:

#### القسم العلوي
- عنوان الأداة
- رقم الإصدار
- إحصائيات سريعة

#### نموذج التوليد
- حقول الإدخال
- خيارات التوليد
- زر المعاينة
- زر التوليد

#### سجل التوليدات
- آخر 10 توليدات
- حالة كل توليد
- خيارات التحميل

### 3.2 نموذج التوليد

#### الحقول الأساسية

**اسم Controller** (مطلوب)
```
مثال: ProductController
```

**نوع Controller** (مطلوب)
- Resource Controller
- API Controller
- Invokable Controller
- Custom Controller

**اسم Model** (مطلوب)
```
مثال: Product
```

**Namespace** (اختياري)
```
افتراضي: App\Http\Controllers
```

#### الخيارات الإضافية

**توليد Form Requests**
- ☑️ Store Request
- ☑️ Update Request

**توليد API Resources**
- ☑️ Resource Class
- ☑️ Collection Class

**توليد Routes**
- ☑️ Web Routes
- ☑️ API Routes

**خيارات متقدمة**
- ☑️ Soft Deletes
- ☑️ Authorization
- ☑️ Events
- ☑️ Caching

### 3.3 المعاينة

قبل التوليد، يمكنك معاينة الكود:

1. اضغط على زر **"معاينة"**
2. سيظهر الكود المولد
3. راجع الكود
4. اضغط **"تأكيد التوليد"** أو **"تعديل"**

### 3.4 التحميل

بعد التوليد:

1. ستظهر رسالة نجاح
2. قائمة بالملفات المولدة
3. خيارات التحميل:
   - تحميل الكل (ZIP)
   - تحميل ملف واحد
   - نسخ الكود

---

<a name="أمثلة-عملية"></a>
## 4. أمثلة عملية

### مثال 1: Resource Controller بسيط

**المتطلب:**
```
أريد Controller لإدارة المنتجات (Products) مع CRUD كامل
```

**الإعدادات:**
```
Name: ProductController
Type: Resource Controller
Model: Product
Generate Requests: ✅
Generate Resources: ❌
```

**النتيجة:**
- ✅ ProductController.php (7 methods)
- ✅ StoreProductRequest.php
- ✅ UpdateProductRequest.php
- ✅ Routes (web.php)

---

### مثال 2: API Controller كامل

**المتطلب:**
```
أريد API لإدارة المستخدمين مع JSON responses
```

**الإعدادات:**
```
Name: UserController
Type: API Controller
Model: User
Namespace: App\Http\Controllers\Api
Generate Requests: ✅
Generate Resources: ✅
```

**النتيجة:**
- ✅ Api/UserController.php
- ✅ StoreUserRequest.php
- ✅ UpdateUserRequest.php
- ✅ UserResource.php
- ✅ UserCollection.php
- ✅ Routes (api.php)

---

### مثال 3: Controller مع علاقات

**المتطلب:**
```
أريد Controller للطلبات (Orders) مع علاقات Customer و Products
```

**الإعدادات:**
```
Name: OrderController
Type: Resource Controller
Model: Order
Relations: customer, products
Eager Loading: ✅
```

**الكود المولد:**
```php
public function index(Request $request): View
{
    $orders = Order::with(['customer', 'products'])
        ->latest()
        ->paginate(15);

    return view('orders.index', compact('orders'));
}

public function show(Order $order): View
{
    $order->load(['customer', 'products']);
    
    return view('orders.show', compact('order'));
}
```

---

### مثال 4: Controller مع بحث وفلترة

**المتطلب:**
```
أريد Controller للمنتجات مع بحث وفلترة حسب الفئة والسعر
```

**الإعدادات:**
```
Name: ProductController
Type: Resource Controller
Model: Product
Search Fields: name, description
Filter Fields: category_id, price
```

**الكود المولد:**
```php
public function index(Request $request): View
{
    $query = Product::query();

    // Search
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    // Filter by category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filter by price range
    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    $products = $query->with('category')
        ->latest()
        ->paginate(15);

    return view('products.index', compact('products'));
}
```

---

### مثال 5: API Controller مع Pagination

**المتطلب:**
```
أريد API للمقالات مع pagination و filtering
```

**الكود المولد:**
```php
public function index(Request $request): ArticleCollection
{
    $query = Article::query();

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by author
    if ($request->filled('author_id')) {
        $query->where('author_id', $request->author_id);
    }

    $articles = $query->with(['author', 'category'])
        ->latest()
        ->paginate($request->input('per_page', 15));

    return new ArticleCollection($articles);
}
```

---

<a name="الميزات-المتقدمة"></a>
## 5. الميزات المتقدمة

### 5.1 التوليد بالذكاء الاصطناعي

يمكنك استخدام الذكاء الاصطناعي لتوليد Controller:

**الطريقة 1: الوصف النصي**
```
أدخل وصفاً بسيطاً:
"أريد Controller لإدارة المنتجات مع بحث متقدم وفلترة حسب الفئة والسعر"
```

**الطريقة 2: JSON Schema**
```json
{
  "name": "ProductController",
  "type": "resource",
  "model": "Product",
  "features": {
    "search": ["name", "description"],
    "filters": ["category_id", "price"],
    "relations": ["category", "supplier"],
    "soft_delete": true
  }
}
```

### 5.2 التخصيص المتقدم

#### إضافة Middleware

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('verified')->only(['create', 'store']);
    $this->middleware('can:manage-products')->except(['index', 'show']);
}
```

#### إضافة Authorization

```php
public function update(UpdateProductRequest $request, Product $product): RedirectResponse
{
    $this->authorize('update', $product);
    
    $product->update($request->validated());
    
    return redirect()
        ->route('products.show', $product)
        ->with('success', 'تم تحديث المنتج بنجاح');
}
```

#### إضافة Events

```php
public function store(StoreProductRequest $request): RedirectResponse
{
    $product = Product::create($request->validated());
    
    event(new ProductCreated($product));
    
    return redirect()
        ->route('products.show', $product)
        ->with('success', 'تم إنشاء المنتج بنجاح');
}
```

#### إضافة Caching

```php
public function index(Request $request): View
{
    $cacheKey = 'products.index.' . md5(serialize($request->all()));
    
    $products = Cache::remember($cacheKey, 3600, function () use ($request) {
        return Product::query()
            ->with('category')
            ->latest()
            ->paginate(15);
    });
    
    return view('products.index', compact('products'));
}
```

### 5.3 Service Layer

يمكن توليد Service Layer منفصل:

```php
// app/Services/ProductService.php
class ProductService
{
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create($data);
            
            $this->updateInventory($product);
            $this->notifyAdmins($product);
            
            return $product;
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);
            
            $this->syncRelations($product, $data);
            $this->clearCache($product);
            
            return $product;
        });
    }
}
```

---

<a name="الأسئلة-الشائعة"></a>
## 6. الأسئلة الشائعة

### س1: كيف أقوم بتعديل Controller بعد التوليد؟

**ج:** يمكنك تعديل الملف المولد مباشرة. الكود مكتوب بطريقة واضحة وموثقة بالكامل.

### س2: هل يمكنني توليد Controller من Model موجود؟

**ج:** نعم! اختر "From Existing Model" وحدد اسم Model.

### س3: كيف أضيف دوال مخصصة؟

**ج:** بعد التوليد، افتح الملف وأضف دوالك الخاصة. الكود منظم ويسهل التعديل عليه.

### س4: هل يدعم Soft Deletes؟

**ج:** نعم! فعّل خيار "Soft Deletes" عند التوليد.

### س5: كيف أتعامل مع الملفات (File Uploads)؟

**ج:** الأداة تولد الكود الأساسي. يمكنك إضافة منطق رفع الملفات في Form Request.

### س6: هل يمكن توليد Tests تلقائياً؟

**ج:** حالياً لا، لكن يمكنك استخدام Test Generator (Task 4) لتوليد الاختبارات.

### س7: كيف أستخدم مع API خارجي؟

**ج:** ولّد API Controller واستخدم Service Layer للتعامل مع API الخارجي.

### س8: هل يدعم Livewire؟

**ج:** حالياً لا، لكن سيتم إضافته في الإصدارات القادمة.

### س9: كيف أحذف Controller مولد؟

**ج:** احذف الملفات يدوياً أو استخدم Git لاستعادة النسخة السابقة.

### س10: أين أجد سجل التوليدات؟

**ج:** في أسفل الصفحة الرئيسية أو في قاعدة البيانات (جدول controller_generations).

---

## 📞 الدعم والمساعدة

### الحصول على المساعدة

- 📧 **البريد الإلكتروني:** support@semop.com
- 💬 **الدردشة المباشرة:** متاحة في لوحة التحكم
- 📚 **التوثيق:** https://docs.semop.com

### الإبلاغ عن مشكلة

إذا واجهت مشكلة:

1. تحقق من سجل الأخطاء (Logs)
2. راجع هذا الدليل
3. تواصل مع الدعم الفني

---

## 🎓 نصائح وحيل

### نصيحة 1: استخدم المعاينة دائماً
قبل التوليد، استخدم خاصية المعاينة للتأكد من الكود.

### نصيحة 2: احفظ الإعدادات
يمكنك حفظ الإعدادات المفضلة لديك لاستخدامها لاحقاً.

### نصيحة 3: استخدم Templates
أنشئ Templates خاصة بك لتسريع العملية.

### نصيحة 4: راجع الكود المولد
دائماً راجع الكود المولد وتأكد من مطابقته لمتطلباتك.

### نصيحة 5: استخدم Git
احفظ التغييرات في Git قبل توليد Controllers جديدة.

---

## 🚀 البدء الآن!

الآن أنت جاهز لاستخدام **Controller Generator v3.27.0**!

افتح الأداة وابدأ بتوليد Controllers احترافية في ثوانٍ.

**رابط الأداة:**
```
https://your-domain.com/developer/controller-generator
```

---

**آخر تحديث:** 2025-12-03 | **الإصدار:** v3.27.0 | **المهمة:** 19/100 ✅
