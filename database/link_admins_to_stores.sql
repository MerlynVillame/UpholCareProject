-- Link all stores to their admin users based on email matching
UPDATE store_locations sl
INNER JOIN users u ON sl.email = u.email
SET sl.admin_id = u.id
WHERE u.role = 'admin';

-- Assign all existing services to mashe store (id=7)
UPDATE services 
SET store_id = 7 
WHERE store_id IS NULL;

-- Verify stores are linked to admins
SELECT id, store_name, email, admin_id 
FROM store_locations;

-- Verify services are assigned
SELECT id, service_name, service_type, store_id 
FROM services;
