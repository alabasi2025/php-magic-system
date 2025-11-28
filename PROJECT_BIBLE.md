# 📖 الكتاب المقدس لمشروع SEMOP

> **⚠️ هذا الملف هو المرجع الرئيسي للمشروع - يجب قراءته قبل أي عملية**

---

## 🔐 بيانات الوصول الكاملة

### 1. GitHub Repository
```
Repository: https://github.com/alabasi2025/php-magic-system
Branch: main
Access: Integrated (gh CLI configured)
```

### 2. Hosting (Hostinger)
```
Domain: mediumblue-albatross-218540.hostingersite.com
URL: https://mediumblue-albatross-218540.hostingersite.com

SSH:
- Host: 82.29.157.218
- Port: 65002
- Username: u306850950
- Password: Jv4hRX9gFx8T@rw
- Status: ⚠️ يرفض الاتصال (محظور أو معطل)

FTP:
- Host: 82.29.157.218
- Port: 21 (default)
- Username: u306850950.mediumblue-albatross-218540.hostingersite.com
- Password: TR.d$4V#Ehaq@j6
- Path: public_html/
- Status: ✅ يعمل

cPanel:
- URL: https://hpanel.hostinger.com
- Access: عبر حساب Hostinger الرئيسي
```

### 3. Database (MySQL)
```
Database Name: u306850950_magic_system
Database User: u306850950_magic_user
Database Host: localhost
Database Size: 2 MB
Created: 2025-11-27
```

### 4. Project Structure
```
Local Path: /home/ubuntu/php-magic-system
Remote Path: /home/u306850950/public_html
Project Files Path: /home/ubuntu/projects/php-c650eed9
```

---

## 📋 خطوات العمل القياسية (SOP)

### ⭐ الخطوات الإلزامية لكل تحديث

```
1. تطوير الكود محلياً
2. اختبار محلي (إن أمكن)
3. Git Commit مع رسالة واضحة
4. تحديث رقم الإصدار في:
   - config/app.php
   - README.md
   - Dashboard
5. Git Push إلى GitHub
6. نشر للـ Hosting عبر FTP
7. تحديث Cache على الـ Hosting
8. التأكد من رقم الإصدار في المتصفح
9. فحص وتجربة الإضافات الجديدة
10. توثيق التغييرات
```

### 📝 تفاصيل كل خطوة

#### 1. تطوير الكود محلياً
```bash
cd /home/ubuntu/php-magic-system
# تطوير الكود هنا
```

#### 2. اختبار محلي
```bash
# إذا كان Laravel يعمل محلياً
php artisan serve
# زيارة: http://localhost:8000
```

#### 3. Git Commit
```bash
git add .
git commit -m "feat: وصف التحديث

- التفاصيل
- المميزات الجديدة
- الإصلاحات"
```

#### 4. تحديث رقم الإصدار
```bash
# في config/app.php
'version' => 'v2.X.X',

# في Dashboard
echo "v2.X.X";
```

#### 5. Git Push
```bash
git push origin main
```

#### 6. نشر للـ Hosting عبر FTP
```bash
# استخدام lftp
lftp -u 'u306850950.mediumblue-albatross-218540.hostingersite.com','TR.d$4V#Ehaq@j6' 82.29.157.218 << 'EOF'
set ftp:ssl-allow no
set ftp:passive-mode on

# رفع Controller
cd ../app/Http/Controllers
put app/Http/Controllers/CONTROLLER_NAME.php

# رفع Routes
cd ../../../routes
put routes/ROUTE_FILE.php

# رفع Views
cd ../resources/views
mput resources/views/PATH/*.blade.php

bye
EOF
```

#### 7. تحديث Cache على الـ Hosting
```bash
# الطريقة 1: عبر سكريبت
curl "https://mediumblue-albatross-218540.hostingersite.com/fix-developer-system.php?key=semop_fix_2024"

# الطريقة 2: عبر SSH (إذا كان متاح)
ssh -p 65002 u306850950@82.29.157.218
cd public_html
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
composer dump-autoload
```

#### 8. التأكد من رقم الإصدار
```bash
# فتح المتصفح
https://mediumblue-albatross-218540.hostingersite.com/dashboard

# البحث عن رقم الإصدار في الصفحة
# يجب أن يظهر: v2.X.X
```

#### 9. فحص الإضافات الجديدة
```
# اختبار كل صفحة جديدة
# اختبار كل وظيفة جديدة
# التأكد من عدم وجود أخطاء
# اختبار على أجهزة مختلفة (Desktop, Mobile)
```

#### 10. توثيق التغييرات
```bash
# تحديث هذا الملف (PROJECT_BIBLE.md)
# إضافة ملاحظات في CHANGELOG.md
# تحديث README.md إذا لزم الأمر
```

---

## 🔧 الأوامر المهمة

