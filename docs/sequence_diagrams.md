# UphoCare – Sequence Diagrams

**System:** UphoCare – Upholstery Repair & Restoration Management System  
**Database:** `db_upholcare`  
**Date:** February 19, 2026  
**Version:** 2.0 (Fully Detailed / Schema-Aligned)

---

## Notation Guide

| Symbol | Meaning |
|---|---|
| → | Message sent (request / call) |
| ← | Return message (response / data) |
| [ALT] | Alternative flow (conditional branch) |
| `table_name` | Database table referenced |

---

## SEQUENCE DIAGRAMS — CUSTOMER

---

### SEQ-C1: Register Account

**Participants:** Customer → Browser → AuthController → `users`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | Browser | Visits `/register` page | — |
| 2 | Browser | AuthController | GET /register | — |
| 3 | AuthController | Browser | ← Return registration form view | — |
| 4 | Customer | Browser | Fills: `username`, `email`, `fullname`, `phone`, `password` | — |
| 5 | Browser | AuthController | POST /register | — |
| 6 | AuthController | `users` | Check if email is unique | `SELECT id FROM users WHERE email=?` |
| 7 | `users` | AuthController | ← Return result (found / not found) | — |
| 8 | [ALT: Email taken] | | | |
| 8a | AuthController | Browser | ← Return error: "Email already registered" | — |
| 9 | [ALT: Email available] | | | |
| 9a | AuthController | AuthController | Hash password using `password_hash()` (bcrypt) | — |
| 9b | AuthController | `users` | Insert new customer record | `INSERT INTO users (username, email, password, fullname, phone, role='customer', status='active', created_at=NOW())` |
| 9c | `users` | AuthController | ← Return new `user_id` | — |
| 10 | AuthController | Browser | ← Redirect to /login with success message | — |

---

### SEQ-C2: Customer Login

**Participants:** Customer → AuthController → `users` → `login_logs`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | AuthController | POST /login (email, password) | — |
| 2 | AuthController | `users` | Find account by email | `SELECT id, email, password, status, role, failed_login_attempts, locked_until FROM users WHERE email=?` |
| 3 | `users` | AuthController | ← Return user record | — |
| 4 | [ALT: Account not found] | | | |
| 4a | AuthController | `login_logs` | Log failed attempt | `INSERT INTO login_logs (user_id=NULL, user_type='customer', email=?, login_status='failed', failure_reason='account_not_found', ip_address, user_agent, login_time=NOW())` |
| 4b | AuthController | Customer | ← Show "Invalid credentials" | — |
| 5 | [ALT: Account found — check lockout] | | | |
| 5a | AuthController | AuthController | Check `locked_until > NOW()` | — |
| 5b | [ALT: Account is locked] | | | |
| 5c | AuthController | Customer | ← Show "Account is temporarily locked" | — |
| 6 | [ALT: Account not locked — verify password] | | | |
| 6a | AuthController | AuthController | Run `password_verify($input, $hash)` | — |
| 7 | [ALT: Password incorrect] | | | |
| 7a | AuthController | `users` | Increment failed attempts | `UPDATE users SET failed_login_attempts = failed_login_attempts + 1` |
| 7b | AuthController | `login_logs` | Log failure | `INSERT INTO login_logs (login_status='failed', failure_reason='wrong_password', ip_address, login_time=NOW())` |
| 7c | AuthController | Customer | ← Show "Invalid credentials" | — |
| 8 | [ALT: Password correct] | | | |
| 8a | AuthController | `users` | Check account status | `SELECT status FROM users WHERE id=?` |
| 8b | [ALT: Status = inactive] | | | |
| 8c | AuthController | Customer | ← Show "Your account has been deactivated" | — |
| 9 | [ALT: Status = active] | | | |
| 9a | AuthController | `users` | Reset failed attempts, update last login | `UPDATE users SET failed_login_attempts=0, locked_until=NULL` |
| 9b | AuthController | `login_logs` | Log success | `INSERT INTO login_logs (user_id=?, user_type='customer', login_status='success', ip_address, user_agent, login_time=NOW())` |
| 9c | AuthController | AuthController | Create `$_SESSION [user_id, role='customer']` | — |
| 9d | AuthController | Customer | ← Redirect to /customer/dashboard | — |

---

### SEQ-C3: Forgot Password

**Participants:** Customer → AuthController → `users` → Email Service

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | AuthController | POST /forgot-password (email) | — |
| 2 | AuthController | `users` | Verify email exists | `SELECT id FROM users WHERE email=? AND role='customer'` |
| 3 | [ALT: Email not found] | | | |
| 3a | AuthController | Customer | ← Show "If email exists, a link will be sent" (security) | — |
| 4 | [ALT: Email found] | | | |
| 4a | AuthController | AuthController | Generate secure random `reset_token` (SHA256) | — |
| 4b | AuthController | `users` | Save token | `UPDATE users SET reset_token=?, reset_token_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR)` |
| 4c | AuthController | Email Service | Send password reset email with link | — |
| 5 | Customer | AuthController | Clicks link, opens /reset-password?token=? | — |
| 6 | AuthController | `users` | Validate token | `SELECT id FROM users WHERE reset_token=? AND reset_token_expiry > NOW()` |
| 7 | [ALT: Token invalid/expired] | | | |
| 7a | AuthController | Customer | ← Show "Link has expired. Request again." | — |
| 8 | [ALT: Token valid] | | | |
| 8a | Customer | AuthController | POST new password | — |
| 8b | AuthController | AuthController | Hash new password (bcrypt) | — |
| 8c | AuthController | `users` | Save new password, clear token | `UPDATE users SET password=?, reset_token=NULL, reset_token_expiry=NULL` |
| 8d | AuthController | Customer | ← Redirect to /login with "Password updated" | — |

