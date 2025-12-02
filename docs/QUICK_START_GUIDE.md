# دليل الاستخدام السريع - جين محاسبة الشراكات

## نظرة عامة

جين **PARTNERSHIP_ACCOUNTING** هو نظام متكامل لإدارة الشراكات في محطات الكهرباء، يتيح لك:
- تسجيل الشركاء ونسب ملكيتهم
- تتبع الإيرادات والمصروفات لكل محطة
- حساب الأرباح تلقائياً
- توزيع الأرباح على الشركاء حسب نسبهم

---

## 1. إضافة شركاء جدد

### مثال: إضافة شريك

**الطلب:**
```bash
POST /api/partnership/partners
Content-Type: application/json

{
  "name": "محمد العباسي",
  "email": "mohammed@example.com",
  "phone": "0501234567",
  "national_id": "1234567890",
  "address": "الرياض، المملكة العربية السعودية",
  "is_active": true
}
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم إنشاء الشريك بنجاح",
  "data": {
    "id": 1,
    "name": "محمد العباسي",
    "email": "mohammed@example.com",
    ...
  }
}
```

---

## 2. تحديد نسب الملكية

### مثال: تعيين حصة 40% لشريك في محطة معينة

**الطلب:**
```bash
POST /api/partnership/partners/1/shares
Content-Type: application/json

{
  "unit_id": 5,
  "project_id": 10,
  "share_percentage": 40.0,
  "is_active": true
}
```

**ملاحظة:** النظام يتحقق تلقائياً من عدم تجاوز مجموع الحصص 100%

---

## 3. تسجيل الإيرادات

### مثال: تسجيل إيراد من بيع الكهرباء

**الطلب:**
```bash
POST /api/partnership/revenues
Content-Type: application/json

{
  "unit_id": 5,
  "project_id": 10,
  "revenue_date": "2025-11-30",
  "amount": 50000.00,
  "revenue_type": "بيع كهرباء",
  "description": "إيراد شهر نوفمبر 2025",
  "reference_number": "REV-2025-11-001"
}
```

---

## 4. تسجيل المصروفات

### مثال: تسجيل مصروف صيانة

**الطلب:**
```bash
POST /api/partnership/expenses
Content-Type: application/json

{
  "unit_id": 5,
  "project_id": 10,
  "expense_date": "2025-11-25",
  "amount": 15000.00,
  "expense_type": "صيانة",
  "description": "صيانة دورية للمولدات",
  "reference_number": "EXP-2025-11-001"
}
```

---

## 5. حساب الأرباح

### مثال: حساب أرباح محطة لشهر معين

**الطلب:**
```bash
POST /api/partnership/profits/calculate
Content-Type: application/json

{
  "unit_id": 5,
  "project_id": 10,
  "period_start": "2025-11-01",
  "period_end": "2025-11-30",
  "description": "أرباح شهر نوفمبر 2025"
}
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم حساب الأرباح بنجاح",
  "data": {
    "id": 1,
    "total_revenues": 50000.00,
    "total_expenses": 15000.00,
    "net_profit": 35000.00,
    "calculation_date": "2025-11-30"
  }
}
```

---

## 6. توزيع الأرباح

### مثال: توزيع الأرباح على الشركاء

**الطلب:**
```bash
POST /api/partnership/profits/distribute/1
Content-Type: application/json

{
  "distribution_date": "2025-11-30",
  "notes": "توزيع أرباح شهر نوفمبر"
}
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم توزيع الأرباح بنجاح",
  "data": {
    "distributions": [
      {
        "partner_id": 1,
        "partner_name": "محمد العباسي",
        "share_percentage": 40.0,
        "amount": 14000.00
      },
      {
        "partner_id": 2,
        "partner_name": "أحمد السعيد",
        "share_percentage": 60.0,
        "amount": 21000.00
      }
    ],
    "total_distributed": 35000.00
  }
}
```

---

## 7. التقارير

### تقرير ملخص الشراكة

**الطلب:**
```bash
GET /api/partnership/reports/partnership-summary/5
```

**الاستجابة:**
```json
{
  "success": true,
  "data": {
    "unit_id": 5,
    "unit_name": "محطة الرياض الرئيسية",
    "partners": [
      {
        "name": "محمد العباسي",
        "share_percentage": 40.0,
        "total_profit_received": 140000.00
      },
      {
        "name": "أحمد السعيد",
        "share_percentage": 60.0,
        "total_profit_received": 210000.00
      }
    ],
    "total_revenues": 500000.00,
    "total_expenses": 150000.00,
    "total_profits": 350000.00
  }
}
```

