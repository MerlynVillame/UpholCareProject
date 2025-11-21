-- ============================================================================
-- Add Ban Tracking Fields to store_locations and admin_registrations
-- This allows tracking when stores/admins are banned and for how long
-- ============================================================================

USE db_upholcare;

-- Add ban tracking fields to store_locations table
ALTER TABLE store_locations 
ADD COLUMN IF NOT EXISTS banned_at TIMESTAMP NULL COMMENT 'When the store was banned',
ADD COLUMN IF NOT EXISTS banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)',
ADD COLUMN IF NOT EXISTS ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)',
ADD COLUMN IF NOT EXISTS ban_reason TEXT NULL COMMENT 'Reason for banning the store',
ADD COLUMN IF NOT EXISTS banned_by INT NULL COMMENT 'Super admin who banned the store';

-- Add ban tracking fields to admin_registrations table
ALTER TABLE admin_registrations 
ADD COLUMN IF NOT EXISTS banned_at TIMESTAMP NULL COMMENT 'When the admin was banned',
ADD COLUMN IF NOT EXISTS banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)',
ADD COLUMN IF NOT EXISTS ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)',
ADD COLUMN IF NOT EXISTS ban_reason TEXT NULL COMMENT 'Reason for banning the admin',
ADD COLUMN IF NOT EXISTS banned_by INT NULL COMMENT 'Super admin who banned the admin';

-- Add ban tracking fields to users table (for admin accounts)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS banned_at TIMESTAMP NULL COMMENT 'When the user was banned',
ADD COLUMN IF NOT EXISTS banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)',
ADD COLUMN IF NOT EXISTS ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)',
ADD COLUMN IF NOT EXISTS ban_reason TEXT NULL COMMENT 'Reason for banning the user',
ADD COLUMN IF NOT EXISTS banned_by INT NULL COMMENT 'Super admin who banned the user';

-- Add indexes for ban tracking
ALTER TABLE store_locations 
ADD INDEX IF NOT EXISTS idx_banned_at (banned_at),
ADD INDEX IF NOT EXISTS idx_banned_until (banned_until);

ALTER TABLE admin_registrations 
ADD INDEX IF NOT EXISTS idx_banned_at (banned_at),
ADD INDEX IF NOT EXISTS idx_banned_until (banned_until);

ALTER TABLE users 
ADD INDEX IF NOT EXISTS idx_banned_at (banned_at),
ADD INDEX IF NOT EXISTS idx_banned_until (banned_until);

-- ============================================================================
-- Display Results
-- ============================================================================
SELECT '✅ Ban tracking fields added successfully!' AS status;