---

### SEQ-C4: Submit Service Booking

**Participants:** Customer → CustomerController → `services` → `store_locations` → `bookings` → `notifications`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | CustomerController | GET /customer/book — Load booking form | — |
| 2 | CustomerController | `services` | Fetch active services | `SELECT id, service_name, service_type, price, requires_fabric FROM services WHERE status='active'` |
| 3 | CustomerController | `store_locations` | Fetch active store branches | `SELECT id, store_name, address, city, rating FROM store_locations WHERE status='active'` |
| 4 | CustomerController | Customer | ← Return form with services and store options | — |
| 5 | Customer | CustomerController | POST /customer/book (service_id, store_location_id, booking_date, service_option, item_description, item_type, notes) | — |
| 6 | CustomerController | `services` | Check daily capacity | `SELECT daily_capacity FROM services WHERE id=?` |
| 7 | CustomerController | `bookings` | Count existing bookings for that date | `SELECT COUNT(*) FROM bookings WHERE service_id=? AND store_location_id=? AND booking_date=? AND status NOT IN ('cancelled','rejected')` |
| 8 | [ALT: Capacity exceeded] | | | |
| 8a | CustomerController | Customer | ← Show "No available slots for that date" | — |
| 9 | [ALT: Capacity available] | | | |
| 9a | CustomerController | `bookings` | Insert booking record | `INSERT INTO bookings (user_id, customer_id, service_id, store_location_id, booking_date, service_option, item_description, item_type, notes, status='pending', payment_status='unpaid', booking_type='personal', created_at=NOW())` |
| 9b | `bookings` | CustomerController | ← Return new `booking_id` | — |
| 9c | CustomerController | `notifications` | Notify Store Admin | `INSERT INTO notifications (user_id=admin_user_id, title='New Booking Request', message='Customer has submitted a new booking.', type='info', is_read=0, created_at=NOW())` |
| 10 | CustomerController | Customer | ← Show booking confirmation with `booking_id` | — |

---

### SEQ-C5: Track & View Booking Status

**Participants:** Customer → CustomerController → `bookings`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | CustomerController | GET /customer/bookings | — |
| 2 | CustomerController | `bookings` | Fetch all customer bookings | `SELECT b.*, sl.store_name, s.service_name FROM bookings b JOIN store_locations sl ON b.store_location_id=sl.id JOIN services s ON b.service_id=s.id WHERE b.user_id=? AND b.is_archived=0 ORDER BY b.created_at DESC` |
| 3 | `bookings` | CustomerController | ← Return booking list | — |
| 4 | CustomerController | Customer | ← Render booking list with status badges | — |
| 5 | Customer | CustomerController | Click on specific booking to view details | — |
| 6 | CustomerController | `bookings` | Fetch single booking details | `SELECT * FROM bookings WHERE id=? AND user_id=?` |
| 7 | CustomerController | Customer | ← Return full booking details: status, total, quotation, admin notes | — |

---

### SEQ-C6: View Quotation & Accept / Reject

**Participants:** Customer → CustomerController → `bookings` → `notifications`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | CustomerController | GET /customer/booking/{id}/quotation | — |
| 2 | CustomerController | `bookings` | Load quotation data | `SELECT grand_total, labor_fee, travel_fee, gas_fee, color_price, delivery_fee, pickup_fee, total_additional_fees, quotation_sent_at FROM bookings WHERE id=? AND user_id=?` |
| 3 | `bookings` | CustomerController | ← Return pricing breakdown | — |
| 4 | CustomerController | Customer | ← Display quotation with itemized costs | — |
| 5 | [ALT: Accept] | | | |
| 5a | Customer | CustomerController | POST /customer/booking/{id}/accept | — |
| 5b | CustomerController | `bookings` | Accept quotation | `UPDATE bookings SET quotation_accepted=1, quotation_accepted_at=NOW(), status='approved' WHERE id=?` |
| 5c | CustomerController | `notifications` | Notify Admin to begin repair | `INSERT INTO notifications (user_id=admin_id, title='Quotation Accepted — Begin Repair', type='success')` |
| 5d | CustomerController | Customer | ← Show "Quotation Accepted. Repair will begin shortly." | — |
| 6 | [ALT: Reject] | | | |
| 6a | Customer | CustomerController | POST /customer/booking/{id}/reject | — |
| 6b | CustomerController | `bookings` | Reject quotation | `UPDATE bookings SET quotation_accepted=0, status='cancelled' WHERE id=?` |
| 6c | CustomerController | `notifications` | Notify Admin of rejection | `INSERT INTO notifications (user_id=admin_id, title='Quotation Rejected by Customer', type='warning')` |
| 6d | CustomerController | Customer | ← Show "Quotation Rejected." | — |

---

### SEQ-C7: Rate Store

**Participants:** Customer → CustomerController → `store_ratings` → `store_locations`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | CustomerController | GET /customer/ratings — Load rating page | `SELECT id, store_name, rating FROM store_locations WHERE status='active'` |
| 2 | Customer | CustomerController | Submit rating form (store_id, rating, review_text) | — |
| 3 | CustomerController | `store_ratings` | Check for existing rating | `SELECT id FROM store_ratings WHERE store_id=? AND user_id=?` |
| 4 | [ALT: First time rating] | | | |
| 4a | CustomerController | `store_ratings` | Insert new rating | `INSERT INTO store_ratings (store_id, user_id, rating, review_text, status='active', created_at=NOW())` |
| 5 | [ALT: Already rated — update] | | | |
| 5a | CustomerController | `store_ratings` | Update existing rating | `UPDATE store_ratings SET rating=?, review_text=?, updated_at=NOW() WHERE store_id=? AND user_id=?` |
| 6 | CustomerController | `store_locations` | Recalculate store average | `UPDATE store_locations SET rating=(SELECT AVG(rating) FROM store_ratings WHERE store_id=? AND status='active') WHERE id=?` |
| 7 | CustomerController | Customer | ← Show "Thank you for your rating!" | — |

