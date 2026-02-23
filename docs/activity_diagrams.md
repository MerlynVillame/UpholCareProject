# UphoCare — Activity Diagrams

**System:** UphoCare – Upholstery Repair & Restoration Management System  
**Database:** `db_upholcare`  
**Date:** February 19, 2026  
**Version:** 2.0 (Detailed / Schema-Aligned)

---

## Notation Guide

| Symbol | Meaning |
|---|---|
| ◉ | Start Node |
| ◎ | End Node |
| ◇ | Decision Point |
| → | Transition / Flow |
| [Condition] | Guard condition |

---

## ACTIVITY DIAGRAMS — CUSTOMER

---

### ACT-C01: Register Account

**Database Tables:** `users`

```
◉ START
→ Customer opens /register page
→ Customer fills form: username, email, fullname, phone, password
→ Customer clicks "Register"
◇ Is email already taken?
  [YES] → Show "Email already exists" error → Return to form
  [NO]  → Hash password (bcrypt)
         → INSERT INTO users (username, email, password, fullname, phone, role='customer', status='active')
         → Show success message
         → Redirect to /login
◎ END
```

---

### ACT-C02: Customer Login

**Database Tables:** `users`, `login_logs`

```
◉ START
→ Customer navigates to /login
→ Customer enters email and password
→ Customer clicks "Login"
◇ Does account exist (WHERE email=?)?
  [NO]  → INSERT INTO login_logs (login_status='failed', failure_reason='account_not_found')
         → Show "Invalid credentials"
         → Return to login form
  [YES] → Run password_verify() against hashed password
          ◇ Password correct?
            [NO]  → INSERT INTO login_logs (login_status='failed', failure_reason='wrong_password')
                   → Show "Invalid credentials"
                   → Return to login form
            [YES] → Check users.status
                    ◇ Is account active?
                      [NO]  → Show "Account is inactive"
                      [YES] → INSERT INTO login_logs (login_status='success')
                             → Set $_SESSION [user_id, role='customer']
                             → Redirect to /customer/dashboard
◎ END
```

---

### ACT-C03: Forgot Password

**Database Tables:** `users`

```
◉ START
→ Customer clicks "Forgot Password"
→ Customer enters their registered email
→ Customer clicks "Send Reset Link"
◇ Email exists in users table?
  [NO]  → Show "Email not found" message
  [YES] → Generate secure reset_token
         → SET users.reset_token = token, reset_token_expiry = NOW() + 1 HOUR
         → Send email with password reset link
         → Customer receives email and clicks link
         ◇ Is token still valid (not expired)?
           [NO]  → Show "Link has expired. Request again."
           [YES] → Customer enters new password
                  → Hash new password
                  → UPDATE users SET password=?, reset_token=NULL, reset_token_expiry=NULL
                  → Show "Password updated" message
                  → Redirect to /login
◎ END
```

---

### ACT-C04: Submit Service Booking

**Database Tables:** `bookings`, `services`, `store_locations`, `notifications`

```
◉ START
→ Customer navigates to Booking form
→ System fetches services: SELECT * FROM services WHERE status='active'
→ System fetches stores: SELECT * FROM store_locations WHERE status='active'
→ Customer selects: Service, Store, Booking Date
→ Customer selects service_option: 'pickup' | 'dropoff' | 'walk_in'
→ Customer enters: item_description, item_type, notes
◇ Is store capacity available for the selected date?
  (CHECK: services.daily_capacity vs. COUNT(bookings) for that day)
  [NO]  → Show "No slots available for this date" → Ask to choose another date
  [YES] → INSERT INTO bookings (
             user_id, service_id, store_location_id,
             booking_date, service_option, item_description,
             status='pending', payment_status='unpaid', booking_type
          )
         → INSERT INTO notifications (user_id=store_admin_id, title='New Booking Request')
         → Show Booking Confirmation with booking_id
◎ END
```

---

### ACT-C05: Track Booking Status

**Database Tables:** `bookings`

