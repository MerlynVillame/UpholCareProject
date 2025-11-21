-- ============================================================================
-- Create Main Admin Panel Table in db_upholcare Database
-- Tracks customer account creation and admin booking acceptance statistics
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- TABLE: main_admin_panel
-- Central dashboard for main admin to track system statistics
-- ============================================================================
CREATE TABLE IF NOT EXISTS main_admin_panel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Customer Statistics
    total_customers INT DEFAULT 0 COMMENT 'Total customer accounts created',
    new_customers_today INT DEFAULT 0 COMMENT 'New customers registered today',
    new_customers_week INT DEFAULT 0 COMMENT 'New customers this week',
    new_customers_month INT DEFAULT 0 COMMENT 'New customers this month',
    
    -- Admin Statistics
    total_admins INT DEFAULT 0 COMMENT 'Total admin accounts',
    active_admins INT DEFAULT 0 COMMENT 'Currently active admins',
    
    -- Booking Statistics
    total_bookings INT DEFAULT 0 COMMENT 'Total bookings in system',
    pending_bookings INT DEFAULT 0 COMMENT 'Bookings awaiting acceptance',
    accepted_bookings INT DEFAULT 0 COMMENT 'Bookings accepted by admins',
    rejected_bookings INT DEFAULT 0 COMMENT 'Bookings rejected',
    completed_bookings INT DEFAULT 0 COMMENT 'Completed bookings',
    
    -- Admin Activity Tracking
    bookings_accepted_today INT DEFAULT 0 COMMENT 'Bookings accepted by admins today',
    bookings_accepted_week INT DEFAULT 0 COMMENT 'Bookings accepted this week',
    bookings_accepted_month INT DEFAULT 0 COMMENT 'Bookings accepted this month',
    
    -- Revenue Statistics
    total_revenue DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total revenue',
    revenue_today DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Revenue today',
    revenue_week DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Revenue this week',
    revenue_month DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Revenue this month',
    
    -- System Health
    system_status ENUM('operational', 'maintenance', 'warning', 'error') DEFAULT 'operational',
    last_backup TIMESTAMP NULL COMMENT 'Last system backup time',
    
    -- Timestamps
    stat_date DATE NOT NULL UNIQUE COMMENT 'Date of these statistics',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_stat_date (stat_date),
    INDEX idx_system_status (system_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Main admin panel for tracking system statistics';

-- ============================================================================
-- TABLE: admin_booking_activity
-- Detailed tracking of which admin accepted which bookings
-- ============================================================================
CREATE TABLE IF NOT EXISTS admin_booking_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL COMMENT 'Admin who performed the action',
    admin_name VARCHAR(100) NOT NULL COMMENT 'Admin full name',
    booking_id INT NOT NULL COMMENT 'Booking that was acted upon',
    customer_id INT NOT NULL COMMENT 'Customer who made the booking',
    customer_name VARCHAR(100) NULL COMMENT 'Customer full name',
    action_type ENUM('accepted', 'rejected', 'modified', 'completed', 'cancelled') NOT NULL,
    booking_amount DECIMAL(10,2) NULL COMMENT 'Booking amount',
    notes TEXT NULL COMMENT 'Action notes or reason',
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_admin_id (admin_id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_action_type (action_type),
    INDEX idx_action_date (action_date),
    
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tracks which admin accepted/rejected each booking';

-- ============================================================================
-- TABLE: customer_registration_log
-- Tracks when and how customers registered
-- ============================================================================
CREATE TABLE IF NOT EXISTS customer_registration_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL COMMENT 'Newly registered customer',
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(191) NOT NULL,
    registration_type ENUM('self', 'admin_created', 'import') DEFAULT 'self',
    registration_source VARCHAR(50) NULL COMMENT 'Website, mobile app, etc',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_by_admin_id INT NULL COMMENT 'If admin created the account',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_registration_date (registration_date),
    INDEX idx_registration_type (registration_type),
    
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Logs all customer account registrations';

-- ============================================================================
-- Initialize Main Admin Panel with Today's Statistics
-- ============================================================================
INSERT INTO main_admin_panel (
    stat_date,
    total_customers,
    total_admins,
    total_bookings,
    pending_bookings,
    accepted_bookings,
    system_status
)
SELECT 
    CURDATE(),
    (SELECT COUNT(*) FROM users WHERE role = 'customer'),
    (SELECT COUNT(*) FROM users WHERE role = 'admin'),
    (SELECT COUNT(*) FROM bookings),
    (SELECT COUNT(*) FROM bookings WHERE status = 'pending'),
    (SELECT COUNT(*) FROM bookings WHERE status IN ('confirmed', 'completed')),
    'operational'
ON DUPLICATE KEY UPDATE
    total_customers = VALUES(total_customers),
    total_admins = VALUES(total_admins),
    total_bookings = VALUES(total_bookings),
    pending_bookings = VALUES(pending_bookings),
    accepted_bookings = VALUES(accepted_bookings),
    updated_at = CURRENT_TIMESTAMP;

-- ============================================================================
-- Display Results
-- ============================================================================
SELECT '✅ Main Admin Panel Tables Created Successfully!' AS status;

SELECT '
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
MAIN ADMIN PANEL TABLES CREATED
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Database: db_upholcare

Tables Created:
  ✓ main_admin_panel - Main statistics dashboard
  ✓ admin_booking_activity - Admin action tracking
  ✓ customer_registration_log - Customer signup tracking

Features:
  📊 Track total customers created
  📊 Track new customers (daily/weekly/monthly)
  📊 Track admin booking acceptances
  📊 Track admin activity details
  📊 Track revenue statistics
  📊 System health monitoring

Usage:
  - View dashboard: SELECT * FROM main_admin_panel;
  - View admin activity: SELECT * FROM admin_booking_activity;
  - View registrations: SELECT * FROM customer_registration_log;

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
' AS summary;

-- Show current statistics
SELECT * FROM main_admin_panel;

-- Show table structures
DESCRIBE main_admin_panel;
DESCRIBE admin_booking_activity;
DESCRIBE customer_registration_log;

