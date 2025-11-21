-- MariaDB Compatible Admin Tables for db_upholcare Database
-- Run this SQL in phpMyAdmin to create admin tables

-- 1. Create booking_numbers table (if not exists)
CREATE TABLE IF NOT EXISTS `booking_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 2. Add missing fields to bookings table
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `booking_number_id` INT(11) DEFAULT NULL AFTER `service_id`,
ADD COLUMN IF NOT EXISTS `total_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `booking_date`,
ADD COLUMN IF NOT EXISTS `payment_status` ENUM('unpaid','partial','paid') DEFAULT 'unpaid' AFTER `total_amount`,
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 3. Create email_logs table
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
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. Create admin_settings table
CREATE TABLE IF NOT EXISTS `admin_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 5. Create admin_activity_log table
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
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 6. Create reservation_queue table
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
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 7. Add category_id to services table
ALTER TABLE `services` 
ADD COLUMN IF NOT EXISTS `category_id` INT(11) DEFAULT NULL AFTER `service_type`,
ADD KEY IF NOT EXISTS `category_id` (`category_id`);

-- 8. Insert sample booking numbers
INSERT IGNORE INTO `booking_numbers` (`booking_number`) VALUES
('BKG-20250115-0001'),
('BKG-20250115-0002'),
('BKG-20250115-0003'),
('BKG-20250115-0004'),
('BKG-20250115-0005'),
('BKG-20250115-0006'),
('BKG-20250115-0007'),
('BKG-20250115-0008'),
('BKG-20250115-0009'),
('BKG-20250115-0010');

-- 9. Insert default admin settings
INSERT IGNORE INTO `admin_settings` (`setting_key`, `setting_value`, `description`) VALUES
('email_enabled', '1', 'Enable/disable email notifications'),
('email_test_mode', '0', 'Test mode - logs emails instead of sending'),
('email_smtp_host', 'smtp.gmail.com', 'SMTP server hostname'),
('email_smtp_port', '587', 'SMTP server port'),
('email_from_address', 'noreply@uphocare.com', 'From email address'),
('email_from_name', 'UphoCare System', 'From name for emails');

-- 10. Update existing services with category IDs
UPDATE `services` SET `category_id` = 1 WHERE `service_type` = 'Vehicle Upholstery';
UPDATE `services` SET `category_id` = 2 WHERE `service_type` = 'Bedding';
UPDATE `services` SET `category_id` = 3 WHERE `service_type` = 'Furniture';

-- 11. Create admin dashboard stats view
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

-- 12. Create admin booking details view
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

-- 13. Insert existing pending bookings into reservation queue
INSERT IGNORE INTO `reservation_queue` (`booking_id`, `priority`, `status`)
SELECT `id`, 'normal', 'pending' 
FROM `bookings` 
WHERE `status` = 'pending' 
AND `id` NOT IN (SELECT `booking_id` FROM `reservation_queue`);

-- Verification queries
SELECT 'Admin tables created successfully!' as message;
SELECT COUNT(*) as booking_numbers_count FROM booking_numbers;
SELECT COUNT(*) as email_logs_count FROM email_logs;
SELECT COUNT(*) as admin_settings_count FROM admin_settings;
SELECT COUNT(*) as admin_activity_log_count FROM admin_activity_log;
SELECT COUNT(*) as reservation_queue_count FROM reservation_queue;