### تقرير مقارنة المحطات

**الطلب:**
```bash
GET /api/partnership/reports/projects-comparison?unit_id=5
```

---

## سيناريو كامل: إدارة شراكة محطة كهرباء

### الخطوة 1: إضافة الشركاء
```bash
# شريك 1: محمد العباسي (40%)
POST /api/partnership/partners
{
  "name": "محمد العباسي",
  "email": "mohammed@example.com",
  "phone": "0501234567"
}

# شريك 2: أحمد السعيد (60%)
POST /api/partnership/partners
{
  "name": "أحمد السعيد",
  "email": "ahmed@example.com",
  "phone": "0509876543"
}
```

### الخطوة 2: تحديد نسب الملكية
```bash
# حصة محمد العباسي: 40%
POST /api/partnership/partners/1/shares
{
  "unit_id": 5,
  "project_id": 10,
  "share_percentage": 40.0
}

# حصة أحمد السعيد: 60%
POST /api/partnership/partners/2/shares
{
  "unit_id": 5,
  "project_id": 10,
  "share_percentage": 60.0
}
```

### الخطوة 3: تسجيل الإيرادات الشهرية
```bash
POST /api/partnership/revenues
{
  "unit_id": 5,
  "project_id": 10,
  "revenue_date": "2025-11-30",
  "amount": 50000.00,
  "revenue_type": "بيع كهرباء",
  "description": "إيراد شهر نوفمبر"
}
```

### الخطوة 4: تسجيل المصروفات الشهرية
```bash
# مصروف الصيانة
POST /api/partnership/expenses
{
  "unit_id": 5,
  "project_id": 10,
  "expense_date": "2025-11-15",
  "amount": 10000.00,
  "expense_type": "صيانة",
  "description": "صيانة دورية"
}

# مصروف الوقود
POST /api/partnership/expenses
{
  "unit_id": 5,
  "project_id": 10,
  "expense_date": "2025-11-20",
  "amount": 5000.00,
  "expense_type": "وقود",
  "description": "وقود المولدات"
}
```

### الخطوة 5: حساب الأرباح
```bash
POST /api/partnership/profits/calculate
{
  "unit_id": 5,
  "project_id": 10,
  "period_start": "2025-11-01",
  "period_end": "2025-11-30",
  "description": "أرباح نوفمبر 2025"
}

# النتيجة:
# إجمالي الإيرادات: 50,000 ريال
# إجمالي المصروفات: 15,000 ريال
# صافي الربح: 35,000 ريال
```

### الخطوة 6: توزيع الأرباح
```bash
POST /api/partnership/profits/distribute/1
{
  "distribution_date": "2025-11-30",
  "notes": "توزيع أرباح نوفمبر"
}

# النتيجة:
# محمد العباسي (40%): 14,000 ريال
# أحمد السعيد (60%): 21,000 ريال
```

---

## نصائح مهمة

### 1. التحقق من النسب
- تأكد دائماً أن مجموع نسب الشركاء = 100%
- النظام يمنع تلقائياً تجاوز 100%

### 2. تنظيم المصروفات
استخدم أنواع مصروفات واضحة مثل:
- صيانة
- وقود
- رواتب
- إيجارات
- مرافق (كهرباء، ماء)

### 3. المراجع
استخدم نظام ترقيم واضح للمراجع:
- `REV-2025-11-001` للإيرادات
- `EXP-2025-11-001` للمصروفات

### 4. التقارير الدورية
احرص على إصدار التقارير بشكل دوري:
- تقرير شهري للإيرادات والمصروفات
- تقرير ربع سنوي لتوزيع الأرباح
- تقرير سنوي شامل

---

## الأسئلة الشائعة

### س: هل يمكن تعديل نسب الشركاء بعد البدء؟
ج: نعم، يمكنك تحديث نسب الشركاء في أي وقت، لكن يُنصح بتوثيق التغييرات.

### س: ماذا لو كان لدي أكثر من محطة؟
ج: يمكنك إدارة كل محطة بشكل منفصل باستخدام `unit_id` و `project_id` مختلفة.

### س: هل يمكن حذف شريك؟
ج: نعم، لكن فقط إذا لم يكن لديه حصص نشطة.

### س: كيف أحسب الأرباح لفترة معينة؟
ج: استخدم `period_start` و `period_end` عند حساب الأرباح.

---

## الدعم الفني

للمساعدة أو الاستفسارات:
- البريد الإلكتروني: support@semop.com
- الهاتف: +966 XX XXX XXXX

---

**إصدار الدليل:** 1.0  
**التاريخ:** 30 نوفمبر 2025