```
◉ START
→ Customer opens "My Bookings" page
→ System fetches: SELECT * FROM bookings WHERE user_id=? ORDER BY created_at DESC
→ System displays current status label for each booking
◇ What is the status?
  [pending]             → "Waiting for Admin Confirmation"
  [confirmed]           → "Booking Confirmed"
  [for_pickup]          → "Pickup Scheduled"
  [picked_up]           → "Item Picked Up - En Route to Shop"
  [inspect_completed]   → "Inspection Done - Awaiting Quotation"
  [for_quotation]       → "Quotation Sent - Awaiting Your Decision"
  [in_progress]         → "Repair In Progress"
  [repair_completed]    → "Repair Done - Quality Check"
  [ready_for_pickup]    → "Ready for Pickup"
  [out_for_delivery]    → "Out for Delivery"
  [completed]           → "Service Completed"
  [cancelled]           → "Cancelled"
→ Customer may click on a booking to view full details
◎ END
```

---

### ACT-C06: View Quotation & Decide

**Database Tables:** `bookings`, `notifications`

```
◉ START
→ Customer receives notification: "Quotation is ready"
→ Customer opens the quotation from their bookings
→ System loads quotation details:
     SELECT grand_total, labor_fee, travel_fee, gas_fee, color_price FROM bookings WHERE id=?
→ Customer reviews price breakdown
◇ Customer Decision?
  [ACCEPT] → POST /acceptQuotation (booking_id)
            → UPDATE bookings SET quotation_accepted=1, quotation_accepted_at=NOW(), status='approved'
            → INSERT INTO notifications (user_id=admin_id, title='Quotation Accepted — Begin Repair')
            → Show "Quotation Accepted" confirmation
  [REJECT] → POST /rejectQuotation (booking_id)
            → UPDATE bookings SET quotation_accepted=0, status='cancelled'
            → INSERT INTO notifications (user_id=admin_id, title='Quotation Rejected')
            → Show "Quotation Rejected" message
◎ END
```

---

### ACT-C07: Rate Store

**Database Tables:** `store_ratings`, `store_locations`

```
◉ START
→ Customer navigates to Store Ratings page
→ System fetches: SELECT * FROM store_locations WHERE status='active'
◇ Has customer already rated this store?
  (CHECK: SELECT id FROM store_ratings WHERE store_id=? AND user_id=?)
  [YES] → Load existing rating for editing
         → Customer updates rating (1.0–5.0) and review text
         → UPDATE store_ratings SET rating=?, review_text=?, updated_at=NOW()
  [NO]  → Customer submits new rating (1.0–5.0) and review text
         → INSERT INTO store_ratings (store_id, user_id, rating, review_text, status='active')
→ System recalculates store average:
     UPDATE store_locations SET rating = (SELECT AVG(rating) FROM store_ratings WHERE store_id=?)
◎ END
```

---

### ACT-C08: File Compliance Report

**Database Tables:** `store_compliance_reports`

```
◉ START
→ Customer selects a store to report
→ Customer selects report_type: 'safety' | 'hygiene' | 'quality' | 'service' | 'pricing' | 'other'
→ Customer selects specific issue_types (JSON array)
→ Customer writes description of the problem
→ Customer submits the report
→ INSERT INTO store_compliance_reports (store_id, customer_id, report_type, issue_types, description, status='pending')
→ System notifies Super Admin of new complaint
→ Customer sees "Report Submitted - Under Review" message
◎ END
```

---

## ACTIVITY DIAGRAMS — ADMIN (STORE MANAGER)

---

### ACT-A01: Self-Register as Store Admin

**Database Tables:** `admin_registrations`

```
◉ START
→ Admin opens /control-panel/register
→ Admin fills registration form:
     fullname, email, username, password, phone,
     business_name, business_address, business_city,
     business_province, business_latitude, business_longitude
→ Admin uploads Business Permit PDF
→ Admin submits form
◇ Is email already registered?
  [YES] → Show "Email already in use" error
  [NO]  → Save permit to: assets/uploads/business_permits/
         → INSERT INTO admin_registrations (
               registration_status='pending',
               business_permit_path, business_permit_filename, ...
            )
         → Show "Application submitted — Awaiting review from Super Admin"
◎ END
```

---

### ACT-A02: Email Verification (Post-Approval)

