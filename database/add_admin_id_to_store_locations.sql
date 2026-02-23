-- Add admin_id column to store_locations table
ALTER TABLE store_locations 
ADD COLUMN admin_id INT NULL COMMENT 'Admin user who manages this store';

-- Add index on admin_id
ALTER TABLE store_locations 
ADD INDEX idx_admin_id (admin_id);

-- Add foreign key constraint
ALTER TABLE store_locations 
ADD CONSTRAINT fk_store_locations_admin_id 
FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL;
