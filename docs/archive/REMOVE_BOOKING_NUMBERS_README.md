# Remove Booking Numbers from Database

## Overview

This migration removes all booking number related columns and tables from the database. The system now uses availability based on:
- **Fabric/color stock** (quantity > 0)
- **Store availability** (color assigned to store)
- **Store capacity** (max 50 active bookings per store)

## What Gets Removed

### From `bookings` Table:
- `booking_number_id` column (foreign key to `booking_numbers` table)
- `customer_booking_number_id` column (if exists)
- Related indexes and foreign key constraints

### Tables Dropped:
- `booking_numbers` table (no longer needed)
- `customer_booking_numbers` table (only if `repair_items` doesn't use it)

## Migration Options

### Option 1: Run SQL Script Directly (phpMyAdmin)

1. Open phpMyAdmin
2. Select `db_upholcare` database
3. Go to SQL tab
4. Copy and paste the contents of `database/remove_booking_numbers.sql`
5. Click "Go" to execute

### Option 2: Run PHP Migration Script

1. Open terminal/command prompt
2. Navigate to project directory:
   ```bash
   cd C:\xampp\htdocs\UphoCare
   ```
3. Run the migration script:
   ```bash
   php database/run_remove_booking_numbers.php
   ```

## Important Notes

### Before Running Migration:

1. **Backup your database first!**
   ```sql
   -- Create backup
   mysqldump -u root -p db_upholcare > backup_before_remove_booking_numbers.sql
   ```

2. **Check for existing data:**
   - The migration will safely remove columns even if they contain data
   - All booking number references will be lost (this is intentional)

3. **Repair Items:**
   - If `repair_items` table uses `customer_booking_number_id`, the `customer_booking_numbers` table will be kept
   - Only the `bookings` table columns will be removed

### After Running Migration:

1. Verify the removal:
   ```sql
   -- Check bookings table structure
   SHOW COLUMNS FROM bookings LIKE '%booking%';
   
   -- Should return no results
   ```

2. Test booking creation:
   - Create a new booking through the customer portal
   - Verify it works without booking numbers
   - Check that availability is based on stock and capacity

## Code Changes Already Made

The following code changes have already been implemented:

1. ✅ Removed booking number display from customer bookings view
2. ✅ Removed booking number assignment in `processBooking()`
3. ✅ Added availability checks for fabric/color stock and store capacity
4. ✅ Removed booking number joins from database queries
5. ✅ Updated Booking model to not select booking numbers

## Troubleshooting

### phpMyAdmin Session Warning

The warning you see in phpMyAdmin:
```
session_regenerate_id(): Session object destruction failed. ID: files (path: C:\xampp\tmp)
```

This is a **phpMyAdmin issue**, not related to your application. It's usually caused by:
- File permissions in `C:\xampp\tmp`
- Disk space issues
- Session file cleanup problems

**Solution:** This warning doesn't affect your application. You can ignore it or:
1. Clear `C:\xampp\tmp` directory
2. Restart Apache in XAMPP Control Panel
3. Check file permissions on `C:\xampp\tmp`

### Foreign Key Constraint Errors

If you get foreign key constraint errors:
```sql
-- First, find the constraint name
SELECT CONSTRAINT_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'booking_number_id';

-- Then drop it manually
ALTER TABLE bookings DROP FOREIGN KEY [constraint_name];
```

### Column Not Found Errors

If columns don't exist, the migration will skip them safely. The PHP script checks for existence before dropping.

## Verification Queries

After migration, run these to verify:

```sql
-- Check bookings table columns
SHOW COLUMNS FROM bookings;

-- Check if booking_numbers table exists (should return 0 rows)
SELECT COUNT(*) FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'booking_numbers';

-- Check if any booking_number_id references remain
SELECT COUNT(*) FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND COLUMN_NAME LIKE '%booking_number%';
```

## Support

If you encounter any issues:
1. Check the error messages carefully
2. Verify database backup was created
3. Review the migration script output
4. Check that all code changes have been applied

