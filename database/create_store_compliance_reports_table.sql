-- Create store_compliance_reports table
-- This table stores compliance reports submitted by customers about stores

USE db_upholcare;

CREATE TABLE IF NOT EXISTS store_compliance_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    customer_id INT NOT NULL,
    report_type ENUM('safety', 'hygiene', 'quality', 'service', 'pricing', 'other') NOT NULL,
    issue_types TEXT NOT NULL COMMENT 'JSON array of selected issue types',
    description TEXT,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_notes TEXT,
    reviewed_by INT NULL COMMENT 'Super admin who reviewed the report',
    reviewed_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_id (store_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES control_panel_admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Store compliance reports table created successfully!' AS status;

