-- Step 1: Check which admin should own mashe store
-- Look at the users table and find the admin user
SELECT id, username, fullname, email, role 
FROM users 
WHERE role = 'admin';

-- Step 2: Update mashe store with the correct admin_id
-- Replace 'X' with the admin user ID from Step 1
-- For example, if the admin user ID is 2, then:
-- UPDATE store_locations SET admin_id = 2 WHERE id = 7;

-- Step 3: Assign all services to mashe store (id = 7)
UPDATE services 
SET store_id = 7 
WHERE store_id IS NULL;

-- Step 4: Verify the update
SELECT id, service_name, store_id 
FROM services;