### Git Commands
```bash
# حالة المشروع
git status

# آخر commit
git log -1 --oneline

# عرض التغييرات
git diff

# التراجع عن تغييرات
git checkout -- FILE_NAME

# إنشاء branch جديد
git checkout -b feature/NEW_FEATURE

# دمج branch
git merge BRANCH_NAME
```

### FTP Commands (lftp)
```bash
# الاتصال
lftp -u 'USERNAME','PASSWORD' HOST

# داخل lftp:
ls                          # عرض الملفات
cd DIRECTORY               # الانتقال لمجلد
lcd LOCAL_DIRECTORY        # تغيير المجلد المحلي
put LOCAL_FILE             # رفع ملف
mput LOCAL_FILES           # رفع عدة ملفات
get REMOTE_FILE            # تحميل ملف
mirror LOCAL_DIR REMOTE    # مزامنة مجلد
```

### Laravel Artisan Commands
```bash
# مسح Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# إنشاء Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# إنشاء ملفات
php artisan make:controller NAME
php artisan make:model NAME
php artisan make:migration NAME
```

### Composer Commands
```bash
# تحديث Autoload
composer dump-autoload

# تثبيت Dependencies
composer install

# تحديث Packages
composer update
```

---

## 🐛 حل المشاكل الشائعة

### 1. خطأ 500 (Server Error)
**الأسباب المحتملة:**
- Routes cache قديم
- Composer autoload قديم
- خطأ في الكود
- ملفات ناقصة

**الحل:**
```bash
# 1. تحديث Cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# 2. تحديث Autoload
composer dump-autoload

# 3. فحص Logs
tail -f storage/logs/laravel.log

# 4. تفعيل Debug مؤقتاً
APP_DEBUG=true في .env
```

### 2. FTP يرفض الاتصال
**الأسباب:**
- كلمة مرور خاطئة
- IP محظور
- الخادم مشغول

**الحل:**
```bash
# 1. التحقق من البيانات
# Username: u306850950.mediumblue-albatross-218540.hostingersite.com
# Password: TR.d$4V#Ehaq@j6

# 2. استخدام passive mode
set ftp:passive-mode on

# 3. تعطيل SSL
set ftp:ssl-allow no

# 4. الانتظار وإعادة المحاولة
```

### 3. SSH يرفض الاتصال
**الأسباب:**
- SSH معطل على الـ Hosting
- IP محظور
- Port خاطئ

**الحل:**
```bash
# استخدام FTP بدلاً من SSH
# أو الاتصال بدعم Hostinger
```

### 4. Routes لا تعمل
**الأسباب:**
- Routes cache قديم
- ملف routes غير محمّل

**الحل:**
```bash
# 1. مسح Routes cache
php artisan route:clear

# 2. التحقق من web.php
# يجب أن يحتوي على:
require __DIR__.'/developer.php';

# 3. عرض جميع Routes
php artisan route:list | grep developer
```

### 5. Views لا تظهر
**الأسباب:**
- ملف blade غير موجود
- مسار خاطئ
- View cache قديم

**الحل:**
```bash
# 1. مسح View cache
php artisan view:clear

# 2. التحقق من المسار
# resources/views/developer/dashboard.blade.php

# 3. التحقق من Controller
# return view('developer.dashboard', $data);
```

---

## 📂 هيكل المشروع

```
php-magic-system/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DeveloperController.php ⭐ (1019 سطر)
│   │       └── ...
│   └── Models/
├── config/
│   └── app.php (رقم الإصدار)
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── index.php
│   ├── update-system.php ⭐
│   └── fix-developer-system.php ⭐
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── partials/
│       │       └── sidebar-developer.blade.php ⭐
│       └── developer/ ⭐
│           ├── dashboard.blade.php
│           ├── artisan/
│           ├── code-generator/
│           ├── database/
│           ├── monitor/
│           ├── cache/
│           ├── logs/
│           └── ai/
├── routes/
│   ├── web.php
│   └── developer.php ⭐ (46 routes)
├── storage/
│   └── logs/
│       └── laravel.log
├── .env
├── composer.json
└── README.md
```

---

## 🎯 الإصدارات

### v2.8.0 (2025-11-29) - Current
**الإضافات:**
- ✅ نظام المطور الشامل
- ✅ DeveloperController (1019 سطر، 50+ function)
- ✅ 8 أقسام رئيسية
- ✅ 46 route جديد
- ✅ تكامل OpenAI API
- ✅ استخدام جميع أدوات Laravel

**الحالة:** 🟡 85% (يحتاج إصلاح خطأ 500)

**آخر Commits:**
- fcde5c2: feat: نظام المطور الشامل v2.8.0
- [pending]: docs: إضافة الكتاب المقدس + CHANGELOG + README

