-- Create Store Ratings Table
-- This table stores customer ratings for stores

USE db_upholcare;

-- Create table without foreign keys first (to avoid errors if referenced tables don't exist)
CREATE TABLE IF NOT EXISTS store_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    user_id INT NOT NULL,
    rating DECIMAL(2, 1) NOT NULL COMMENT 'Rating from 1.0 to 5.0',
    review_text TEXT NULL COMMENT 'Optional review text',
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_store_user_rating (store_id, user_id),
    INDEX idx_store_id (store_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign keys separately (optional - may fail if tables don't exist or have different structure)
-- These can be added manually if needed
-- ALTER TABLE store_ratings 
-- ADD CONSTRAINT fk_store_ratings_store_id 
-- FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE;

-- ALTER TABLE store_ratings 
-- ADD CONSTRAINT fk_store_ratings_user_id 
-- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Add trigger to automatically update store_locations.rating when a new rating is added
DELIMITER $$

CREATE TRIGGER update_store_rating_on_insert
AFTER INSERT ON store_ratings
FOR EACH ROW
BEGIN
    UPDATE store_locations
    SET rating = (
        SELECT AVG(rating)
        FROM store_ratings
        WHERE store_id = NEW.store_id
        AND status = 'active'
    )
    WHERE id = NEW.store_id;
END$$

CREATE TRIGGER update_store_rating_on_update
AFTER UPDATE ON store_ratings
FOR EACH ROW
BEGIN
    UPDATE store_locations
    SET rating = (
        SELECT AVG(rating)
        FROM store_ratings
        WHERE store_id = NEW.store_id
        AND status = 'active'
    )
    WHERE id = NEW.store_id;
END$$

CREATE TRIGGER update_store_rating_on_delete
AFTER DELETE ON store_ratings
FOR EACH ROW
BEGIN
    UPDATE store_locations
    SET rating = COALESCE((
        SELECT AVG(rating)
        FROM store_ratings
        WHERE store_id = OLD.store_id
        AND status = 'active'
    ), 0.00)
    WHERE id = OLD.store_id;
END$$

DELIMITER ;

