-- ============================================================================
-- Fix super_admin_activity Table Connections
-- Add missing foreign key relationships
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- Check current structure
-- ============================================================================
DESCRIBE super_admin_activity;

-- ============================================================================
-- 1. Ensure super_admin_id has foreign key to users table
-- ============================================================================
-- Check if foreign key already exists
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = 'db_upholcare' 
    AND TABLE_NAME = 'super_admin_activity' 
    AND CONSTRAINT_NAME = 'super_admin_activity_ibfk_1'
);

-- Add foreign key if it doesn't exist
SET @sql = IF(@fk_exists > 0,
    'SELECT "Foreign key super_admin_activity_ibfk_1 already exists" AS status;',
    'ALTER TABLE super_admin_activity 
     ADD CONSTRAINT super_admin_activity_ibfk_1 
     FOREIGN KEY (super_admin_id) REFERENCES users(id) ON DELETE CASCADE;'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 2. Add foreign key for target_admin_id to users table (if target is admin)
-- ============================================================================
-- Check if target_admin_id column exists
SET @column_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'db_upholcare' 
    AND TABLE_NAME = 'super_admin_activity' 
    AND COLUMN_NAME = 'target_admin_id'
);

-- Check if foreign key for target_admin_id already exists
SET @fk_target_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = 'db_upholcare' 
    AND TABLE_NAME = 'super_admin_activity' 
    AND CONSTRAINT_NAME = 'super_admin_activity_ibfk_2'
);

-- Add foreign key for target_admin_id if column exists and FK doesn't exist
SET @sql2 = IF(@column_exists > 0 AND @fk_target_exists = 0,
    'ALTER TABLE super_admin_activity 
     ADD CONSTRAINT super_admin_activity_ibfk_2 
     FOREIGN KEY (target_admin_id) REFERENCES users(id) ON DELETE SET NULL;',
    IF(@column_exists = 0,
        'SELECT "Column target_admin_id does not exist" AS status;',
        'SELECT "Foreign key super_admin_activity_ibfk_2 already exists" AS status;'
    )
);

PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- ============================================================================
-- 3. Add connection to admin_registrations table (optional - via target_admin_id)
-- Note: This is a soft reference since target_admin_id might point to users.id
-- ============================================================================

-- ============================================================================
-- 4. Add connection to control_panel_admins (if needed)
-- Note: super_admin_id might also reference control_panel_admins
-- ============================================================================
-- Check if we should add a connection to control_panel_admins
-- This would be useful if super admins are stored in control_panel_admins table

-- Check if control_panel_admins table exists
SET @cp_admins_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'db_upholcare' 
    AND TABLE_NAME = 'control_panel_admins'
);

-- Optional: Add a column to link to control_panel_admins if needed
-- ALTER TABLE super_admin_activity 
-- ADD COLUMN IF NOT EXISTS super_admin_cp_id INT NULL COMMENT 'Link to control_panel_admins',
-- ADD CONSTRAINT super_admin_activity_ibfk_3 
-- FOREIGN KEY (super_admin_cp_id) REFERENCES control_panel_admins(id) ON DELETE SET NULL;

-- ============================================================================
-- Verification
-- ============================================================================
SELECT '✅ Super Admin Activity table connections verified!' AS status;

-- Show all foreign keys for super_admin_activity
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'db_upholcare'
AND TABLE_NAME = 'super_admin_activity'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Show table structure
DESCRIBE super_admin_activity;

-- ============================================================================
-- Summary of Connections
-- ============================================================================
SELECT '
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SUPER_ADMIN_ACTIVITY TABLE CONNECTIONS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Foreign Key Relationships:
  ✓ super_admin_id → users(id) [ON DELETE CASCADE]
  ✓ target_admin_id → users(id) [ON DELETE SET NULL] (if column exists)

Soft References (No FK):
  • target_admin_id can reference admin_registrations (via email/name matching)
  • super_admin_id can reference control_panel_admins (via user lookup)

Connected Tables:
  1. users (via super_admin_id) - Who performed the action
  2. users (via target_admin_id) - Target of the action (if admin)
  3. admin_registrations (soft reference) - Admin registration being acted upon

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
' AS summary;

