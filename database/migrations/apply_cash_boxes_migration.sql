-- Migration: Add intermediate account and unit fields to cash_boxes table
-- Date: 2025-01-03
-- Description: Add unit_id and intermediate_account_id fields with foreign keys

-- Check if columns already exist before adding
SET @exist_unit_id := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cash_boxes' 
    AND COLUMN_NAME = 'unit_id');

SET @exist_intermediate := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cash_boxes' 
    AND COLUMN_NAME = 'intermediate_account_id');

-- Add columns if they don't exist
SET @sql_unit_id = IF(@exist_unit_id = 0,
    'ALTER TABLE `cash_boxes` ADD COLUMN `unit_id` BIGINT UNSIGNED NULL AFTER `id` COMMENT ''الوحدة التنظيمية التابع لها الصندوق''',
    'SELECT ''Column unit_id already exists'' AS message');
    
PREPARE stmt FROM @sql_unit_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql_intermediate = IF(@exist_intermediate = 0,
    'ALTER TABLE `cash_boxes` ADD COLUMN `intermediate_account_id` BIGINT UNSIGNED NULL AFTER `unit_id` COMMENT ''الحساب الوسيط المرتبط بالصندوق''',
    'SELECT ''Column intermediate_account_id already exists'' AS message');
    
PREPARE stmt FROM @sql_intermediate;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes if they don't exist
SET @exist_index_unit := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cash_boxes' 
    AND INDEX_NAME = 'cash_boxes_unit_id_index');

SET @exist_index_intermediate := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cash_boxes' 
    AND INDEX_NAME = 'cash_boxes_intermediate_account_id_index');

SET @sql_index_unit = IF(@exist_index_unit = 0,
    'ALTER TABLE `cash_boxes` ADD INDEX `cash_boxes_unit_id_index` (`unit_id`)',
    'SELECT ''Index cash_boxes_unit_id_index already exists'' AS message');
    
PREPARE stmt FROM @sql_index_unit;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql_index_intermediate = IF(@exist_index_intermediate = 0,
    'ALTER TABLE `cash_boxes` ADD INDEX `cash_boxes_intermediate_account_id_index` (`intermediate_account_id`)',
    'SELECT ''Index cash_boxes_intermediate_account_id_index already exists'' AS message');
    
PREPARE stmt FROM @sql_index_intermediate;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign keys if they don't exist
SET @exist_fk_unit := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cash_boxes' 
    AND CONSTRAINT_NAME = 'cash_boxes_unit_id_foreign');

SET @exist_fk_intermediate := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cash_boxes' 
    AND CONSTRAINT_NAME = 'cash_boxes_intermediate_account_id_foreign');

SET @sql_fk_unit = IF(@exist_fk_unit = 0,
    'ALTER TABLE `cash_boxes` ADD CONSTRAINT `cash_boxes_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE',
    'SELECT ''Foreign key cash_boxes_unit_id_foreign already exists'' AS message');
    
PREPARE stmt FROM @sql_fk_unit;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql_fk_intermediate = IF(@exist_fk_intermediate = 0,
    'ALTER TABLE `cash_boxes` ADD CONSTRAINT `cash_boxes_intermediate_account_id_foreign` FOREIGN KEY (`intermediate_account_id`) REFERENCES `chart_accounts` (`id`) ON DELETE SET NULL',
    'SELECT ''Foreign key cash_boxes_intermediate_account_id_foreign already exists'' AS message');
    
PREPARE stmt FROM @sql_fk_intermediate;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Migration completed successfully!' AS status;
