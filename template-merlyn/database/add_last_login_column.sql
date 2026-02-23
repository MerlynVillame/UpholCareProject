-- Add last_login column to users table
-- Run this in phpMyAdmin to add the missing column

USE db_upholcare;

-- Add last_login column to users table
ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL AFTER updated_at;

-- Verify the column was added
DESCRIBE users;
