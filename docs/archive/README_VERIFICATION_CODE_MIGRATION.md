# Verification Code Migration

## Problem
When clicking "Accept" in admin registration, you may get an error:
```
Failed to approve registration: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'verification_code' in 'field list'
```

This happens because the `verification_code` columns don't exist in the `admin_registrations` table.

## Solution

Run the SQL migration script to add the required columns:

### Option 1: Run via phpMyAdmin
1. Open phpMyAdmin
2. Select the `db_upholcare` database
3. Click on the "SQL" tab
4. Copy and paste the contents of `database/add_verification_code_to_admin_registrations.sql`
5. Click "Go" to execute

### Option 2: Run via Command Line
```bash
mysql -u root -p db_upholcare < database/add_verification_code_to_admin_registrations.sql
```

### Option 3: Run via MySQL Client
```sql
USE db_upholcare;
SOURCE database/add_verification_code_to_admin_registrations.sql;
```

## What the Migration Does

The migration script will:
1. Add `verification_code` column (VARCHAR(10)) to store the 6-digit code
2. Add `verification_code_sent_at` column (TIMESTAMP) to track when code was sent
3. Add `verification_code_verified_at` column (TIMESTAMP) to track when code was verified
4. Add `verification_attempts` column (INT) to track verification attempts
5. Add index on `verification_code` for faster lookups
6. Update `registration_status` ENUM to include 'pending_verification' status

## After Running the Migration

After running the migration:
- The "Accept" button will work correctly
- Verification codes will be stored in the database
- Admins can view verification codes on the verification page
- The verification workflow will function properly

## Notes

- The code has been updated to handle missing columns gracefully
- If columns don't exist, the approval will still work but verification codes won't be stored
- It's recommended to run the migration to enable full functionality

