-- Admin-specific tables for UphoCare email notification system
-- This script only creates tables needed for admin functionality
-- Run this on your existing db_upholcare database

-- Create booking_numbers table for admin-managed booking numbers
CREATE TABLE IF NOT EXISTS `booking_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert initial booking numbers for admin to manage
INSERT INTO `booking_numbers` (`booking_number`) VALUES
('BKG-20250115-0001'),
('BKG-20250115-0002'),
('BKG-20250115-0003'),
('BKG-20250115-0004'),
('BKG-20250115-0005'),
('BKG-20250115-0006'),
('BKG-20250115-0007'),
('BKG-20250115-0008'),
('BKG-20250115-0009'),
('BKG-20250115-0010'),
('BKG-20250115-0011'),
('BKG-20250115-0012'),
('BKG-20250115-0013'),
('BKG-20250115-0014'),
('BKG-20250115-0015'),
('BKG-20250115-0016'),
('BKG-20250115-0017'),
('BKG-20250115-0018'),
('BKG-20250115-0019'),
('BKG-20250115-0020');

-- Add missing fields to existing bookings table for admin functionality
ALTER TABLE `bookings` 
ADD COLUMN `booking_number_id` INT(11) DEFAULT NULL AFTER `service_id`,
ADD COLUMN `total_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `booking_date`,
ADD COLUMN `payment_status` ENUM('unpaid','partial','paid') DEFAULT 'unpaid' AFTER `total_amount`,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add foreign key constraint for booking numbers
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_booking_number_fk` FOREIGN KEY (`booking_number_id`) REFERENCES `booking_numbers` (`id`) ON DELETE SET NULL;

-- Add indexes for better performance
ALTER TABLE `bookings` 
ADD KEY `booking_number_id` (`booking_number_id`),
ADD KEY `status` (`status`),
ADD KEY `payment_status` (`payment_status`);

-- Add category relationship to services table
ALTER TABLE `services` 
ADD COLUMN `category_id` INT(11) DEFAULT NULL AFTER `service_type`,
ADD KEY `category_id` (`category_id`);

-- Add foreign key constraint for service categories
ALTER TABLE `services`
ADD CONSTRAINT `services_category_fk` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL;

-- Update existing services with category IDs
UPDATE `services` SET `category_id` = 1 WHERE `service_type` = 'Vehicle Upholstery';
UPDATE `services` SET `category_id` = 2 WHERE `service_type` = 'Bedding';
UPDATE `services` SET `category_id` = 3 WHERE `service_type` = 'Furniture';

-- Create email logs table for admin to track email notifications
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `customer_email` varchar(100) NOT NULL,
  `email_type` enum('approval','rejection','test') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `email_type` (`email_type`),
  KEY `status` (`status`),
  KEY `sent_at` (`sent_at`),
  CONSTRAINT `email_logs_booking_fk` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create admin settings table for email configuration
CREATE TABLE IF NOT EXISTS `admin_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert default admin settings
INSERT INTO `admin_settings` (`setting_key`, `setting_value`, `description`) VALUES
('email_enabled', '1', 'Enable/disable email notifications'),
('email_test_mode', '0', 'Test mode - logs emails instead of sending'),
('email_smtp_host', 'smtp.gmail.com', 'SMTP server hostname'),
('email_smtp_port', '587', 'SMTP server port'),
('email_from_address', 'noreply@uphocare.com', 'From email address'),
('email_from_name', 'UphoCare System', 'From name for emails'),
('notification_approval_template', 'default', 'Approval email template'),
('notification_rejection_template', 'default', 'Rejection email template');

-- Create admin activity log table
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  KEY `target_type` (`target_type`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `admin_activity_admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create reservation queue table for admin to manage pending reservations
CREATE TABLE IF NOT EXISTS `reservation_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `assigned_admin_id` int(11) DEFAULT NULL,
  `status` enum('pending','in_review','approved','rejected') DEFAULT 'pending',
  `review_notes` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_id` (`booking_id`),
  KEY `priority` (`priority`),
  KEY `status` (`status`),
  KEY `assigned_admin_id` (`assigned_admin_id`),
  CONSTRAINT `reservation_queue_booking_fk` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_queue_admin_fk` FOREIGN KEY (`assigned_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert existing pending bookings into reservation queue
INSERT INTO `reservation_queue` (`booking_id`, `priority`, `status`)
SELECT `id`, 'normal', 'pending' 
FROM `bookings` 
WHERE `status` = 'pending' 
AND `id` NOT IN (SELECT `booking_id` FROM `reservation_queue`);

-- Create admin dashboard statistics view
CREATE OR REPLACE VIEW `admin_dashboard_stats` AS
SELECT 
    (SELECT COUNT(*) FROM `bookings`) as total_bookings,
    (SELECT COUNT(*) FROM `bookings` WHERE `status` = 'pending') as pending_bookings,
    (SELECT COUNT(*) FROM `bookings` WHERE `status` = 'confirmed') as confirmed_bookings,
    (SELECT COUNT(*) FROM `bookings` WHERE `status` = 'completed') as completed_bookings,
    (SELECT COUNT(*) FROM `bookings` WHERE `status` = 'cancelled') as cancelled_bookings,
    (SELECT COALESCE(SUM(`total_amount`), 0) FROM `bookings` WHERE `payment_status` = 'paid') as total_revenue,
    (SELECT COUNT(*) FROM `users` WHERE `role` = 'customer') as total_customers,
    (SELECT COUNT(*) FROM `email_logs` WHERE `status` = 'sent' AND DATE(`sent_at`) = CURDATE()) as emails_sent_today;

-- Create booking details view for admin
CREATE OR REPLACE VIEW `admin_booking_details` AS
SELECT 
    b.`id`,
    b.`user_id`,
    b.`service_id`,
    b.`booking_number_id`,
    bn.`booking_number`,
    b.`booking_date`,
    b.`total_amount`,
    b.`status`,
    b.`payment_status`,
    b.`notes`,
    b.`created_at`,
    b.`updated_at`,
    u.`fullname` as customer_name,
    u.`email` as customer_email,
    u.`phone` as customer_phone,
    s.`service_name`,
    s.`service_type`,
    s.`price` as service_price,
    sc.`category_name`,
    rq.`priority` as queue_priority,
    rq.`assigned_admin_id`,
    rq.`review_notes`
FROM `bookings` b
LEFT JOIN `booking_numbers` bn ON b.`booking_number_id` = bn.`id`
LEFT JOIN `users` u ON b.`user_id` = u.`id`
LEFT JOIN `services` s ON b.`service_id` = s.`id`
LEFT JOIN `service_categories` sc ON s.`category_id` = sc.`id`
LEFT JOIN `reservation_queue` rq ON b.`id` = rq.`booking_id`;

-- Create indexes for better performance
CREATE INDEX `idx_bookings_status_created` ON `bookings` (`status`, `created_at`);
CREATE INDEX `idx_bookings_payment_status` ON `bookings` (`payment_status`);
CREATE INDEX `idx_users_role_status` ON `users` (`role`, `status`);
CREATE INDEX `idx_email_logs_type_status` ON `email_logs` (`email_type`, `status`);

-- Insert sample admin activity log entry
INSERT INTO `admin_activity_log` (`admin_id`, `action`, `target_type`, `details`, `ip_address`)
SELECT 
    `id`, 
    'database_setup', 
    'system', 
    'Admin tables created for email notification system', 
    '127.0.0.1'
FROM `users` 
WHERE `role` = 'admin' 
LIMIT 1;
