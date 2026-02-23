# UphoCare — Use Case Diagrams

**System:** UphoCare – Upholstery Repair & Restoration Management System  
**Database:** `db_upholcare`  
**Date:** February 19, 2026  
**Version:** 2.0 (Fully Detailed / Schema-Aligned)

---

## Actors

| Actor | Description | Primary Tables |
|---|---|---|
| **Customer** | Registered end-user who books upholstery repair and restoration services | `users`, `bookings`, `notifications` |
| **Admin (Store Manager)** | Approved upholstery shop operator who manages bookings, inventory, and services for their branch | `control_panel_admins`, `store_locations`, `bookings`, `inventory` |
| **Super Admin** | System Owner / Developer who governs all accounts, approvals, and platform-wide settings from the Control Panel | `control_panel_admins`, `admin_registrations`, `system_activities`, `super_admin_activity` |

---

## USE CASE DIAGRAM 1 — CUSTOMER

### System Boundary: Customer Portal

---

### Subsystem 1.1 — Authentication & Profile Management

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-C01 | Register Account | 1. Customer opens `/register` page. 2. Customer fills out: `username`, `email`, `fullname`, `phone`, `password`. 3. System validates email uniqueness. 4. System hashes password and inserts into `users` (role='customer', status='active'). 5. System redirects to `/login`. | `users` |
| UC-C02 | Login | 1. Customer opens `/login` page. 2. Customer enters email and password. 3. System queries `users` table by email. 4. System runs `password_verify()` on stored hash. 5. System checks if `status='active'`. 6. System logs result to `login_logs`. 7. System creates session and redirects to Customer Dashboard. | `users`, `login_logs` |
| UC-C03 | Logout | 1. Customer clicks "Logout". 2. System destroys `$_SESSION`. 3. System redirects to Landing Page. | Session |
| UC-C04 | Forgot Password | 1. Customer clicks "Forgot Password". 2. Customer enters registered email. 3. System generates secure `reset_token` and sets `reset_token_expiry`. 4. System emails the password reset link. 5. Customer opens the link and enters a new password. 6. System hashes and saves new password. 7. System clears `reset_token` and `reset_token_expiry`. | `users` |
| UC-C05 | Edit Profile | 1. Customer opens Profile Settings. 2. Customer updates `fullname`, `phone`, or `username`. 3. Customer optionally uploads new `profile_image` or `cover_image`. 4. System validates and saves changes to `users`. 5. System shows a success message. | `users` |

---

### Subsystem 1.2 — Browse & Explore

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-C06 | View Landing Page | 1. Visitor opens the UphoCare website. 2. System displays services, features, testimonials, and store locations. 3. System links to Register or Login. | — |
| UC-C07 | Browse Services | 1. Customer navigates to the Services page. 2. System fetches all active services with their categories and prices. 3. Customer views service name, description, price, and fabric requirement. | `services`, `service_categories` |
| UC-C08 | Browse Fabric/Material Catalog | 1. Customer opens the Fabric Catalog page. 2. System fetches inventory items with `status='in-stock'` or `status='low-stock'`. 3. Customer views: `color_name`, `color_hex`, `leather_type`, `price_per_meter`. 4. Customer can filter by store or material type. | `inventory`, `store_locations` |
| UC-C09 | View Store Locations & Map | 1. Customer opens the Store Map page. 2. System fetches `store_locations` where `status='active'`. 3. System displays store pins on map using `latitude` and `longitude`. 4. Customer clicks a pin to view: `store_name`, `address`, `operating_hours`, `phone`, `rating`. | `store_locations` |

---