**Database Tables:** `admin_registrations`, `control_panel_admins`

```
◉ START
→ Admin receives email with 6-digit verification_code
→ Admin opens verification page and enters code
→ System fetches: SELECT verification_code, verification_code_sent_at FROM admin_registrations WHERE email=?
◇ Is code expired? (sent_at + 24 hours < NOW())
  [YES] → Show "Code expired — contact Super Admin for a new code"
◇ Does entered code match?
  [NO]  → UPDATE admin_registrations SET verification_attempts = verification_attempts + 1
         → Show "Invalid code"
  [YES] → UPDATE admin_registrations SET registration_status='approved', verification_code_verified_at=NOW()
         → INSERT INTO control_panel_admins (email, password, fullname, role='admin', status='active')
         → Show "Account Activated — You can now login"
         → Redirect to /admin/login
◎ END
```

---

### ACT-A03: Admin Full Repair Workflow

**Database Tables:** `bookings`, `notifications`, `payments`, `inventory`

```
◉ START

[PHASE 1: CONFIRM & SCHEDULE]
→ Admin views pending bookings: SELECT * FROM bookings WHERE status='pending'
→ Admin reviews customer request details
◇ Accept or Reject Booking?
  [REJECT] → UPDATE bookings SET status='rejected'
            → INSERT INTO notifications (user_id=customer_id, title='Booking Rejected')
            → STOP
  [ACCEPT] → UPDATE bookings SET status='confirmed'
            → INSERT INTO notifications (user_id=customer_id, title='Booking Confirmed')

[PHASE 2: LOGISTICS / PICKUP]
→ Admin sets: pickup_date, pickup_address, distance_km
→ Admin calculates: travel_fee, gas_fee
→ UPDATE bookings SET status='for_pickup', pickup_address=?, distance_km=?
→ Admin executes pickup
→ UPDATE bookings SET status='picked_up'

[PHASE 3: INSPECTION]
→ Admin opens inspection form for the booking
→ Admin fills: item_description (final), damage severity, notes
→ Admin uploads: preview photo/receipt image
→ UPDATE bookings SET status='inspect_completed', admin_notes=?

[PHASE 4: QUOTATION]
→ Admin calculates total:
     grand_total = base_service_price + labor_fee + travel_fee + gas_fee + color_price
→ Admin updates pricing fields:
     UPDATE bookings SET grand_total=?, labor_fee=?, quotation_sent=1,
                         quotation_sent_at=NOW(), status='for_quotation'
→ INSERT INTO notifications (customer_id, title='Your Quotation is Ready')

◇ Customer accepts quotation?
  [NO]  → UPDATE bookings SET status='cancelled'
          → INSERT INTO notifications (admin, title='Quotation Rejected by Customer')
          → STOP
  [YES] → UPDATE bookings SET quotation_accepted=1, quotation_accepted_at=NOW(), status='approved'

[PHASE 5: REPAIR EXECUTION]
→ UPDATE bookings SET status='in_queue'
→ UPDATE bookings SET status='in_progress', repair_start_date=NOW()
→ Admin manages inventory usage during repair:
     UPDATE inventory SET quantity = quantity - used_amount
     ◇ quantity <= threshold?
       [YES] → UPDATE inventory SET status='low-stock'
     ◇ quantity = 0?
       [YES] → UPDATE inventory SET status='out-of-stock'
→ UPDATE bookings SET status='under_repair'
→ UPDATE bookings SET status='repair_completed'
→ UPDATE bookings SET status='for_quality_check'

[PHASE 6: DELIVERY & PAYMENT]
◇ Delivery method?
  [DELIVERY] → UPDATE bookings SET status='repair_completed_ready_to_deliver', delivery_date=?, delivery_address=?
              → UPDATE bookings SET status='out_for_delivery'
  [PICKUP]   → UPDATE bookings SET status='ready_for_pickup'

→ Admin records payment:
     INSERT INTO payments (booking_id, amount, payment_method, payment_status='paid', payment_date=NOW())
→ UPDATE bookings SET payment_status='paid', status='completed', completion_date=NOW()

◎ END
```

---

### ACT-A04: Manage Inventory

