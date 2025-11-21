-- Customer Booking Number Assignment System
-- This creates the tables needed for admin-assigned booking numbers

-- 1. Create customer_booking_numbers table
CREATE TABLE IF NOT EXISTS `customer_booking_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `booking_number_id` int(11) NOT NULL,
  `assigned_by_admin_id` int(11) NOT NULL,
  `status` enum('active','revoked','used') DEFAULT 'active',
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `revoked_by_admin_id` int(11) DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `revoke_reason` text DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_active_booking` (`customer_id`, `status`),
  KEY `booking_number_id` (`booking_number_id`),
  KEY `assigned_by_admin_id` (`assigned_by_admin_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 2. Create repair_items table for customer repair requests
CREATE TABLE IF NOT EXISTS `repair_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `customer_booking_number_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text NOT NULL,
  `item_type` enum('vehicle','furniture','bedding','other') NOT NULL,
  `urgency` enum('low','normal','high','urgent') DEFAULT 'normal',
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','quoted','approved','in_progress','completed','cancelled') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `customer_booking_number_id` (`customer_booking_number_id`),
  KEY `status` (`status`),
  KEY `urgency` (`urgency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 3. Create repair_quotations table
CREATE TABLE IF NOT EXISTS `repair_quotations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `repair_item_id` int(11) NOT NULL,
  `quoted_by_admin_id` int(11) NOT NULL,
  `quotation_number` varchar(50) NOT NULL,
  `labor_cost` decimal(10,2) NOT NULL,
  `material_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `estimated_days` int(11) DEFAULT NULL,
  `quotation_notes` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','expired') DEFAULT 'pending',
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotation_number` (`quotation_number`),
  KEY `repair_item_id` (`repair_item_id`),
  KEY `quoted_by_admin_id` (`quoted_by_admin_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. Update bookings table to link with customer booking numbers
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `customer_booking_number_id` INT(11) DEFAULT NULL AFTER `booking_number_id`,
ADD COLUMN IF NOT EXISTS `repair_item_id` INT(11) DEFAULT NULL AFTER `service_id`,
ADD KEY IF NOT EXISTS `customer_booking_number_id` (`customer_booking_number_id`),
ADD KEY IF NOT EXISTS `repair_item_id` (`repair_item_id`);

-- 5. Insert sample repair item types
INSERT IGNORE INTO `service_categories` (`id`, `category_name`, `description`) VALUES
(4, 'Vehicle Repair', 'Car seats, truck covers, motorcycle seats repair'),
(5, 'Furniture Repair', 'Sofa repair, chair restoration, table repair'),
(6, 'Bedding Repair', 'Mattress repair, pillow restoration, bed frame repair');

-- 6. Create repair workflow view
CREATE OR REPLACE VIEW `repair_workflow_view` AS
SELECT 
    ri.`id` as repair_item_id,
    ri.`item_name`,
    ri.`item_description`,
    ri.`item_type`,
    ri.`urgency`,
    ri.`estimated_cost`,
    ri.`status` as repair_status,
    ri.`admin_notes`,
    ri.`created_at` as repair_created_at,
    cbn.`id` as customer_booking_number_id,
    bn.`booking_number`,
    u.`fullname` as customer_name,
    u.`email` as customer_email,
    u.`phone` as customer_phone,
    admin.`fullname` as assigned_by_admin,
    cbn.`assigned_at`,
    rq.`id` as quotation_id,
    rq.`quotation_number`,
    rq.`total_cost` as quoted_cost,
    rq.`estimated_days`,
    rq.`status` as quotation_status,
    rq.`valid_until`
FROM `repair_items` ri
LEFT JOIN `customer_booking_numbers` cbn ON ri.`customer_booking_number_id` = cbn.`id`
LEFT JOIN `booking_numbers` bn ON cbn.`booking_number_id` = bn.`id`
LEFT JOIN `users` u ON ri.`customer_id` = u.`id`
LEFT JOIN `users` admin ON cbn.`assigned_by_admin_id` = admin.`id`
LEFT JOIN `repair_quotations` rq ON ri.`id` = rq.`repair_item_id` AND rq.`status` = 'pending'
ORDER BY ri.`created_at` DESC;

-- 7. Create admin dashboard repair stats view
CREATE OR REPLACE VIEW `admin_repair_stats` AS
SELECT 
    (SELECT COUNT(*) FROM `repair_items` WHERE `status` = 'pending') as pending_repairs,
    (SELECT COUNT(*) FROM `repair_items` WHERE `status` = 'quoted') as quoted_repairs,
    (SELECT COUNT(*) FROM `repair_items` WHERE `status` = 'approved') as approved_repairs,
    (SELECT COUNT(*) FROM `repair_items` WHERE `status` = 'in_progress') as in_progress_repairs,
    (SELECT COUNT(*) FROM `repair_items` WHERE `status` = 'completed') as completed_repairs,
    (SELECT COUNT(*) FROM `customer_booking_numbers` WHERE `status` = 'active') as active_booking_numbers,
    (SELECT COUNT(*) FROM `customer_booking_numbers` WHERE `status` = 'used') as used_booking_numbers,
    (SELECT COALESCE(SUM(`total_cost`), 0) FROM `repair_quotations` WHERE `status` = 'accepted') as total_quoted_revenue;

-- 8. Insert sample data for testing
INSERT IGNORE INTO `customer_booking_numbers` (`customer_id`, `booking_number_id`, `assigned_by_admin_id`, `status`) VALUES
(1, 1, 1, 'active'),
(2, 2, 1, 'active');

-- Verification queries
SELECT 'Repair workflow tables created successfully!' as message;
SELECT COUNT(*) as customer_booking_numbers_count FROM customer_booking_numbers;
SELECT COUNT(*) as repair_items_count FROM repair_items;
SELECT COUNT(*) as repair_quotations_count FROM repair_quotations;
