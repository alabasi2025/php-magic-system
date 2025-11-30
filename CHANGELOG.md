# Changelog - SEMOP

## [v2.8.2] - 2025-11-30 - Partnership Accounting Edition

### Added
- ✅ **جين محاسبة الشراكات (PARTNERSHIP_ACCOUNTING)** - نظام كامل لإدارة الشراكات في محطات الكهرباء
  - 6 جداول قاعدة بيانات (partners, partnership_shares, simple_revenues, simple_expenses, profit_calculations, profit_distributions)
  - 36 مسار API لإدارة الشراكات
  - 6 Controllers (PartnerController, ExpenseController, RevenueController, ProfitController, PartnershipReportController, PartnershipController)
  - 6 Models كاملة مع العلاقات
  
- ✅ **واجهة مستخدم كاملة للجين**
  - صفحة رئيسية مع إحصائيات شاملة
  - صفحة إدارة الشركاء (جدول تفاعلي + بحث + CRUD)
  - صفحة إدارة الإيرادات (جدول + إحصائيات)
  - صفحة إدارة المصروفات (جدول + إحصائيات)
  - صفحة حساب وتوزيع الأرباح
  - صفحة التقارير (6 أنواع تقارير)
  - صفحة الإعدادات
  
- ✅ **تبويب في القائمة الجانبية**
  - إضافة "محاسبة الشراكات" في القائمة الرئيسية
  - أيقونة مخصصة (fa-handshake)
  - تصميم متناسق مع باقي النظام

### Fixed
- ✅ إصلاح خطأ 500 في النظام (مسار developer.logs)
- ✅ تحديث ملف index.php ليتوافق مع Laravel 12

### Technical Details
- Laravel Version: 12.40.2
- PHP Version: 8.x
- Database: MySQL
- Frontend: Tailwind CSS + Font Awesome
- Total Files: 7 Views + 6 Controllers + 6 Models + 6 Migrations

---

## [v2.8.1] - 2025-11-29 - Task Scheduler Edition

### Previous version