**Database Tables:** `inventory`

```
◉ START
→ Admin opens Inventory page
→ System fetches: SELECT * FROM inventory WHERE store_location_id=?
→ Admin views current stock list

◇ Action?
  [ADD NEW ITEM]
  → Admin fills: color_code, color_name, color_hex, leather_type, quantity, price_per_meter, standard_price, premium_price
  → Check for duplicate: SELECT COUNT(*) FROM inventory WHERE color_code=? AND store_location_id=?
    ◇ Duplicate exists?
      [YES] → Show "This color code already exists for this store"
      [NO]  → INSERT INTO inventory (status='in-stock')

  [UPDATE QUANTITY]
  → Admin enters new quantity value
  → UPDATE inventory SET quantity=?, updated_at=NOW()
  → ◇ quantity > 5?  → SET status='in-stock'
       quantity 1–5? → SET status='low-stock'
       quantity = 0? → SET status='out-of-stock'

◎ END
```

---

### ACT-A05: Generate Reports

**Database Tables:** `bookings`, `payments`

```
◉ START
→ Admin opens Reports page
→ Admin selects filters:
     - Date range (start_date, end_date)
     - Status filter (e.g., 'completed')
     - Store (auto-filled if store-specific admin)
→ System executes:
     SELECT bookings.*, payments.amount, payments.payment_method
     FROM bookings LEFT JOIN payments ON payments.booking_id = bookings.id
     WHERE booking_date BETWEEN ? AND ? AND status=?
→ System renders summary table

◇ Export Format?
  [PDF]  → Render HTML report → Convert via Dompdf → Download .pdf
  [CSV]  → Serialize data → Output as .csv file
  [VIEW] → Render table in browser

◎ END
```

---

## ACTIVITY DIAGRAMS — SUPER ADMIN (CONTROL PANEL)

---

### ACT-S01: Super Admin Login (With Lockout Protection)

**Database Tables:** `control_panel_admins`, `login_logs`

```
◉ START
→ Super Admin opens /control-panel/login
→ Enters email and password
◇ Account exists?
  [NO]  → INSERT INTO login_logs (login_status='failed', failure_reason='account_not_found')
         → Show error
◇ Is account locked? (locked_until > NOW())
  [YES] → Show "Account locked. Try again later."
◇ Password correct?
  [NO]  → INSERT INTO login_logs (login_status='failed')
         → UPDATE control_panel_admins SET failed_login_attempts = failed_login_attempts + 1
         ◇ failed_login_attempts >= 5?
           [YES] → UPDATE control_panel_admins SET locked_until = NOW() + INTERVAL 1 HOUR
                  → Show "Account locked due to too many failed attempts"
  [YES] → INSERT INTO login_logs (login_status='success')
         → UPDATE control_panel_admins SET last_login=NOW(), failed_login_attempts=0
         → Create admin session
         → Redirect to /control-panel/dashboard
◎ END
```

---

### ACT-S02: Review & Approve Admin Registration

**Database Tables:** `admin_registrations`, `control_panel_admins`, `system_activities`

```
◉ START
→ Super Admin opens Admin Registrations page
→ System fetches: SELECT * FROM admin_registrations WHERE registration_status IN ('pending', 'pending_verification')
→ Super Admin clicks "View Details" on an application
→ System loads: fullname, email, business_name, business_address, business_permit_path
→ Super Admin clicks "Open Document" to view the uploaded PDF

◇ Decision?
  [APPROVE]
  → Super Admin clicks "Accept & Approve"
  → System generates 6-digit code
  → UPDATE admin_registrations SET
         registration_status='pending_verification',
         verification_code='XXXXXX',
         verification_code_sent_at=NOW(),
         approved_by=super_admin_id,
         approved_at=NOW()
  → Send verification code to admin's email
  → INSERT INTO system_activities (activity_type='user_created', affected_table='admin_registrations')
  → Show "Approval sent — code emailed to applicant"

  [REJECT]
  → Super Admin enters rejection_reason
  → UPDATE admin_registrations SET registration_status='rejected', rejection_reason=?
  → Send rejection email to applicant
  → INSERT INTO system_activities (activity_type='user_modified', affected_table='admin_registrations')
  → Show "Application rejected"

◎ END
```

