-- ============================================================================
-- Create Super Admin System for Central Admin Panel
-- Monitors admin registrations and activities
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- TABLE: admin_registrations
-- Tracks all admin registration requests
-- ============================================================================
CREATE TABLE IF NOT EXISTS admin_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL COMMENT 'Control panel admin ID if approved',
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    phone VARCHAR(20) NULL,
    registration_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT NULL COMMENT 'Super admin who approved',
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (registration_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: admin_sales_activity
-- Tracks admin sales and booking acceptance/rejection
-- ============================================================================
CREATE TABLE IF NOT EXISTS admin_sales_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    admin_name VARCHAR(100) NOT NULL,
    activity_type ENUM('booking_accepted', 'booking_rejected', 'booking_completed', 'payment_received') NOT NULL,
    booking_id INT NULL,
    customer_id INT NULL,
    customer_name VARCHAR(100) NULL,
    amount DECIMAL(10,2) NULL,
    description TEXT NULL,
    activity_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_id (admin_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_activity_date (activity_date),
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: super_admin_activity
-- Tracks super admin actions
-- ============================================================================
CREATE TABLE IF NOT EXISTS super_admin_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    super_admin_id INT NOT NULL,
    super_admin_name VARCHAR(100) NOT NULL,
    action_type ENUM('admin_approved', 'admin_rejected', 'admin_deactivated', 'system_config') NOT NULL,
    target_admin_id INT NULL,
    target_admin_name VARCHAR(100) NULL,
    description TEXT NULL,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_super_admin_id (super_admin_id),
    INDEX idx_action_date (action_date),
    FOREIGN KEY (super_admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Display Results
-- ============================================================================
SELECT '✅ Super Admin System Tables Created!' AS status;

SELECT '
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SUPER ADMIN SYSTEM CREATED
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Tables Created:
  ✓ admin_registrations - Track admin signup requests
  ✓ admin_sales_activity - Monitor admin sales/activities
  ✓ super_admin_activity - Track super admin actions

Super Admin Capabilities:
  📊 Monitor admin registrations
  ✅ Approve/Reject admin accounts
  📈 Track admin sales activities
  👁️ Monitor booking acceptances
  🔍 View all admin performance

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
' AS summary;

-- Show table structures
DESCRIBE admin_registrations;
DESCRIBE admin_sales_activity;
DESCRIBE super_admin_activity;

