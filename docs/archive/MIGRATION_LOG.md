# Database Migration Log

## Overview

This document tracks all database schema changes for the UphoCare system.

---

## Recent Migrations

### ✅ Migration #1: PICKUP Workflow Statuses

**Date:** December 2, 2025  
**File:** `run_pickup_workflow_migration.php`  
**Status:** ✅ Completed Successfully

**Changes:**

- Updated `bookings.status` ENUM to include:
  - `for_pickup`
  - `picked_up`
  - `for_inspection`
  - `for_quotation`
  - `in_progress`
  - `paid`
  - `closed`

**Result:**

```
✓ Status ENUM updated successfully
✓ 20 status values now available
✓ Default: 'pending'
```

---

### ✅ Migration #2: Quotation Sent Tracking

**Date:** December 2, 2025  
**File:** `run_quotation_field_migration.php`  
**Status:** ✅ Completed Successfully

**Changes:**

- Added `quotation_sent_at` DATETIME column
- Tracks when final quotation email was sent after inspection

**Result:**

```
✓ Column 'quotation_sent_at' added successfully
✓ Type: datetime
✓ Default: NULL
```

---

### ✅ Migration #3: Service Option Column

**Date:** December 2, 2025  
**File:** `run_service_option_migration.php`  
**Status:** ✅ Completed Successfully

**Changes:**

- Added `service_option` VARCHAR(50) column
- Stores service delivery method: pickup, delivery, both, walk_in
- Placed after `service_type` column
- Default value: 'pickup'

**Result:**

```
✓ Column 'service_option' added successfully
✓ Type: varchar(50)
✓ Default: 'pickup'
✓ All existing bookings set to 'pickup'
```

**Impact:**

- Fixes error: "Unknown column 'service_option' in 'field list'"
- Enables admin to see service option in All Bookings table
- Supports PICKUP workflow properly
- Admin can approve based on service feasibility

---

### ✅ Migration #4: Pickup/Delivery Address and Date Columns

**Date:** December 2, 2025  
**File:** `run_address_date_migration.php`  
**Status:** ✅ Completed Successfully

**Changes:**

- Added `pickup_address` TEXT column
- Added `delivery_address` TEXT column
- Added `delivery_date` DATE column
- Column `pickup_date` already existed

**Result:**

```
✓ Columns added: pickup_address, delivery_address, delivery_date
✓ Type: TEXT for addresses, DATE for dates
✓ Default: NULL (optional fields)
✓ All existing bookings updated
```

**Impact:**

- Fixes error: "Unknown column 'pickup_address' in 'field list'"
- Enables proper address tracking for pickup/delivery
- Supports service option workflow
- Admin can manage pickup and delivery logistics

---

### ✅ Migration #5: Complete Schema Verification and Missing Columns

**Date:** December 2, 2025  
**File:** `check_and_fix_all_columns.php`  
**Status:** ✅ Completed Successfully

**Changes:**

- Added `admin_notes` TEXT column
- Added `selected_color_id` INT column
- Added `color_type` VARCHAR(50) column
- Added `color_price` DECIMAL(10,2) column
- Added `booking_type` VARCHAR(50) column

**Result:**

```
✓ Total columns verified: 38
✓ All key columns confirmed present
✓ No missing required columns
✓ Database schema complete
```

**Impact:**

- Comprehensive database schema verification
- Prevents future "column not found" errors
- Ensures all features have required columns
- Database ready for production use

---

## Migration Summary

### Total Migrations Run: 5

| #   | Migration                    | Status     | Date        |
| --- | ---------------------------- | ---------- | ----------- |
| 1   | PICKUP Workflow Statuses     | ✅ Success | Dec 2, 2025 |
| 2   | Quotation Sent Tracking      | ✅ Success | Dec 2, 2025 |
| 3   | Service Option Column        | ✅ Success | Dec 2, 2025 |
| 4   | Address and Date Columns     | ✅ Success | Dec 2, 2025 |
| 5   | Complete Schema Verification | ✅ Success | Dec 2, 2025 |

---

## Database Schema Changes

### Bookings Table - New Columns

```sql
-- New columns added through migrations:
service_option VARCHAR(50) DEFAULT 'pickup'
pickup_address TEXT NULL
delivery_address TEXT NULL
delivery_date DATE NULL
quotation_sent_at DATETIME NULL DEFAULT NULL
admin_notes TEXT NULL
selected_color_id INT(11) NULL
color_type VARCHAR(50) NULL
color_price DECIMAL(10,2) DEFAULT 0.00
booking_type VARCHAR(50) DEFAULT 'personal'
```

**Note:** `pickup_date` already existed in the database.

**Total Columns in Bookings Table:** 38

