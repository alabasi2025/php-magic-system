# 🤝 PARTNERSHIP_ACCOUNTING Gene

> **جين محاسبة الشراكات**  
> نظام شامل لإدارة الشراكات والإيرادات والمصروفات وتوزيع الأرباح

---

## 📋 نظرة عامة

جين **PARTNERSHIP_ACCOUNTING** هو نظام متكامل لإدارة الشراكات التجارية، مصمم خصيصاً للأعمال التي تحتوي على شركاء متعددين بنسب ملكية مختلفة. يوفر الجين نظاماً مبسطاً لتسجيل الإيرادات والمصروفات، حساب الأرباح تلقائياً، وتوزيعها على الشركاء حسب نسبهم.

---

## 🎯 الغرض من الجين

### المشكلة
الشركات التي تعمل بنظام الشراكات تواجه تحديات في:
- تتبع نسب ملكية الشركاء
- حساب الأرباح بدقة
- توزيع الأرباح بشكل عادل
- إعداد تقارير مالية واضحة لكل شريك

### الحل
يوفر هذا الجين:
- ✅ إدارة مركزية للشركاء ونسب ملكيتهم
- ✅ تسجيل مبسط للإيرادات والمصروفات
- ✅ حساب تلقائي للأرباح (إيرادات - مصروفات)
- ✅ توزيع تلقائي للأرباح حسب النسب
- ✅ تقارير شاملة لكل شراكة ومشروع

---

## 🏗️ البنية التنظيمية

يتكامل الجين مع البنية الخماسية لـ SEMOP:

```
Holding (الشركة القابضة)
  └── Unit (الوحدة) = الشراكة
      ├── Partners (الشركاء)
      │   └── Partnership Shares (نسب الملكية)
      └── Projects (المشاريع)
          ├── Revenues (إيرادات)
          ├── Expenses (مصروفات)
          └── Profits (أرباح)
```

### مثال عملي
```
Holding: "مجموعة أعمال العباسي"
  └── Unit: "شراكة محطات الحديدة"
      ├── Partner 1: العباسي (70%)
      ├── Partner 2: الشريك الأول (30%)
      └── Projects:
          ├── محطة الدهمية
          ├── محطة الصبالية
          ├── محطة جمال
          ├── محطة غليل
          └── محطة الساحل الغربي
```

---

## 📊 قاعدة البيانات

### الجداول (6 جداول)

#### 1. partners - الشركاء
```sql
- id
- code (كود فريد)
- name (الاسم)
- email
- phone
- national_id (رقم الهوية)
- holding_id
- status (active/inactive)
- created_by, updated_by
- timestamps, soft_deletes
```

#### 2. partnership_shares - نسب الملكية
```sql
- id
- partner_id (الشريك)
- unit_id (الوحدة/الشراكة)
- ownership_percentage (نسبة الملكية)
- start_date (تاريخ البداية)
- end_date (تاريخ الانتهاء - nullable)
- notes
- created_by, updated_by
- timestamps, soft_deletes
```

#### 3. simple_revenues - الإيرادات
```sql
- id
- revenue_number (رقم الإيراد)
- holding_id
- unit_id
- project_id
- amount (المبلغ)
- revenue_date (تاريخ الإيراد)
- revenue_source (مصدر الإيراد)
- description
- notes
- created_by, updated_by
- timestamps, soft_deletes
```

#### 4. simple_expenses - المصروفات
```sql
- id
- expense_number (رقم المصروف)
- holding_id
- unit_id
- project_id
- amount (المبلغ)
- expense_date (تاريخ المصروف)
- expense_type (نوع المصروف)
- description
- notes
- created_by, updated_by
- timestamps, soft_deletes
```

#### 5. profit_calculations - حسابات الأرباح
```sql
- id
- calculation_number (رقم الحساب)
- unit_id (الوحدة/الشراكة)
- period_start (بداية الفترة)
- period_end (نهاية الفترة)
- total_revenues (إجمالي الإيرادات)
- total_expenses (إجمالي المصروفات)
- net_profit (صافي الربح)
- calculation_date (تاريخ الحساب)
- notes
- created_by, updated_by
- timestamps, soft_deletes
```

