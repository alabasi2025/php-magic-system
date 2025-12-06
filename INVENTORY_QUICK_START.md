# 🚀 دليل البدء السريع - نظام إدارة المخازن v4.1.0

## ⚡ التثبيت السريع

### 1. تشغيل Migrations
```bash
php artisan migrate
```

### 2. تشغيل Seeder (اختياري)
```bash
php artisan db:seed --class=InventorySystemSeeder
```

### 3. الوصول للنظام
```
http://your-domain/inventory/dashboard
```

---

## 📋 الملفات الرئيسية

### Models
```
app/Models/Warehouse.php       # المخازن
app/Models/Item.php            # الأصناف
app/Models/ItemUnit.php        # الوحدات
app/Models/StockMovement.php   # حركات المخزون
```

### Controllers
```
app/Http/Controllers/WarehouseController.php
app/Http/Controllers/ItemController.php
app/Http/Controllers/StockMovementController.php
app/Http/Controllers/InventoryReportController.php
```

### Services (منطق الأعمال)
```
app/Services/InventoryService.php          # العمليات الأساسية
app/Services/StockMovementService.php      # معالجة الحركات + التكامل المحاسبي
app/Services/InventoryReportService.php    # التقارير
```

---

## 🎯 الوظائف الأساسية

### إنشاء مخزن جديد
```php
Warehouse::create([
    'code' => 'WH001',
    'name' => 'المخزن الرئيسي',
    'location' => 'صنعاء',
    'status' => 'active',
]);
```

### إنشاء صنف جديد
```php
Item::create([
    'sku' => 'ITEM001',
    'name' => 'صنف تجريبي',
    'unit_id' => 1,
    'min_stock' => 10,
    'max_stock' => 100,
    'unit_price' => 50,
    'status' => 'active',
]);
```

### إضافة حركة مخزون (عبر Service)
```php
$service = new StockMovementService();

$movement = $service->createMovement([
    'movement_type' => 'stock_in',
    'warehouse_id' => 1,
    'item_id' => 1,
    'quantity' => 100,
    'unit_cost' => 50,
    'movement_date' => now(),
    'created_by' => auth()->id(),
]);

// اعتماد الحركة (سيتم إنشاء قيد محاسبي تلقائياً)
$service->approveMovement($movement);
```

### الحصول على المخزون الحالي
```php
$inventoryService = new InventoryService();
$currentStock = $inventoryService->getCurrentStock($itemId, $warehouseId);
```

---

## 📊 التقارير

### تقرير المخزون الحالي
```
GET /inventory/reports/current-stock?warehouse_id=1
```

### الأصناف الناقصة
```
GET /inventory/reports/below-min-stock
```

### تقرير الحركات
```
GET /inventory/reports/movements?date_from=2025-01-01&date_to=2025-12-31
```

### تصدير Excel
```
GET /inventory/reports/export-current-stock?warehouse_id=1
```

---

## 🔗 التكامل مع النظام المحاسبي

عند اعتماد حركة المخزون، يتم تلقائياً:

### إدخال بضاعة (Stock In)
```
مدين: المخزون
دائن: الموردين
```

### إخراج بضاعة (Stock Out)
```
مدين: تكلفة البضاعة المباعة
دائن: المخزون
```

### تسوية المخزون (Adjustment)
```
مدين: المخزون (إذا موجب)
دائن: تسوية المخزون
```

---

## 🧪 الاختبارات

```bash
# تشغيل جميع اختبارات النظام
php artisan test --filter InventorySystemTest

# تشغيل اختبار محدد
php artisan test --filter test_can_create_warehouse
```

---

## 🛠️ الصيانة

### إضافة وحدة قياس جديدة
```php
ItemUnit::create([
    'code' => 'TON',
    'name' => 'طن',
    'name_en' => 'Ton',
    'symbol' => 't',
    'status' => 'active',
]);
```

### تعطيل مخزن
```php
$warehouse->update(['status' => 'inactive']);
```

### حذف صنف (Soft Delete)
```php
$item->delete(); // يحتفظ بالسجل في قاعدة البيانات
```

---

## 🔐 الصلاحيات (جاهز للتطبيق)

```php
// في Controller
$this->authorize('view', $warehouse);
$this->authorize('create', Warehouse::class);
$this->authorize('update', $warehouse);
$this->authorize('delete', $warehouse);
```

---

## 📱 المسارات الرئيسية

| المسار | الوصف |
|--------|-------|
| `/inventory/dashboard` | لوحة التحكم |
| `/inventory/warehouses` | قائمة المخازن |
| `/inventory/items` | قائمة الأصناف |
| `/inventory/stock-movements` | حركات المخزون |
| `/inventory/reports/current-stock` | تقرير المخزون الحالي |
| `/inventory/reports/below-min-stock` | الأصناف الناقصة |

---

## 🐛 استكشاف الأخطاء

### خطأ: "Class not found"
```bash
composer dump-autoload
```

### خطأ: "Table doesn't exist"
```bash
php artisan migrate
```

### خطأ: "Foreign key constraint"
تأكد من تشغيل Migrations بالترتيب الصحيح:
1. item_units
2. warehouses
3. items
4. stock_movements

---

## 📞 الدعم

للتفاصيل الكاملة، راجع: `INVENTORY_SYSTEM_REPORT.md`

---

**تم بحمد الله ✨**
