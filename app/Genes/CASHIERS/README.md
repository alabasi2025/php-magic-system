# 🧬 Gene: CASHIERS (الصرافين)

## 📖 نظرة عامة

جين الصرافين (CASHIERS) هو أحد الجينات الأساسية في النظام السحري، مسؤول عن إدارة الصرافين والخزائن النقدية في المنشآت.

**الإصدار:** 1.0.0  
**تاريخ الإنشاء:** 2025-11-27  
**المهام المغطاة:** 2001-2100 (100 مهمة)

---

## 🎯 الهدف

إدارة شاملة للصرافين والعمليات النقدية اليومية، تشمل:
- إدارة الصرافين والخزائن
- تسجيل المعاملات النقدية (إيداع/سحب)
- التسويات اليومية والشهرية
- التقارير والإحصائيات

---

## 🏗️ البنية الخماسية

### 1. Models (النماذج)
- **Cashier**: نموذج الصراف
- **CashierTransaction**: نموذج المعاملات النقدية
- **CashierSettlement**: نموذج التسويات

### 2. Services (الخدمات)
- **CashierService**: خدمة إدارة الصرافين
- **CashierTransactionService**: خدمة إدارة المعاملات
- **CashierSettlementService**: خدمة إدارة التسويات

### 3. Controllers (المتحكمات)
- **CashierController**: متحكم الصرافين
- **CashierTransactionController**: متحكم المعاملات
- **CashierSettlementController**: متحكم التسويات

### 4. Database (قاعدة البيانات)
- **Migrations**: 3 جداول (cashiers, cashier_transactions, cashier_settlements)

### 5. Routes (المسارات)
- مسارات Web
- مسارات API

---

## 📊 قاعدة البيانات

### جدول: cashiers

| الحقل | النوع | الوصف |
|------|------|-------|
| id | bigint | المعرف الفريد |
| code | string | كود الصراف |
| name | string | اسم الصراف |
| entity_id | bigint | المنشأة |
| branch_id | bigint | الفرع |
| user_id | bigint | المستخدم المسؤول |
| safe_id | bigint | الخزنة المرتبطة |
| opening_balance | decimal | الرصيد الافتتاحي |
| current_balance | decimal | الرصيد الحالي |
| daily_limit | decimal | الحد اليومي |
| max_transaction_limit | decimal | الحد الأقصى للمعاملة |
| status | enum | الحالة (active/inactive/closed) |
| is_active | boolean | نشط؟ |

### جدول: cashier_transactions

| الحقل | النوع | الوصف |
|------|------|-------|
| id | bigint | المعرف الفريد |
| code | string | كود المعاملة |
| cashier_id | bigint | الصراف |
| entity_id | bigint | المنشأة |
| transaction_type | enum | نوع المعاملة (deposit/withdrawal/transfer) |
| amount | decimal | المبلغ |
| currency_id | bigint | العملة |
| exchange_rate | decimal | سعر الصرف |
| amount_in_base_currency | decimal | المبلغ بالعملة الأساسية |
| reference_type | string | نوع المرجع |
| reference_id | bigint | معرف المرجع |
| description | text | الوصف |
| transaction_date | datetime | تاريخ المعاملة |
| status | enum | الحالة (pending/approved/rejected/cancelled) |

### جدول: cashier_settlements

| الحقل | النوع | الوصف |
|------|------|-------|
| id | bigint | المعرف الفريد |
| code | string | كود التسوية |
| cashier_id | bigint | الصراف |
| entity_id | bigint | المنشأة |
| settlement_date | date | تاريخ التسوية |
| opening_balance | decimal | الرصيد الافتتاحي |
| total_deposits | decimal | إجمالي الإيداعات |
| total_withdrawals | decimal | إجمالي السحوبات |
| closing_balance | decimal | الرصيد الختامي |
| actual_balance | decimal | الرصيد الفعلي |
| difference | decimal | الفرق |
| status | enum | الحالة (pending/approved/rejected) |

