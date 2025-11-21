-- Update users table to allow 'pending' status
ALTER TABLE `users` MODIFY COLUMN `status` enum('active','inactive','pending') DEFAULT 'active';

-- Create business_info table for admin business validation
CREATE TABLE IF NOT EXISTS `business_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_license_number` varchar(100) NOT NULL,
  `business_address` text NOT NULL,
  `business_city` varchar(100) NOT NULL,
  `business_state` varchar(100) DEFAULT NULL,
  `business_zip` varchar(20) DEFAULT NULL,
  `business_country` varchar(100) DEFAULT 'Philippines',
  `business_phone` varchar(20) NOT NULL,
  `business_email` varchar(100) NOT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `tax_id_number` varchar(50) DEFAULT NULL,
  `business_registration_number` varchar(100) DEFAULT NULL,
  `business_website` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verification_notes` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `verification_status` (`verification_status`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