---

### SEQ-C8: File Compliance Report

**Participants:** Customer → CustomerController → `store_compliance_reports`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | CustomerController | GET /customer/report/{store_id} — Load report form | `SELECT id, store_name FROM store_locations WHERE id=?` |
| 2 | Customer | CustomerController | POST /customer/report (store_id, report_type, issue_types, description) | — |
| 3 | CustomerController | `store_compliance_reports` | Insert report | `INSERT INTO store_compliance_reports (store_id, customer_id, report_type, issue_types=JSON, description, status='pending', created_at=NOW())` |
| 4 | `store_compliance_reports` | CustomerController | ← Return new report ID | — |
| 5 | CustomerController | Customer | ← Show "Your report has been submitted. A Super Admin will review it." | — |

---

### SEQ-C9: Register Customer Business

**Participants:** Customer → CustomerController → `customer_businesses` → `business_types`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Customer | CustomerController | GET /customer/business/register — Load form | `SELECT id, type_name FROM business_types` |
| 2 | Customer | CustomerController | POST (business_name, business_type_id, business_address, permit_file) | — |
| 3 | CustomerController | CustomerController | Save uploaded permit PDF to server storage | — |
| 4 | CustomerController | `customer_businesses` | Insert business application | `INSERT INTO customer_businesses (user_id, business_name, business_type_id, business_address, permit_file=path, status='pending', created_at=NOW())` |
| 5 | CustomerController | Customer | ← Show "Business registration submitted. Awaiting Super Admin review." | — |

---

## SEQUENCE DIAGRAMS — ADMIN (STORE MANAGER)

---

### SEQ-A1: Self-Register as Store Admin

**Participants:** Admin → RegistrationController → `admin_registrations`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | RegistrationController | GET /control-panel/register — Load form | — |
| 2 | Admin | RegistrationController | POST /control-panel/register (fullname, email, username, password, phone, business details, permit PDF) | — |
| 3 | RegistrationController | `admin_registrations` | Check for duplicate email | `SELECT id FROM admin_registrations WHERE email=?` |
| 4 | [ALT: Duplicate found] | | | |
| 4a | RegistrationController | Admin | ← Show "Email already registered" | — |
| 5 | [ALT: No duplicate] | | | |
| 5a | RegistrationController | RegistrationController | Hash password (bcrypt) | — |
| 5b | RegistrationController | RegistrationController | Save business permit PDF to `assets/uploads/business_permits/` | — |
| 5c | RegistrationController | `admin_registrations` | Insert new application | `INSERT INTO admin_registrations (fullname, email, username, password, phone, business_name, business_address, business_city, business_province, business_latitude, business_longitude, business_permit_path, business_permit_filename, registration_status='pending', created_at=NOW())` |
| 5d | `admin_registrations` | RegistrationController | ← Return new registration ID | — |
| 6 | RegistrationController | Admin | ← Show "Application submitted. Awaiting Super Admin review." | — |

---

### SEQ-A2: Email Verification (Post-Approval)

**Participants:** Admin → ControlPanelController → `admin_registrations` → `admin_verification_codes` → `control_panel_admins` → `store_locations`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | ControlPanelController | GET /control-panel/verify — Load verification form | — |
| 2 | Admin | ControlPanelController | POST /control-panel/verify (email, verification_code) | — |
| 3 | ControlPanelController | `admin_registrations` | Find pending_verification record | `SELECT id, verification_code, verification_code_sent_at, verification_attempts FROM admin_registrations WHERE email=? AND registration_status='pending_verification'` |
| 4 | [ALT: No record found] | | | |
| 4a | ControlPanelController | Admin | ← Show "No pending verification found for this email" | — |
| 5 | [ALT: Record found] | | | |
| 5a | ControlPanelController | `admin_verification_codes` | Verify code is valid & not expired | `SELECT id, status, expires_at FROM admin_verification_codes WHERE verification_code=? AND assigned_to_email=? AND status IN ('reserved','available')` |
| 6 | [ALT: Code expired or invalid] | | | |
| 6a | ControlPanelController | `admin_registrations` | Increment verification attempts | `UPDATE admin_registrations SET verification_attempts = verification_attempts + 1` |
| 6b | ControlPanelController | Admin | ← Show "Invalid or expired code. Attempts logged." | — |
| 7 | [ALT: Code valid] | | | |
| 7a | ControlPanelController | `admin_verification_codes` | Mark code as used | `UPDATE admin_verification_codes SET status='used', updated_at=NOW() WHERE id=?` |
| 7b | ControlPanelController | `admin_registrations` | Mark as verified | `UPDATE admin_registrations SET registration_status='approved', verification_code_verified_at=NOW(), verified_at=NOW()` |
| 7c | ControlPanelController | `control_panel_admins` | Create active admin account | `INSERT INTO control_panel_admins (email, password, fullname, role='admin', status='active', created_at=NOW())` |
| 7d | `control_panel_admins` | ControlPanelController | ← Return new admin ID | — |
| 7e | ControlPanelController | `store_locations` | Create associated store branch | `INSERT INTO store_locations (store_name=business_name, address, city, province, latitude, longitude, phone, email, status='active', admin_id=new_admin_id)` |
| 8 | ControlPanelController | Admin | ← "Account activated! You may now login." → Redirect to /admin/login | — |

---

### SEQ-A3: Admin Login

