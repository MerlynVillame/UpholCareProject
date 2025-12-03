-- Add calculation fields for admin to calculate total payment (bayronon)
-- Admin must examine item, measure fabric, and calculate total before approval

ALTER TABLE bookings 
ADD COLUMN fabric_length DECIMAL(10,2) NULL AFTER color_price,
ADD COLUMN fabric_width DECIMAL(10,2) NULL AFTER fabric_length,
ADD COLUMN fabric_area DECIMAL(10,2) NULL AFTER fabric_width,
ADD COLUMN fabric_cost_per_meter DECIMAL(10,2) NULL AFTER fabric_area,
ADD COLUMN fabric_total DECIMAL(10,2) NULL AFTER fabric_cost_per_meter,
ADD COLUMN material_cost DECIMAL(10,2) DEFAULT 0.00 AFTER fabric_total,
ADD COLUMN service_fees DECIMAL(10,2) DEFAULT 0.00 AFTER material_cost,
ADD COLUMN calculated_total_saved TINYINT(1) DEFAULT 0 AFTER service_fees,
ADD COLUMN calculation_notes TEXT NULL AFTER calculated_total_saved;

-- Add index for faster queries
CREATE INDEX idx_calculated_total_saved ON bookings(calculated_total_saved);

