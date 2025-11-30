# 🧬 دليل تثبيت نظام الجينات

**التاريخ:** 2025-11-30  
**الإصدار:** v1.0

---

## 📦 الملفات الجاهزة

جميع الملفات موجودة في: `/home/ubuntu/genes_upload/`

**الملفات (9 ملفات):**
1. `2025_11_30_000000_create_clients_table.php` - Migration
2. `2025_11_30_000001_create_client_genes_table.php` - Migration
3. `Client.php` - Model
4. `ClientGene.php` - Model
5. `GeneHelper.php` - Helper
6. `system.php` - Config
7. `GeneController.php` - Controller
8. `ClientSeeder.php` - Seeder
9. `PartnershipSeeder.php` - Seeder

---

## 🚀 خطوات التثبيت

### الخطوة 1: رفع الملفات للسيرفر

```bash
# الاتصال بالسيرفر
ssh -p 65002 u220657238@[server-address]

# رفع Migrations
scp -P 65002 2025_11_30_000000_create_clients_table.php u220657238@[server]:/home/u220657238/public_html/database/migrations/
scp -P 65002 2025_11_30_000001_create_client_genes_table.php u220657238@[server]:/home/u220657238/public_html/database/migrations/

# رفع Models
scp -P 65002 Client.php u220657238@[server]:/home/u220657238/public_html/app/Models/
scp -P 65002 ClientGene.php u220657238@[server]:/home/u220657238/public_html/app/Models/

# رفع Helper
scp -P 65002 GeneHelper.php u220657238@[server]:/home/u220657238/public_html/app/Helpers/

# رفع Config
scp -P 65002 system.php u220657238@[server]:/home/u220657238/public_html/config/

# رفع Controller
scp -P 65002 GeneController.php u220657238@[server]:/home/u220657238/public_html/app/Http/Controllers/

# رفع Seeders
scp -P 65002 ClientSeeder.php u220657238@[server]:/home/u220657238/public_html/database/seeders/
scp -P 65002 PartnershipSeeder.php u220657238@[server]:/home/u220657238/public_html/database/seeders/
```

---

### الخطوة 2: تشغيل Migrations

```bash
ssh -p 65002 u220657238@[server]

cd /home/u220657238/public_html

# تشغيل migrations
php artisan migrate

# يجب أن ترى:
# ✅ 2025_11_30_000000_create_clients_table
# ✅ 2025_11_30_000001_create_client_genes_table
```

---

### الخطوة 3: تشغيل Seeders

```bash
# إضافة العميل العباسي وتفعيل الجينات
php artisan db:seed --class=ClientSeeder

# إضافة بيانات الشراكات
php artisan db:seed --class=PartnershipSeeder
```

---

### الخطوة 4: تحديث Routes

إضافة المسارات التالية إلى `routes/web.php`:

```php
use App\Http\Controllers\GeneController;

// مسارات الجينات
Route::middleware(['auth'])->group(function () {
    Route::get('/genes', [GeneController::class, 'index'])->name('genes.index');
    Route::post('/genes/{gene}/activate', [GeneController::class, 'activate'])->name('genes.activate');
    Route::post('/genes/{gene}/deactivate', [GeneController::class, 'deactivate'])->name('genes.deactivate');
    Route::post('/genes/{gene}/configure', [GeneController::class, 'configure'])->name('genes.configure');
    Route::get('/genes/{gene}', [GeneController::class, 'show'])->name('genes.show');
});
```

---

### الخطوة 5: تحديث .env

إضافة المتغيرات التالية إلى `.env`:

```env
SYSTEM_CLIENT_NAME="العباسي"
SYSTEM_CLIENT_CODE="ALABASI"
```

---

### الخطوة 6: مسح الكاش

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

### الخطوة 7: إنشاء جين CLIENT_REQUIREMENTS

