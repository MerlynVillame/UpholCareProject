# PICKUP Workflow Implementation Guide

## Overview

This document explains the implementation of the **PICKUP service option workflow** for the UphoCare upholstery system, where **pricing is sent AFTER the item is picked up and inspected**.

This workflow is designed specifically for upholstery work where accurate pricing depends on actual inspection and measurement, which cannot be fully determined during online booking.

---

## Why This Workflow is Necessary

### In Upholstery Work, Accurate Pricing Requires Physical Inspection Because:

1. **Exact measurements can only be taken once the item is physically in the shop**
   - Customers usually don't know correct measurements
   - Actual size may be larger than expected
   - Hidden complexity discovered during inspection

2. **Hidden damages are discovered only during inspection**
   - Internal foam damage
   - Broken springs, torn lining, rotted wood
   - Mold or smell issues
   - Stitches needing replacement

3. **Material usage depends on exact size**
   - Leather and fabric cost based on actual size
   - Pattern layout, number of panels
   - Stitch length, foam thickness

4. **Prevents wrong pricing and customer complaints**
   - ❌ Sending prices BEFORE pickup can lead to disputes if costs increase
   - ✅ Sending prices AFTER pickup is accurate, fair, and transparent

---

## Workflow Diagram

### PICKUP Service Option Workflow

```
Customer Creates Booking (Service Option: PICKUP)
     ↓
📧 Email #1 Sent (NO PRICING)
   - Confirmation + Queue Number
   - "Pricing will be provided after inspection"
     ↓
Status: PENDING → FOR_PICKUP
     ↓
Item is PICKED UP from customer
     ↓
Status: PICKED_UP → FOR_INSPECTION
     ↓
Admin inspects item:
   - Correct measurements
   - Actual damage assessment
   - Material requirements
     ↓
Status: FOR_QUOTATION
     ↓
Admin prepares FINAL and ACCURATE pricing
     ↓
Admin clicks "Send Final Quotation" button
     ↓
📧 Email #2 Sent (WITH TOTAL PAYMENT)
   - Based on real measurement and inspection
   - Complete pricing breakdown
     ↓
Customer reviews and approves quotation
     ↓
Status: APPROVED → IN_PROGRESS
     ↓
Work starts
     ↓
Status: COMPLETED → PAID → CLOSED
```

---

## Status Flow

### Complete Status Options for PICKUP Workflow

| Status | Description | When Used |
|--------|-------------|-----------|
| **pending** | Customer submitted booking, admin has not reviewed yet | Immediately after booking creation |
| **for_pickup** | Admin approved, waiting to collect item | After admin reviews and schedules pickup |
| **picked_up** | Item collected, waiting for inspection | As soon as item arrives at shop |
| **for_inspection** | Item being inspected | During measurement and damage assessment |
| **for_quotation** | Inspection done, admin preparing final price | After inspection, before sending Email #2 |
| **approved** | Customer approved quotation, ready for repair | After customer accepts final pricing |
| **in_progress** | Technicians working on the item | During repair work |
| **completed** | Work finished | When repair is complete |
| **paid** | Full payment received | After customer pays |
| **closed** | Booking completed and archived | Final status |

---

## Database Changes

### 1. New Status Options

**File:** `database/update_pickup_workflow_statuses.sql`

Added new ENUM values to `bookings.status` column:
- `for_pickup`
- `picked_up`
- `for_inspection`
- `for_quotation`
- `in_progress`
- `paid`
- `closed`

**Run migration:**
```bash
php database/run_pickup_workflow_migration.php
```

### 2. New Field: quotation_sent_at

**File:** `database/add_quotation_sent_field.sql`

Added `quotation_sent_at` DATETIME field to track when final quotation was sent.

**Run migration:**
```bash
php database/run_quotation_field_migration.php
```

---

## Email Templates

### Email #1: Booking Confirmation (NO PRICING)

**When sent:** Immediately after customer creates booking with PICKUP option

**Modified file:** `core/NotificationService.php` → `getBookingConfirmationTemplate()`

**Contains:**
- Queue number
- Booking details (item description, pickup date)
- **Special note for PICKUP:** "Final pricing will be provided after inspection"
- **NO pricing information**

**Example message:**
> "Since you selected PICKUP service, our team will collect your item for inspection. Final pricing will be provided after we inspect the item at our shop, as accurate pricing requires physical measurements and damage assessment."

### Email #2: Final Quotation After Inspection (WITH PRICING)

**When sent:** After item is picked up and inspected, admin manually triggers this

**New method:** `core/NotificationService.php` → `sendQuotationAfterInspection()`

**Contains:**
- "Inspection Completed" confirmation
- Complete pricing breakdown:
  - Labor Fee
  - Pickup Fee
  - Delivery Fee (if applicable)
  - Fabric/Color Price
  - **TOTAL AMOUNT**
- Explanation that pricing is based on actual inspection
- Next steps for customer approval

---

## Admin Functionality

### Send Final Quotation After Inspection

**File:** `controllers/AdminController.php` → `sendQuotationAfterInspection()`

**How it works:**

1. Admin views booking details in Admin Dashboard
2. When status is `picked_up`, `for_inspection`, or `for_quotation`
3. Admin clicks **"Send Final Quotation"** button
4. System verifies:
   - Booking exists
   - Service option is PICKUP
   - Status is appropriate for sending quotation