### Subsystem 1.3 — Booking & Reservation

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-C10 | Submit Service Booking | 1. Customer clicks "Book a Service". 2. Customer selects: service (`service_id`), store (`store_location_id`), date (`booking_date`). 3. Customer selects `service_option`: pickup, drop-off, or walk-in. 4. Customer enters `item_description`, `item_type`, and optional `notes`. 5. System checks store's daily capacity. 6. System inserts into `bookings` (status='pending', payment_status='unpaid'). 7. System notifies Store Admin via `notifications`. 8. System shows booking confirmation with booking ID. | `bookings`, `services`, `store_locations`, `notifications` |
| UC-C11 | Submit Repair Reservation | 1. Customer opens the Repair Reservation form/modal. 2. Customer submits a specific repair item request with urgency level. 3. System inserts into `repair_items` (status='pending'). 4. System links reservation to customer's account. | `bookings`, `repair_items` |
| UC-C12 | Track Booking / Repair Status | 1. Customer opens "My Bookings". 2. System fetches all bookings for that customer. 3. System displays the current status label for each booking (20+ status stages from 'pending' to 'completed'). 4. Customer can click a booking to view step-by-step progress. | `bookings` |
| UC-C13 | View Booking History | 1. Customer opens "Booking History". 2. System fetches `bookings` where `user_id=?` ordered by `created_at DESC`. 3. Customer views past bookings with date, service, status, and total cost. | `bookings` |
| UC-C14 | Archive Booking | 1. Customer selects a completed booking. 2. Customer clicks "Archive". 3. System sets `is_archived=1` on the booking record. 4. Archived bookings are hidden from the main bookings list. | `bookings` |
| UC-C15 | Cancel Booking | 1. Customer selects a pending booking. 2. Customer clicks "Cancel Booking". 3. System checks if booking is still in 'pending' status. 4. System updates: `UPDATE bookings SET status='cancelled'`. 5. System notifies the Store Admin. | `bookings`, `notifications` |

---

### Subsystem 1.4 — Quotation & Payment

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-C16 | View Quotation | 1. Customer receives a notification "Quotation is Ready". 2. Customer opens the booking details. 3. System loads quotation fields from `bookings`: `grand_total`, `labor_fee`, `travel_fee`, `gas_fee`, `color_price`. 4. Customer reviews full price breakdown. | `bookings` |
| UC-C17 | Accept Quotation | 1. Customer reviews the quotation and clicks "Accept". 2. System updates: `UPDATE bookings SET quotation_accepted=1, quotation_accepted_at=NOW(), status='approved'`. 3. System inserts notification for the Store Admin to begin repair. | `bookings`, `notifications` |
| UC-C18 | Reject Quotation | 1. Customer reviews the quotation and clicks "Reject". 2. System updates: `UPDATE bookings SET quotation_accepted=0, status='cancelled'`. 3. System notifies the Store Admin that the quotation was rejected. | `bookings`, `notifications` |
| UC-C19 | View Receipt / Payment Summary | 1. After repair is completed and payment is recorded by admin, customer opens booking. 2. System loads final payment details from `payments` and booking grand total. 3. Customer views receipt: service, amount paid, method, date. | `bookings`, `payments` |

---

### Subsystem 1.5 — Feedback & Compliance

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-C20 | Rate Store | 1. Customer opens the Store Ratings page after a completed service. 2. Customer selects a store and submits a rating (1.0–5.0) with optional `review_text`. 3. System checks for an existing rating for that customer–store pair. 4. If new: `INSERT INTO store_ratings`. If existing: `UPDATE store_ratings`. 5. System recalculates the store's average rating. | `store_ratings`, `store_locations` |
| UC-C21 | File Compliance Report | 1. Customer selects a store to report. 2. Customer selects `report_type`: safety, hygiene, quality, service, pricing, or other. 3. Customer selects from `issue_types` (JSON array) and fills `description`. 4. Customer submits the form. 5. System inserts: `INSERT INTO store_compliance_reports (status='pending')`. 6. System notifies Super Admin. | `store_compliance_reports` |
| UC-C22 | Register Business Account | 1. Customer opens "Register my Business". 2. Customer fills: `business_name`, `business_type_id`, `business_address`. 3. Customer uploads a valid Business Permit PDF (`permit_file`). 4. System inserts: `INSERT INTO customer_businesses (status='pending')`. 5. System notifies Super Admin for review and approval. | `customer_businesses`, `business_types` |
| UC-C23 | Manage Notifications | 1. System generates `notifications` for every major status change. 2. Customer opens the Notifications panel. 3. Customer reads the message and clicks "Mark as Read". 4. System updates: `UPDATE notifications SET is_read=1`. | `notifications` |

---

## USE CASE DIAGRAM 2 — ADMIN (STORE MANAGER)

### System Boundary: Admin Panel

---

