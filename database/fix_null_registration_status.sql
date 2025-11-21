-- Fix NULL or empty registration_status in admin_registrations table
-- This ensures all pending admin registrations show Accept/Reject buttons

-- Update NULL or empty registration_status to 'pending'
UPDATE admin_registrations 
SET registration_status = 'pending' 
WHERE registration_status IS NULL 
   OR registration_status = '' 
   OR registration_status NOT IN ('pending', 'pending_verification', 'approved', 'rejected');

-- Verify the update
SELECT 
    id, 
    email, 
    fullname, 
    registration_status,
    created_at
FROM admin_registrations 
WHERE registration_status = 'pending'
ORDER BY created_at DESC;

