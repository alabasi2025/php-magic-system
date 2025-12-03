# توثيق محسن الكود (Code Optimizer Documentation)

**المؤلف:** Manus AI
**التاريخ:** 3 ديسمبر 2025

---

## 1. نظرة عامة (Overview)

محسن الكود (Code Optimizer) هو مكون أساسي ضمن نظام PHP Magic System، مصمم لرفع كفاءة وأداء تطبيقات PHP و Laravel. يهدف هذا المحسن إلى تطبيق أفضل الممارسات العالمية تلقائيًا على الكود المصدري والملفات الثابتة، مما يؤدي إلى تقليل حجم الملفات، تسريع وقت التحميل، وتحسين قابلية صيانة الكود.

تم بناء المحسن مع الأخذ في الاعتبار معايير **Laravel/Tailwind best practices** لضمان التوافق الكامل والتحسين الأمثل للبيئات الحديثة. إنه يعمل كطبقة ما بعد المعالجة (Post-processing Layer) تضمن أن الكود النهائي المنشور نظيف، احترافي، ومُحسّن للأداء.

## 2. المميزات (Features)

يقدم محسن الكود مجموعة شاملة من المميزات التي تغطي جوانب متعددة من تحسين التطبيق:

| الميزة | الوصف | الفائدة الرئيسية |
| :--- | :--- | :--- |
| **تحسين كود PHP الأساسي** | إزالة التعليقات، المسافات البيضاء الزائدة، والأسطر الفارغة من ملفات PHP دون التأثير على المنطق البرمجي. | تقليل حجم الملفات وتحسين سرعة التحميل. |
| **تحسين قوالب Blade** | تطبيق تقنيات تصغير (Minification) متقدمة على ملفات Blade، وتحسين عملية تجميع (Compilation) العروض. | تسريع عرض الواجهات الأمامية وتقليل استهلاك الذاكرة. |
| **تكامل Tailwind CSS (Purge/JIT)** | التكامل العميق مع أدوات Tailwind CSS لضمان إزالة جميع أنماط CSS غير المستخدمة (Dead CSS) من حزمة الإنتاج. | أصغر حجم ممكن لملف CSS، مما يضمن أداءً فائقًا. |
| **إزالة الكود الميت (Dead Code Elimination)** | محاولة تحديد وإزالة أو وضع علامة على الدوال والمتغيرات غير المستخدمة في سياقات معينة. | كود أنظف وأكثر أمانًا وأسهل في الصيانة. |
| **تنظيم استخدامات (Imports) PHP** | فرز وتنظيف عبارات `use` في ملفات PHP وفقًا لمعايير PSR-12. | كود نظيف وموحد يسهل قراءته والعمل عليه. |
| **تحسين الأصول الثابتة** | تصغير ملفات JavaScript و CSS (غير Tailwind) والصور (ضغط بدون فقدان الجودة). | تحسين درجات الأداء في أدوات مثل Google PageSpeed. |

## 3. دليل الاستخدام (Usage Guide)

تم تصميم محسن الكود ليكون سهل الاستخدام عبر واجهة سطر الأوامر (CLI) الخاصة بـ Laravel Artisan.

### 3.1. التثبيت

يتم تثبيت المحسن عبر Composer كجزء من نظام PHP Magic System:

```bash
composer require php-magic-system/code-optimizer
```

### 3.2. إعدادات التهيئة (Configuration)

لإجراء تخصيص متقدم، يمكنك نشر ملف التهيئة الخاص بالمحسن:

```bash
php artisan vendor:publish --tag=optimizer-config
```

سيؤدي هذا إلى إنشاء ملف `config/optimizer.php` حيث يمكنك تحديد:
*   المسارات التي يجب استثناؤها من عملية التحسين.
*   مستوى التحسين (مثل: `basic`, `standard`, `aggressive`).
*   تفعيل أو تعطيل مميزات معينة (مثل: `blade_minification`, `tailwind_purge`).

### 3.3. تشغيل المحسن

يتم تشغيل عملية التحسين بالكامل عبر أمر Artisan واحد:

```bash
php artisan optimizer:run
```

**الخيارات المتاحة:**

| الخيار | الوصف | مثال |
| :--- | :--- | :--- |
| `--path=` | تحسين مسار محدد بدلاً من المشروع بأكمله. | `php artisan optimizer:run --path=app/Http/Controllers` |
| `--force` | تجاهل ذاكرة التخزين المؤقت وتشغيل التحسين على جميع الملفات. | `php artisan optimizer:run --force` |
| `--dry-run` | محاكاة عملية التحسين وعرض التغييرات دون تطبيقها فعليًا. | `php artisan optimizer:run --dry-run` |

