-- ============================================================
-- Migration: Add ID verification fields
-- Run this if the columns don't exist yet in the database.
-- ============================================================

-- 1. Add Employee ID to admin_registrations table
ALTER TABLE admin_registrations 
    ADD COLUMN IF NOT EXISTS employee_id VARCHAR(100) DEFAULT NULL 
    COMMENT 'Employee ID or Admin ID provided during registration' 
    AFTER phone;

-- 2. Add Admin Valid ID image columns to admin_registrations table
ALTER TABLE admin_registrations
    ADD COLUMN IF NOT EXISTS valid_id_path VARCHAR(255) DEFAULT NULL
    COMMENT 'Path to uploaded admin valid government ID image';

ALTER TABLE admin_registrations
    ADD COLUMN IF NOT EXISTS valid_id_filename VARCHAR(255) DEFAULT NULL
    COMMENT 'Original filename of admin valid ID image';

-- 3. Add Customer Valid ID image columns to users table
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS customer_id_path VARCHAR(255) DEFAULT NULL
    COMMENT 'Path to uploaded customer valid government ID image';

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS customer_id_filename VARCHAR(255) DEFAULT NULL
    COMMENT 'Original filename of uploaded customer valid ID';
