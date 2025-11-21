# Booking Number ID Fix - Summary

## Problem Description

Users were encountering two related errors when creating new bookings:

1. **Initial Error**: `SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails`

   - The booking_number_id being inserted didn't exist in the booking_numbers table

2. **Secondary Error**: `SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'booking_number_id' cannot be null`
   - The booking_number_id column was set to NOT NULL in the database schema

## Root Cause

The system design requires that:

- Bookings are created by customers WITHOUT a booking number
- Admins assign booking numbers AFTER reviewing/accepting the booking
- Therefore, `booking_number_id` should be NULL during initial booking creation

However, the database schema had two issues:

1. The `booking_number_id` column was set to NOT NULL
2. The application code wasn't explicitly setting `booking_number_id` to NULL

## Solution Implemented

### 1. Database Schema Changes

Modified the `booking_number_id` column to allow NULL values:

```sql
ALTER TABLE `bookings`
MODIFY COLUMN `booking_number_id` INT(11) NULL;
```

Added proper foreign key constraint with ON DELETE SET NULL:

```sql
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_ibfk_3`
FOREIGN KEY (`booking_number_id`)
REFERENCES `booking_numbers` (`id`)
ON DELETE SET NULL;
```

**Migration Script**: `database/fix_booking_number_id_column.sql`

### 2. Application Code Changes

Updated `controllers/CustomerController.php` in two locations:

#### Location 1: `processBooking()` method (line ~124)

- Explicitly set `'booking_number_id' => null` in $bookingData array
- Modified array_filter to preserve NULL value for booking_number_id

#### Location 2: `processRepairReservation()` method (line ~647)

- Explicitly set `'booking_number_id' => null` in $reservationData array

### 3. Array Filtering Logic

Changed from:

```php
$bookingData = array_filter($bookingData, function($value) {
    return $value !== null;
});
```

To:

```php
$bookingData = array_filter($bookingData, function($value, $key) {
    // Keep booking_number_id even if it's null (it should be null initially)
    if ($key === 'booking_number_id') {
        return true;
    }
    return $value !== null;
}, ARRAY_FILTER_USE_BOTH);
```

## Verification

After applying the fixes, verify:

1. **Database Schema**:

   ```sql
   DESCRIBE bookings;
   -- booking_number_id should show "YES" in the Null column
   ```

2. **Foreign Key Constraint**:

   ```sql
   SELECT CONSTRAINT_NAME, DELETE_RULE
   FROM information_schema.REFERENTIAL_CONSTRAINTS
   WHERE TABLE_NAME = 'bookings' AND CONSTRAINT_NAME = 'bookings_ibfk_3';
   -- DELETE_RULE should show "SET NULL"
   ```

3. **Booking Creation**: Test creating a new booking as a customer - it should succeed without errors

## Files Modified

1. `database/fix_booking_number_id_column.sql` (NEW - migration script)
2. `controllers/CustomerController.php` (MODIFIED - lines ~120-144 and ~645-667)

## Testing

To test the fix:

1. Log in as a customer
2. Create a new repair reservation
3. The booking should be created successfully with status "pending"
4. The booking_number_id should be NULL in the database
5. Admin can later assign a booking number when accepting the booking

## Notes

- The `booking_number_id` will remain NULL until an admin reviews and accepts the booking
- Admins use the `assignBookingNumber()` method in the Booking model to set the booking number
- This design allows for a proper workflow: Create → Review → Accept → Assign Number
