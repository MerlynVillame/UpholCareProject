-- Create Store Logistic Capacities Table
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

-- Create default capacity settings for stores (optional, could be in store_locations)
-- For now, we will assume if no record exists for a date, we use a default.
-- Or we can have a global default.
