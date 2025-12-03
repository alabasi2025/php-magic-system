-- تطبيق تعديلات الدليل الرئيسي ودليل الحسابات الوسيطة
-- تاريخ: 2025-01-03

-- إضافة حقول جديدة لجدول chart_groups
ALTER TABLE `chart_groups` 
ADD COLUMN `parent_group_id` BIGINT UNSIGNED NULL AFTER `unit_id` COMMENT 'الدليل الأب (للفروع)',
ADD COLUMN `source_group_id` BIGINT UNSIGNED NULL AFTER `parent_group_id` COMMENT 'الدليل الأصلي (لفروع الحسابات الوسيطة)',
ADD COLUMN `is_master` TINYINT(1) NOT NULL DEFAULT 0 AFTER `type` COMMENT 'هل هو الدليل الرئيسي للوحدة؟',
ADD INDEX `chart_groups_parent_group_id_index` (`parent_group_id`),
ADD INDEX `chart_groups_source_group_id_index` (`source_group_id`),
ADD INDEX `chart_groups_is_master_index` (`is_master`),
ADD CONSTRAINT `chart_groups_parent_group_id_foreign` FOREIGN KEY (`parent_group_id`) REFERENCES `chart_groups` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `chart_groups_source_group_id_foreign` FOREIGN KEY (`source_group_id`) REFERENCES `chart_groups` (`id`) ON DELETE CASCADE;

-- تحديث enum type لإضافة master_chart و intermediate_master
ALTER TABLE `chart_groups` 
MODIFY COLUMN `type` ENUM(
    'master_chart',
    'intermediate_master',
    'payroll',
    'final_accounts',
    'assets',
    'budget',
    'projects',
    'inventory',
    'sales',
    'purchases',
    'custom'
) DEFAULT 'custom' COMMENT 'نوع الدليل';

-- ملاحظات:
-- 1. سيتم إنشاء الدليل الرئيسي تلقائياً عند أول زيارة لصفحة الدليل الرئيسي
-- 2. سيتم إنشاء دليل الحسابات الوسيطة تلقائياً
-- 3. عند إنشاء دليل فرعي جديد، سيتم إنشاء فرع تلقائي في دليل الحسابات الوسيطة