---

## 🔌 API Endpoints

### Cashiers

```
GET    /api/cashiers           - قائمة الصرافين
POST   /api/cashiers           - إنشاء صراف جديد
GET    /api/cashiers/{id}      - تفاصيل صراف
PUT    /api/cashiers/{id}      - تحديث صراف
DELETE /api/cashiers/{id}      - حذف صراف
```

### Transactions

```
GET    /api/cashiers/transactions              - قائمة المعاملات
POST   /api/cashiers/transactions              - إنشاء معاملة جديدة
GET    /api/cashiers/transactions/{id}         - تفاصيل معاملة
POST   /api/cashiers/transactions/{id}/approve - اعتماد معاملة
POST   /api/cashiers/transactions/{id}/reject  - رفض معاملة
POST   /api/cashiers/transactions/{id}/cancel  - إلغاء معاملة
```

### Settlements

```
GET    /api/cashiers/settlements        - قائمة التسويات
POST   /api/cashiers/settlements        - إنشاء تسوية جديدة
GET    /api/cashiers/settlements/{id}   - تفاصيل تسوية
PUT    /api/cashiers/settlements/{id}   - تحديث تسوية
DELETE /api/cashiers/settlements/{id}   - حذف تسوية
```

---

## 💡 أمثلة الاستخدام

### إنشاء صراف جديد

```php
use App\Genes\CASHIERS\Services\CashierService;

$cashierService = new CashierService();

$cashier = $cashierService->createCashier([
    'name' => 'صراف الفرع الرئيسي',
    'entity_id' => 1,
    'branch_id' => 1,
    'user_id' => 5,
    'opening_balance' => 10000.00,
    'daily_limit' => 50000.00,
    'max_transaction_limit' => 5000.00,
]);
```

### تسجيل معاملة

```php
use App\Genes\CASHIERS\Services\CashierTransactionService;

$transactionService = new CashierTransactionService();

$transaction = $transactionService->createTransaction([
    'cashier_id' => 1,
    'entity_id' => 1,
    'transaction_type' => 'deposit',
    'amount' => 1000.00,
    'description' => 'إيداع نقدي من العميل',
    'transaction_date' => now(),
]);

// اعتماد المعاملة
$transactionService->approveTransaction($transaction->id);
```

### إنشاء تسوية يومية

```php
use App\Genes\CASHIERS\Services\CashierSettlementService;

$settlementService = new CashierSettlementService();

$settlement = $settlementService->createSettlement([
    'cashier_id' => 1,
    'entity_id' => 1,
    'settlement_date' => today(),
    'opening_balance' => 10000.00,
    'total_deposits' => 5000.00,
    'total_withdrawals' => 3000.00,
    'closing_balance' => 12000.00,
    'actual_balance' => 12000.00,
    'difference' => 0.00,
]);
```

---

## 📈 الإحصائيات

- **Models:** 3
- **Services:** 3
- **Controllers:** 3
- **Routes:** 20+ endpoint
- **Database Tables:** 3
- **Methods:** 40+ method

---

## 🔄 التكامل

يتكامل هذا الجين مع:
- **INTERMEDIATE_ACCOUNTS**: الحسابات الوسيطة
- **SAFES**: الخزائن
- **BRANCHES**: الفروع
- **USERS**: المستخدمين
- **CURRENCIES**: العملات

---

## 📝 ملاحظات

- جميع المعاملات تخضع لنظام الموافقات
- يتم تسجيل جميع العمليات في سجل التدقيق
- الرصيد يتم تحديثه تلقائياً عند اعتماد المعاملات
- التسويات اليومية إلزامية

---

## 🚀 الإصدارات القادمة

- [ ] تكامل مع نظام نقاط البيع (POS)
- [ ] تقارير متقدمة
- [ ] إشعارات تلقائية
- [ ] تكامل مع البنوك

---

**آخر تحديث:** 2025-11-27  
**الحالة:** ✅ مكتمل ونشط
