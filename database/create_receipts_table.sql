-- Create Official Receipts Table
-- This table stores official receipts issued to customers

CREATE TABLE IF NOT EXISTS `official_receipts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `booking_id` INT(11) NOT NULL,
  `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
  `issued_by` INT(11) DEFAULT NULL COMMENT 'Admin user ID who issued the receipt',
  `issued_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `receipt_data` TEXT DEFAULT NULL COMMENT 'JSON data of receipt details',
  `pdf_path` VARCHAR(255) DEFAULT NULL COMMENT 'Path to PDF file if saved',
  `email_sent` TINYINT(1) DEFAULT 0 COMMENT 'Whether receipt was emailed to customer',
  `email_sent_at` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('draft', 'issued', 'cancelled') DEFAULT 'issued',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_receipt_number` (`receipt_number`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_issued_by` (`issued_by`),
  KEY `idx_issued_at` (`issued_at`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add receipt_issued column to bookings table
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `receipt_issued` TINYINT(1) DEFAULT 0 COMMENT 'Whether official receipt has been issued',
ADD COLUMN IF NOT EXISTS `receipt_issued_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When receipt was issued',
ADD COLUMN IF NOT EXISTS `receipt_number` VARCHAR(50) DEFAULT NULL COMMENT 'Official receipt number';

-- Add index for receipt lookup
ALTER TABLE `bookings`
ADD INDEX IF NOT EXISTS `idx_receipt_issued` (`receipt_issued`),
ADD INDEX IF NOT EXISTS `idx_receipt_number` (`receipt_number`);