**Participants:** Admin → AdminController → `control_panel_admins` → `login_logs`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | POST /admin/login (email, password) | — |
| 2 | AdminController | `control_panel_admins` | Find admin by email | `SELECT id, email, password, fullname, role, status, failed_login_attempts, locked_until, last_login FROM control_panel_admins WHERE email=?` |
| 3 | [ALT: Not found] | | | |
| 3a | AdminController | `login_logs` | Log failure | `INSERT INTO login_logs (user_type='admin', email=?, login_status='failed', failure_reason='account_not_found', ip_address, login_time=NOW())` |
| 3b | AdminController | Admin | ← Show "Invalid credentials" | — |
| 4 | [ALT: Found — check lockout] | | | |
| 4a | AdminController | AdminController | Check `locked_until > NOW()` | — |
| 4b | [ALT: Locked] | | | |
| 4c | AdminController | Admin | ← Show "Account is temporarily locked" | — |
| 5 | [ALT: Not locked — verify password] | | | |
| 5a | AdminController | AdminController | Run `password_verify()` | — |
| 6 | [ALT: Wrong password] | | | |
| 6a | AdminController | `control_panel_admins` | Increment failed attempts | `UPDATE control_panel_admins SET failed_login_attempts = failed_login_attempts + 1 WHERE id=?` |
| 6b | AdminController | `login_logs` | Log failure | `INSERT INTO login_logs (login_status='failed', failure_reason='wrong_password', user_type='admin')` |
| 6c | AdminController | Admin | ← Show "Invalid credentials" | — |
| 7 | [ALT: Password correct] | | | |
| 7a | AdminController | `control_panel_admins` | Reset attempts, update last login | `UPDATE control_panel_admins SET failed_login_attempts=0, locked_until=NULL, last_login=NOW() WHERE id=?` |
| 7b | AdminController | `login_logs` | Log success | `INSERT INTO login_logs (user_id=admin_id, user_type='admin', login_status='success', ip_address, login_time=NOW())` |
| 7c | AdminController | AdminController | Create `$_SESSION [admin_id, role='admin']` | — |
| 7d | AdminController | Admin | ← Redirect to /admin/dashboard | — |

---

### SEQ-A4: Confirm Booking & Schedule Logistics

**Participants:** Admin → AdminController → `bookings` → `notifications`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | GET /admin/bookings — Load booking list | `SELECT b.*, u.fullname, u.phone, s.service_name FROM bookings b JOIN users u ON b.user_id=u.id JOIN services s ON b.service_id=s.id WHERE b.store_location_id=? AND b.status='pending'` |
| 2 | AdminController | Admin | ← Return pending bookings list | — |
| 3 | Admin | AdminController | Click "View" on a booking | `SELECT * FROM bookings WHERE id=?` |
| 4 | Admin | AdminController | POST /admin/booking/{id}/confirm | — |
| 5 | AdminController | `bookings` | Assign booking number and confirm | `UPDATE bookings SET customer_booking_number_id=?, status='confirmed', updated_at=NOW() WHERE id=?` |
| 6 | AdminController | `notifications` | Notify customer | `INSERT INTO notifications (user_id=customer_id, title='Booking Confirmed', message='Your booking has been confirmed.', type='success')` |
| 7 | Admin | AdminController | POST /admin/booking/{id}/set-pickup (pickup_date, pickup_address, distance_km, travel_fee, gas_fee) | — |
| 8 | AdminController | `bookings` | Set logistics details | `UPDATE bookings SET status='for_pickup', pickup_date=?, pickup_address=?, distance_km=?, travel_fee=?, gas_fee=?, updated_at=NOW()` |
| 9 | Admin | AdminController | POST /admin/booking/{id}/mark-picked-up | — |
| 10 | AdminController | `bookings` | Mark as picked up | `UPDATE bookings SET status='picked_up', updated_at=NOW()` |
| 11 | AdminController | `notifications` | Notify customer | `INSERT INTO notifications (user_id=customer_id, title='Item Picked Up', type='info')` |

---

### SEQ-A5: Conduct Inspection

**Participants:** Admin → AdminController → `bookings`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | POST /admin/booking/{id}/inspect (item_description, damage_severity, admin_notes) | — |
| 2 | AdminController | `bookings` | Save inspection results | `UPDATE bookings SET item_description=?, admin_notes=?, status='inspect_completed', updated_at=NOW() WHERE id=?` |
| 3 | `bookings` | AdminController | ← Return updated record | — |
| 4 | AdminController | Admin | ← Show "Inspection saved. Proceed to Quotation." | — |

---

### SEQ-A6: Create & Send Quotation

**Participants:** Admin → AdminController → `bookings` → `notifications`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | GET /admin/booking/{id}/quotation — Load quotation form | `SELECT b.*, s.price FROM bookings b JOIN services s ON b.service_id=s.id WHERE b.id=?` |
| 2 | AdminController | Admin | ← Return booking details with base price | — |
| 3 | Admin | AdminController | POST /admin/booking/{id}/quotation (labor_fee, color_price, delivery_fee, pickup_fee, total_additional_fees) | — |
| 4 | AdminController | AdminController | Compute `grand_total = labor_fee + color_price + travel_fee + gas_fee + delivery_fee + pickup_fee` | — |
| 5 | AdminController | `bookings` | Save quotation and mark as sent | `UPDATE bookings SET labor_fee=?, color_price=?, delivery_fee=?, pickup_fee=?, total_additional_fees=?, grand_total=?, quotation_sent=1, quotation_sent_at=NOW(), status='for_quotation', updated_at=NOW() WHERE id=?` |
| 6 | AdminController | `notifications` | Notify customer | `INSERT INTO notifications (user_id=customer_id, title='Quotation Ready for Review', message='Please review and accept or reject your quotation.', type='info')` |
| 7 | AdminController | Admin | ← Show "Quotation sent successfully." | — |

