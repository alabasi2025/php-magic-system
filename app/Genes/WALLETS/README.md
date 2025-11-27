# 🧬 Gene: WALLETS (المحافظ)

## 📖 نظرة عامة

جين المحافظ (WALLETS) هو أحد الجينات الأساسية في النظام السحري، مسؤول عن إدارة المحافظ الإلكترونية للعملاء والموردين.

**الإصدار:** 1.0.0  
**تاريخ الإنشاء:** 2025-11-27  
**المهام المغطاة:** 2101-2200 (100 مهمة)

---

## 🎯 الهدف

إدارة شاملة للمحافظ الإلكترونية، تشمل:
- إنشاء وإدارة المحافظ
- تسجيل المعاملات (إيداع/سحب)
- التحويلات بين المحافظ
- التقارير والأرصدة

---

## 🏗️ البنية الخماسية

### 1. Models (النماذج)
- **Wallet**: نموذج المحفظة
- **WalletTransaction**: نموذج المعاملات
- **WalletTransfer**: نموذج التحويلات

### 2. Services (الخدمات)
- **WalletService**: خدمة إدارة المحافظ
- **WalletTransactionService**: خدمة إدارة المعاملات

### 3. Controllers (المتحكمات)
- **WalletController**: متحكم المحافظ
- **WalletTransactionController**: متحكم المعاملات
- **WalletTransferController**: متحكم التحويلات

### 4. Database (قاعدة البيانات)
- **Migrations**: 3 جداول (wallets, wallet_transactions, wallet_transfers)

### 5. Routes (المسارات)
- مسارات Web
- مسارات API

---

## 📊 قاعدة البيانات

### جدول: wallets

| الحقل | النوع | الوصف |
|------|------|-------|
| id | bigint | المعرف الفريد |
| code | string | كود المحفظة |
| name | string | اسم المحفظة |
| entity_id | bigint | المنشأة |
| owner_type | string | نوع المالك (customer/supplier/employee) |
| owner_id | bigint | معرف المالك |
| currency_id | bigint | العملة |
| balance | decimal | الرصيد الحالي |
| available_balance | decimal | الرصيد المتاح |
| reserved_balance | decimal | الرصيد المحجوز |
| min_balance | decimal | الحد الأدنى للرصيد |
| max_balance | decimal | الحد الأقصى للرصيد |
| status | enum | الحالة (active/inactive/blocked/closed) |
| is_active | boolean | نشط؟ |

### جدول: wallet_transactions

| الحقل | النوع | الوصف |
|------|------|-------|
| id | bigint | المعرف الفريد |
| code | string | كود المعاملة |
| wallet_id | bigint | المحفظة |
| entity_id | bigint | المنشأة |
| transaction_type | enum | نوع المعاملة (credit/debit) |
| amount | decimal | المبلغ |
| balance_before | decimal | الرصيد قبل المعاملة |
| balance_after | decimal | الرصيد بعد المعاملة |
| reference_type | string | نوع المرجع |
| reference_id | bigint | معرف المرجع |
| description | text | الوصف |
| transaction_date | datetime | تاريخ المعاملة |
| status | enum | الحالة (completed/reversed) |

### جدول: wallet_transfers

| الحقل | النوع | الوصف |
|------|------|-------|
| id | bigint | المعرف الفريد |
| code | string | كود التحويل |
| entity_id | bigint | المنشأة |
| from_wallet_id | bigint | المحفظة المرسلة |
| to_wallet_id | bigint | المحفظة المستقبلة |
| amount | decimal | المبلغ |
| fees | decimal | الرسوم |
| net_amount | decimal | المبلغ الصافي |
| description | text | الوصف |
| transfer_date | datetime | تاريخ التحويل |
| status | enum | الحالة (pending/approved/rejected/cancelled) |

---

## 🔌 API Endpoints

### Wallets

```
GET    /api/wallets                - قائمة المحافظ
POST   /api/wallets                - إنشاء محفظة جديدة
GET    /api/wallets/{id}           - تفاصيل محفظة
PUT    /api/wallets/{id}           - تحديث محفظة
DELETE /api/wallets/{id}           - حذف محفظة
GET    /api/wallets/{id}/balance   - الرصيد الحالي
```

