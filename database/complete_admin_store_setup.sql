-- ============================================================================
-- COMPLETE SETUP: Link ALL admin users to their stores and assign services
-- ============================================================================

-- STEP 1: Add admin_id column to store_locations (if not already done)
-- ----------------------------------------------------------------------------
ALTER TABLE store_locations 
ADD COLUMN IF NOT EXISTS admin_id INT NULL COMMENT 'Admin user who manages this store';

ALTER TABLE store_locations 
ADD INDEX IF NOT EXISTS idx_admin_id (admin_id);

ALTER TABLE store_locations 
ADD CONSTRAINT IF NOT EXISTS fk_store_locations_admin_id 
FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL;


-- STEP 2: Link ALL stores to their admin users based on email matching
-- ----------------------------------------------------------------------------
-- This automatically matches store emails with user emails where role='admin'
UPDATE store_locations sl
INNER JOIN users u ON sl.email = u.email
SET sl.admin_id = u.id
WHERE u.role = 'admin';


-- STEP 3: Verify all stores are now linked to their admins
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


-- STEP 4: For existing services, assign them to specific stores
-- ----------------------------------------------------------------------------
-- Option A: Assign ALL existing services to mashe store (id=7)
UPDATE services 
SET store_id = 7 
WHERE store_id IS NULL;

-- Option B: If you want to assign services to different stores manually:
-- UPDATE services SET store_id = 4 WHERE id IN (1, 2);  -- Assign services 1,2 to store 4
-- UPDATE services SET store_id = 5 WHERE id IN (3, 4);  -- Assign services 3,4 to store 5
-- UPDATE services SET store_id = 7 WHERE id = 5;        -- Assign service 5 to store 7


-- STEP 5: Verify services are assigned to stores
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


-- ============================================================================
-- WHAT THIS ACCOMPLISHES:
-- ============================================================================
-- 1. ✅ All stores are linked to their admin users (via admin_id)
-- 2. ✅ All existing services are assigned to stores (via store_id)
-- 3. ✅ When admins create NEW services in the future, they automatically
--       get assigned to their store (via AdminController code)
-- 4. ✅ Each admin can only see/manage their own store's services
-- 5. ✅ Customers only see services for the specific store they're viewing
-- ============================================================================
