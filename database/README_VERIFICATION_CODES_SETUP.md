# Admin Verification Codes Setup

## Overview
This system uses a pre-generated dataset of verification codes (1000-9999) that are automatically assigned to admin registrations when the Super Admin approves pending accounts.

## Database Setup

### Step 1: Run the Verification Codes Table Creation Script

Run the following SQL script to create the `admin_verification_codes` table and populate it with codes from 1000 to 9999:

```sql
-- Run in phpMyAdmin or MySQL command line
USE db_upholcare;
SOURCE database/create_admin_verification_codes_table.sql;
```

Or copy and paste the contents of `database/create_admin_verification_codes_table.sql` into phpMyAdmin.

### Step 2: Verify the Setup

After running the script, verify that the table was created and populated:

```sql
-- Check total codes
SELECT COUNT(*) as total_codes FROM admin_verification_codes;

-- Should return: 9000

-- Check available codes
SELECT COUNT(*) as available_codes 
FROM admin_verification_codes 
WHERE status = 'available';

-- Should return: 9000 (initially)

-- View sample codes
SELECT * FROM admin_verification_codes 
ORDER BY verification_code 
LIMIT 10;
```

## How It Works

1. **Code Assignment**: When a Super Admin approves a pending admin registration, the system:
   - Automatically selects the next available code (1000-9999) from the `admin_verification_codes` table
   - Marks the code as "reserved" to prevent duplicate assignment
   - Stores the code in the `admin_registrations` table
   - Marks the code as "used" and links it to the registration
   - Sends the code via email to the admin

2. **Code Statuses**:
   - `available`: Code is ready to be assigned
   - `reserved`: Code is temporarily reserved during assignment (prevents race conditions)
   - `used`: Code has been assigned to an admin registration
   - `expired`: Code has expired (7 days after assignment)

3. **Code Tracking**:
   - Each code is linked to the admin registration ID
   - Records which Super Admin assigned the code
   - Records when the code was assigned
   - Codes expire 7 days after assignment

## Table Structure

```sql
CREATE TABLE admin_verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    verification_code VARCHAR(10) NOT NULL UNIQUE,  -- Code from 1000-9999
    status ENUM('available', 'reserved', 'used', 'expired'),
    admin_registration_id INT NULL,                 -- Link to admin_registrations
    assigned_to_email VARCHAR(191) NULL,            -- Admin email
    assigned_to_name VARCHAR(100) NULL,             -- Admin name
    assigned_by_super_admin_id INT NULL,            -- Super Admin who assigned it
    assigned_at TIMESTAMP NULL,                     -- When assigned
    expires_at TIMESTAMP NULL,                      -- Expiration (7 days)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);
```

## Benefits

1. **Controlled Codes**: All codes are pre-generated and tracked
2. **No Duplicates**: Database constraints prevent duplicate code assignment
3. **Audit Trail**: Every code assignment is logged with timestamps and user information
4. **Automatic Assignment**: Super Admin doesn't need to manually enter codes
5. **Expiration Tracking**: Codes automatically expire after 7 days

## Maintenance

### View Used Codes
```sql
SELECT * FROM admin_verification_codes 
WHERE status = 'used' 
ORDER BY assigned_at DESC;
```

### View Available Codes Count
```sql
SELECT COUNT(*) as available 
FROM admin_verification_codes 
WHERE status = 'available';
```

### Reset Expired Codes (if needed)
```sql
UPDATE admin_verification_codes 
SET status = 'expired' 
WHERE expires_at < NOW() 
AND status = 'used';
```

### Re-populate Codes (if codes run out)
If all 9000 codes are used, you can reset them or add more:
```sql
-- Reset used codes back to available (use with caution)
UPDATE admin_verification_codes 
SET status = 'available',
    admin_registration_id = NULL,
    assigned_to_email = NULL,
    assigned_to_name = NULL,
    assigned_by_super_admin_id = NULL,
    assigned_at = NULL,
    expires_at = NULL
WHERE status = 'used';
```

## Troubleshooting

### Error: "No available codes found"
- Check if the table exists: `SHOW TABLES LIKE 'admin_verification_codes';`
- Check available codes: `SELECT COUNT(*) FROM admin_verification_codes WHERE status = 'available';`
- Re-run the population script if needed

### Error: "Table doesn't exist"
- Run the `create_admin_verification_codes_table.sql` script
- Verify the table was created: `DESCRIBE admin_verification_codes;`

### Codes Not Being Assigned
- Check database transaction is working
- Check for errors in the application log
- Verify the `approveAdmin` method is being called correctly

## Notes

- Codes are assigned sequentially (1000, 1001, 1002, etc.)
- Each code can only be used once
- Codes are automatically linked to admin registrations
- The system falls back to random code generation if the table is unavailable