### Subsystem 2.1 — Registration, Access & Profile

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-A01 | Self-Register | 1. Admin opens the registration page. 2. Admin submits: `fullname`, `email`, `username`, `password`, `phone`. 3. Admin fills in business details: `business_name`, `business_address`, `business_city`, `business_province`, with GPS coordinates. 4. Admin uploads Business Permit PDF. 5. System stores in `admin_registrations` (status='pending'). 6. System returns "Application submitted. Awaiting Super Admin review." | `admin_registrations` |
| UC-A02 | Email Verification (Post-Approval) | 1. Super Admin approves application and system generates a 4–6 digit code. 2. System saves code to `admin_registrations.verification_code` and logs to `admin_verification_codes`. 3. System sends code to admin's email. 4. Admin enters code on the verification page. 5. System validates: checks code match, expiry (`expires_at`), and `verification_attempts`. 6. System creates account in `control_panel_admins` and `store_locations`. 7. System marks: `UPDATE admin_registrations SET registration_status='approved'`. | `admin_registrations`, `admin_verification_codes`, `control_panel_admins`, `store_locations` |
| UC-A03 | Login to Admin Panel | 1. Admin opens `/admin/login`. 2. Admin enters email and password. 3. System queries `control_panel_admins` by email. 4. System checks `status='active'` and `locked_until`. 5. System verifies password hash. 6. System logs to `login_logs`. 7. System creates session and redirects to Admin Dashboard. | `control_panel_admins`, `login_logs` |
| UC-A04 | Logout | 1. Admin clicks "Logout". 2. System destroys session. | Session |
| UC-A05 | Edit Profile & Store Settings | 1. Admin opens Profile/Settings page. 2. Admin updates: `fullname`, `phone`, `email`, or `password`. 3. Admin updates store details: `store_name`, `address`, `operating_hours`, `features`, `phone`. 4. System saves to `control_panel_admins` and `store_locations`. | `control_panel_admins`, `store_locations` |

---

### Subsystem 2.2 — Booking Management (Full Repair Workflow)

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-A06 | View All Bookings | 1. Admin opens Bookings page. 2. System fetches bookings for that store: `SELECT * FROM bookings WHERE store_location_id=?`. 3. Admin can filter by status, date range, or booking type. 4. Admin sees list of pending, active, and closed repair requests. | `bookings` |
| UC-A07 | Confirm Booking | 1. Admin clicks on a pending booking. 2. Admin reviews customer details and item description. 3. Admin clicks "Confirm". 4. System assigns `customer_booking_number_id`. 5. System updates: `UPDATE bookings SET status='confirmed'`. 6. System sends customer notification: "Booking Confirmed". | `bookings`, `notifications` |
| UC-A08 | Reject Booking | 1. Admin reviews a pending booking and decides it cannot be accommodated. 2. Admin clicks "Reject" and provides a reason. 3. System updates: `UPDATE bookings SET status='rejected', admin_notes=?`. 4. System notifies customer. | `bookings`, `notifications` |
| UC-A09 | Schedule Pickup / Logistics | 1. Admin sets `pickup_date`, `pickup_address`, and calculates `distance_km`. 2. Admin inputs `travel_fee` and `gas_fee`. 3. System updates: `UPDATE bookings SET status='for_pickup', pickup_address=?, distance_km=?, travel_fee=?, gas_fee=?`. | `bookings` |
| UC-A10 | Mark Item Picked Up | 1. Admin confirms that the physical item has been collected. 2. System updates: `UPDATE bookings SET status='picked_up'`. 3. System sends customer notification: "Your item has been picked up." | `bookings`, `notifications` |
| UC-A11 | Conduct Inspection | 1. Admin opens the Inspection form for the booking. 2. Admin fills in: damage severity, damage types, measurements, final `item_description`. 3. Admin uploads inspection photo or preview receipt image. 4. System updates: `UPDATE bookings SET status='inspect_completed', admin_notes=?`. | `bookings` |
| UC-A12 | Create & Send Quotation | 1. Admin reviews inspection data and calculates costs. 2. Admin inputs: `labor_fee`, material costs, and confirms `travel_fee` and `delivery_fee`. 3. System computes: `grand_total = labor_fee + color_price + travel_fee + gas_fee + delivery_fee`. 4. System updates: `UPDATE bookings SET grand_total=?, quotation_sent=1, quotation_sent_at=NOW(), status='for_quotation'`. 5. System sends notification to customer: "Your quotation is ready. Please review." | `bookings`, `notifications` |
| UC-A13 | Start Repair (After Acceptance) | 1. System detects `quotation_accepted=1`. 2. Admin queues the booking: `UPDATE bookings SET status='in_queue'`. 3. Admin begins repair: `UPDATE bookings SET status='in_progress'`. | `bookings` |
| UC-A14 | Update Repair Progress | 1. Admin transitions the booking through repair stages. 2. Stages: `in_queue` → `in_progress` → `under_repair` → `repair_completed` → `for_quality_check`. 3. System updates `status` at each stage with a timestamp. | `bookings` |
| UC-A15 | Complete Repair & Quality Check | 1. Admin confirms repair passed quality inspection. 2. System updates: `UPDATE bookings SET status='repair_completed'`. 3. System moves to: `repair_completed_ready_to_deliver` if delivery is needed. | `bookings` |
| UC-A16 | Arrange Delivery or Pickup | 1. Admin selects delivery method. 2. If Delivery: Admin sets `delivery_date`, `delivery_address`. System updates `status='out_for_delivery'`. 3. If Pickup: System updates `status='ready_for_pickup'`. | `bookings` |
| UC-A17 | Record Payment | 1. Admin opens the Payment section for a completed booking. 2. Admin inputs: `amount`, `payment_method` (cash, cod). 3. System inserts: `INSERT INTO payments (booking_id, amount, payment_method, payment_status='paid', payment_date=NOW())`. 4. System updates: `UPDATE bookings SET payment_status='paid_full_cash'|'paid_on_delivery_cod', status='completed', completion_date=NOW()`. | `payments`, `bookings` |
| UC-A18 | Close / Archive Booking | 1. Admin finalizes a completed booking. 2. Admin clicks "Close". 3. System updates: `UPDATE bookings SET status='closed'`. | `bookings` |