**الملفات المرفوعة للـ Hosting:**
- ✅ DeveloperController.php (18.3 KB)
- ✅ dashboard.blade.php (5.3 KB)
- ✅ app.blade.php (25.9 KB)
- ✅ routes/developer.php
- ✅ routes/web.php
- ✅ fix-developer-system.php (3.2 KB)
- ✅ update-system.php (1.9 KB)

**الملفات الناقصة على الـ Hosting:**
- ❌ 10 ملفات blade (artisan, code-generator, database, cache, logs, ai)

### v2.7.0 (2025-11-28)
**الإضافات:**
- معلومات النظام
- معلومات قاعدة البيانات

**الحالة:** ✅ مكتمل

### v2.6.0 (قبل 2025-11-28)
**الحالة:** ✅ مكتمل

---

## 🔑 مفاتيح API

### OpenAI API
```bash
# في .env
OPENAI_API_KEY=sk-...

# الاستخدام في DeveloperController
private $openaiApiKey;
$this->openaiApiKey = env('OPENAI_API_KEY');
```

### سكريبتات الأمان
```bash
# update-system.php
Key: semop_secure_2024
URL: /update-system.php?key=semop_secure_2024

# fix-developer-system.php
Key: semop_fix_2024
URL: /fix-developer-system.php?key=semop_fix_2024
```

---

## 📊 إحصائيات المشروع

### الكود
- **إجمالي السطور**: ~50,000+
- **Controllers**: 10+
- **Models**: 15+
- **Views**: 50+
- **Routes**: 100+

### نظام المطور (v2.8.0)
- **DeveloperController**: 1019 سطر
- **Functions**: 50+
- **Routes**: 46
- **Views**: 8 (مطورة) + 10 (تحتاج رفع)
- **AI Tools**: 6

---

## 📞 جهات الاتصال

### Hostinger Support
```
Website: https://www.hostinger.com/support
Email: support@hostinger.com
Live Chat: متاح 24/7
```

### GitHub Issues
```
URL: https://github.com/alabasi2025/php-magic-system/issues
```

---

## 📝 ملاحظات مهمة

### ⚠️ تحذيرات
1. **لا تنشر** ملفات `.env` على GitHub
2. **لا تشارك** كلمات المرور في الـ commits
3. **احذف** ملفات الاختبار قبل النشر
4. **فعّل** APP_DEBUG=false في Production
5. **احمِ** routes نظام المطور بـ middleware

### ✅ أفضل الممارسات
1. **اختبر** محلياً قبل النشر
2. **وثّق** كل تغيير
3. **احفظ** نسخة احتياطية قبل التحديثات الكبيرة
4. **راجع** الكود قبل الـ commit
5. **اتبع** الخطوات القياسية دائماً

### 🎯 الأولويات الحالية (بالترتيب)

#### المرحلة 1: إصلاح خطأ 500 (أولوية قصوى)
```bash
# الخطوة 1: تحديث Cache عبر سكريبت
curl "https://mediumblue-albatross-218540.hostingersite.com/fix-developer-system.php?key=semop_fix_2024"

# الخطوة 2: إذا لم ينجح، استخدم cPanel Terminal
# File Manager → Terminal
cd public_html
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
composer dump-autoload

# الخطوة 3: اختبار
https://mediumblue-albatross-218540.hostingersite.com/developer
```

#### المرحلة 2: رفع Views المتبقية
- ❌ artisan/index.blade.php
- ❌ code-generator/index.blade.php
- ❌ database/tables.blade.php
- ❌ database/structure.blade.php
- ❌ database/data.blade.php
- ❌ cache/overview.blade.php
- ❌ cache/keys.blade.php
- ❌ logs/index.blade.php
- ❌ logs/viewer.blade.php
- ❌ ai/index.blade.php

#### المرحلة 3: اختبار شامل
1. التأكد من رقم الإصدار (v2.8.0)
2. اختبار Dashboard
3. اختبار كل قسم من الـ 8 أقسام
4. اختبار AI Tools (يحتاج OpenAI API Key)

#### المرحلة 4: التوثيق والأمان
1. توثيق API
2. إضافة Middleware للحماية
3. تحديث CHANGELOG.md
4. تحديث README.md

---

## 🔄 آخر تحديث

**التاريخ:** 2025-11-29 23:45 GMT+3  
**الإصدار:** v2.8.0  
**المحدث بواسطة:** Manus AI  
**الحالة:** 🟡 قيد التطوير (85%)  
**آخر تحديث:** إضافة التوثيق الشامل (PROJECT_BIBLE.md, CHANGELOG.md, README.md)

---

## 📖 كيفية استخدام هذا الملف

1. **اقرأ هذا الملف** قبل أي عملية
2. **اتبع الخطوات القياسية** دائماً
3. **حدّث هذا الملف** عند أي تغيير
4. **ارجع لهذا الملف** عند أي مشكلة
5. **شارك هذا الملف** مع أي مطور جديد

---

> **💡 نصيحة:** احفظ هذا الملف في مكان آمن وارجع إليه دائماً!
