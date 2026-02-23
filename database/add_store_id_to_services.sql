-- Add store_id column to services table to link services to specific stores
-- Each admin manages one store, and each store has its own services

-- Step 1: Add store_id column
ALTER TABLE services 
ADD COLUMN store_id INT NULL COMMENT 'Store that offers this service';

-- Step 2: Add index on store_id for better query performance
ALTER TABLE services 
ADD INDEX idx_store_id (store_id);

-- Step 3: Add foreign key constraint to ensure referential integrity
ALTER TABLE services 
ADD CONSTRAINT fk_services_store_id 
FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE;