---

### Subsystem 2.3 — Inventory & Service Management

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-A19 | View Inventory | 1. Admin opens Inventory page. 2. System fetches: `SELECT * FROM inventory WHERE store_location_id=?`. 3. Admin sees: color code, color name, color hex, leather type, quantity, price, and status. | `inventory`, `store_locations` |
| UC-A20 | Add Fabric Item | 1. Admin clicks "Add New Material". 2. Admin fills: `color_code`, `color_name`, `color_hex`, `leather_type`, `quantity`, `standard_price`, `premium_price`, `price_per_meter`. 3. System checks for duplicates: same `color_code` + `store_location_id`. 4. If unique: `INSERT INTO inventory (status='in-stock')`. | `inventory` |
| UC-A21 | Edit & Update Fabric Stock | 1. Admin opens an inventory item. 2. Admin updates quantity or price fields. 3. System saves changes and auto-updates `status`: in-stock (>5), low-stock (1–5), out-of-stock (0). 4. System updates `updated_at=NOW()`. | `inventory` |
| UC-A22 | Manage Daily Capacity | 1. Admin opens Service Settings. 2. Admin sets `daily_capacity` per service (e.g., max 10 bookings/day). 3. System saves the updated limit to `services.daily_capacity`. 4. New customer bookings will be validated against this capacity. | `services` |
| UC-A23 | Manage Services | 1. Admin opens Services list. 2. Admin can: add new service with category, name, price, description. 3. Admin can update service `status` to active or inactive. 4. System saves to `services` table. | `services`, `service_categories` |

---

### Subsystem 2.4 — Reports, Analytics & Ratings

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-A24 | View Admin Dashboard Stats | 1. Admin opens Dashboard. 2. System fetches: pending bookings, active repairs, completed bookings today, revenue for the period. 3. System renders KPI cards from aggregated `bookings` and `payments` data. | `bookings`, `payments`, `main_admin_panel` |
| UC-A25 | Generate Booking Report | 1. Admin opens Reports page. 2. Admin sets filters: date range (start–end), status, booking type. 3. System queries: `SELECT bookings.*, payments.* WHERE booking_date BETWEEN ? AND ?`. 4. System renders a tabular report with totals. | `bookings`, `payments` |
| UC-A26 | Export Report as PDF | 1. Admin clicks "Export PDF" after filtering report. 2. System compiles the HTML report into a PDF using Dompdf. 3. System triggers a download for the `.pdf` file. | `bookings`, `payments` |
| UC-A27 | Export Report as CSV | 1. Admin clicks "Export CSV" after filtering report. 2. System serializes matching booking records into CSV format. 3. System triggers a download for the `.csv` file. | `bookings`, `payments` |
| UC-A28 | View Store Ratings & Reviews | 1. Admin opens Ratings page. 2. System fetches: `SELECT * FROM store_ratings WHERE store_id=?`. 3. Admin views customer names, ratings (1.0–5.0), review text, and status. | `store_ratings` |

