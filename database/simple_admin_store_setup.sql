-- ============================================================================
-- SIMPLE SETUP: Link ALL admin users to their stores and assign services
-- ============================================================================

-- STEP 1: Link ALL stores to their admin users based on email matching
-- ----------------------------------------------------------------------------
UPDATE store_locations sl
INNER JOIN users u ON sl.email = u.email
SET sl.admin_id = u.id
WHERE u.role = 'admin';


-- STEP 2: Verify all stores are now linked to their admins
-- ----------------------------------------------------------------------------
SELECT 
    sl.id AS store_id,
    sl.store_name,
    sl.email AS store_email,
    sl.admin_id,
    u.username AS admin_username,
    u.fullname AS admin_name
FROM store_locations sl
LEFT JOIN users u ON sl.admin_id = u.id
ORDER BY sl.id;


-- STEP 3: Assign ALL existing services to mashe store (id=7)
-- ----------------------------------------------------------------------------
UPDATE services 
SET store_id = 7 
WHERE store_id IS NULL;


-- STEP 4: Verify services are assigned to stores
-- ----------------------------------------------------------------------------
SELECT 
    s.id AS service_id,
    s.service_name,
    s.service_type,
    s.store_id,
    sl.store_name,
    u.username AS admin_username
FROM services s
LEFT JOIN store_locations sl ON s.store_id = sl.id
LEFT JOIN users u ON sl.admin_id = u.id
ORDER BY s.store_id, s.service_name;
