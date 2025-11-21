-- Add verification code fields to admin_registrations table
USE db_upholcare;

ALTER TABLE admin_registrations 
ADD COLUMN verification_code VARCHAR(10) NULL COMMENT '6-digit verification code sent via email',
ADD COLUMN verification_code_sent_at TIMESTAMP NULL COMMENT 'When verification code was sent',
ADD COLUMN verification_code_verified_at TIMESTAMP NULL COMMENT 'When verification code was verified',
ADD COLUMN verification_attempts INT DEFAULT 0 COMMENT 'Number of verification attempts',
ADD INDEX idx_verification_code (verification_code);

-- Update registration_status enum to include 'pending_verification'
ALTER TABLE admin_registrations 
MODIFY COLUMN registration_status ENUM('pending_verification', 'pending', 'approved', 'rejected') DEFAULT 'pending_verification';

SELECT '✅ Verification code fields added to admin_registrations table!' AS status;