#### 6. profit_distributions - توزيعات الأرباح
```sql
- id
- distribution_number (رقم التوزيع)
- profit_calculation_id (حساب الربح)
- partner_id (الشريك)
- ownership_percentage (نسبة الملكية)
- profit_share (حصة الربح)
- distribution_date (تاريخ التوزيع)
- payment_status (pending/paid)
- payment_date (تاريخ الدفع)
- notes
- created_by, updated_by
- timestamps, soft_deletes
```

---

## 🎨 Models (6 Models)

### 1. Partner
```php
- Relations: hasMany(PartnershipShare), hasMany(ProfitDistribution)
- Scopes: active(), inactive()
- Accessors: formatted_phone, full_info
```

### 2. PartnershipShare
```php
- Relations: belongsTo(Partner), belongsTo(Unit)
- Scopes: active(), byUnit(), byPartner()
- Accessors: formatted_percentage
```

### 3. SimpleRevenue
```php
- Relations: belongsTo(Holding), belongsTo(Unit), belongsTo(Project)
- Scopes: byPeriod(), byProject(), byUnit()
- Accessors: formatted_amount
```

### 4. SimpleExpense
```php
- Relations: belongsTo(Holding), belongsTo(Unit), belongsTo(Project)
- Scopes: byPeriod(), byProject(), byUnit(), byType()
- Accessors: formatted_amount
```

### 5. ProfitCalculation
```php
- Relations: belongsTo(Unit), hasMany(ProfitDistribution)
- Scopes: byPeriod(), byUnit()
- Accessors: formatted_net_profit
```

### 6. ProfitDistribution
```php
- Relations: belongsTo(ProfitCalculation), belongsTo(Partner)
- Scopes: pending(), paid(), byPartner()
- Accessors: formatted_profit_share
```

---

## 🔧 Controllers (4 Controllers)

### 1. PartnerController
**الوظائف:**
- `index()` - قائمة الشركاء
- `store()` - إضافة شريك جديد
- `show()` - عرض تفاصيل شريك
- `update()` - تحديث بيانات شريك
- `destroy()` - حذف شريك
- `getShares()` - عرض نسب ملكية الشريك
- `updateShares()` - تحديث نسب الملكية

### 2. RevenueController
**الوظائف:**
- `index()` - قائمة الإيرادات
- `store()` - تسجيل إيراد جديد
- `show()` - عرض تفاصيل إيراد
- `update()` - تحديث إيراد
- `destroy()` - حذف إيراد
- `byProject()` - إيرادات حسب المشروع
- `byUnit()` - إيرادات حسب الوحدة

### 3. ExpenseController
**الوظائف:**
- `index()` - قائمة المصروفات
- `store()` - تسجيل مصروف جديد
- `show()` - عرض تفاصيل مصروف
- `update()` - تحديث مصروف
- `destroy()` - حذف مصروف
- `byProject()` - مصروفات حسب المشروع
- `byUnit()` - مصروفات حسب الوحدة
- `byType()` - مصروفات حسب النوع

### 4. ProfitController
**الوظائف:**
- `calculate()` - حساب الأرباح لفترة محددة
- `listCalculations()` - قائمة حسابات الأرباح
- `showCalculation()` - عرض تفاصيل حساب
- `distribute()` - توزيع الأرباح على الشركاء
- `listDistributions()` - قائمة التوزيعات
- `showDistribution()` - عرض تفاصيل توزيع
- `byUnit()` - أرباح حسب الوحدة
- `byPartner()` - أرباح حسب الشريك

---

## ⚙️ Services (1 Service)

### ProfitCalculationService

**الوظائف الرئيسية:**

#### 1. calculateProfit(unitId, periodStart, periodEnd)
```php
// حساب الأرباح لوحدة معينة في فترة محددة
// الخطوات:
// 1. جمع الإيرادات في الفترة
// 2. جمع المصروفات في الفترة
// 3. حساب صافي الربح = إيرادات - مصروفات
// 4. حفظ النتيجة في profit_calculations
```

#### 2. distributeProfit(profitCalculationId)
```php
// توزيع الأرباح على الشركاء
// الخطوات:
// 1. جلب حساب الربح
// 2. جلب الشركاء ونسبهم
// 3. حساب حصة كل شريك = صافي الربح × نسبة الملكية
// 4. حفظ التوزيعات في profit_distributions
```

#### 3. getPartnerProfitHistory(partnerId)
```php
// تاريخ أرباح شريك معين
```

