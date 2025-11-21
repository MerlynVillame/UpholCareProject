-- ============================================================================
-- Update super_admin_activity table to support customer approval/rejection
-- ============================================================================

USE db_upholcare;

-- Update the action_type ENUM to include customer_approved and customer_rejected
ALTER TABLE super_admin_activity 
MODIFY COLUMN action_type ENUM(
    'admin_approved', 
    'admin_rejected', 
    'admin_deactivated', 
    'customer_approved',
    'customer_rejected',
    'system_config'
) NOT NULL;

-- Verify the update
SELECT '✅ Super Admin Activity action types updated successfully!' AS status;