### Bookings Table - Updated ENUM

```sql
-- Updated status ENUM:
status ENUM(
    'pending', 'for_pickup', 'picked_up', 'for_inspection',
    'for_quotation', 'approved', 'in_queue', 'in_progress',
    'under_repair', 'for_quality_check', 'ready_for_pickup',
    'out_for_delivery', 'completed', 'paid', 'closed',
    'cancelled', 'confirmed', 'accepted', 'rejected',
    'declined', 'admin_review'
) DEFAULT 'pending'
```

---

## Verification

### Check Current Schema

```sql
DESCRIBE bookings;
```

### Verify New Columns

```sql
SELECT
    service_type,
    service_option,
    status,
    quotation_sent_at
FROM bookings
LIMIT 10;
```

### Check Status Distribution

```sql
SELECT status, COUNT(*) as count
FROM bookings
GROUP BY status
ORDER BY count DESC;
```

---

## Rollback Instructions

**If needed, to rollback migrations:**

### Remove service_option column:

```sql
ALTER TABLE bookings DROP COLUMN service_option;
```

### Remove quotation_sent_at column:

```sql
ALTER TABLE bookings DROP COLUMN quotation_sent_at;
```

### Revert status ENUM:

```sql
ALTER TABLE bookings
MODIFY COLUMN status ENUM(
    'pending', 'approved', 'in_queue', 'under_repair',
    'for_quality_check', 'ready_for_pickup', 'out_for_delivery',
    'completed', 'cancelled'
) DEFAULT 'pending';
```

**⚠️ WARNING:** Rollback will lose data in the removed columns!

---

## Migration Files

### SQL Scripts (Manual Execution)

1. `update_pickup_workflow_statuses.sql`
2. `add_quotation_sent_field.sql`
3. `add_service_option_column.sql`

### PHP Scripts (Automated Execution)

1. `run_pickup_workflow_migration.php` ✅
2. `run_quotation_field_migration.php` ✅
3. `run_service_option_migration.php` ✅

---

## Testing After Migration

### Verify System Works:

1. **Admin Panel:**

   - [ ] Can view all bookings
   - [ ] Service Option column displays correctly
   - [ ] Can approve bookings
   - [ ] Can update statuses
   - [ ] New statuses work properly

2. **Customer Booking:**

   - [ ] Can create new booking
   - [ ] Service option is saved
   - [ ] Confirmation email sent
   - [ ] Status displays correctly

3. **PICKUP Workflow:**
   - [ ] Can send quotation after inspection
   - [ ] Quotation_sent_at is recorded
   - [ ] Email sent successfully
   - [ ] Status progression works

---

## Known Issues & Resolutions

### Issue #1: service_option column missing

**Error:** `Unknown column 'service_option' in 'field list'`  
**Resolution:** ✅ Run `run_service_option_migration.php`  
**Status:** Fixed

### Issue #2: Status ENUM missing new values

**Error:** `Invalid status value`  
**Resolution:** ✅ Run `run_pickup_workflow_migration.php`  
**Status:** Fixed

### Issue #3: quotation_sent_at column missing

**Error:** `Unknown column 'quotation_sent_at'`  
**Resolution:** ✅ Run `run_quotation_field_migration.php`  
**Status:** Fixed

### Issue #4: pickup_address column missing

**Error:** `Unknown column 'pickup_address' in 'field list'`  
**Resolution:** ✅ Run `run_address_date_migration.php`  
**Status:** Fixed

### Issue #5: Multiple columns missing

**Error:** Various "column not found" errors  
**Resolution:** ✅ Run `check_and_fix_all_columns.php`  
**Status:** Fixed - All 38 columns verified

---

## Migration Best Practices

### Before Running Migration:

1. ✅ Backup database
2. ✅ Test on development server first
3. ✅ Review migration script
4. ✅ Check for existing columns
5. ✅ Plan rollback strategy

### During Migration:

1. ✅ Run one migration at a time
2. ✅ Check output for errors
3. ✅ Verify column was added
4. ✅ Test basic queries

### After Migration:

1. ✅ Verify in application
2. ✅ Test affected features
3. ✅ Check for errors
4. ✅ Update documentation
5. ✅ Inform team

---

## Maintenance Schedule

### Regular Database Checks:

- **Weekly:** Check for errors in logs
- **Monthly:** Review schema for optimization
- **Quarterly:** Full database audit
- **Annually:** Performance tuning

---

## Contact

**For Migration Issues:**

- System Admin: admin@uphocare.com
- Developer: support@uphocare.com
- Emergency: [Emergency contact]

---

**Last Updated:** December 2, 2025  
**Maintained By:** UphoCare Development Team  
**Version:** 1.0
