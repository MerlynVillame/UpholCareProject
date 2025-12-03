# Admin Quick Reference Guide

## 🚀 Quick Start

### Login
- URL: `/admin/login`
- Use your admin credentials
- Dashboard loads automatically

---

## 📊 Status Flow Chart

### Standard Workflow
```
Pending → Approved → In Progress → Completed → Paid → Closed
```

### PICKUP Workflow (Inspection Required)
```
Pending → For Pickup → Picked Up → For Inspection → 
For Quotation → Approved → In Progress → Completed → Paid → Closed
```

---

## 🔄 Status Quick Reference

| Status | Color | Meaning | Your Action |
|--------|-------|---------|-------------|
| **Pending** | 🟡 Yellow | New booking | Review & Approve |
| **For Pickup** | 🔵 Blue | Approved, need to collect | Schedule pickup |
| **Picked Up** | 🔵 Blue | Item at shop | Start inspection |
| **For Inspection** | 🔵 Blue | Inspecting | Measure & assess |
| **For Quotation** | 🟡 Yellow | Ready to price | Update pricing, send quotation |
| **Approved** | 🟢 Green | Customer approved | Start work |
| **In Progress** | 🔵 Blue | Working | Continue work |
| **Completed** | 🟢 Green | Work done | Arrange delivery/pickup |
| **Paid** | 🟢 Green | Payment received | Close booking |
| **Closed** | ⚫ Black | Finished | Archive |
| **Cancelled** | ⚪ Gray | Cancelled | Handle refund if needed |

---

## 🔧 Common Actions (Optimized Workflow Order)

### 1. View Details (FIRST - Required)
1. Click blue 👁️ button
2. **Check Service Option** (Pickup/Delivery/Both/Walk-in)
3. Review customer information
4. Verify addresses and dates
5. Check item description
6. Close when reviewed

**⚠️ IMPORTANT:** Always view details BEFORE approving!

### 2. Approve Booking (SECOND - Only for Pending)
1. After viewing details, click green ✅ button
2. Confirm approval
3. Status changes based on service option:
   - Pickup → "For Pickup"
   - Delivery → "Approved"
   - Both → "For Pickup"
   - Walk-in → "Approved"

### 3. Update Status (THIRD - Throughout Workflow)
1. Click blue ✏️ pencil button
2. Select new status
3. Update payment status (if applicable)
4. Add notes
5. Check "Notify customer"
6. Save

### 4. Delete Booking (LAST - Use Sparingly)
1. Click red 🗑️ trash button
2. Confirm deletion
3. Booking permanently removed

### Send Quotation (PICKUP only)
1. Open booking details
2. Verify pricing fields are updated
3. Click "Send Final Quotation"
4. Customer receives email

---

## 💰 Payment Status

| Status | When to Use |
|--------|-------------|
| **Unpaid** | Default, no payment yet |
| **Paid (Full Cash)** | Customer paid upfront |
| **Paid on Delivery (COD)** | Customer paid on delivery |
| **Cancelled** | Booking cancelled |

---

## 📧 Email Notifications

### Automatic Emails:
- ✅ Booking confirmation (when customer books)
- ✅ Queue number assignment
- ✅ Status updates (if enabled)
- ✅ Payment receipt

### Manual Emails:
- 📧 Final quotation (PICKUP - click button)
- 📧 Preview email (click "Send Preview")

---

## 🛠️ PICKUP Workflow Checklist

**When customer chooses PICKUP:**

- [ ] **Step 1:** Review booking → Approve → Status: "For Pickup"
- [ ] **Step 2:** Schedule & collect item → Status: "Picked Up"
- [ ] **Step 3:** Inspect item thoroughly
  - [ ] Measure dimensions
  - [ ] Check all damage
  - [ ] Document findings
  - [ ] Take photos
  - [ ] Status: "For Inspection"
- [ ] **Step 4:** Calculate pricing
  - [ ] Labor fee
  - [ ] Pickup fee  
  - [ ] Delivery fee
  - [ ] Fabric price
  - [ ] Status: "For Quotation"
- [ ] **Step 5:** Send quotation
  - [ ] Update pricing in system
  - [ ] Click "Send Final Quotation"
  - [ ] Wait for customer approval
- [ ] **Step 6:** Customer approves → Status: "Approved"
- [ ] **Step 7:** Complete work → Status: "Completed"
- [ ] **Step 8:** Collect payment → Status: "Paid"
- [ ] **Step 9:** Archive → Status: "Closed"

---

## 🎯 Daily Task Checklist

### Morning (9:00 AM)
- [ ] Check new bookings
- [ ] Review pending approvals  
- [ ] Check today's pickups
- [ ] Respond to urgent messages