---

### SEQ-A7: Execute Full Repair Lifecycle

**Participants:** Admin → AdminController → `bookings` → `notifications`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | AdminController | AdminController | Detect: `quotation_accepted=1` — Customer accepted | — |
| 2 | Admin | AdminController | Queue the repair | `UPDATE bookings SET status='in_queue', updated_at=NOW()` |
| 3 | Admin | AdminController | Begin repair | `UPDATE bookings SET status='in_progress', updated_at=NOW()` |
| 4 | Admin | AdminController | Active work on item | `UPDATE bookings SET status='under_repair', updated_at=NOW()` |
| 5 | Admin | AdminController | Mark repair done | `UPDATE bookings SET status='repair_completed', updated_at=NOW()` |
| 6 | Admin | AdminController | Conduct quality check | `UPDATE bookings SET status='for_quality_check', updated_at=NOW()` |
| 7 | [ALT: Delivery] | | | |
| 7a | Admin | AdminController | Schedule delivery | `UPDATE bookings SET status='repair_completed_ready_to_deliver', delivery_date=?, delivery_address=?, updated_at=NOW()` |
| 7b | Admin | AdminController | Mark out for delivery | `UPDATE bookings SET status='out_for_delivery', updated_at=NOW()` |
| 8 | [ALT: Pickup] | | | |
| 8a | Admin | AdminController | Mark ready for customer pickup | `UPDATE bookings SET status='ready_for_pickup', updated_at=NOW()` |
| 9 | AdminController | `notifications` | Notify customer: item is ready | `INSERT INTO notifications (user_id=customer_id, title='Your item is ready!', type='success')` |

---

### SEQ-A8: Record Payment & Close Booking

**Participants:** Admin → AdminController → `payments` → `bookings`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | POST /admin/booking/{id}/payment (amount, payment_method) | — |
| 2 | AdminController | `payments` | Insert payment record | `INSERT INTO payments (booking_id, amount, payment_method, payment_status='paid', payment_date=NOW(), created_at=NOW())` |
| 3 | `payments` | AdminController | ← Return payment record ID | — |
| 4 | AdminController | `bookings` | Update booking as paid and completed | `UPDATE bookings SET payment_status='paid_full_cash' OR 'paid_on_delivery_cod', status='completed', completion_date=NOW(), updated_at=NOW() WHERE id=?` |
| 5 | Admin | AdminController | POST /admin/booking/{id}/close | — |
| 6 | AdminController | `bookings` | Close booking | `UPDATE bookings SET status='closed', updated_at=NOW() WHERE id=?` |
| 7 | AdminController | Admin | ← Show "Booking closed and archived." | — |

---

### SEQ-A9: Manage Inventory

**Participants:** Admin → AdminController → `inventory`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | GET /admin/inventory — Load inventory | `SELECT i.*, sl.store_name FROM inventory i JOIN store_locations sl ON i.store_location_id=sl.id WHERE i.store_location_id=?` |
| 2 | AdminController | Admin | ← Return fabric list with color, quantity, status | — |
| 3 | [ALT: Add new item] | | | |
| 3a | Admin | AdminController | POST /admin/inventory/add (color_code, color_name, color_hex, leather_type, quantity, price fields) | — |
| 3b | AdminController | `inventory` | Check unique constraint | `SELECT id FROM inventory WHERE color_code=? AND store_location_id=?` |
| 3c | [ALT: Duplicate] | | | |
| 3d | AdminController | Admin | ← Show "This color code already exists for this store." | — |
| 3e | [ALT: Not duplicate] | | | |
| 3f | AdminController | `inventory` | Insert new material | `INSERT INTO inventory (color_code, color_name, color_hex, leather_type, quantity, standard_price, premium_price, price_per_meter, store_location_id, status='in-stock', created_at=NOW())` |
| 4 | [ALT: Update quantity] | | | |
| 4a | Admin | AdminController | POST /admin/inventory/{id}/update (quantity) | — |
| 4b | AdminController | AdminController | Determine status: qty>5 → in-stock, qty 1-5 → low-stock, qty=0 → out-of-stock | — |
| 4c | AdminController | `inventory` | Update stock | `UPDATE inventory SET quantity=?, status=?, updated_at=NOW() WHERE id=?` |

---

### SEQ-A10: Generate & Export Report

**Participants:** Admin → AdminController → `bookings` → `payments`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Admin | AdminController | GET /admin/reports — Load reports page | — |
| 2 | Admin | AdminController | POST /admin/reports/filter (date_from, date_to, status) | — |
| 3 | AdminController | `bookings` | Fetch filtered records | `SELECT b.id, b.status, b.booking_date, b.grand_total, b.payment_status, b.completion_date, p.amount, p.payment_method, s.service_name, u.fullname FROM bookings b LEFT JOIN payments p ON p.booking_id=b.id JOIN services s ON b.service_id=s.id JOIN users u ON b.user_id=u.id WHERE b.store_location_id=? AND b.booking_date BETWEEN ? AND ? AND b.status=?` |
| 4 | `bookings` | AdminController | ← Return matching booking records | — |
| 5 | [ALT: Export PDF] | | | |
| 5a | AdminController | AdminController | Render HTML → Convert via Dompdf | — |
| 5b | AdminController | Admin | ← Force download `report_{date}.pdf` | — |
| 6 | [ALT: Export CSV] | | | |
| 6a | AdminController | AdminController | Serialize records to CSV format | — |
| 6b | AdminController | Admin | ← Force download `report_{date}.csv` | — |
| 7 | [ALT: View in Browser] | | | |
| 7a | AdminController | Admin | ← Render HTML table with totals | — |

---

