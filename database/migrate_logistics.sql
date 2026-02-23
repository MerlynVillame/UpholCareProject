-- Add new statuses to bookings table
-- First, change the column to allow more values
ALTER TABLE `bookings` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'pending';

-- Map existing statuses if needed (though VARCHAR is safer than ENUM for frequent changes)
-- But if we want to stay with ENUM:
-- ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending','approved','for_pickup','for_dropoff','for_inspect','picked_up','to_inspect','for_inspection','inspect_completed','inspection_completed_waiting_approval','preview_receipt_sent','for_repair','in_queue','in_progress','under_repair','for_quality_check','repair_completed','repair_completed_ready_to_deliver','ready_for_pickup','out_for_delivery','completed','delivered_and_paid','paid','closed','cancelled','confirmed','accepted','rejected','declined','admin_review', 'pending_schedule', 'scheduled', 'reschedule_requested') DEFAULT 'pending';

-- Let's stick with VARCHAR for flexibility since we are adding many statuses
ALTER TABLE `bookings` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'pending_schedule';

-- Ensure store_logistic_capacities table exists
CREATE TABLE IF NOT EXISTS `store_logistic_capacities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `store_id` INT NOT NULL,
    `date` DATE NOT NULL,
    `max_pickup` INT DEFAULT 2,
    `max_delivery` INT DEFAULT 2,
    `max_inspection` INT DEFAULT 3,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_store_date` (`store_id`, `date`),
    FOREIGN KEY (`store_id`) REFERENCES `store_locations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
