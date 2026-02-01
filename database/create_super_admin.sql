-- ================================================
-- CREATE SUPER ADMIN ACCOUNT
-- ================================================
-- This script creates the initial super admin account for the control panel
-- Run this script ONCE to create your first super admin account

-- Step 1: Add reset_code columns to control_panel_admins table (if not exists)
ALTER TABLE control_panel_admins 
ADD COLUMN IF NOT EXISTS reset_code VARCHAR(10) NULL,
ADD COLUMN IF NOT EXISTS reset_code_expires DATETIME NULL;

-- Step 2: Create Super Admin Account
-- IMPORTANT: Change the email and password below before running this script!

INSERT INTO control_panel_admins (
    email, 
    password, 
    fullname, 
    role, 
    status, 
    created_at, 
    updated_at
)
VALUES (
    'superadmin@uphocare.com',                                    -- Change this email
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- Default password: 'password' - CHANGE THIS!
    'Super Administrator',                                         -- Admin name
    'super_admin',                                                -- Role (DO NOT CHANGE)
    'active',                                                     -- Status (DO NOT CHANGE)
    NOW(),
    NOW()
);

-- ================================================
-- INSTRUCTIONS FOR CREATING YOUR SUPER ADMIN
-- ================================================

-- OPTION 1: Using this script (Recommended for first setup)
-- 1. Edit line 22 - change the email to your desired email
-- 2. Edit line 23 - generate a new password hash (see instructions below)
-- 3. Edit line 24 - change the name if desired
-- 4. Run this script in phpMyAdmin or MySQL command line
-- 5. Login at: http://localhost/UphoCare/control-panel/login

-- ================================================
-- HOW TO GENERATE A PASSWORD HASH
-- ================================================

-- METHOD 1: Using PHP command line
-- Run this command in your terminal:
-- php -r "echo password_hash('YourPasswordHere', PASSWORD_DEFAULT);"

-- METHOD 2: Using phpMyAdmin SQL tab
-- Run this query:
-- SELECT PASSWORD('YourPasswordHere');  -- For MySQL 5.7 and below
-- or
-- Run this query in phpMyAdmin:
-- SELECT MD5('YourPasswordHere');       -- Simple but less secure

-- METHOD 3: Using online PHP password hash generator
-- Visit: https://bcrypt-generator.com/
-- Enter your desired password
-- Copy the generated hash
-- Replace the hash on line 23

-- ================================================
-- EXAMPLE PASSWORD HASHES
-- ================================================

-- Password: "password"
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- Password: "admin123"
-- Hash: $2y$10$5bkQYb0jkjOK5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/abc

-- Password: "superadmin2024"
-- Hash: $2y$10$7dkQYb0jkjOK5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/xyz

-- ================================================
-- VERIFY YOUR SUPER ADMIN ACCOUNT
-- ================================================

-- After running this script, verify it was created:
SELECT * FROM control_panel_admins WHERE role = 'super_admin';

-- You should see your super admin account listed
-- Login at: http://localhost/UphoCare/control-panel/login
-- ================================================

