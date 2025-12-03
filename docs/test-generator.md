# مولد الاختبارات الذكي (AI Test Generator)

## مقدمة

"مولد الاختبارات الذكي" هو ميزة متقدمة تدمج قوة الذكاء الاصطناعي من **Manus AI** مباشرة في بيئة تطوير Laravel الخاصة بك. يهدف هذا المكون إلى تسريع عملية كتابة الاختبارات (Unit, Feature, Integration) من خلال تحليل كود المصدر الخاص بك واقتراح أو إنشاء اختبارات كاملة وموثوقة.

يدعم المولد كلاً من إطار عمل **PHPUnit** القياسي و **Pest** الحديث، مما يضمن التوافق مع تفضيلات فريق التطوير الخاص بك.

## المتطلبات

*   **Laravel 12+**
*   **PHP 8.2+**
*   حزمة `php-magic-system/test-generator` (يتم افتراض تثبيتها).
*   مفتاح API صالح من Manus AI.

## الإعدادات

بعد تثبيت الحزمة، يجب نشر ملف الإعدادات وتحديثه.

### 1. نشر ملف الإعدادات

قم بتنفيذ الأمر التالي لنشر ملف الإعدادات `config/test-generator.php`:

```bash
php artisan vendor:publish --tag=test-generator-config
```

### 2. تحديث مفتاح API والنموذج

قم بتحديث ملف `.env` الخاص بك لتعيين مفتاح API ونموذج الذكاء الاصطناعي.

| المتغير | القيمة المطلوبة | الوصف |
| :--- | :--- | :--- |
| `MANUS_AI_API_KEY` | `sk-4-tSe7JkjRuRPoZ70EWgVWA_Kr9v2ldVSfo8z5VsVJGhbNjAodRsNM618fEaYGGWvvKHofv-HSTwglnGZcizlVrTDQQt` | مفتاح الوصول الخاص بك إلى Manus AI. |
| `MANUS_AI_MODEL` | `gpt-4.1-mini` | النموذج الافتراضي المستخدم للتوليد. |

**مثال على ملف `.env`:**

```dotenv
MANUS_AI_API_KEY="sk-4-tSe7JkjRuRPoZ70EWgVWA_Kr9v2ldVSfo8z5VsVJGhbNjAodRsNM618fEaYGGWvvKHofv-HSTwglnGZcizlVrTDQQt"
MANUS_AI_MODEL="gpt-4.1-mini"
```

### 3. خيارات التكوين الإضافية

يمكنك تخصيص الإعدادات الافتراضية في ملف `config/test-generator.php`:

```php
// config/test-generator.php

return [
    // ...
    'default_framework' => env('TEST_GENERATOR_FRAMEWORK', 'phpunit'), // 'phpunit' أو 'pest'
    'default_type' => env('TEST_GENERATOR_TYPE', 'unit'), // 'unit', 'feature', 'integration'
    'output_path' => base_path('tests/Generated'), // مسار حفظ الاختبارات المولدة
];
```

## الاستخدام

يمكن استخدام مولد الاختبارات الذكي عبر واجهة المستخدم الرسومية (GUI) أو عبر سطر الأوامر (CLI).

### 1. عبر واجهة المستخدم (GUI)

تم تصميم واجهة المستخدم الخاصة بالمولد باستخدام **Tailwind CSS** لضمان مظهر احترافي ومتجاوب يتكامل بسلاسة مع لوحة تحكم Laravel الخاصة بك.

**المسار:** `/test-generator`

**الخطوات:**

1.  حدد مسار الملف أو اسم الفئة (Class) التي تريد إنشاء اختبارات لها.
2.  اختر **نوع الاختبار** المطلوب (Unit, Feature, Integration).
3.  اختر **إطار العمل** (PHPUnit أو Pest).
4.  انقر على زر **"توليد الاختبارات"**.
5.  سيقوم النظام بعرض الكود المقترح، مع إمكانية مراجعته وحفظه مباشرة في مجلد `tests/Generated`.

### 2. عبر سطر الأوامر (CLI)

يمكنك استخدام أمر Artisan المخصص لتوليد الاختبارات مباشرة من الطرفية.

**الصيغة العامة:**

```bash
php artisan ai:generate-test {class_name} --type={type} --framework={framework}
```

**أمثلة الاستخدام:**

| الوظيفة | الأمر الطرفي | الوصف |
| :--- | :--- | :--- |
| **Unit Test** لـ `UserService` باستخدام **PHPUnit** | `php artisan ai:generate-test App\\Services\\UserService --type=unit --framework=phpunit` | توليد اختبار وحدة لخدمة معينة. |
| **Feature Test** لـ `PostController` باستخدام **Pest** | `php artisan ai:generate-test App\\Http\\Controllers\\PostController --type=feature --framework=pest` | توليد اختبار وظيفي لوحدة تحكم. |
| **Integration Test** لعملية دفع | `php artisan ai:generate-test App\\Actions\\ProcessPayment --type=integration` | توليد اختبار تكامل لعملية معقدة (باستخدام الإعداد الافتراضي لإطار العمل). |

## أنواع الاختبارات المدعومة

| النوع | الوصف | الاستخدام النموذجي |
| :--- | :--- | :--- |
| **Unit Tests** (اختبارات الوحدة) | اختبار أصغر جزء من الكود بشكل منفصل (مثل دالة أو ميثود). | التأكد من أن منطق العمل الأساسي يعمل بشكل صحيح. |
| **Feature Tests** (اختبارات الوظائف) | اختبار تفاعل أجزاء مختلفة من التطبيق، وغالباً ما تتضمن طلبات HTTP. | اختبار مسارات (Routes) ووحدات التحكم (Controllers) للتأكد من استجابة التطبيق بشكل صحيح. |
| **Integration Tests** (اختبارات التكامل) | اختبار تدفقات العمل المعقدة التي تتضمن تفاعلات مع قواعد البيانات أو خدمات خارجية. | التأكد من أن النظام يعمل كوحدة واحدة متكاملة. |

## دعم إطاري PHPUnit و Pest

يمكن للمولد التبديل تلقائيًا بين إطاري الاختبار بناءً على المعامل `--framework` أو الإعداد الافتراضي في ملف التكوين.

| إطار العمل | المعامل | مثال على ملف الاختبار المولّد |
| :--- | :--- | :--- |
| **PHPUnit** | `--framework=phpunit` | `<?php use PHPUnit\\Framework\\TestCase; class UserServiceTest extends TestCase { ... }` |
| **Pest** | `--framework=pest` | `<?php it('can create a new user', function () { ... });` |

## أمثلة برمجية متقدمة

### استخدام المولد برمجياً

يمكنك استدعاء المولد مباشرة داخل الكود الخاص بك لأتمتة عمليات التطوير:

```php
use App\Generators\TestGenerator;
use App\Services\UserService;

$generator = app(TestGenerator::class);

// توليد اختبار وحدة لـ UserService
$testCode = $generator->generate(
    UserService::class,
    'unit',
    'phpunit'
);

// حفظ الكود في ملف
file_put_contents(
    base_path('tests/Unit/UserServiceTest.php'),
    $testCode
);
```

---
**ملاحظة:** يتم تحديث هذا التوثيق باستمرار ليعكس أحدث المميزات في مولد الاختبارات الذكي. يرجى مراجعة ملف `config/test-generator.php` للحصول على أحدث خيارات التكوين.