---

### Use Case Relationships (Admin)

- **UC-A12** `<<extends>>` **UC-A11** — Quotation can only be created after inspection is complete
- **UC-A13** `<<includes>>` **UC-A17** — Accepting quotation is required before starting repair
- **UC-A17** `<<includes>>` **UC-A15** — Payment recording requires repair to be completed
- **UC-A26** `<<extends>>` **UC-A25** — PDF export extends from Generate Report
- **UC-A27** `<<extends>>` **UC-A25** — CSV export extends from Generate Report
- **UC-A02** `<<includes>>` **UC-A01** — Verification code step is required after registration

---

## USE CASE DIAGRAM 3 — SUPER ADMIN (CONTROL PANEL)

### System Boundary: Control Panel

---

### Subsystem 3.1 — Control Panel Access

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S01 | Login to Control Panel | 1. Super Admin opens `/control-panel/login`. 2. Super Admin enters email and password. 3. System queries `control_panel_admins` by email (role='super_admin'). 4. System checks: `locked_until` — if locked, shows error. 5. System verifies password hash. 6. On failure: increments `failed_login_attempts`; locks after 5 attempts. 7. On success: logs to `login_logs`, resets `failed_login_attempts`, updates `last_login`, creates session. | `control_panel_admins`, `login_logs` |
| UC-S02 | View Control Panel Dashboard | 1. Super Admin opens Dashboard. 2. System shows summary: pending admin registrations, active admins, total customers, total bookings, recent activity. 3. System fetches from `system_statistics` for the current date. | `admin_registrations`, `control_panel_admins`, `system_statistics`, `main_admin_panel` |
| UC-S03 | Logout | 1. Super Admin clicks "Logout". 2. System destroys session. | Session |
| UC-S04 | Reset Password | 1. Super Admin clicks "Forgot Password" on the Control Panel login page. 2. System generates `reset_token` and `reset_token_expiry`. 3. System sends reset link by email. 4. Super Admin sets new password via the link. | `control_panel_admins` |

---

### Subsystem 3.2 — Admin Account Governance

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S05 | View Admin Registrations | 1. Super Admin opens Admin Registrations page. 2. System fetches: `SELECT * FROM admin_registrations WHERE registration_status IN ('pending', 'pending_verification', 'approved', 'rejected')`. 3. System shows badge count of pending applications. | `admin_registrations` |
| UC-S06 | Review Admin Application Details | 1. Super Admin clicks "View" on a specific application. 2. System loads: fullname, email, phone, business_name, business_address, GPS coordinates, and document file path. 3. Super Admin can click "Open Document" to view the uploaded PDF permit. | `admin_registrations` |
| UC-S07 | Approve Admin Registration | 1. Super Admin reviews documents and clicks "Accept & Approve". 2. System selects an available code from `admin_verification_codes` (status='available'). 3. System updates: `UPDATE admin_registrations SET registration_status='pending_verification', verification_code=?, verification_code_sent_at=NOW(), approved_by=?, approved_at=NOW()`. 4. System marks code: `UPDATE admin_verification_codes SET status='reserved'`. 5. System sends verification email to the Admin. 6. System logs to `super_admin_activity` (action_type='admin_approved'). | `admin_registrations`, `admin_verification_codes`, `super_admin_activity` |
| UC-S08 | Reject Admin Registration | 1. Super Admin enters a rejection reason and clicks "Reject". 2. System updates: `UPDATE admin_registrations SET registration_status='rejected', rejection_reason=?`. 3. System sends rejection notification email. 4. System logs to `super_admin_activity` (action_type='admin_rejected'). | `admin_registrations`, `super_admin_activity` |
| UC-S09 | Resend Verification Code | 1. Super Admin navigates to a 'pending_verification' application. 2. Super Admin clicks "Resend Code". 3. System selects a new available code from `admin_verification_codes`. 4. System updates the code and `verification_code_sent_at`. 5. System re-sends the code by email. | `admin_registrations`, `admin_verification_codes` |
| UC-S10 | View Active Admins | 1. Super Admin opens Admin Accounts page. 2. System fetches: `SELECT * FROM control_panel_admins WHERE role='admin'`. 3. Super Admin can see: fullname, email, status, `last_login`, branch store. | `control_panel_admins` |
| UC-S11 | Deactivate Admin Account | 1. Super Admin clicks "Deactivate" on an admin account. 2. System updates: `UPDATE control_panel_admins SET status='inactive'`. 3. System logs to `system_activities` (activity_type='user_modified', old_value='active', new_value='inactive'). | `control_panel_admins`, `system_activities` |
| UC-S12 | Reactivate Admin Account | 1. Super Admin clicks "Activate" on an inactive admin account. 2. System updates: `UPDATE control_panel_admins SET status='active'`. 3. System logs to `system_activities`. | `control_panel_admins`, `system_activities` |
| UC-S13 | Ban Admin Account | 1. Super Admin sets `ban_reason` and `ban_duration_days`. 2. System sets `banned_at=NOW()` and calculates `banned_until`. 3. System also optionally bans the associated store. 4. System logs to `super_admin_activity`. | `admin_registrations`, `store_locations`, `super_admin_activity` |