### Transactions

```
GET    /api/wallets/transactions              - قائمة المعاملات
POST   /api/wallets/transactions              - إنشاء معاملة جديدة
GET    /api/wallets/transactions/{id}         - تفاصيل معاملة
POST   /api/wallets/transactions/{id}/reverse - عكس معاملة
```

### Transfers

```
GET    /api/wallets/transfers              - قائمة التحويلات
POST   /api/wallets/transfers              - إنشاء تحويل جديد
GET    /api/wallets/transfers/{id}         - تفاصيل تحويل
POST   /api/wallets/transfers/{id}/approve - اعتماد تحويل
POST   /api/wallets/transfers/{id}/reject  - رفض تحويل
POST   /api/wallets/transfers/{id}/cancel  - إلغاء تحويل
```

---

## 💡 أمثلة الاستخدام

### إنشاء محفظة جديدة

```php
use App\Genes\WALLETS\Services\WalletService;

$walletService = new WalletService();

$wallet = $walletService->createWallet([
    'name' => 'محفظة العميل أحمد',
    'entity_id' => 1,
    'owner_type' => 'customer',
    'owner_id' => 10,
    'currency_id' => 1,
    'min_balance' => 0.00,
    'max_balance' => 100000.00,
]);
```

### تسجيل معاملة

```php
use App\Genes\WALLETS\Services\WalletTransactionService;

$transactionService = new WalletTransactionService();

// إيداع
$transaction = $transactionService->createTransaction([
    'wallet_id' => 1,
    'entity_id' => 1,
    'transaction_type' => 'credit',
    'amount' => 500.00,
    'description' => 'إيداع من فاتورة مبيعات',
    'reference_type' => 'invoice',
    'reference_id' => 123,
]);

// سحب
$transaction = $transactionService->createTransaction([
    'wallet_id' => 1,
    'entity_id' => 1,
    'transaction_type' => 'debit',
    'amount' => 200.00,
    'description' => 'سحب لدفع فاتورة',
]);
```

### تحويل بين محفظتين

```php
use App\Genes\WALLETS\Services\WalletTransferService;

$transferService = new WalletTransferService();

$transfer = $transferService->createTransfer([
    'entity_id' => 1,
    'from_wallet_id' => 1,
    'to_wallet_id' => 2,
    'amount' => 1000.00,
    'fees' => 10.00,
    'description' => 'تحويل بين محافظ العملاء',
]);

// اعتماد التحويل
$transferService->approveTransfer($transfer->id);
```

---

## 📈 الإحصائيات

- **Models:** 3
- **Services:** 2
- **Controllers:** 3
- **Routes:** 18+ endpoint
- **Database Tables:** 3
- **Methods:** 35+ method

---

## 🔄 التكامل

يتكامل هذا الجين مع:
- **CUSTOMERS**: العملاء
- **SUPPLIERS**: الموردين
- **EMPLOYEES**: الموظفين
- **CURRENCIES**: العملات
- **INVOICES**: الفواتير
- **PAYMENTS**: المدفوعات

---

## 📝 ملاحظات

- لا يمكن تعديل أو حذف المعاملات بعد إنشائها (Financial Integrity)
- يتم استخدام معاملات عكسية (Reversal) لإلغاء المعاملات
- التحويلات تخضع لنظام الموافقات
- الرصيد يتم تحديثه تلقائياً وفورياً

---

## 🔒 الأمان

- جميع المعاملات محمية بـ Database Transactions
- التحقق من الرصيد قبل أي عملية سحب
- سجل كامل لجميع العمليات
- منع التلاعب بالأرصدة

---

## 🚀 الإصدارات القادمة

- [ ] دعم العملات المتعددة
- [ ] رسوم التحويلات المتغيرة
- [ ] حدود يومية وشهرية
- [ ] إشعارات فورية
- [ ] تكامل مع بوابات الدفع

---

**آخر تحديث:** 2025-11-27  
**الحالة:** ✅ مكتمل ونشط