```bash
# إنشاء المجلدات
mkdir -p /home/u220657238/public_html/app/Genes/CLIENT_REQUIREMENTS/CLIENTS/ALABASI

# إنشاء الملفات
touch /home/u220657238/public_html/app/Genes/CLIENT_REQUIREMENTS/README.md
touch /home/u220657238/public_html/app/Genes/CLIENT_REQUIREMENTS/CLIENTS/ALABASI/requirements.md
touch /home/u220657238/public_html/app/Genes/CLIENT_REQUIREMENTS/CLIENTS/ALABASI/conversations.md
touch /home/u220657238/public_html/app/Genes/CLIENT_REQUIREMENTS/CLIENTS/ALABASI/implementation.md
touch /home/u220657238/public_html/app/Genes/CLIENT_REQUIREMENTS/CLIENTS/ALABASI/status.md
```

ثم نسخ المحتوى من الملفات الموجودة في `/home/ubuntu/upload/`

---

### الخطوة 8: تحديث صفحة الجينات

تحديث ملف `resources/views/modules/genes.blade.php` لعرض الجينات المتاحة.

---

## ✅ التحقق من التثبيت

### 1. التحقق من الجداول

```bash
php artisan tinker

# فحص الجداول
DB::table('clients')->count();
DB::table('client_genes')->count();
```

### 2. التحقق من العميل

```bash
php artisan tinker

$client = \App\Models\Client::where('code', 'ALABASI')->first();
echo $client->name; // يجب أن يطبع: العباسي
```

### 3. التحقق من الجينات المفعلة

```bash
php artisan tinker

$genes = \App\Helpers\GeneHelper::getActiveGenes();
print_r($genes); // يجب أن يعرض: PARTNERSHIP_ACCOUNTING, CLIENT_REQUIREMENTS
```

### 4. فتح صفحة الجينات

افتح المتصفح:
```
https://mediumblue-albatross-218540.hostingersite.com/genes
```

يجب أن ترى:
- ✅ قائمة الجينات المتاحة
- ✅ حالة كل جين (مفعل/معطل)
- ✅ أزرار التحكم

---

## 🎯 ما تم إنجازه

✅ **نظام إدارة الجينات:**
- جدول clients
- جدول client_genes
- Model Client
- Model ClientGene
- Helper GeneHelper
- Config system.php
- Controller GeneController

✅ **العميل العباسي:**
- تم إضافته بكود ALABASI
- تم تفعيل جين PARTNERSHIP_ACCOUNTING
- تم تفعيل جين CLIENT_REQUIREMENTS

✅ **بيانات الشراكات:**
- 5 شركاء (العباسي + 4 شركاء آخرين)
- 3 شراكات (محطات الحديدة، محطة معبر، سوبر ماركت صنعاء)

✅ **جين CLIENT_REQUIREMENTS:**
- هيكل كامل للتوثيق
- مجلد خاص بالعميل العباسي
- 4 ملفات توثيق

---

## 📊 الإحصائيات

| المكون | العدد |
|--------|-------|
| Migrations | 2 |
| Models | 2 |
| Helpers | 1 |
| Configs | 1 |
| Controllers | 1 |
| Seeders | 2 |
| **المجموع** | **9 ملفات** |

---

## 🚨 ملاحظات مهمة

### 1. نسب الملكية
⚠️ يجب تحديد نسب الملكية الفعلية للشركاء بعد إنشاء Units:
- محطات الحديدة: العباسي (؟%) + الشريك الأول (؟%)
- محطة معبر: العباسي (؟%) + الثاني (؟%) + الثالث (؟%)
- سوبر ماركت صنعاء: العباسي (؟%) + الرابع (؟%) + الخامس (؟%)

### 2. ربط الشركاء بالـ Units
⚠️ بعد إنشاء Units، يجب إضافة السجلات في جدول `partnership_shares`:

```php
PartnershipShare::create([
    'partner_id' => $partner->id,
    'unit_id' => $unit->id,
    'share_percentage' => 70.00,
]);
```

### 3. إنشاء المحطات
⚠️ يجب إنشاء Projects للمحطات الخمس:
1. محطة الدهمية
2. محطة الصبالية
3. محطة جمال
4. محطة غليل
5. محطة الساحل الغربي

---

## 📞 الدعم

إذا واجهت أي مشكلة:
1. راجع ملف اللوج: `storage/logs/laravel.log`
2. تأكد من صلاحيات الملفات
3. تأكد من تشغيل Migrations
4. تأكد من مسح الكاش

---

**آخر تحديث:** 2025-11-30  
**الحالة:** ✅ جاهز للتثبيت
