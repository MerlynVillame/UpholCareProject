-- ============================================================================
-- UpholCare Complete Database Schema
-- Repair and Restoration Management System for Upholstery Shops
-- Version: 2.0
-- Date: Decemb-- Database Name: db_upholcare
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- CREATE DATABASE
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `db_upholcare` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_upholcare`;

-- ============================================================================
-- TABLE: users
-- Customer and admin accounts (shared table)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: service_categories
-- Service category definitions
-- ============================================================================

CREATE TABLE IF NOT EXISTS `service_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: services
-- Available services
-- ============================================================================

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`category_id`) REFERENCES `service_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: store_locations
-- Physical store locations
-- ============================================================================

CREATE TABLE IF NOT EXISTS `store_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `operating_hours` text,
  `services_offered` text,
  `features` text,
  `rating` decimal(3,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: inventory
-- Color/fabric inventory with premium/standard types
-- ============================================================================

CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color_code` varchar(50) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_hex` varchar(7) DEFAULT '#000000',
  `fabric_type` enum('premium','standard') DEFAULT 'standard',
  `inventory_type` enum('premium','standard') DEFAULT 'standard',
  `price_per_unit` decimal(10,2) DEFAULT 0.00 COMMENT 'Price per unit (roll/meter)',
  `price_per_meter` decimal(10,2) DEFAULT 0.00 COMMENT 'Price per meter for leather material',
  `premium_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Additional price for premium type',
  `quantity` decimal(10,2) DEFAULT 0.00 COMMENT 'Available quantity in rolls',
  `store_location_id` int(11) DEFAULT NULL COMMENT 'Which store/shop has this inventory',
  `status` enum('in-stock','low-stock','out-of-stock') DEFAULT 'in-stock',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_color_store` (`color_code`, `store_location_id`),
  KEY `idx_store_location` (`store_location_id`),
  KEY `idx_fabric_type` (`fabric_type`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`store_location_id`) REFERENCES `store_locations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: bookings
-- Service bookings and reservations
-- ============================================================================

CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `store_location_id` int(11) DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `service_option` varchar(50) DEFAULT 'pickup',
  `selected_color_id` int(11) DEFAULT NULL,
  `color_type` enum('premium','standard') DEFAULT 'standard',
  `color_price` decimal(10,2) DEFAULT 0.00,
  `booking_date` date DEFAULT NULL,
  `pickup_address` text,
  `pickup_date` date DEFAULT NULL,
  `delivery_address` text,
  `delivery_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `status` enum(
    'pending',
    'for_pickup',
    'picked_up',
    'for_inspection',
    'to_inspect',
    'for_inspect',
    'inspect_completed',
    'inspection_completed_waiting_approval',
    'preview_receipt_sent',
    'for_quotation',
    'approved',
    'in_queue',
    'in_progress',
    'start_repair',
    'under_repair',
    'for_quality_check',
    'repair_completed',
    'repair_completed_ready_to_deliver',
    'ready_for_pickup',
    'out_for_delivery',
    'completed',
    'paid',
    'closed',
    'cancelled',
    'confirmed',
    'accepted',
    'rejected',
    'declined',
    'admin_review',
    'delivered_and_paid'
  ) DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid','paid_full_cash','paid_on_delivery_cod') DEFAULT 'unpaid',
  `notes` text,
  `additional_notes` text,
  -- Measurement fields
  `measurement_height` decimal(10,2) DEFAULT NULL,
  `measurement_width` decimal(10,2) DEFAULT NULL,
  `measurement_thickness` decimal(10,2) DEFAULT NULL,
  `measurement_notes` text,
  -- Damage fields
  `damage_description` text,
  `damage_severity` varchar(50) DEFAULT NULL,
  `damage_types` text,
  -- Material/Receipt fields
  `number_of_meters` decimal(10,2) DEFAULT NULL,
  `price_per_meter` decimal(10,2) DEFAULT NULL,
  `labor_fee` decimal(10,2) DEFAULT NULL,
  `repair_days` int(11) DEFAULT NULL,
  `repair_start_date` datetime DEFAULT NULL,
  -- Calculation fields
  `fabric_length` decimal(10,2) DEFAULT NULL,
  `fabric_width` decimal(10,2) DEFAULT NULL,
  `fabric_area` decimal(10,2) DEFAULT NULL,
  `fabric_cost_per_meter` decimal(10,2) DEFAULT NULL,
  `fabric_total` decimal(10,2) DEFAULT NULL,
  `fabric_cost` decimal(10,2) DEFAULT NULL,
  `material_cost` decimal(10,2) DEFAULT 0.00,
  `service_fees` decimal(10,2) DEFAULT 0.00,
  `calculated_total_saved` tinyint(1) DEFAULT 0,
  `calculation_notes` text,
  -- Receipt fields
  `receipt_issued` tinyint(1) DEFAULT 0,
  `receipt_issued_at` timestamp NULL DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  -- Preview receipt fields
  `preview_image` varchar(255) DEFAULT NULL,
  `preview_sent_at` timestamp NULL DEFAULT NULL,
  `preview_notes` text,
  -- Quotation fields
  `quotation_accepted` tinyint(1) DEFAULT 0,
  `quotation_accepted_at` datetime DEFAULT NULL,
  `quotation_sent` tinyint(1) DEFAULT 0,
  `quotation_sent_at` datetime DEFAULT NULL,
  -- Dates
  `completion_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_store_location` (`store_location_id`),
  KEY `idx_selected_color` (`selected_color_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_repair_start_date` (`repair_start_date`),
  KEY `idx_status_repair_start` (`status`, `repair_start_date`),
  KEY `idx_calculated_total_saved` (`calculated_total_saved`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`store_location_id`) REFERENCES `store_locations`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`selected_color_id`) REFERENCES `inventory`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: payments
-- Payment transactions
-- ============================================================================

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_payment_status` (`payment_status`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: quotations
-- General quotations
-- ============================================================================

CREATE TABLE IF NOT EXISTS `quotations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `quotation_number` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('draft','sent','accepted','rejected') DEFAULT 'draft',
  `valid_until` date DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotation_number` (`quotation_number`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: notifications
-- User notifications
-- ============================================================================

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CONTROL PANEL TABLES
-- ============================================================================

-- ============================================================================
-- TABLE: control_panel_admins
-- Control panel administrator accounts
-- ============================================================================

CREATE TABLE IF NOT EXISTS `control_panel_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `role` enum('super_admin','admin') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: login_logs
-- All login attempts tracking
-- ============================================================================

CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'User ID from users table',
  `user_type` enum('customer','admin','control_panel') NOT NULL,
  `email` varchar(191) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `login_status` enum('success','failed') NOT NULL,
  `failure_reason` varchar(255) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_login_status` (`login_status`),
  KEY `idx_login_time` (`login_time`),
  KEY `idx_email` (`email`),
  KEY `idx_user_type_status` (`user_type`, `login_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: system_activities
-- System activity audit trail
-- ============================================================================

CREATE TABLE IF NOT EXISTS `system_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `activity_type` enum('user_created','user_modified','user_deleted','booking_modified','settings_changed','other') NOT NULL,
  `description` text NOT NULL,
  `affected_table` varchar(100) DEFAULT NULL,
  `affected_record_id` int(11) DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: control_panel_sessions
-- Active session tracking
-- ============================================================================

CREATE TABLE IF NOT EXISTS `control_panel_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `session_id` varchar(191) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_last_activity` (`last_activity`),
  FOREIGN KEY (`admin_id`) REFERENCES `control_panel_admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: system_statistics
-- Daily statistics summaries
-- ============================================================================

CREATE TABLE IF NOT EXISTS `system_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stat_date` date NOT NULL,
  `total_logins` int(11) DEFAULT 0,
  `successful_logins` int(11) DEFAULT 0,
  `failed_logins` int(11) DEFAULT 0,
  `customer_logins` int(11) DEFAULT 0,
  `admin_logins` int(11) DEFAULT 0,
  `unique_users` int(11) DEFAULT 0,
  `new_users` int(11) DEFAULT 0,
  `new_bookings` int(11) DEFAULT 0,
  `completed_bookings` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stat_date` (`stat_date`),
  KEY `idx_stat_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INSERT SAMPLE DATA
-- ============================================================================

-- Insert sample service categories
INSERT INTO `service_categories` (`id`, `category_name`, `description`, `status`, `created_at`) VALUES
(1, 'Vehicle Upholstery', 'Car seats, truck covers, motorcycle seats', 'active', NOW()),
(2, 'Bedding', 'Mattress covers, pillow cases, bed sheets', 'active', NOW()),
(3, 'Furniture', 'Sofa covers, chair cushions, table cloths', 'active', NOW())
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

-- Insert sample services
INSERT INTO `services` (`id`, `service_name`, `service_type`, `category_id`, `description`, `price`, `status`, `created_at`) VALUES
(1, 'Car Seat Repair', 'Vehicle Upholstery', 1, 'Repair and restore car seats', 150.00, 'active', NOW()),
(2, 'Truck Cover Custom', 'Vehicle Upholstery', 1, 'Custom truck bed covers', 300.00, 'active', NOW()),
(3, 'Mattress Cover', 'Bedding', 2, 'Custom mattress covers', 80.00, 'active', NOW()),
(4, 'Sofa Reupholstery', 'Furniture', 3, 'Complete sofa reupholstering', 500.00, 'active', NOW())
ON DUPLICATE KEY UPDATE `service_name` = VALUES(`service_name`);

-- Insert sample store locations
INSERT INTO `store_locations` (`id`, `store_name`, `address`, `city`, `province`, `latitude`, `longitude`, `phone`, `email`, `operating_hours`, `services_offered`, `features`, `rating`, `status`, `created_at`) VALUES
(1, 'UphoCare Main Branch', '123 Main Street, Barangay Central', 'Manila', 'Metro Manila', 14.5995, 120.9842, '02-8123-4567', 'main@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, 24/7 Customer Support, Quality Guarantee', 4.8, 'active', NOW()),
(2, 'UphoCare Quezon City', '456 Quezon Avenue, Diliman', 'Quezon City', 'Metro Manila', 14.6760, 121.0437, '02-8123-4568', 'qc@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Express Service, Design Consultation', 4.6, 'active', NOW()),
(3, 'UphoCare Makati', '789 Ayala Avenue, Makati City', 'Makati', 'Metro Manila', 14.5547, 121.0244, '02-8123-4569', 'makati@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Premium Materials, Custom Design', 4.9, 'active', NOW()),
(4, 'UphoCare Pasig', '321 Ortigas Avenue, Pasig City', 'Pasig', 'Metro Manila', 14.5764, 121.0851, '02-8123-4570', 'pasig@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Eco-Friendly Materials, Quick Turnaround', 4.7, 'active', NOW()),
(5, 'UphoCare Taguig', '654 BGC High Street, Taguig City', 'Taguig', 'Metro Manila', 14.5539, 121.0500, '02-8123-4571', 'taguig@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Luxury Materials, VIP Service', 4.8, 'active', NOW())
ON DUPLICATE KEY UPDATE `store_name` = VALUES(`store_name`);

-- ============================================================================
-- RESTORE FOREIGN KEY CHECKS
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- END OF DATABASE SCHEMA
-- ============================================================================

SELECT '✅ UpholCare Database Schema Created Successfully!' AS status;
SELECT 'Database: db_upholcare' AS info;
SELECT 'Total Tables: 13' AS info;
SELECT 'Main Tables: users, services, service_categories, store_locations, inventory, bookings, payments, quotations, notifications' AS info;
SELECT 'Control Panel Tables: control_panel_admins, login_logs, system_activities, control_panel_sessions, system_statistics' AS info;