---

### Subsystem 3.3 — Customer Account Management

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S14 | View Customer Accounts | 1. Super Admin opens Customer Management. 2. System fetches: `SELECT * FROM users WHERE role='customer' ORDER BY created_at DESC`. 3. Super Admin sees: name, email, phone, status, registration date. | `users` |
| UC-S15 | View Customer Login History | 1. Super Admin selects a customer. 2. System fetches: `SELECT * FROM login_logs WHERE user_type='customer' AND email=?`. 3. System shows: IP address, user_agent, login_status, failure_reason, login_time. | `login_logs`, `users` |
| UC-S16 | Deactivate Customer Account | 1. Super Admin clicks "Deactivate" on a customer record. 2. System updates: `UPDATE users SET status='inactive'`. 3. System logs to `system_activities`. | `users`, `system_activities` |
| UC-S17 | Reactivate Customer Account | 1. Super Admin clicks "Activate" on an inactive customer. 2. System updates: `UPDATE users SET status='active'`. 3. System logs to `system_activities`. | `users`, `system_activities` |

---

### Subsystem 3.4 — Customer Business Registration Review

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S18 | View Business Registrations | 1. Super Admin opens Business Registrations. 2. System fetches: `SELECT * FROM customer_businesses WHERE status='pending'`. 3. Super Admin sees list of customers applying for business status. | `customer_businesses`, `users` |
| UC-S19 | Review Business Documents | 1. Super Admin clicks on an application. 2. System loads: `business_name`, `business_address`, `business_type_id`, and `permit_file` path. 3. Super Admin clicks "Open Document" to view the PDF. | `customer_businesses`, `business_types` |
| UC-S20 | Approve Business Application | 1. Super Admin ticks the verification checkbox. 2. Super Admin clicks "Approve". 3. System updates: `UPDATE customer_businesses SET status='approved', approved_by=?, approved_at=NOW()`. 4. System notifies customer. | `customer_businesses`, `notifications` |
| UC-S21 | Reject Business Application | 1. Super Admin enters `rejected_reason` and clicks "Reject". 2. System updates: `UPDATE customer_businesses SET status='rejected', rejected_reason=?`. 3. System notifies customer. | `customer_businesses`, `notifications` |

---