#### 4. getUnitProfitSummary(unitId)
```php
// ملخص أرباح وحدة معينة
```

---

## 📊 التقارير

### 1. تقرير الإيرادات
- إيرادات حسب الفترة
- إيرادات حسب المشروع
- إيرادات حسب المصدر

### 2. تقرير المصروفات
- مصروفات حسب الفترة
- مصروفات حسب المشروع
- مصروفات حسب النوع

### 3. تقرير الأرباح
- أرباح حسب الفترة
- أرباح حسب الوحدة
- مقارنة الأرباح بين الفترات

### 4. تقرير توزيع الأرباح
- توزيعات حسب الشريك
- توزيعات حسب الفترة
- حالة الدفع

### 5. تقرير ملخص الشراكة
- نظرة شاملة على الشراكة
- الشركاء ونسبهم
- الإيرادات والمصروفات
- الأرباح الموزعة

### 6. تقرير مقارنة المحطات
- مقارنة أداء المشاريع
- أعلى إيرادات
- أعلى مصروفات
- أعلى أرباح

---

## 🛣️ API Endpoints

### الشركاء
```
GET    /api/partnership/partners
POST   /api/partnership/partners
GET    /api/partnership/partners/{id}
PUT    /api/partnership/partners/{id}
DELETE /api/partnership/partners/{id}
GET    /api/partnership/partners/{id}/shares
POST   /api/partnership/partners/{id}/shares
```

### الإيرادات
```
GET    /api/partnership/revenues
POST   /api/partnership/revenues
GET    /api/partnership/revenues/{id}
PUT    /api/partnership/revenues/{id}
DELETE /api/partnership/revenues/{id}
GET    /api/partnership/revenues/project/{projectId}
GET    /api/partnership/revenues/unit/{unitId}
```

### المصروفات
```
GET    /api/partnership/expenses
POST   /api/partnership/expenses
GET    /api/partnership/expenses/{id}
PUT    /api/partnership/expenses/{id}
DELETE /api/partnership/expenses/{id}
GET    /api/partnership/expenses/project/{projectId}
GET    /api/partnership/expenses/unit/{unitId}
GET    /api/partnership/expenses/by-type
```

### الأرباح
```
POST   /api/partnership/profits/calculate
GET    /api/partnership/profits/calculations
GET    /api/partnership/profits/calculations/{id}
POST   /api/partnership/profits/distribute/{calculationId}
GET    /api/partnership/profits/distributions
GET    /api/partnership/profits/distributions/{id}
GET    /api/partnership/profits/unit/{unitId}
GET    /api/partnership/profits/partner/{partnerId}
```

### التقارير
```
GET    /api/partnership/reports/revenues
GET    /api/partnership/reports/expenses
GET    /api/partnership/reports/profits
GET    /api/partnership/reports/distributions
GET    /api/partnership/reports/partnership-summary/{unitId}
GET    /api/partnership/reports/projects-comparison
```

---

## 🚀 الاستخدام

### 1. إعداد الشراكة

```php
// 1. إنشاء الشركاء
POST /api/partnership/partners
{
    "name": "العباسي",
    "email": "alabasi@example.com",
    "phone": "777123456"
}

POST /api/partnership/partners
{
    "name": "الشريك الأول",
    "email": "partner1@example.com",
    "phone": "777654321"
}

// 2. تحديد نسب الملكية
POST /api/partnership/partners/1/shares
{
    "unit_id": 1,
    "ownership_percentage": 70
}

POST /api/partnership/partners/2/shares
{
    "unit_id": 1,
    "ownership_percentage": 30
}
```

### 2. تسجيل الإيرادات والمصروفات

```php
// تسجيل إيراد
POST /api/partnership/revenues
{
    "unit_id": 1,
    "project_id": 1,
    "amount": 50000,
    "revenue_date": "2025-11-01",
    "revenue_source": "بيع كهرباء",
    "description": "إيراد شهر نوفمبر - محطة الدهمية"
}

// تسجيل مصروف
POST /api/partnership/expenses
{
    "unit_id": 1,
    "project_id": 1,
    "amount": 20000,
    "expense_date": "2025-11-05",
    "expense_type": "وقود",
    "description": "ديزل شهر نوفمبر"
}
```

### 3. حساب وتوزيع الأرباح

