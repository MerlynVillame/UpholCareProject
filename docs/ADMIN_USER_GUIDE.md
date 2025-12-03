# UphoCare Admin User Guide

## 📋 Table of Contents

1. [Overview](#overview)
2. [Dashboard Overview](#dashboard-overview)
3. [Booking Management](#booking-management)
4. [Status Management](#status-management)
5. [PICKUP Workflow](#pickup-workflow)
6. [Payment Management](#payment-management)
7. [Customer Management](#customer-management)
8. [Inventory Management](#inventory-management)
9. [Reports & Analytics](#reports--analytics)
10. [Best Practices](#best-practices)

---

## Overview

The UphoCare Admin Panel is your central hub for managing all upholstery service bookings, customer relationships, inventory, and business operations.

### Admin Capabilities

As an admin, you can:

- ✅ View and manage all customer bookings
- ✅ Approve or update booking statuses
- ✅ Send quotations after item inspection (PICKUP workflow)
- ✅ Track payment status
- ✅ Manage customer information
- ✅ Control inventory (fabrics, colors, materials)
- ✅ Generate reports and analytics
- ✅ Send notifications to customers

### Access Requirements

- **Role:** Admin
- **Login:** Use your admin credentials at `/admin/login`
- **Permissions:** Full access to all booking and customer data

---

## Dashboard Overview

### Main Dashboard Sections

#### 1. **Statistics Cards**

- **Total Bookings:** Overall booking count
- **Active Bookings:** Bookings currently in progress
- **Completed Bookings:** Finished services
- **Pending Approvals:** Bookings awaiting your review

#### 2. **Quick Actions**

- View All Bookings
- Manage Inventory
- Customer List
- Generate Reports

#### 3. **Recent Activity**

- Latest customer bookings
- Recent status updates
- Payment notifications
- Customer inquiries

---

## Booking Management

### Accessing All Bookings

**Navigation:** Admin Dashboard → All Bookings

### Booking List Features

#### Table Columns

| Column             | Description                                                          |
| ------------------ | -------------------------------------------------------------------- |
| **Booking #**      | Queue number (e.g., Queue #0001)                                     |
| **Customer**       | Customer name and contact                                            |
| **Service**        | Type of upholstery service                                           |
| **Category**       | Service category                                                     |
| **Service Option** | Pickup, Delivery, Both, or Walk-in (Admin's main basis for approval) |
| **Status**         | Current booking status                                               |
| **Date**           | Booking creation date                                                |
| **Actions**        | Available actions for the booking                                    |

**Why Service Option is shown (not Amount/Payment):**

- ✅ Service Option is the admin's **primary basis for approval**
- ✅ Amount is not final until after inspection (especially for PICKUP)
- ✅ Payment status is always UNPAID initially
- ✅ Amount and Payment details are available in View Details

### Action Buttons (Optimized Workflow)

The action buttons are arranged in the **recommended order of workflow**:

#### 1. **👁️ View Details (Blue Button - REQUIRED FIRST STEP)**

- **Always view details BEFORE approving**
- Check the Service Option (Pickup/Delivery/Both/Walk-in)
- Review customer address and contact
- Check item description
- Verify pickup/delivery dates
- See color/material selected
- Review complete booking information

**Why this is first:** Admin must review service option before approval

#### 2. **✅ Approve (Green Button - Only for Pending)**

- Approve the booking based on service option
- Only appears for pending bookings
- Status changes based on service option:
  - **Pickup** → Status: "For Pickup"
  - **Delivery** → Status: "Approved"
  - **Both** → Status: "For Pickup"
  - **Walk-in** → Status: "Approved"
- Customer receives confirmation email

**Why this is second:** Approve only after reviewing details

#### 3. **✏️ Update Status (Blue Pencil Button - For All Statuses)**

- Change booking status as work progresses
- Update payment status
- Add admin notes
- Send notifications to customer
- Essential for workflow progression

**Why this is third:** Used throughout booking lifecycle

#### 4. **🗑️ Delete (Red Trash Button)**

- Remove duplicate or spam bookings
- Use sparingly - permanent deletion
- Confirm before deleting

**Why this is last:** Destructive action, rarely used

### Viewing Booking Details

Click the **View Details** button to see:

- **Customer Information**

  - Full name
  - Email address
  - Phone number
  - Address

- **Booking Information**

  - Service category and type
  - Service option (Pickup, Delivery, Both, Walk-in)
  - Item description
  - Pickup/delivery dates and addresses
  - Special notes

- **Pricing Details**

  - Labor fee
  - Pickup fee
  - Delivery fee
  - Fabric/color price
  - Total amount

- **Status History**
  - Current status
  - Status change log
  - Admin notes

---

## Status Management

### Understanding Booking Statuses

#### Initial Stage

- **Pending** 🟡
  - Customer has submitted the booking
  - Awaiting admin review
  - **Action:** Review and approve or update status

#### PICKUP Workflow (For items requiring inspection)

- **For Pickup** 🔵
  - Admin approved, waiting to collect item
  - Schedule pickup with customer
- **Picked Up** 🔵
  - Item collected from customer
  - Item is now at shop
- **For Inspection** 🔵
  - Item being physically inspected
  - Take measurements, assess damage
- **For Quotation** 🟡
  - Inspection complete
  - Preparing final pricing
  - **Action:** Update pricing fields and send quotation

#### Work in Progress

- **Approved** 🟢
  - Customer approved quotation
  - Ready to start repair work
- **In Queue** 🔵
  - Waiting to be processed
- **In Progress** 🔵
  - Work has started
  - Technicians working on item
- **Under Repair** 🔵
  - Active repair work ongoing
- **For Quality Check** 🔵
  - Final inspection before completion

#### Completion Stages

- **Ready for Pickup** 🟢
  - Item ready for customer to collect
- **Out for Delivery** 🟡
  - Item being delivered to customer
- **Completed** 🟢
  - Work finished successfully
- **Paid** 🟢
  - Full payment received
- **Closed** ⚫
  - Booking completed and archived

#### Other

- **Cancelled** ⚪
  - Booking cancelled by shop or customer

### How to Update Status

1. **Click "Update Status" button** on the booking
2. **Select new status** from dropdown
3. **Update payment status** (if applicable)
4. **Add admin notes** (optional but recommended)
5. **Check "Send notification"** to inform customer
6. **Click "Update Status"** to save

### Status Update Tips

✅ **Do:**

- Always add notes when changing status
- Notify customers of important changes
- Update payment status when payment is received
- Follow the logical status progression

❌ **Don't:**

- Skip important stages (e.g., inspection for PICKUP)
- Update status without checking actual progress
- Forget to notify customers of completion
- Change to "Paid" without receiving payment

---

## PICKUP Workflow

### Overview

The PICKUP workflow is specifically designed for upholstery services where **accurate pricing requires physical inspection** of the item.

### Why PICKUP Workflow is Different

**Traditional Workflow:**

- Customer books → Pricing determined online → Work starts

**PICKUP Workflow:**

- Customer books → Item picked up → **Item inspected** → **Final pricing determined** → Work starts

### Step-by-Step PICKUP Process

#### Step 1: Review New PICKUP Booking

**Status:** Pending

**Actions:**

1. Check booking details
2. Verify customer information
3. Review item description
4. Note pickup address and date

#### Step 2: Approve and Schedule Pickup

**Status:** Pending → For Pickup

**Actions:**

1. Click "Approve" button
2. Status changes to "For Pickup"
3. Contact customer to confirm pickup time
4. Assign driver/technician for pickup

#### Step 3: Collect Item from Customer

**Status:** For Pickup → Picked Up

**Actions:**

1. Send team to collect item
2. Update status to "Picked Up"
3. Transport item to shop
4. Log item arrival

#### Step 4: Inspect the Item

**Status:** Picked Up → For Inspection

**Actions:**

1. Update status to "For Inspection"
2. **Physically inspect the item:**
   - Measure all dimensions accurately
   - Check fabric condition
   - Assess internal damage (foam, springs, frame)
   - Document all findings
   - Take photos (recommended)
   - List required materials

**Inspection Checklist:**

- [ ] Length, width, height measurements
- [ ] Fabric type and condition
- [ ] Foam density and wear
- [ ] Spring condition (if applicable)
- [ ] Frame integrity
- [ ] Hidden damage inside
- [ ] Special requirements

#### Step 5: Prepare Quotation

**Status:** For Inspection → For Quotation

**Actions:**

1. Update status to "For Quotation"
2. Calculate accurate pricing:
   - **Labor Fee:** Based on work complexity
   - **Pickup Fee:** Transportation cost
   - **Delivery Fee:** Return delivery cost
   - **Fabric/Color Price:** Based on material selected
3. **Update pricing fields in system**
4. Verify all calculations
5. Double-check grand total

#### Step 6: Send Final Quotation to Customer

**Status:** For Quotation

**Actions:**

1. Click **"Send Final Quotation"** button (appears in booking details)
2. System sends Email #2 with complete pricing
3. Customer receives:
   - Inspection completion confirmation
   - Complete pricing breakdown
   - Total amount
   - Next steps
4. Wait for customer approval

#### Step 7: Customer Approval

**Status:** For Quotation → Approved

**Actions:**

1. Wait for customer to review quotation
2. Customer contacts you to approve
3. Update status to "Approved"
4. Begin work

#### Step 8: Complete the Work

**Status:** Approved → In Progress → Completed → Paid → Closed

**Follow normal workflow:**

- Work on the item
- Quality check
- Notify customer when ready
- Arrange delivery/pickup
- Collect payment
- Close booking

### PICKUP Workflow Best Practices

#### ✅ Do This:

- Always inspect items physically before pricing
- Take detailed notes during inspection
- Document unexpected damage with photos
- Update pricing fields before sending quotation
- Communicate findings clearly to customer
- Wait for customer approval before starting work

#### ❌ Avoid This:

- Never send quotation without inspection
- Don't guess measurements
- Don't skip the inspection stage
- Don't start work before customer approves price
- Don't send multiple quotations (confusing)

### PICKUP Workflow Emails

#### Email #1: Initial Confirmation (NO PRICING)

**Sent:** Automatically when customer creates booking

**Contains:**

- Queue number
- Booking confirmation
- Pickup date
- **Special note:** "Pricing will be provided after inspection"
- **NO pricing information**

#### Email #2: Final Quotation (WITH PRICING)

**Sent:** Manually by admin after inspection

**Contains:**

- Inspection completion confirmation
- Complete pricing breakdown
- Total amount
- Customer approval instructions

---

## Payment Management

### Payment Status Options

| Status                     | Description                                  | When to Use           |
| -------------------------- | -------------------------------------------- | --------------------- |
| **Unpaid**                 | No payment received                          | Default status        |
| **Paid (Full Cash)**       | Full payment received in cash before service | Customer paid upfront |
| **Paid on Delivery (COD)** | Payment received when item delivered         | COD payment           |
| **Cancelled**              | Payment cancelled                            | Booking cancelled     |

### Updating Payment Status

1. Open booking details
2. Click "Update Status"
3. Select appropriate payment status
4. Add payment notes (amount, method, date)
5. Click "Update Status"

### Payment Workflow

#### For PICKUP Service:

1. Customer books (Unpaid)
2. Item inspected, quotation sent
3. Customer approves
4. Work completed
5. **Customer pays → Update to "Paid (Full Cash)"**
6. Status: Paid → Closed

#### For Delivery Service:

1. Customer books (Unpaid)
2. Work completed
3. Item delivered to customer
4. **Customer pays on delivery → Update to "Paid on Delivery (COD)"**
5. Status: Paid → Closed

#### For Walk-in Service:

1. Customer brings item (Unpaid)
2. Work completed
3. **Customer pays full cash → Update to "Paid (Full Cash)"**
4. Status: Paid → Closed

### Payment Receipt

When you update status to "Paid":

- System automatically generates receipt
- Receipt email sent to customer
- Receipt stored in notifications
- Customer can download receipt

---

## Customer Management

### Viewing Customer Information

**Navigation:** Admin Dashboard → Customers / All Bookings → View Details

### Customer Details Available:

- Full name
- Email address
- Phone number
- Address
- Registration date
- Total bookings
- Booking history
- Payment history

### Customer Communication

#### Automatic Notifications:

- Booking confirmation
- Status updates (if enabled)
- Final quotation (PICKUP)
- Completion notification
- Payment receipt

#### Manual Notifications:

- Click "Send notification" when updating status
- Send preview email with notes
- Send quotation after inspection

---

## Inventory Management

### Managing Colors and Fabrics

**Navigation:** Admin Dashboard → Inventory

### Adding New Colors:

1. Click "Add New Color"
2. Enter color details:
   - Color name
   - Color code
   - Hex value (for display)
   - Type (Standard/Premium)
   - Price
   - Stock quantity
3. Upload color swatch image (optional)
4. Save

### Updating Inventory:

1. Find the color in inventory list
2. Click "Edit"
3. Update fields:
   - Price changes
   - Stock quantity
   - Availability status
4. Save changes

### Inventory Best Practices:

- Keep stock quantities updated
- Set low stock alerts
- Update prices regularly
- Remove discontinued items
- Add new arrivals promptly

---

## Reports & Analytics

### Available Reports

1. **Booking Summary**

   - Total bookings by period
   - Status distribution
   - Service type breakdown

2. **Revenue Reports**

   - Total revenue
   - Revenue by service type
   - Payment method distribution

3. **Customer Reports**

   - New customers
   - Repeat customers
   - Customer satisfaction

4. **Inventory Reports**
   - Stock levels
   - Popular colors
   - Material usage

### Generating Reports

1. Go to Reports section
2. Select report type
3. Choose date range
4. Apply filters (optional)
5. Click "Generate Report"
6. Export to PDF/Excel (optional)

---

## Best Practices

### Daily Admin Tasks

#### Morning:

- [ ] Check new bookings
- [ ] Review pending approvals
- [ ] Check scheduled pickups for today
- [ ] Respond to customer inquiries

#### During Day:

- [ ] Update booking statuses as work progresses
- [ ] Conduct inspections for PICKUP items
- [ ] Send quotations after inspections
- [ ] Process payments
- [ ] Communicate with customers

#### End of Day:

- [ ] Review completed bookings
- [ ] Update inventory
- [ ] Prepare for next day pickups/deliveries
- [ ] Check pending quotations

### Communication Best Practices

#### With Customers:

- ✅ Respond promptly to inquiries
- ✅ Be clear about pricing and timeline
- ✅ Set realistic expectations
- ✅ Keep customers informed of progress
- ✅ Confirm appointments

#### Internal Communication:

- ✅ Add detailed admin notes
- ✅ Document inspection findings
- ✅ Note customer preferences
- ✅ Log important conversations
- ✅ Track material requirements

### Quality Control

#### Before Sending Quotation:

- [ ] Item physically inspected
- [ ] All measurements taken
- [ ] Damage documented
- [ ] Materials calculated
- [ ] Pricing double-checked
- [ ] Total verified

#### Before Marking Completed:

- [ ] Work quality checked
- [ ] All repairs done properly
- [ ] Item cleaned
- [ ] Photos taken (before/after)
- [ ] Customer notified
- [ ] Delivery scheduled

### Data Management

#### Keep Records Of:

- Customer preferences
- Inspection findings
- Material usage
- Payment transactions
- Customer feedback
- Issues/complaints

#### Regular Maintenance:

- Archive old bookings (Closed status)
- Update inventory regularly
- Review customer data
- Back up important data
- Clean up old notifications

---

## Common Scenarios & Solutions

### Scenario 1: Customer Books PICKUP Service

**Workflow:**

1. Check booking → Approve → Schedule pickup
2. Collect item → Update to "Picked Up"
3. Inspect item → Update to "For Inspection"
4. Calculate pricing → Update to "For Quotation"
5. Send quotation → Wait for approval
6. Customer approves → Update to "Approved"
7. Complete work → Collect payment

### Scenario 2: Customer Wants to Change Service Option

**Solution:**

1. Go to booking details
2. Click "Update Status"
3. Modify service option if needed
4. Recalculate fees if applicable
5. Update customer
6. Save changes

### Scenario 3: Pricing Needs to be Adjusted

**For PICKUP (Before Quotation Sent):**

1. Update pricing fields in booking
2. Verify new total
3. Send quotation with correct pricing

**For PICKUP (After Quotation Sent):**

1. Contact customer directly
2. Explain reason for adjustment
3. Get customer approval
4. Update pricing in system
5. Send revised quotation (or confirmation)

### Scenario 4: Customer Cannot be Reached

**Actions:**

1. Try multiple contact methods (email, phone, SMS)
2. Add notes in booking
3. Wait 2-3 business days
4. Send follow-up email
5. If still no response, set status to "On Hold"
6. Document all contact attempts

### Scenario 5: Item More Damaged Than Expected

**Actions:**

1. Document additional damage with photos
2. Update pricing to reflect additional work
3. Contact customer immediately
4. Explain findings clearly
5. Get approval for new price
6. Update quotation in system
7. Proceed only after customer confirms

---

## Troubleshooting

### Issue: Cannot Update Booking Status

**Solutions:**

- Refresh the page
- Clear browser cache
- Check internet connection
- Try different browser
- Contact system administrator

### Issue: Quotation Email Not Sending

**Solutions:**

- Verify customer email address
- Check email configuration
- Review notification logs
- Resend from booking details
- Contact customer directly if urgent

### Issue: Pricing Calculation Incorrect

**Solutions:**

- Review all fee fields
- Check FeeCalculator settings
- Verify service option
- Recalculate manually
- Update fields and verify total

### Issue: Customer Reports Not Receiving Emails

**Solutions:**

- Verify email address in customer profile
- Check spam folder (ask customer)
- Review email logs
- Test email system
- Consider alternative contact method

---

## Security & Access

### Password Management

- Use strong passwords
- Change password regularly
- Never share admin credentials
- Log out when finished

### Data Protection

- Handle customer data responsibly
- Follow privacy regulations
- Secure sensitive information
- Don't share customer details publicly

### Session Management

- Auto-logout after inactivity
- Don't leave admin panel open unattended
- Use secure internet connections
- Avoid public computers

---

## Support & Help

### Getting Help

**For Technical Issues:**

- Check this documentation first
- Review error messages
- Check system logs
- Contact system administrator

**For Business Questions:**

- Consult management
- Review business policies
- Check standard procedures
- Refer to training materials

### Contact Information

**System Administrator:**

- Email: admin@uphocare.com
- Phone: [Your phone number]
- Available: [Business hours]

**Technical Support:**

- Email: support@uphocare.com
- Help Desk: [Help desk link]
- Response Time: 24-48 hours

---

## Keyboard Shortcuts

| Shortcut   | Action          |
| ---------- | --------------- |
| `Ctrl + F` | Search bookings |
| `Ctrl + N` | New booking     |
| `Ctrl + R` | Refresh page    |
| `Esc`      | Close modal     |

---

## Glossary

- **Booking:** Customer service request
- **Queue Number:** Auto-assigned booking identifier (e.g., Queue #0001)
- **Service Option:** How service will be provided (Pickup, Delivery, Both, Walk-in)
- **PICKUP Workflow:** Process where pricing is determined after item inspection
- **Quotation:** Final pricing sent to customer after inspection
- **COD:** Cash on Delivery payment method
- **Labor Fee:** Cost of repair/upholstery work
- **Pickup Fee:** Cost to collect item from customer
- **Delivery Fee:** Cost to return item to customer

---

## Version History

| Version | Date        | Changes                             |
| ------- | ----------- | ----------------------------------- |
| 1.0     | Dec 2, 2025 | Initial admin guide created         |
| 1.1     | Dec 2, 2025 | Added PICKUP workflow documentation |
| 1.2     | Dec 2, 2025 | Updated status management section   |

---

## Appendix

### Quick Reference: Status Codes

```
pending → for_pickup → picked_up → for_inspection →
for_quotation → approved → in_progress → completed →
paid → closed
```

### Quick Reference: Payment Flow

```
Unpaid → Work Completed → Payment Received →
Paid (Full Cash / COD) → Closed
```

---

**Document End**

For questions or suggestions about this documentation, please contact the system administrator.

**Last Updated:** December 2, 2025  
**Version:** 1.2  
**Maintained by:** UphoCare Development Team