## SEQUENCE DIAGRAMS — SUPER ADMIN (CONTROL PANEL)

---

### SEQ-S1: Super Admin Login (With Lockout Protection)

**Participants:** Super Admin → ControlPanelController → `control_panel_admins` → `login_logs`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | POST /control-panel/login (email, password) | — |
| 2 | ControlPanelController | `control_panel_admins` | Find account by email | `SELECT id, email, password, role, status, failed_login_attempts, locked_until, last_login FROM control_panel_admins WHERE email=?` |
| 3 | [ALT: Not found] | | | |
| 3a | ControlPanelController | `login_logs` | Log failure | `INSERT INTO login_logs (user_type='control_panel', email=?, login_status='failed', failure_reason='account_not_found', ip_address, login_time=NOW())` |
| 3b | ControlPanelController | Super Admin | ← Show "Invalid credentials" | — |
| 4 | [ALT: Found — check lock] | | | |
| 4a | ControlPanelController | ControlPanelController | Check `locked_until > NOW()` | — |
| 4b | [ALT: Locked] | | | |
| 4c | ControlPanelController | Super Admin | ← Show "Account locked until [datetime]" | — |
| 5 | [ALT: Not locked — verify] | | | |
| 5a | ControlPanelController | ControlPanelController | Run `password_verify()` | — |
| 6 | [ALT: Wrong password] | | | |
| 6a | ControlPanelController | `control_panel_admins` | Increment attempts; lock if >= 5 | `UPDATE control_panel_admins SET failed_login_attempts=failed_login_attempts+1, locked_until=IF(failed_login_attempts+1>=5, DATE_ADD(NOW(), INTERVAL 1 HOUR), NULL) WHERE id=?` |
| 6b | ControlPanelController | `login_logs` | Log failure | `INSERT INTO login_logs (login_status='failed', failure_reason='wrong_password', user_type='control_panel')` |
| 6c | ControlPanelController | Super Admin | ← Show "Invalid credentials" | — |
| 7 | [ALT: Password correct] | | | |
| 7a | ControlPanelController | `control_panel_admins` | Reset attempts, update last login | `UPDATE control_panel_admins SET failed_login_attempts=0, locked_until=NULL, last_login=NOW() WHERE id=?` |
| 7b | ControlPanelController | `login_logs` | Log success | `INSERT INTO login_logs (user_id=admin_id, user_type='control_panel_admin', login_status='success', ip_address, login_time=NOW())` |
| 7c | ControlPanelController | ControlPanelController | Create `$_SESSION [super_admin_id, role='super_admin']` | — |
| 7d | ControlPanelController | Super Admin | ← Redirect to /control-panel/dashboard | — |

---

### SEQ-S2: Review & Approve Admin Registration

**Participants:** Super Admin → ControlPanelController → `admin_registrations` → `admin_verification_codes` → Email Service → `super_admin_activity`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/admin-registrations | `SELECT * FROM admin_registrations WHERE registration_status IN ('pending','pending_verification') ORDER BY created_at DESC` |
| 2 | ControlPanelController | Super Admin | ← Return pending list with badge counts | — |
| 3 | Super Admin | ControlPanelController | Click "View Details" on an application | `SELECT * FROM admin_registrations WHERE id=?` |
| 4 | ControlPanelController | Super Admin | ← Return: fullname, email, phone, business info, permit path | — |
| 5 | Super Admin | ControlPanelController | Click "Open Document" to view PDF | — |
| 6 | [ALT: Approve] | | | |
| 6a | Super Admin | ControlPanelController | POST /control-panel/admin/{id}/approve | — |
| 6b | ControlPanelController | `admin_verification_codes` | Select an available code | `SELECT id, verification_code FROM admin_verification_codes WHERE status='available' LIMIT 1` |
| 6c | ControlPanelController | `admin_verification_codes` | Reserve the code | `UPDATE admin_verification_codes SET status='reserved', admin_registration_id=?, assigned_to_email=?, assigned_to_name=?, assigned_by_super_admin_id=?, assigned_at=NOW(), expires_at=DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id=?` |
| 6d | ControlPanelController | `admin_registrations` | Set to pending_verification | `UPDATE admin_registrations SET registration_status='pending_verification', verification_code=?, verification_code_sent_at=NOW(), approved_by=super_admin_id, approved_at=NOW() WHERE id=?` |
| 6e | ControlPanelController | Email Service | Send verification code email to admin | — |
| 6f | ControlPanelController | `super_admin_activity` | Log approval action | `INSERT INTO super_admin_activity (super_admin_id, super_admin_name, action_type='admin_approved', target_admin_name, description, action_date=NOW())` |
| 6g | ControlPanelController | Super Admin | ← Show "Application approved. Verification code sent." | — |
| 7 | [ALT: Reject] | | | |
| 7a | Super Admin | ControlPanelController | POST /control-panel/admin/{id}/reject (rejection_reason) | — |
| 7b | ControlPanelController | `admin_registrations` | Mark rejected | `UPDATE admin_registrations SET registration_status='rejected', rejection_reason=? WHERE id=?` |
| 7c | ControlPanelController | Email Service | Send rejection email to applicant | — |
| 7d | ControlPanelController | `super_admin_activity` | Log rejection | `INSERT INTO super_admin_activity (action_type='admin_rejected', description='Rejected: reason', action_date=NOW())` |
| 7e | ControlPanelController | Super Admin | ← Show "Application rejected." | — |

---

### SEQ-S3: Deactivate / Reactivate Admin Account

