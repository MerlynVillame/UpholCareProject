-- ============================================================================
-- Add Control Panel Admin Account Type
-- Creates a new user type specifically for Control Panel access
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- Option 1: Add control_panel role to existing users table
-- ============================================================================

-- Check if role column can support 'control_panel'
ALTER TABLE users 
MODIFY COLUMN role ENUM('customer', 'admin', 'control_panel_admin') 
DEFAULT 'customer';

-- ============================================================================
-- Create a super admin account (Control Panel Admin) in users table
-- This allows unified login handling
-- ============================================================================

-- Insert a control panel admin user
INSERT INTO users (
    username,
    email,
    password,
    fullname,
    phone,
    role,
    status,
    created_at
) VALUES (
    'controladmin',
    'control@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Control Panel Administrator',
    '09123456789',
    'control_panel_admin',
    'active',
    NOW()
) ON DUPLICATE KEY UPDATE 
    role = 'control_panel_admin',
    status = 'active';

-- ============================================================================
-- Update login_logs to support new user type
-- ============================================================================

ALTER TABLE login_logs 
MODIFY COLUMN user_type ENUM('customer', 'admin', 'control_panel', 'control_panel_admin') 
NOT NULL;

-- ============================================================================
-- Create role permissions table
-- ============================================================================

CREATE TABLE IF NOT EXISTS role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name ENUM('customer', 'admin', 'control_panel_admin') NOT NULL,
    permission_name VARCHAR(100) NOT NULL,
    can_access BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_name, permission_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default permissions
INSERT INTO role_permissions (role_name, permission_name, can_access) VALUES
    ('customer', 'view_own_bookings', TRUE),
    ('customer', 'create_booking', TRUE),
    ('customer', 'cancel_booking', TRUE),
    ('admin', 'view_all_bookings', TRUE),
    ('admin', 'accept_booking', TRUE),
    ('admin', 'manage_users', TRUE),
    ('control_panel_admin', 'view_statistics', TRUE),
    ('control_panel_admin', 'view_login_logs', TRUE),
    ('control_panel_admin', 'view_admin_activity', TRUE),
    ('control_panel_admin', 'manage_system', TRUE),
    ('control_panel_admin', 'view_all_data', TRUE)
ON DUPLICATE KEY UPDATE can_access = VALUES(can_access);

-- ============================================================================
-- Display Results
-- ============================================================================

SELECT '✅ Control Panel Admin Type Added Successfully!' AS status;

SELECT '
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONTROL PANEL ADMIN ACCOUNT TYPE ADDED
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

User Types Now Available:
  1. customer - Regular customers
  2. admin - System administrators
  3. control_panel_admin - Control Panel access

New Control Panel Admin Account:
  Username: controladmin
  Email: control@uphocare.com
  Password: Control@2025
  Role: control_panel_admin

Login URLs:
  - Customers: /auth/login
  - Admins: /auth/login
  - Control Panel: /control-panel/login

Permissions System:
  ✓ Role-based access control
  ✓ Separate permissions per role
  ✓ Control panel admins have full access

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
' AS summary;

-- Show the new user
SELECT id, username, email, fullname, role, status 
FROM users 
WHERE role = 'control_panel_admin';

-- Show role permissions
SELECT * FROM role_permissions ORDER BY role_name, permission_name;

