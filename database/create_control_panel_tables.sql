-- ============================================================================
-- Create Control Panel Tables in db_upholcare Database
-- This creates a super admin control panel with login tracking
-- All tables are created in the main db_upholcare database
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- TABLE 1: control_panel_admins
-- Stores control panel administrator accounts (separate from regular admins)
-- ============================================================================
CREATE TABLE IF NOT EXISTS control_panel_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 2: login_logs
-- Records all login attempts from customers, admins, and control panel
-- ============================================================================
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'User ID from users table',
    user_type ENUM('customer', 'admin', 'control_panel') NOT NULL,
    email VARCHAR(191) NOT NULL,
    fullname VARCHAR(100) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    login_status ENUM('success', 'failed') NOT NULL,
    failure_reason VARCHAR(255) NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_user_type (user_type),
    INDEX idx_login_status (login_status),
    INDEX idx_login_time (login_time),
    INDEX idx_email (email),
    INDEX idx_user_type_status (user_type, login_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 3: system_activities
-- Track system activities and admin actions for audit trail
-- ============================================================================
CREATE TABLE IF NOT EXISTS system_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    activity_type ENUM('user_created', 'user_modified', 'user_deleted', 
                       'booking_modified', 'settings_changed', 'other') NOT NULL,
    description TEXT NOT NULL,
    affected_table VARCHAR(100) NULL,
    affected_record_id INT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_id (admin_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 4: control_panel_sessions
-- Track active control panel sessions for security monitoring
-- ============================================================================
CREATE TABLE IF NOT EXISTS control_panel_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    session_id VARCHAR(191) NOT NULL UNIQUE,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    INDEX idx_admin_id (admin_id),
    INDEX idx_session_id (session_id),
    INDEX idx_last_activity (last_activity),
    FOREIGN KEY (admin_id) REFERENCES control_panel_admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 5: system_statistics
-- Store daily/weekly/monthly statistics summaries for dashboard
-- ============================================================================
CREATE TABLE IF NOT EXISTS system_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_date DATE NOT NULL UNIQUE,
    total_logins INT DEFAULT 0,
    successful_logins INT DEFAULT 0,
    failed_logins INT DEFAULT 0,
    customer_logins INT DEFAULT 0,
    admin_logins INT DEFAULT 0,
    unique_users INT DEFAULT 0,
    new_users INT DEFAULT 0,
    new_bookings INT DEFAULT 0,
    completed_bookings INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_stat_date (stat_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- NOTE: No default admin account is created
-- Super Admins must register first at: /control-panel/register
-- After first Super Admin is created, registration is restricted
-- ============================================================================

-- ============================================================================
-- Display Results
-- ============================================================================
SELECT '✅ Control Panel Tables Created Successfully!' AS status;

SELECT 'Tables in db_upholcare:' AS info;
SHOW TABLES LIKE '%control_panel%';
SHOW TABLES LIKE '%login_logs%';
SHOW TABLES LIKE '%system_%';

SELECT 'Super Admin Registration:' AS info;
SELECT 'Visit /control-panel/register to create your first Super Admin account' AS message;

-- ============================================================================
-- Quick Reference
-- ============================================================================
SELECT '
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONTROL PANEL SETUP COMPLETE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Database: db_upholcare

Tables Created:
  ✓ control_panel_admins     - Admin accounts
  ✓ login_logs               - All login attempts  
  ✓ system_activities        - Activity tracking
  ✓ control_panel_sessions   - Session management
  ✓ system_statistics        - Daily statistics

Getting Started:
  1. Visit: http://localhost/UphoCare/control-panel/register
  2. Register your first Super Admin account
  3. Super Admin can approve regular admin registrations
  4. Admins monitor sales and manage bookings

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
' AS summary;