**Participants:** Super Admin → ControlPanelController → `control_panel_admins` → `system_activities`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/admins — View active admins | `SELECT id, fullname, email, role, status, last_login FROM control_panel_admins WHERE role='admin'` |
| 2 | [ALT: Deactivate] | | | |
| 2a | Super Admin | ControlPanelController | POST /control-panel/admin/{id}/deactivate | — |
| 2b | ControlPanelController | `control_panel_admins` | Deactivate account | `UPDATE control_panel_admins SET status='inactive', updated_at=NOW() WHERE id=?` |
| 2c | ControlPanelController | `system_activities` | Log action | `INSERT INTO system_activities (admin_id=super_admin_id, activity_type='user_modified', description='Admin deactivated', affected_table='control_panel_admins', affected_record_id=target_admin_id, old_value='active', new_value='inactive', ip_address)` |
| 2d | ControlPanelController | `super_admin_activity` | Log super admin action | `INSERT INTO super_admin_activity (action_type='admin_deactivated', description, action_date=NOW())` |
| 3 | [ALT: Reactivate] | | | |
| 3a | Super Admin | ControlPanelController | POST /control-panel/admin/{id}/activate | — |
| 3b | ControlPanelController | `control_panel_admins` | Reactivate account | `UPDATE control_panel_admins SET status='active', updated_at=NOW() WHERE id=?` |
| 3c | ControlPanelController | `system_activities` | Log action | `INSERT INTO system_activities (activity_type='user_modified', old_value='inactive', new_value='active')` |

---

### SEQ-S4: Customer Account Management

**Participants:** Super Admin → ControlPanelController → `users` → `login_logs` → `system_activities`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/customers | `SELECT id, fullname, email, phone, status, created_at FROM users WHERE role='customer' ORDER BY created_at DESC` |
| 2 | ControlPanelController | Super Admin | ← Return customer list | — |
| 3 | Super Admin | ControlPanelController | Click "View Login History" for a customer | `SELECT ll.*, u.fullname FROM login_logs ll JOIN users u ON ll.user_id=u.id WHERE ll.user_type='customer' AND ll.email=? ORDER BY ll.login_time DESC` |
| 4 | ControlPanelController | Super Admin | ← Return: IP, user_agent, login_status, failure_reason, login_time | — |
| 5 | [ALT: Deactivate] | | | |
| 5a | Super Admin | ControlPanelController | POST /control-panel/customer/{id}/deactivate | — |
| 5b | ControlPanelController | `users` | Deactivate customer | `UPDATE users SET status='inactive', updated_at=NOW() WHERE id=?` |
| 5c | ControlPanelController | `system_activities` | Log action | `INSERT INTO system_activities (activity_type='user_modified', affected_table='users', old_value='active', new_value='inactive', ip_address)` |
| 6 | [ALT: Reactivate] | | | |
| 6a | Super Admin | ControlPanelController | POST /control-panel/customer/{id}/activate | — |
| 6b | ControlPanelController | `users` | Reactivate customer | `UPDATE users SET status='active', updated_at=NOW() WHERE id=?` |
| 6c | ControlPanelController | `system_activities` | Log action | `INSERT INTO system_activities (activity_type='user_modified', old_value='inactive', new_value='active')` |

---

### SEQ-S5: Customer Business Registration Review

**Participants:** Super Admin → ControlPanelController → `customer_businesses` → `notifications`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/businesses | `SELECT cb.*, u.fullname, u.email, bt.type_name FROM customer_businesses cb JOIN users u ON cb.user_id=u.id LEFT JOIN business_types bt ON cb.business_type_id=bt.id WHERE cb.status='pending'` |
| 2 | ControlPanelController | Super Admin | ← Return pending business list | — |
| 3 | Super Admin | ControlPanelController | Click "Review" on an application | `SELECT * FROM customer_businesses WHERE id=?` |
| 4 | Super Admin | ControlPanelController | Click "Open Document" to view permit PDF | — |
| 5 | Super Admin | ControlPanelController | Ticks verification checkbox (documents reviewed) | — |
| 6 | [ALT: Approve] | | | |
| 6a | Super Admin | ControlPanelController | POST /control-panel/business/{id}/approve | — |
| 6b | ControlPanelController | `customer_businesses` | Approve application | `UPDATE customer_businesses SET status='approved', approved_by=super_admin_id, approved_at=NOW(), updated_at=NOW() WHERE id=?` |
| 6c | ControlPanelController | `notifications` | Notify customer | `INSERT INTO notifications (user_id=customer_id, title='Business Application Approved', type='success')` |
| 7 | [ALT: Reject] | | | |
| 7a | Super Admin | ControlPanelController | POST /control-panel/business/{id}/reject (rejected_reason) | — |
| 7b | ControlPanelController | `customer_businesses` | Reject application | `UPDATE customer_businesses SET status='rejected', rejected_reason=?, updated_at=NOW() WHERE id=?` |
| 7c | ControlPanelController | `notifications` | Notify customer | `INSERT INTO notifications (user_id=customer_id, title='Business Application Rejected', type='error')` |

---

### SEQ-S6: Store Compliance Review & Ban

