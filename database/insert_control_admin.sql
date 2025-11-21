-- Insert default control panel admin
-- Email: control@uphocare.com
-- Password: Control@2025

USE db_upholcare;

-- Password hash for "Control@2025" using PHP password_hash()
-- You can change this password later by updating the record
INSERT INTO control_panel_admins (email, password, fullname, status) 
VALUES (
    'control@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Control Panel Admin',
    'active'
);

-- Display the created admin
SELECT 'Control panel admin created!' AS status;
SELECT id, email, fullname, status, created_at FROM control_panel_admins WHERE email = 'control@uphocare.com';

-- Note: Default password is "Control@2025"
-- Please change this password after first login for security

