# PICKUP Workflow - Quick Setup Guide

## 📋 Overview

This guide will help you set up the PICKUP workflow for UphoCare, where pricing is sent **AFTER** items are picked up and inspected.

---

## 🚀 Quick Setup (3 Steps)

### Step 1: Run Database Migrations

Open your terminal/command prompt and navigate to your UphoCare project directory:

```bash
cd C:\xampp\htdocs\UphoCare
```

Run the following migrations in order:

#### 1.1 Update Status ENUM

```bash
php database/run_pickup_workflow_migration.php
```

**Expected output:**
```
Updating bookings table status ENUM for PICKUP workflow...

✓ Status ENUM updated successfully!

Current status column definition:
Type: enum('pending','for_pickup','picked_up','for_inspection','for_quotation','approved',...) 
Default: pending
```

#### 1.2 Add quotation_sent_at Field

```bash
php database/run_quotation_field_migration.php
```

**Expected output:**
```
Adding quotation_sent_at field to bookings table...

✓ Column 'quotation_sent_at' added successfully!

Bookings table columns:
  - quotation_sent_at (datetime)
  - preview_sent_at (datetime)
  - service_option (varchar(50))
  - status (enum(...))
```

### Step 2: Verify Setup

Log in to your database (phpMyAdmin or MySQL command line):

```sql
USE db_upholcare;

-- Check status column
DESCRIBE bookings;

-- Verify new statuses are available
SHOW COLUMNS FROM bookings LIKE 'status';
```

You should see the status column includes:
- `for_pickup`
- `picked_up`
- `for_inspection`
- `for_quotation`
- `in_progress`
- `paid`
- `closed`

### Step 3: Test the Workflow

1. **Clear browser cache** (Ctrl+Shift+Del)
2. **Login as Admin**
3. **Create or view a booking with service_option = 'pickup'**
4. **Change status** to any new status (e.g., `for_pickup`)
5. **Verify** the status updates successfully

---

## 🧪 Testing the Complete Workflow

### Test Scenario: Customer Books a Sofa Repair (PICKUP)

#### 1. Customer Side

1. Login as customer
2. Create new booking:
   - Service: Sofa Repair
   - Service Option: **PICKUP**
   - Item: 3-seater sofa with torn fabric
   - Pickup Address: 123 Main Street
   - Pickup Date: Tomorrow
3. Submit booking
4. **Check email** - Should receive Email #1 with:
   - ✅ Queue number
   - ✅ Booking confirmation
   - ✅ Note: "Pricing will be provided after inspection"
   - ❌ NO pricing details

#### 2. Admin Side - Pickup Stage

1. Login as admin
2. Go to **All Bookings**
3. Find the new booking
4. Click to view details
5. Change status to: **For Pickup**
6. Save changes
7. Item is scheduled for pickup

#### 3. Admin Side - Inspection Stage

1. After item is picked up, change status to: **Picked Up**
2. Inspect the item physically:
   - Measure: 200cm x 90cm x 80cm
   - Damage: Torn fabric on all cushions, foam worn
   - Materials: Need 5 meters of fabric, new foam inserts
3. Change status to: **For Inspection** (during inspection)
4. Change status to: **For Quotation** (after inspection)

#### 4. Admin Side - Send Final Quotation

1. Update pricing fields:
   - Labor Fee: ₱2,000
   - Pickup Fee: ₱500
   - Fabric Price: ₱3,500 (premium)
   - **Total: ₱6,000**
2. In booking details modal, click: **"Send Final Quotation"**
3. Confirm the action
4. **Check email** - Customer should receive Email #2 with:
   - ✅ "Inspection Completed" message
   - ✅ Complete pricing breakdown
   - ✅ Total amount: ₱6,000
   - ✅ Next steps for approval

#### 5. Customer Approval

1. Customer receives Email #2
2. Customer reviews quotation
3. Customer approves (via phone or email)
4. Admin changes status to: **Approved**

#### 6. Work Progress

1. Admin changes status to: **In Progress**
2. Technicians work on the sofa
3. Admin changes status to: **Completed**
4. Customer pays
5. Admin changes status to: **Paid**
6. Booking is archived: **Closed**