**Participants:** Super Admin → ControlPanelController → `store_compliance_reports` → `store_locations` → `super_admin_activity`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/compliance | `SELECT scr.*, sl.store_name, u.fullname AS customer_name FROM store_compliance_reports scr JOIN store_locations sl ON scr.store_id=sl.id JOIN users u ON scr.customer_id=u.id WHERE scr.status='pending'` |
| 2 | ControlPanelController | Super Admin | ← Return compliance report list | — |
| 3 | Super Admin | ControlPanelController | Open specific report | `SELECT * FROM store_compliance_reports WHERE id=?` |
| 4 | [ALT: Resolve / Dismiss] | | | |
| 4a | Super Admin | ControlPanelController | POST with admin_notes and decision | — |
| 4b | ControlPanelController | `store_compliance_reports` | Update report status | `UPDATE store_compliance_reports SET status='resolved' OR 'dismissed', admin_notes=?, reviewed_by=super_admin_id, reviewed_at=NOW() WHERE id=?` |
| 5 | [ALT: Ban Store] | | | |
| 5a | Super Admin | ControlPanelController | POST /control-panel/store/{id}/ban (ban_reason, ban_duration_days) | — |
| 5b | ControlPanelController | `store_locations` | Ban the store | `UPDATE store_locations SET status='inactive', banned_at=NOW(), banned_until=DATE_ADD(NOW(), INTERVAL ban_duration_days DAY), ban_duration_days=?, ban_reason=?, banned_by=super_admin_id WHERE id=?` |
| 5c | ControlPanelController | `store_compliance_reports` | Mark report reviewed | `UPDATE store_compliance_reports SET status='reviewed', reviewed_by=?, reviewed_at=NOW() WHERE id=?` |
| 5d | ControlPanelController | `super_admin_activity` | Log ban action | `INSERT INTO super_admin_activity (super_admin_id, action_type='admin_deactivated', target_admin_name=store_name, description='Banned store...', action_date=NOW())` |
| 5e | ControlPanelController | Super Admin | ← Show "Store has been banned." | — |

---

### SEQ-S7: System Monitoring — Login Logs, Audit Trail & KPIs

**Participants:** Super Admin → ControlPanelController → `login_logs` → `system_activities` → `system_statistics`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/login-logs | — |
| 2 | Super Admin | ControlPanelController | Apply filters: user_type, login_status, date range, IP | — |
| 3 | ControlPanelController | `login_logs` | Fetch filtered records | `SELECT ll.id, ll.email, ll.user_type, ll.login_status, ll.failure_reason, ll.ip_address, ll.user_agent, ll.login_time, u.fullname FROM login_logs ll LEFT JOIN users u ON ll.user_id=u.id WHERE ll.user_type=? AND ll.login_status=? AND ll.login_time BETWEEN ? AND ? ORDER BY ll.login_time DESC` |
| 4 | ControlPanelController | Super Admin | ← Return login history table | — |
| 5 | Super Admin | ControlPanelController | GET /control-panel/activity-log | — |
| 6 | ControlPanelController | `system_activities` | Fetch audit trail | `SELECT sa.*, cp.fullname AS admin_name FROM system_activities sa LEFT JOIN control_panel_admins cp ON sa.admin_id=cp.id ORDER BY sa.created_at DESC` |
| 7 | ControlPanelController | Super Admin | ← Return: activity_type, description, old_value, new_value, affected_table, ip_address, timestamp | — |
| 8 | Super Admin | ControlPanelController | GET /control-panel/statistics | — |
| 9 | ControlPanelController | `system_statistics` | Fetch today's KPIs | `SELECT total_logins, successful_logins, failed_logins, customer_logins, admin_logins, new_users, new_bookings, completed_bookings FROM system_statistics WHERE stat_date = CURDATE()` |
| 10 | ControlPanelController | Super Admin | ← Render KPI dashboard cards | — |

---

### SEQ-S8: Store Ratings Moderation

**Participants:** Super Admin → ControlPanelController → `store_ratings` → `store_locations`

| Step | From | To | Message | DB Operation |
|---|---|---|---|---|
| 1 | Super Admin | ControlPanelController | GET /control-panel/ratings | `SELECT sr.id, sr.rating, sr.review_text, sr.status, sr.created_at, sl.store_name, u.fullname AS customer_name FROM store_ratings sr JOIN store_locations sl ON sr.store_id=sl.id JOIN users u ON sr.user_id=u.id ORDER BY sr.created_at DESC` |
| 2 | ControlPanelController | Super Admin | ← Return ratings table with store and customer info | — |
| 3 | [ALT: Hide Review] | | | |
| 3a | Super Admin | ControlPanelController | POST /control-panel/rating/{id}/hide | — |
| 3b | ControlPanelController | `store_ratings` | Hide review | `UPDATE store_ratings SET status='hidden', updated_at=NOW() WHERE id=?` |
| 3c | ControlPanelController | `store_locations` | Recalculate store average | `UPDATE store_locations SET rating=(SELECT AVG(rating) FROM store_ratings WHERE store_id=? AND status='active') WHERE id=?` |
| 4 | [ALT: Restore Review] | | | |
| 4a | Super Admin | ControlPanelController | POST /control-panel/rating/{id}/restore | — |
| 4b | ControlPanelController | `store_ratings` | Restore review | `UPDATE store_ratings SET status='active', updated_at=NOW() WHERE id=?` |
| 4c | ControlPanelController | `store_locations` | Recalculate store average | `UPDATE store_locations SET rating=(SELECT AVG(rating) FROM store_ratings WHERE store_id=? AND status='active') WHERE id=?` |
| 5 | ControlPanelController | Super Admin | ← Return updated ratings list | — |

---

## Sequence Diagram Summary

| Role | Diagrams | Coverage |
|---|---|---|
| Customer | SEQ-C1 to SEQ-C9 | Register, Login, Forgot Password, Booking, Tracking, Quotation, Rating, Compliance, Business Registration |
| Admin | SEQ-A1 to SEQ-A10 | Self-Register, Verification, Login, Booking Confirm, Inspection, Quotation, Repair Lifecycle, Payment, Inventory, Reports |
| Super Admin | SEQ-S1 to SEQ-S8 | Login (Lockout), Admin Review, Deactivate/Reactivate, Customer Management, Business Review, Compliance/Ban, Monitoring, Ratings |
| **TOTAL** | **27 Sequence Diagrams** | All 21 database tables covered |

---

*End of Sequence Diagrams Document*  
*UphoCare System — db_upholcare v2.0*
