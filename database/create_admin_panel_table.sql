-- ============================================================================
-- Create Admin Panel Table in db_upholcare Database
-- This table stores admin panel settings and configurations
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- TABLE: admin_panel
-- Stores admin panel settings, configurations, and dashboard preferences
-- ============================================================================
CREATE TABLE IF NOT EXISTS admin_panel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,
    setting_type ENUM('general', 'notification', 'display', 'security', 'system') DEFAULT 'general',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admin_id (admin_id),
    INDEX idx_setting_key (setting_key),
    INDEX idx_setting_type (setting_type),
    INDEX idx_is_active (is_active),
    UNIQUE KEY unique_admin_setting (admin_id, setting_key),
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Insert Default Admin Panel Settings
-- ============================================================================

-- Default settings for admin dashboard
INSERT INTO admin_panel (admin_id, setting_key, setting_value, setting_type, is_active) 
VALUES 
    (1, 'dashboard_layout', 'default', 'display', TRUE),
    (1, 'items_per_page', '25', 'display', TRUE),
    (1, 'theme', 'light', 'display', TRUE),
    (1, 'email_notifications', 'enabled', 'notification', TRUE),
    (1, 'sms_notifications', 'disabled', 'notification', TRUE),
    (1, 'auto_refresh', 'enabled', 'general', TRUE),
    (1, 'refresh_interval', '60', 'general', TRUE),
    (1, 'sidebar_collapsed', 'false', 'display', TRUE),
    (1, 'show_statistics', 'true', 'display', TRUE),
    (1, 'date_format', 'Y-m-d', 'general', TRUE)
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    updated_at = CURRENT_TIMESTAMP;

-- ============================================================================
-- Display Results
-- ============================================================================
SELECT '✅ Admin Panel Table Created Successfully!' AS status;

-- Show table structure
DESCRIBE admin_panel;

-- Show inserted settings
SELECT * FROM admin_panel;

-- ============================================================================
-- Quick Reference
-- ============================================================================
SELECT '
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ADMIN PANEL TABLE CREATED
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Database: db_upholcare
Table: admin_panel

Columns:
  - id: Primary key
  - admin_id: Foreign key to users table
  - setting_key: Configuration key name
  - setting_value: Configuration value
  - setting_type: Type of setting
  - is_active: Active status
  - created_at: Creation timestamp
  - updated_at: Last update timestamp

Default Settings Inserted:
  ✓ Dashboard layout preferences
  ✓ Display settings (theme, items per page)
  ✓ Notification preferences
  ✓ General settings (refresh, date format)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
' AS summary;

