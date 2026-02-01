-- ============================================================================
-- Remove Unused Tables from db_upholcare Database
-- ============================================================================
-- This script removes tables that exist in the database but are NOT used
-- in the current codebase.
--
-- WARNING: Always backup your database before running this script!
-- 
-- Unused Tables to Remove:
-- 1. admin_booking_activity
-- 2. admin_sales_activity
-- 3. admin_verification_keys
-- 4. main_admin_panel
-- 5. role_permissions
--
-- Generated: December 2025
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- BACKUP RECOMMENDATION
-- ============================================================================
-- Before running this script, create a backup:
-- mysqldump -u root -p db_upholcare > backup_before_cleanup_$(date +%Y%m%d).sql
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. Remove admin_booking_activity table
-- ============================================================================
-- Status: NOT USED in codebase
-- Purpose: Appears to be for tracking admin booking activities
DROP TABLE IF EXISTS `admin_booking_activity`;

-- ============================================================================
-- 2. Remove admin_sales_activity table
-- ============================================================================
-- Status: NOT USED in codebase
-- Purpose: Appears to be for tracking admin sales activities
DROP TABLE IF EXISTS `admin_sales_activity`;

-- ============================================================================
-- 3. Remove admin_verification_keys table
-- ============================================================================
-- Status: NOT USED in codebase
-- Note: System uses admin_verification_codes instead
-- Purpose: Appears to be for storing verification keys (alternative system)
DROP TABLE IF EXISTS `admin_verification_keys`;

-- ============================================================================
-- 4. Remove main_admin_panel table
-- ============================================================================
-- Status: NOT USED in codebase
-- Purpose: Unknown - appears to be a legacy table
DROP TABLE IF EXISTS `main_admin_panel`;

-- ============================================================================
-- 5. Remove role_permissions table
-- ============================================================================
-- Status: NOT USED in codebase
-- Note: System uses simple role enum ('admin', 'customer') in users table
-- Purpose: Appears to be for role-based access control (RBAC) - not implemented
DROP TABLE IF EXISTS `role_permissions`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- Verification
-- ============================================================================
SELECT '✅ Unused tables removed successfully!' AS status;
SELECT 'Removed tables: admin_booking_activity, admin_sales_activity, admin_verification_keys, main_admin_panel, role_permissions' AS info;

-- Show remaining tables
SELECT 'Remaining tables in db_upholcare:' AS info;
SHOW TABLES;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================