### Subsystem 3.5 — Store Compliance & Moderation

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S22 | View Compliance Reports | 1. Super Admin opens Compliance Reports. 2. System fetches: `SELECT * FROM store_compliance_reports WHERE status='pending'`. 3. Super Admin sees: store name, report type, issue types, description, submission date. | `store_compliance_reports`, `store_locations`, `users` |
| UC-S23 | Review Compliance Report | 1. Super Admin opens a specific report. 2. System loads: full description, `issue_types` JSON array, `report_type`, customer info, and store info. 3. Super Admin reviews and writes `admin_notes`. | `store_compliance_reports` |
| UC-S24 | Resolve / Dismiss Report | 1. Super Admin adds notes and clicks "Resolve" or "Dismiss". 2. System updates: `UPDATE store_compliance_reports SET status='resolved'|'dismissed', admin_notes=?, reviewed_by=?, reviewed_at=NOW()`. | `store_compliance_reports` |
| UC-S25 | Ban / Suspend Store | 1. Super Admin selects a store to ban. 2. Super Admin fills: `ban_reason` and ban duration (or sets permanent). 3. System updates: `UPDATE store_locations SET status='inactive', banned_at=NOW(), banned_until=?, ban_duration_days=?, ban_reason=?, banned_by=?`. 4. System logs to `super_admin_activity`. 5. System optionally also bans the associated admin account. | `store_locations`, `admin_registrations`, `super_admin_activity` |
| UC-S26 | Unban Store | 1. Super Admin navigates to banned stores. 2. Super Admin clicks "Unban". 3. System clears ban fields: `UPDATE store_locations SET status='active', banned_at=NULL, banned_until=NULL, ban_reason=NULL`. 4. System logs to `super_admin_activity`. | `store_locations`, `super_admin_activity` |
| UC-S27 | View & Moderate Store Ratings | 1. Super Admin opens Store Ratings Moderation. 2. System fetches all ratings joined with store names and customer names. 3. Super Admin can click "Hide" on an inappropriate review. 4. System updates: `UPDATE store_ratings SET status='hidden'`. 5. System recalculates the store's average rating. | `store_ratings`, `store_locations` |

---

### Subsystem 3.6 — System Monitoring & Audit Trail

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S28 | View Login Logs | 1. Super Admin opens Login Logs. 2. Super Admin applies filters: `user_type`, `login_status`, date range, IP address. 3. System fetches: `SELECT * FROM login_logs WHERE ...`. 4. System shows: email, fullname, IP, user agent, login time, failure reason. | `login_logs`, `users` |
| UC-S29 | View System Activity Audit Trail | 1. Super Admin opens Audit Trail. 2. System fetches: `SELECT * FROM system_activities ORDER BY created_at DESC`. 3. System shows: `activity_type`, `description`, `affected_table`, `old_value`, `new_value`, `ip_address`, and timestamp. | `system_activities` |
| UC-S30 | View Super Admin Activity Log | 1. Super Admin opens their own activity log. 2. System fetches: `SELECT * FROM super_admin_activity`. 3. System shows: `action_type` (approved, rejected, deactivated), `target_admin_name`, `description`, `action_date`. | `super_admin_activity` |
| UC-S31 | View System Statistics (KPI) | 1. Super Admin opens the Statistics Dashboard. 2. System fetches: `SELECT * FROM system_statistics WHERE stat_date = CURDATE()`. 3. System renders: `total_logins`, `successful_logins`, `failed_logins`, `new_users`, `new_bookings`, `completed_bookings`. | `system_statistics` |

---

### Subsystem 3.7 — Platform Metadata Management

| Use Case ID | Use Case Name | Step-by-Step Description | Database Tables |
|---|---|---|---|
| UC-S32 | Manage Business Types | 1. Super Admin opens Business Type Settings. 2. Super Admin adds or edits a `business_type`: `type_name`, `description`. 3. System saves to `business_types` table. 4. Business type dropdown in Customer Business Registration is automatically updated. | `business_types` |
| UC-S33 | Manage Service Categories | 1. Super Admin opens Service Categories. 2. Super Admin adds or edits a category: name, description. 3. System saves to `service_categories` table. 4. Admin service setup dropdowns are automatically updated. | `service_categories` |

---

### Use Case Relationships (Super Admin)

- **UC-S07** `<<includes>>` **UC-S06** — Must review details before approving
- **UC-S08** `<<includes>>` **UC-S06** — Must review details before rejecting
- **UC-S09** `<<extends>>` **UC-S07** — Resend code extends from the approval flow
- **UC-S20** `<<includes>>` **UC-S19** — Must review documents before approving business
- **UC-S25** `<<extends>>` **UC-S23** — Banning a store extends from reviewing compliance report
- **UC-S26** `<<extends>>` **UC-S25** — Unban is opposite / follow-up of the ban action
- **UC-S13** `<<extends>>` **UC-S08** — Admin ban extends from rejection handling

---

## Use Case Summary

| Role | Total Use Cases |
|---|---|
| Customer | 23 (UC-C01 to UC-C23) |
| Admin / Store Manager | 28 (UC-A01 to UC-A28) |
| Super Admin | 33 (UC-S01 to UC-S33) |
| **TOTAL** | **84 Use Cases** |

---

*End of Use Case Diagrams Document*  
*UphoCare System — db_upholcare v2.0*