```php
// حساب الأرباح
POST /api/partnership/profits/calculate
{
    "unit_id": 1,
    "period_start": "2025-11-01",
    "period_end": "2025-11-30"
}

// توزيع الأرباح
POST /api/partnership/profits/distribute/1
```

### 4. الحصول على التقارير

```php
// تقرير ملخص الشراكة
GET /api/partnership/reports/partnership-summary/1

// تقرير مقارنة المحطات
GET /api/partnership/reports/projects-comparison?unit_id=1
```

---

## ✅ المميزات

### 1. البساطة
- ❌ بدون دليل محاسبي معقد
- ✅ معادلة بسيطة: إيرادات - مصروفات = أرباح
- ✅ واجهات سهلة الاستخدام

### 2. الدقة
- ✅ حساب تلقائي للأرباح
- ✅ توزيع دقيق حسب النسب
- ✅ تتبع كامل للعمليات

### 3. المرونة
- ✅ دعم شراكات متعددة
- ✅ دعم مشاريع متعددة
- ✅ نسب ملكية قابلة للتعديل

### 4. الشفافية
- ✅ تقارير واضحة لكل شريك
- ✅ تتبع كامل للإيرادات والمصروفات
- ✅ سجل كامل للتوزيعات

---

## 🔐 الأمان

- ✅ **Authentication:** جميع المسارات محمية بـ `auth:api`
- ✅ **Audit Trail:** تسجيل `created_by` و `updated_by` لكل عملية
- ✅ **Soft Deletes:** الحذف الآمن للبيانات
- ✅ **Validation:** التحقق من صحة البيانات قبل الحفظ

---

## 📝 ملاحظات مهمة

### 1. نسب الملكية
- يجب أن يكون مجموع نسب الملكية = 100%
- يتم التحقق من ذلك عند إضافة/تحديث النسب

### 2. الفترات المالية
- يمكن حساب الأرباح لأي فترة زمنية
- الفترات لا تتداخل تلقائياً

### 3. التوزيعات
- التوزيع يتم بعد الحساب
- يمكن تتبع حالة الدفع (pending/paid)

---

## 🔄 التكامل مع الجينات الأخرى

### CASHIERS
- يمكن ربط الإيرادات بالصرافين
- تسجيل الإيرادات من الصرافين مباشرة

### INTERMEDIATE_ACCOUNTS
- استخدام الحسابات الوسيطة للتسويات
- تسويات بين الشراكة والمحطات

### WALLETS
- إنشاء محافظ للشركاء
- تحويل الأرباح للمحافظ تلقائياً

---

## 📚 أمثلة الاستخدام

### مثال 1: شراكة محطات الحديدة

**السيناريو:**
- العباسي (70%) + الشريك الأول (30%)
- 5 محطات كهرباء
- إيرادات شهرية: 250,000 ريال
- مصروفات شهرية: 150,000 ريال

**النتيجة:**
- صافي الربح: 100,000 ريال
- حصة العباسي: 70,000 ريال (70%)
- حصة الشريك الأول: 30,000 ريال (30%)

### مثال 2: شراكة ثلاثية

**السيناريو:**
- شريك أ (50%) + شريك ب (30%) + شريك ج (20%)
- مشروع واحد
- إيرادات: 300,000 ريال
- مصروفات: 200,000 ريال

**النتيجة:**
- صافي الربح: 100,000 ريال
- حصة شريك أ: 50,000 ريال
- حصة شريك ب: 30,000 ريال
- حصة شريك ج: 20,000 ريال

---

## 🛠️ التطوير المستقبلي

### المرحلة 2
- [ ] إدارة العدادات والعملاء
- [ ] فواتير تفصيلية
- [ ] نظام الاشتراكات

### المرحلة 3
- [ ] تكامل مع البنوك
- [ ] دفع إلكتروني للشركاء
- [ ] إشعارات تلقائية

### المرحلة 4
- [ ] تحليلات متقدمة
- [ ] توقعات الأرباح
- [ ] لوحات معلومات تفاعلية

---

## 📞 الدعم

لأي استفسارات أو مشاكل، يرجى التواصل مع فريق التطوير.

---

**آخر تحديث:** 2025-11-30  
**الإصدار:** 1.0.0  
**الحالة:** ✅ جاهز للاستخدام
