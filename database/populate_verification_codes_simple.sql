-- ============================================================================
-- Simple Populate Verification Codes (1000-9999)
-- More compatible approach that works with all MySQL versions
-- ============================================================================

USE db_upholcare;

-- First, ensure the table exists (run create_admin_verification_codes_table.sql first)
-- Then populate with codes using a simple loop approach

-- Clear existing codes (optional - comment out if you want to keep existing)
-- DELETE FROM admin_verification_codes WHERE status = 'available';

-- Method 1: Using a temporary table with numbers
-- Create a temporary table with numbers 1000-9999
CREATE TEMPORARY TABLE IF NOT EXISTS temp_numbers AS
SELECT 1000 + (t1.num * 1000 + t2.num * 100 + t3.num * 10 + t4.num) AS num
FROM 
    (SELECT 0 AS num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
    (SELECT 0 AS num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
    (SELECT 0 AS num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
    (SELECT 0 AS num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
WHERE (t1.num * 1000 + t2.num * 100 + t3.num * 10 + t4.num) <= 8999;

-- Insert codes from temporary table (ignore duplicates)
INSERT IGNORE INTO admin_verification_codes (verification_code, status)
SELECT LPAD(num, 4, '0'), 'available'
FROM temp_numbers
WHERE num BETWEEN 1000 AND 9999;

-- Drop temporary table
DROP TEMPORARY TABLE IF EXISTS temp_numbers;

-- Verify insertion
SELECT 
    COUNT(*) as total_codes,
    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_codes,
    MIN(verification_code) as first_code,
    MAX(verification_code) as last_code
FROM admin_verification_codes;

SELECT '✅ Verification codes populated successfully!' AS status;

