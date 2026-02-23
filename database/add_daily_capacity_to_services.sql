-- Add daily_capacity column to services table
-- This allows each service to have its own daily booking capacity

ALTER TABLE services 
ADD COLUMN daily_capacity INT DEFAULT 10 COMMENT 'Maximum bookings per day for this service';

-- Update existing services with reasonable default capacities
-- You can adjust these based on your business needs
UPDATE services SET daily_capacity = 10 WHERE daily_capacity IS NULL;