---

### ACT-S03: Customer Account Management

**Database Tables:** `users`, `login_logs`, `system_activities`

```
◉ START
→ Super Admin opens Customer Accounts page
→ System fetches: SELECT * FROM users WHERE role='customer' ORDER BY created_at DESC
→ Super Admin searches or filters customer records

◇ Action?
  [VIEW LOGIN HISTORY]
  → SELECT * FROM login_logs WHERE user_type='customer' AND email=? ORDER BY login_time DESC
  → Display: IP address, user_agent, login_status, failure_reason, login_time

  [DEACTIVATE ACCOUNT]
  → UPDATE users SET status='inactive' WHERE id=?
  → INSERT INTO system_activities (affected_table='users', old_value='active', new_value='inactive')
  → Show "Account deactivated"

  [REACTIVATE ACCOUNT]
  → UPDATE users SET status='active' WHERE id=?
  → INSERT INTO system_activities (old_value='inactive', new_value='active')
  → Show "Account reactivated"

◎ END
```

---

### ACT-S04: Store Compliance Review & Ban

**Database Tables:** `store_compliance_reports`, `store_locations`, `system_activities`

```
◉ START
→ Super Admin opens Compliance Reports page
→ System fetches: SELECT * FROM store_compliance_reports WHERE status='pending'
→ Super Admin reviews report: report_type, issue_types, description, store info

◇ Decision?
  [RESOLVE — NO VIOLATION]
  → UPDATE store_compliance_reports SET status='resolved', admin_notes=?, reviewed_at=NOW()
  → Show "Report marked as resolved"

  [DISMISS REPORT]
  → UPDATE store_compliance_reports SET status='dismissed', admin_notes=?
  → Show "Report dismissed"

  [BAN STORE]
  → Super Admin sets ban_reason and ban duration
  → UPDATE store_locations SET status='inactive', banned_at=NOW(), banned_until=?
  → INSERT INTO system_activities (activity_type='user_modified', affected_table='store_locations')
  → UPDATE store_compliance_reports SET status='reviewed', reviewed_at=NOW(), reviewed_by=super_admin_id
  → Show "Store has been banned"

◎ END
```

---

### ACT-S05: System Monitoring & Audit

**Database Tables:** `login_logs`, `system_activities`, `system_statistics`

```
◉ START
→ Super Admin opens system monitoring panel

◇ View Section?
  [LOGIN LOGS]
  → Super Admin applies filters: user_type, login_status, date range, IP address
  → SELECT * FROM login_logs WHERE user_type=? AND login_status=? AND login_time BETWEEN ? AND ?
  → System renders: email, ip_address, user_agent, login_time, failure_reason

  [ACTIVITY AUDIT TRAIL]
  → SELECT * FROM system_activities ORDER BY created_at DESC
  → System renders: admin_id, activity_type, description, old_value, new_value, affected_table, ip_address

  [KPI DASHBOARD]
  → SELECT * FROM system_statistics WHERE stat_date = CURDATE()
  → System renders:
      total_logins, successful_logins, failed_logins,
      customer_logins, admin_logins,
      new_users, new_bookings, completed_bookings

◎ END
```

---

### ACT-S06: Store Ratings Moderation

**Database Tables:** `store_ratings`, `store_locations`

```
◉ START
→ Super Admin opens Store Ratings page
→ System fetches:
     SELECT sr.*, sl.store_name, u.fullname
     FROM store_ratings sr
     JOIN store_locations sl ON sr.store_id = sl.id
     JOIN users u ON sr.user_id = u.id
     ORDER BY sr.created_at DESC

◇ Action on a rating?
  [HIDE REVIEW]
  → UPDATE store_ratings SET status='hidden' WHERE id=?
  → Re-calculate store average (exclude hidden):
       UPDATE store_locations SET rating = (SELECT AVG(rating) FROM store_ratings WHERE store_id=? AND status='active')

  [RESTORE REVIEW]
  → UPDATE store_ratings SET status='active' WHERE id=?
  → Re-calculate store average

◎ END
```