## 4. مرجع واجهة برمجة التطبيقات (API Reference)

يمكن استخدام محسن الكود برمجياً داخل الكود الخاص بك لتطبيق التحسينات في سياقات مخصصة أو عمليات نشر متقدمة.

### 4.1. فئة `Optimizer`

الفئة الرئيسية للتحسين هي `\PhpMagicSystem\Optimizer\Optimizer`. يمكنك حقنها (Inject) أو استخدام الواجهة (Facade) الخاصة بها.

**الواجهة (Facade):**

```php
use PhpMagicSystem\Optimizer\Facades\Optimizer;

// تحسين محتوى سلسلة نصية مباشرة
$optimizedContent = Optimizer::optimizeString($rawContent, 'php');
```

### 4.2. الدوال الأساسية

| الدالة | الوصف | المعلمات | القيمة المرجعة |
| :--- | :--- | :--- | :--- |
| `optimizeFile(string $path, array $options = []): bool` | تحسين ملف واحد في مكانه. | `path`: مسار الملف. `options`: خيارات تحسين إضافية. | `true` عند النجاح، `false` عند الفشل. |
| `optimizeDirectory(string $directory, array $options = []): array` | تحسين جميع الملفات المدعومة داخل دليل محدد. | `directory`: مسار الدليل. `options`: خيارات تحسين إضافية. | مصفوفة تحتوي على قائمة بالملفات التي تم تحسينها. |
| `optimizeString(string $content, string $type): string` | تحسين سلسلة نصية بناءً على نوع المحتوى (`php`, `blade`, `css`, `js`). | `content`: المحتوى الخام. `type`: نوع المحتوى. | المحتوى المُحسّن. |

## 5. أمثلة (Examples)

### المثال 1: التحسين في بيئة الإنتاج (Production)

لضمان أن يكون كود الإنتاج (Production) مُحسّنًا دائمًا، يمكنك إضافة الأمر إلى خطوة النشر (Deployment Pipeline) الخاصة بك:

```bash
# 1. سحب التغييرات
git pull origin main

# 2. تثبيت التبعيات
composer install --no-dev -o

# 3. تشغيل محسن الكود
php artisan optimizer:run --force

# 4. مسح ذاكرة التخزين المؤقت لتطبيق Laravel
php artisan optimize

# 5. إعادة تشغيل الخادم (إذا لزم الأمر)
sudo service php-fpm restart
```

### المثال 2: استخدام API لتحسين ملف Blade مخصص

لنفترض أن لديك نظامًا لإنشاء قوالب Blade ديناميكيًا وتريد تحسينها قبل حفظها في ذاكرة التخزين المؤقت:

```php
// app/Services/CustomTemplateService.php

namespace App\Services;

use PhpMagicSystem\Optimizer\Facades\Optimizer;

class CustomTemplateService
{
    public function saveOptimizedTemplate(string $templateName, string $rawBladeContent): void
    {
        // تطبيق تحسينات Blade على المحتوى الخام
        $optimizedContent = Optimizer::optimizeString($rawBladeContent, 'blade');

        // حفظ المحتوى المُحسّن في مكان ما (مثل قاعدة البيانات أو نظام الملفات)
        \Storage::put("templates/{$templateName}.blade.php", $optimizedContent);

        \Log::info("Template '{$templateName}' optimized and saved.");
    }
}
```

### المثال 3: استثناء مسار معين من التحسين

لتجنب تحسين ملفات معينة (مثل ملفات مكتبة خارجية تم وضعها في مجلد `resources/js/vendor`):

**في ملف `config/optimizer.php`:**

```php
return [
    // ... إعدادات أخرى
    'exclude_paths' => [
        // استثناء جميع ملفات JS داخل هذا المجلد
        'resources/js/vendor/**/*.js',
        // استثناء ملف واحد محدد
        'app/Console/Commands/HeavyCommand.php',
    ],
    // ...
];
```

---
**ملاحظة:** يضمن محسن الكود أن جميع التحسينات المطبقة تحافظ على **نظافة الكود واحترافيته** وتتوافق مع أفضل ممارسات Laravel و Tailwind، مما يدعم **التصميم الاحترافي** للتطبيق النهائي.