---

## 🔍 Verification Checklist

Use this checklist to verify everything is working:

- [ ] Database migrations ran successfully
- [ ] New statuses appear in admin status dropdown
- [ ] "Send Final Quotation" button appears for PICKUP bookings
- [ ] Email #1 (confirmation) does NOT show pricing for PICKUP
- [ ] Email #2 (quotation) DOES show complete pricing
- [ ] Status updates work correctly
- [ ] Customer receives both emails
- [ ] In-app notifications are created
- [ ] Status labels display correctly on customer dashboard

---

## 📊 Status Workflow Reference

### For PICKUP Service Option

```
pending 
   → for_pickup 
   → picked_up 
   → for_inspection 
   → for_quotation 
   ↓ [Admin sends Email #2 with pricing]
   → approved 
   → in_progress 
   → completed 
   → paid 
   → closed
```

### For Other Service Options (Delivery, Both, Walk-in)

```
pending 
   → approved 
   → in_progress 
   → completed 
   → paid 
   → closed
```

---

## ⚠️ Important Notes

### For PICKUP Workflow:

1. **Email #1 (Initial Confirmation):**
   - Sent automatically when booking is created
   - NO pricing included
   - Contains pickup schedule

2. **Email #2 (Final Quotation):**
   - Sent manually by admin AFTER inspection
   - Complete pricing breakdown
   - Only available for PICKUP service option

3. **When to send Email #2:**
   - After item is physically inspected
   - After measurements are taken
   - After damage is fully assessed
   - After pricing is finalized

### Admin Best Practices:

1. Always update pricing fields before sending quotation
2. Take photos during inspection (optional feature)
3. Document any hidden damage found
4. Communicate clearly with customer about findings
5. Only send quotation once pricing is final

---

## 🐛 Troubleshooting

### Problem: Migration fails with "ENUM value already exists"

**Solution:**
- This is normal if you already ran the migration
- The system will skip adding duplicate values
- No action needed

### Problem: "Send Final Quotation" button doesn't appear

**Checklist:**
1. Is the booking service_option = 'pickup'? 
2. Is the status one of: picked_up, for_inspection, for_quotation?
3. Did you clear browser cache?
4. Check browser console for JavaScript errors

**Fix:**
```javascript
// Open browser console (F12) and run:
console.log(booking.service_option);
console.log(booking.status);
```

### Problem: Status update fails with "Invalid status value"

**Solution:**
1. Check if migration ran successfully:
```bash
php database/run_pickup_workflow_migration.php
```

2. Verify in database:
```sql
DESCRIBE bookings;
```

3. Check AdminController allowed statuses array

### Problem: Email not sending

**Checklist:**
1. Check email configuration: `config/email.php`
2. Verify SMTP settings are correct
3. Check if customer has valid email address
4. Look at notification logs: `logs/notification.log`

**Test email:**
```php
// In admin panel, run email test
require_once 'core/NotificationService.php';
$notificationService = new NotificationService();
$result = $notificationService->testEmailConfiguration();
echo $result ? "Email working" : "Email failed";
```

---

## 📞 Support

If you encounter issues:

1. Check the full documentation: `docs/PICKUP_WORKFLOW_IMPLEMENTATION.md`
2. Review error logs in `logs/` folder
3. Check browser console for JavaScript errors
4. Verify database schema matches expected structure

---

## ✅ Setup Complete!

Once you've completed all steps and verified the checklist, your PICKUP workflow is ready to use!

**Key Features Now Available:**
- ✅ Proper pricing workflow for PICKUP service
- ✅ Inspection-based quotations
- ✅ Email #1: Confirmation without pricing
- ✅ Email #2: Final quotation after inspection
- ✅ Complete status tracking
- ✅ Admin control over pricing communication

**Next Steps:**
- Train your admin staff on the new workflow
- Inform customers about the inspection-based pricing
- Monitor the first few bookings closely
- Gather feedback and optimize as needed

---

**Setup Date:** December 2, 2025  
**Version:** 1.0  
**Estimated Setup Time:** 5-10 minutes

