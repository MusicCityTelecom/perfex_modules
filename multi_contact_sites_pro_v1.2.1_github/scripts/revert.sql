-- Multi Contact Sites PRO: SQL Revert Script
-- NOTES:
-- 1) Replace `tbl` with your actual DB prefix if different.
-- 2) Run during a maintenance window.
-- 3) Ensure no duplicate emails exist across customers before re-adding a global unique.

START TRANSACTION;

-- Drop composite unique on (userid,email) if it exists
SET @has_composite := (
  SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tblcontacts'
    AND INDEX_NAME = 'ux_contacts_userid_email'
);
SET @sql := IF(@has_composite > 0, 'ALTER TABLE `tblcontacts` DROP INDEX `ux_contacts_userid_email`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Re-add a global unique index on email (rename as desired)
-- WARNING: Fails if duplicates exist globally.
ALTER TABLE `tblcontacts` ADD UNIQUE `ux_contacts_email` (`email`);

-- Optional: remove the link table
DROP TABLE IF EXISTS `tblmcsp_contact_sites`;

COMMIT;
