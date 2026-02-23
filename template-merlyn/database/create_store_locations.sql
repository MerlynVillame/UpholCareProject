-- Create Store Locations Table
-- This table will store information about upholstery store locations

USE db_upholcare;

CREATE TABLE IF NOT EXISTS store_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    province VARCHAR(50) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    operating_hours TEXT,
    services_offered TEXT,
    features TEXT,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert sample store locations
INSERT INTO store_locations (store_name, address, city, province, latitude, longitude, phone, email, operating_hours, services_offered, features, rating) VALUES
('UphoCare Main Branch', '123 Main Street, Barangay Central', 'Manila', 'Metro Manila', 14.5995, 120.9842, '02-8123-4567', 'main@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, 24/7 Customer Support, Quality Guarantee', 4.8),
('UphoCare Quezon City', '456 Quezon Avenue, Diliman', 'Quezon City', 'Metro Manila', 14.6760, 121.0437, '02-8123-4568', 'qc@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Express Service, Design Consultation', 4.6),
('UphoCare Makati', '789 Ayala Avenue, Makati City', 'Makati', 'Metro Manila', 14.5547, 121.0244, '02-8123-4569', 'makati@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Premium Materials, Custom Design', 4.9),
('UphoCare Pasig', '321 Ortigas Avenue, Pasig City', 'Pasig', 'Metro Manila', 14.5764, 121.0851, '02-8123-4570', 'pasig@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Eco-Friendly Materials, Quick Turnaround', 4.7),
('UphoCare Taguig', '654 BGC High Street, Taguig City', 'Taguig', 'Metro Manila', 14.5539, 121.0500, '02-8123-4571', 'taguig@uphocare.com', 'Mon-Fri: 8AM-6PM, Sat: 9AM-4PM', 'Furniture Reupholstery, Car Seat Repair, Mattress Services', 'Free Pickup, Luxury Materials, VIP Service', 4.8);

-- Add store_location_id to bookings table
ALTER TABLE bookings ADD COLUMN store_location_id INT AFTER service_id;
ALTER TABLE bookings ADD CONSTRAINT fk_store_location FOREIGN KEY (store_location_id) REFERENCES store_locations(id) ON DELETE SET NULL;