### Midday (12:00 PM)
- [ ] Update booking statuses
- [ ] Process payments
- [ ] Send quotations (PICKUP)
- [ ] Check work progress

### Afternoon (3:00 PM)
- [ ] Conduct inspections
- [ ] Follow up with customers
- [ ] Update inventory
- [ ] Prepare deliveries

### End of Day (5:00 PM)
- [ ] Review completed bookings
- [ ] Update all statuses
- [ ] Plan tomorrow's schedule
- [ ] Back up important data

---

## ⚠️ Important Reminders

### PICKUP Workflow:
- ⚠️ **Never send quotation before inspection**
- ⚠️ **Always measure items physically**
- ⚠️ **Document all damage found**
- ⚠️ **Update pricing before sending quotation**
- ⚠️ **Wait for customer approval before starting work**

### General:
- ⚠️ Always add notes when updating status
- ⚠️ Double-check pricing calculations
- ⚠️ Verify customer contact information
- ⚠️ Keep customers informed
- ⚠️ Document everything

---

## 🔍 Troubleshooting

| Problem | Quick Fix |
|---------|-----------|
| Can't update status | Refresh page, clear cache |
| Email not sending | Check customer email, verify system logs |
| Wrong pricing | Review fee fields, recalculate |
| Button not working | Clear browser cache, try different browser |
| Customer not responding | Try phone, email, SMS; add notes |

---

## 📞 Quick Contacts

| Need | Contact |
|------|---------|
| **Technical Issue** | support@uphocare.com |
| **System Admin** | admin@uphocare.com |
| **Help Desk** | [Help desk link] |
| **Emergency** | [Emergency number] |

---

## 🔑 Keyboard Shortcuts

| Keys | Action |
|------|--------|
| `Ctrl + F` | Search |
| `Ctrl + R` | Refresh |
| `Esc` | Close modal |

---

## 📝 Quick Notes Template

```
Date: [Date]
Booking: [Queue #XXXX]
Customer: [Name]
Item: [Description]
Findings:
- [Finding 1]
- [Finding 2]
Action Taken: [Action]
Next Step: [Next step]
```

---

## 💡 Pro Tips

1. **Use Admin Notes:** Add detailed notes for every status change
2. **Take Photos:** Document item condition before and after
3. **Set Reminders:** For pickups, inspections, follow-ups
4. **Batch Updates:** Update multiple bookings at once when possible
5. **Check Twice:** Always verify pricing before sending quotations
6. **Communicate Early:** Keep customers informed proactively
7. **Stay Organized:** Use status flow properly
8. **Follow Up:** Don't let bookings sit too long in one status

---

## 🎓 Common Mistakes to Avoid

❌ Skipping inspection stage (PICKUP)  
❌ Sending quotation without updating pricing  
❌ Not notifying customers of status changes  
❌ Forgetting to update payment status  
❌ Not adding admin notes  
❌ Starting work before customer approval  
❌ Not documenting damage found  
❌ Closing bookings before payment received  

---

## ✅ Best Practices

✅ Review all new bookings within 24 hours  
✅ Inspect items thoroughly before pricing  
✅ Communicate findings clearly to customers  
✅ Update statuses as work progresses  
✅ Keep accurate records  
✅ Follow up on pending approvals  
✅ Maintain inventory regularly  
✅ Generate reports weekly  

---

## 📋 Inspection Checklist (PICKUP)

**Item Details:**
- [ ] Item type noted
- [ ] Dimensions measured (L x W x H)
- [ ] Photos taken (all angles)

**Condition Assessment:**
- [ ] Fabric type & condition
- [ ] Foam density & wear
- [ ] Spring condition
- [ ] Frame integrity
- [ ] Internal damage checked
- [ ] Special issues documented

**Requirements:**
- [ ] Materials needed listed
- [ ] Labor hours estimated
- [ ] Special tools/skills needed
- [ ] Timeline estimated

**Pricing:**
- [ ] Labor fee calculated
- [ ] Material cost calculated
- [ ] Pickup fee confirmed
- [ ] Delivery fee confirmed
- [ ] Total verified

---

## 🎯 Success Metrics

Track these to measure performance:

- **Response Time:** Approve bookings within 24 hours
- **Quotation Accuracy:** Minimize pricing revisions
- **Completion Time:** Meet estimated timelines
- **Customer Satisfaction:** Positive feedback rate
- **Payment Collection:** Collect on time
- **Status Updates:** Keep bookings moving

---

## 📱 Mobile Tips

If using mobile device:
- Rotate to landscape for better view
- Use responsive menu (☰)
- Swipe for more actions
- Long press for details
- Pinch to zoom tables

---

**Quick Reference v1.0**  
**Last Updated:** December 2, 2025  
**Print this page for daily use!**

