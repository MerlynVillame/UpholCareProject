# UpholCare: Admin Action Workflow (Organized)

## Overview
This document describes the organized admin action buttons and workflow for handling bookings, especially those with the **PICKUP** service option.

## Workflow Status Flow

### 1️⃣ **STATUS: Pending**
**Meaning:** Customer submitted a booking request. No one has reviewed it yet.

**Admin Actions:**
- **View** (eye icon) - Review booking details
- **Approve** - Approve booking → Status changes to **For Pickup**

**System Action:**
- 📧 Email #1 sent to customer: "Your booking has been approved. Your item will be picked up as scheduled."
- Note: No pricing included yet (accurate pricing only after inspection)

---

### 2️⃣ **STATUS: For Pickup**
**Meaning:** Booking approved. The shop is waiting to collect the item from the customer.

**Admin Actions:**
- **View** - Review booking details
- **Mark as Picked Up** - Item collected → Status changes to **Picked Up / For Inspection**
- **Update Status** - Manual status update
- **Delete** - Remove booking

---

### 3️⃣ **STATUS: Picked Up / For Inspection**
**Meaning:** The item is now in the shop. Inspection is required before final pricing.

**Admin Actions:**
- **View** - Review booking details
- **Generate Quotation** - Opens booking details modal to:
  - Inspect item physically
  - Take exact measurements
  - Assess damage
  - Compute accurate quotation
  - Calculate total payment (bayronon)
- **Update Status** - Manual status update
- **Delete** - Remove booking

**After Inspection:**
- Click **"For Quotation / Awaiting Customer Approval"** → Status changes to **For Quotation**

---

### 4️⃣ **STATUS: For Quotation / Awaiting Customer Approval**
**Meaning:** Inspection is complete. Admin has given the TOTAL accurate price.

**Admin Actions:**
- **View** - Review booking details
- **Approve Quotation** - Customer accepted price → Status changes to **Approved / Ready for Repair**
- **Update Quotation** - Modify quotation details
- **Update Status** - Manual status update
- **Delete** - Remove booking

**System Action:**
- 📧 Email #2 (Final Quotation) sent to customer with:
  - Total price
  - Material list
  - Labor details
  - Project timeline

---

### 5️⃣ **STATUS: Approved / Ready for Repair**
**Meaning:** Customer approved the quotation and confirmed to proceed.

**Admin Actions:**
- **View** - Review booking details
- **Start Repair** - Begin work → Status changes to **In Progress**
- **Update Status** - Manual status update
- **Delete** - Remove booking

---

### 6️⃣ **STATUS: In Progress**
**Meaning:** The repair and reupholstery work is ongoing.

**Admin Actions:**
- **View** - Review booking details
- **Mark Completed** - Work finished → Status changes to **Completed**
- **Update Status** - Manual status update
- **Delete** - Remove booking

---

### 7️⃣ **STATUS: Completed**
**Meaning:** The repair is 100% done. The item is ready for release.

**Admin Actions:**
- **View** - Review booking details
- **Confirm Payment** - Payment received → Status changes to **Paid**
- **Update Status** - Manual status update
- **Delete** - Remove booking

---

### 8️⃣ **STATUS: Paid**
**Meaning:** Payment is fully settled.

**Admin Actions:**
- **View** - Review booking details
- **Close Booking** - Finalize booking → Status changes to **Closed**
- **Update Status** - Manual status update
- **Delete** - Remove booking

---

### 9️⃣ **STATUS: Closed**
**Meaning:** The booking is complete. No further actions are needed.

**Admin Actions:**
- **View** - Review booking details (read-only)
- **Update Status** - Manual status update (if needed)
- **Delete** - Remove booking (if needed)

---

## Button Organization by Status

| Status | Primary Action Buttons | Always Available |
|--------|----------------------|------------------|
| **Pending** | Approve | View, Update Status, Delete |
| **For Pickup** | Mark as Picked Up | View, Update Status, Delete |
| **Picked Up / For Inspection** | Generate Quotation | View, Update Status, Delete |
| **For Quotation** | Approve Quotation, Update Quotation | View, Update Status, Delete |
| **Approved** | Start Repair | View, Update Status, Delete |
| **In Progress** | Mark Completed | View, Update Status, Delete |
| **Completed** | Confirm Payment | View, Update Status, Delete |
| **Paid** | Close Booking | View, Update Status, Delete |
| **Closed** | (No primary actions) | View, Update Status, Delete |

## Key Features

### ✅ Always Available Buttons
- **View Details** - Required first step for all bookings
- **Update Status** - Manual status update for all bookings
- **Delete** - Remove booking (with confirmation)

### ✅ Status-Specific Buttons
- Buttons appear based on current booking status
- Workflow buttons guide admin through correct process
- Quick status updates with confirmation dialogs

### ✅ Workflow Enforcement
- **Pending → For Pickup**: Automatic when approving PICKUP bookings
- **For Pickup → Picked Up**: Manual when item is collected
- **Picked Up → For Quotation**: After inspection and calculation
- **For Quotation → Approved**: When customer accepts
- **Approved → In Progress**: When work starts
- **In Progress → Completed**: When work finishes
- **Completed → Paid**: When payment received
- **Paid → Closed**: When booking finalized

## Technical Implementation

### PHP Function: `getStatusActionButtons($booking)`
- Returns appropriate buttons based on booking status
- Handles all workflow statuses
- Includes always-available buttons (View, Update Status, Delete)

### JavaScript Functions:
- `handleQuickStatusUpdate(bookingId, newStatus, event)` - Quick status updates
- `handleConfirmPayment(bookingId, event)` - Confirm payment received
- `handleViewDetails(bookingId)` - View booking details
- `handleApprove(bookingId)` - Approve booking
- `handleUpdateStatus(bookingId, currentStatus)` - Manual status update
- `handleDelete(bookingId, event)` - Delete booking

### Backend:
- `acceptReservation()` - Sets status to "for_pickup" for PICKUP bookings
- `updateBookingStatus()` - Handles all status updates
- Email notifications sent at key workflow points

## Notes

- All buttons include loading states and error handling
- Confirmation dialogs prevent accidental actions
- Status changes trigger email notifications to customers
- Workflow is enforced through button availability
- Manual status updates always available via "Update Status" button

