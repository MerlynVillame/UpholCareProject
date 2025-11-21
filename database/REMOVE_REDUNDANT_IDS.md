# Removing Redundant ID Columns from Database

## Summary

After analyzing the UphoCare database and codebase, I identified **ONE redundant ID column** that should be removed.

## Redundant Column Found

### ❌ `bookings` table - `customer_id` column

**Status:** REDUNDANT - Can be safely removed

**Reason:**
- The `bookings` table has both `user_id` and `customer_id` columns
- Both columns store the same value (reference to `users.id`)
- The entire codebase uses `user_id` in all queries:
  - `models/Booking.php` - All queries use `user_id`
  - `models/Quotation.php` - All queries use `user_id`
  - `controllers/CustomerController.php` - Previously inserted both, now fixed to use only `user_id`
- `customer_id` was added for "compatibility" but serves no purpose

**Impact of Removal:**
- ✅ No impact on system functionality
- ✅ All code uses `user_id`
- ✅ Database will be cleaner and more efficient
- ✅ Reduces confusion for developers

## Tables Analyzed (NOT Redundant)

### ✅ `repair_items` table - `customer_id` column
**Status:** NOT redundant - Keep this column

**Reason:**
- This is the primary foreign key to link repair items to customers
- Used correctly in queries
- No duplicate column exists

### ✅ `customer_booking_numbers` table - `customer_id` column
**Status:** NOT redundant - Keep this column

**Reason:**
- This is the primary foreign key to link booking numbers to customers
- Used correctly in queries
- No duplicate column exists

### ✅ `store_compliance_reports` table - `customer_id` column
**Status:** NOT redundant - Keep this column

**Reason:**
- This is the primary foreign key to link reports to customers
- Used correctly in queries
- No duplicate column exists

## Action Required

### Step 1: Backup Database
```bash
# Always backup before making schema changes!
mysqldump -u root -p db_upholcare > backup_before_removing_customer_id.sql
```

### Step 2: Run the SQL Script
Execute the provided SQL script to remove the redundant column:

```bash
mysql -u root -p db_upholcare < database/remove_redundant_customer_id.sql
```

Or manually in phpMyAdmin/MySQL Workbench:
1. Open the SQL file: `database/remove_redundant_customer_id.sql`
2. Review the safety check query
3. Execute all statements

### Step 3: Verify Code Changes
The following code changes have already been made:

#### ✅ Fixed: `controllers/CustomerController.php`
- **Line 230-231:** Removed redundant `'customer_id' => $userId` insertion
- **Line 1111:** Changed `'customer_id'` to `'user_id'` in business reservation

## Verification Checklist

After removal, verify the following:

- [ ] Database backup created
- [ ] SQL script executed successfully
- [ ] `bookings` table no longer has `customer_id` column
- [ ] All customer bookings still visible in dashboard
- [ ] New bookings can be created
- [ ] Booking details page works
- [ ] Admin can view all bookings
- [ ] No PHP errors in logs

## Technical Details

### Before (Redundant)
```sql
CREATE TABLE bookings (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,           -- Used in all queries ✓
  customer_id int(11) DEFAULT NULL,       -- Redundant, not used ✗
  service_id int(11) DEFAULT NULL,
  ...
);
```

### After (Clean)
```sql
CREATE TABLE bookings (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,           -- Single source of truth ✓
  service_id int(11) DEFAULT NULL,
  ...
);
```

## Benefits of Removal

1. **Cleaner Schema** - No duplicate columns
2. **Better Performance** - One less column to index and maintain
3. **Reduced Confusion** - Developers won't wonder which column to use
4. **Smaller Database** - Less storage space
5. **Simplified Maintenance** - Only one column to manage

## Questions?

If you have any questions about this change or need help with the removal process, please review:
- The SQL script: `database/remove_redundant_customer_id.sql`
- The code changes in: `controllers/CustomerController.php`
- The Booking model: `models/Booking.php`

---
**Date:** November 18, 2025  
**Analyst:** AI Assistant  
**Status:** Ready to implement