5. Sends Email #2 with complete pricing
6. Updates `quotation_sent_at` timestamp
7. Creates in-app notification for customer

**Access:** Available in booking details modal when viewing a PICKUP booking

---

## UI Changes

### Admin Dashboard (all_bookings.php)

1. **Updated Status Dropdown**
   - Added all new PICKUP workflow statuses
   - Organized into logical groups:
     - Initial Stages
     - PICKUP Workflow
     - Work in Progress
     - Completion
     - Other

2. **New Button: "Send Final Quotation"**
   - Visible only for PICKUP service option
   - Only appears when status is: `picked_up`, `for_inspection`, or `for_quotation`
   - Located in booking details modal footer

### Customer Dashboard (bookings.php)

1. **Updated Status Labels**
   - Added badges and icons for new statuses:
     - 🚚 For Pickup
     - 📦 Picked Up
     - 🔍 For Inspection
     - 💰 For Quotation
     - ✅ Approved
     - 🔄 In Progress
     - 💵 Paid
     - 📁 Closed

---

## Testing the Workflow

### Step-by-Step Test

1. **Customer creates booking:**
   - Select service with PICKUP option
   - Submit booking
   - ✅ Check Email #1 received (NO pricing)

2. **Admin reviews booking:**
   - Login as admin
   - View booking in All Bookings
   - Change status to `for_pickup`
   - Update booking

3. **Admin picks up item:**
   - Change status to `picked_up`
   - Update booking

4. **Admin inspects item:**
   - Change status to `for_inspection`
   - Perform physical inspection
   - Take measurements
   - Assess damage

5. **Admin prepares quotation:**
   - Change status to `for_quotation`
   - Update pricing fields if needed
   - Update booking

6. **Admin sends final quotation:**
   - Click "Send Final Quotation" button
   - ✅ Check Email #2 received (WITH pricing)
   - ✅ Verify customer receives in-app notification

7. **Customer approves:**
   - Admin changes status to `approved`
   - Work begins

8. **Complete workflow:**
   - `in_progress` → `completed` → `paid` → `closed`

---

## API Endpoints

### Send Quotation After Inspection

**Endpoint:** `POST /admin/sendQuotationAfterInspection`

**Parameters:**
- `booking_id` (required): The booking ID

**Response:**
```json
{
  "success": true,
  "message": "Final quotation sent to customer successfully! Customer will receive email with complete pricing details."
}
```

**Error responses:**
```json
{
  "success": false,
  "message": "Quotation after inspection is only for PICKUP service option. This booking is: delivery"
}
```

---

## Important Notes

### For Administrators

1. **Always inspect the item before sending quotation**
   - Take accurate measurements
   - Document all damage
   - Calculate exact material requirements

2. **Update pricing fields before sending quotation**
   - Ensure all fees are correct
   - Verify labor fee, pickup fee, fabric price
   - Double-check grand total

3. **Only send quotation once**
   - The system tracks `quotation_sent_at`
   - If pricing changes, communicate directly with customer

### For Developers

1. **Service option detection**
   - Always check `booking.service_option` field
   - Convert to lowercase for comparison
   - Valid values: `pickup`, `delivery`, `both`, `walk_in`

2. **Status validation**
   - Use allowed statuses array in AdminController
   - Update both frontend and backend validation

3. **Email sending**
   - All emails go through `NotificationService`
   - Always create in-app notification alongside email
   - Check email configuration in `config/email.php`

---

## Files Modified

### Core Files
- `core/NotificationService.php` - Added quotation email template and modified confirmation template

### Controllers
- `controllers/AdminController.php` - Added `sendQuotationAfterInspection()` method and updated allowed statuses

### Views
- `views/admin/all_bookings.php` - Added status dropdown options and "Send Final Quotation" button
- `views/customer/bookings.php` - Added new status labels with icons

### Database
- `database/update_pickup_workflow_statuses.sql` - Updated status ENUM
- `database/run_pickup_workflow_migration.php` - Migration script for statuses
- `database/add_quotation_sent_field.sql` - Added quotation_sent_at field
- `database/run_quotation_field_migration.php` - Migration script for field

---

## Troubleshooting

### Issue: "Send Final Quotation" button not appearing

**Solution:**
1. Verify booking has `service_option = 'pickup'`
2. Check status is one of: `picked_up`, `for_inspection`, `for_quotation`
3. Clear browser cache and reload

### Issue: Email not being sent

**Solution:**
1. Check `config/email.php` for correct SMTP settings
2. Verify customer has valid email address
3. Check error logs at `logs/notification.log`
4. Test email with `NotificationService::testEmailConfiguration()`

### Issue: Status update fails

**Solution:**
1. Run database migration: `php database/run_pickup_workflow_migration.php`
2. Verify ENUM values in database: `DESCRIBE bookings;`
3. Check AdminController allowed statuses array

---

## Summary

The PICKUP workflow implementation ensures that:

✅ Customers receive initial confirmation without pricing  
✅ Admin can inspect items before determining final cost  
✅ Accurate pricing based on physical inspection  
✅ Clear communication with customers at each stage  
✅ Proper status tracking throughout the workflow  
✅ Prevents pricing disputes and customer complaints  

This workflow is specifically designed for the upholstery industry where pricing accuracy depends on physical inspection and measurement.

---

**Implementation Date:** December 2, 2025  
**Version:** 1.0  
**Author:** UphoCare Development Team

