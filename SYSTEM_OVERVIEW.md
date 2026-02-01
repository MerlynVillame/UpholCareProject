# UphoCare System - Complete Overview

## 📋 Table of Contents
1. [System Overview](#system-overview)
2. [User Roles](#user-roles)
3. [Registration & Authentication](#registration--authentication)
4. [Customer Journey](#customer-journey)
5. [Admin Operations](#admin-operations)
6. [Booking Management](#booking-management)
7. [Payment Process](#payment-process)
8. [Key Features](#key-features)
9. [Technical Architecture](#technical-architecture)

---

## 🎯 System Overview

**UphoCare** is a comprehensive upholstery repair and restoration service management system that connects customers with professional repair services for:
- **Vehicle Covers** (car seats, truck covers, automotive upholstery)
- **Bedding Services** (mattress covers, bed sheets, bedroom furniture)
- **Furniture Restoration** (sofas, chairs, ottomans, and more)

The system facilitates the entire service lifecycle from booking to completion, including quotations, payments, and service delivery.

---

## 👥 User Roles

### 1. **Customer**
- Book repair and restoration services
- Track booking status
- Manage profile and contact information
- View booking history
- Request and download receipts
- Make payments

### 2. **Admin** (Business Owner/Shop Admin)
- Manage customer bookings
- Send quotations to customers
- Update booking status
- Manage services and pricing
- View business analytics
- Handle customer communications
- Requires verification via business permit

### 3. **Super Admin** (System Administrator)
- Approve admin registrations
- Send verification codes
- Manage system settings
- Oversee all operations
- Access control panel

---

## 🔐 Registration & Authentication

### Customer Registration Flow

```
1. Landing Page → Click "Sign Up"
   ↓
2. Role Selection Page
   → Select "Customer" role
   ↓
3. Customer Registration Form
   - Full Name
   - Email Address *
   - Phone Number (11 digits)
   - Password (minimum 6 characters)
   - Confirm Password
   ↓
4. Submit Registration
   ↓
5. Account Created → Redirect to Login
   ↓
6. Login with credentials
   ↓
7. Customer Dashboard
```

**Key Validations:**
- Email must be unique and valid format
- Phone must be exactly 11 digits (numeric only)
- Password minimum 6 characters
- Passwords must match

---

### Admin Registration Flow

```
1. Landing Page → Click "Sign Up"
   ↓
2. Role Selection Page
   → Select "Admin" role
   ↓
3. Admin Registration Form
   ├── Personal Information
   │   - Full Name *
   │   - Email Address *
   │   - Phone Number (11 digits)
   │   - Password *
   │   - Confirm Password *
   │
   └── Business Information
       - Business/Store Name *
       - Complete Business Address *
       - City (Bohol)
       - Province (Bohol)
       - Business Permit Upload (PDF only, max 5MB)
       - Terms & Conditions Agreement
   ↓
4. Submit Registration
   → Status: "Pending Verification"
   ↓
5. Super Admin Reviews Application
   ↓
6. Super Admin Sends Verification Code
   (Code sent via system, visible on verification page)
   ↓
7. Admin Enters Verification Code
   → Verification Page
   ↓
8. Code Verified
   → Status: "Pending Approval"
   ↓
9. Super Admin Approves Account
   → Status: "Active"
   ↓
10. Admin Can Login
    ↓
11. Admin Dashboard
```

**Admin Registration States:**
1. **pending** - Just registered, awaiting Super Admin to send code
2. **code_sent** - Verification code sent, awaiting admin to enter code
3. **verified** - Code verified, awaiting final Super Admin approval
4. **active** - Fully approved, can login and use system
5. **rejected** - Application rejected by Super Admin

---

## 🛒 Customer Journey

### Complete Booking Process

```
1. Customer Login
   ↓
2. Customer Dashboard
   - View statistics (Total, Pending, In Progress, Completed)
   - View recent bookings with Booking IDs (BK-000001)
   ↓
3. Create New Booking
   Click "Repair Reservation" button
   ↓
4. Repair Reservation Form
   
   STEP 1: Service Category Selection
   ├── Vehicle Covers
   ├── Bedding
   └── Furniture
   
   STEP 2: Service Details
   ├── Item Type (e.g., Sofa, Car Seat, Mattress)
   ├── Item Dimensions
   ├── Fabric Type Selection
   ├── Color/Design Selection
   ├── Damage Description
   ├── Special Instructions
   └── Upload Photos
   
   STEP 3: Service Options
   ├── Pickup & Delivery
   │   - Pickup Date & Time
   │   - Pickup Address
   │   - Delivery Date & Time
   │   - Delivery Address
   │
   ├── Drop-off & Pickup
   │   - Drop-off Date & Time
   │   - Pickup Date & Time
   │   - Store Selection
   │
   └── Drop-off Only
       - Drop-off Date & Time
       - Store Selection
   
   STEP 4: Review & Submit
   - Review all details
   - Confirm booking
   ↓
5. Booking Created
   Status: "Pending"
   Booking ID Generated: BK-XXXXXX
   ↓
6. Admin Reviews Booking
   ↓
7. Admin Sends Quotation
   ├── Labor Fee
   ├── Materials Cost
   ├── Fabric Cost
   ├── Service Fee
   ├── Pickup Fee (if applicable)
   ├── Delivery Fee (if applicable)
   └── Grand Total
   ↓
8. Customer Reviews Quotation
   Options:
   ├── Accept Quotation → Proceed to Payment
   └── Decline Quotation → Booking Cancelled
   ↓
9. Payment Options (if accepted)
   ├── Full Payment (Cash)
   ├── Partial Payment (50% down payment)
   └── Cash on Delivery (COD)
   ↓
10. Payment Confirmation
    Upload proof of payment (if applicable)
    ↓
11. Admin Confirms Payment
    Status: "Confirmed" → "In Progress"
    ↓
12. Service Execution
    Admin updates status as work progresses:
    - In Queue
    - In Progress
    - Quality Check
    - Ready for Pickup/Delivery
    ↓
13. Completion
    ├── Pickup/Delivery Scheduled
    ├── Customer Receives Item
    └── Status: "Completed"
    ↓
14. Receipt Generation
    Customer can request and download receipt
    ↓
15. Booking History
    Completed booking appears in history
```

---

## 👨‍💼 Admin Operations

### Admin Dashboard Features

```
Admin Dashboard
├── Statistics Overview
│   ├── Total Bookings
│   ├── Pending Bookings
│   ├── Active Services
│   └── Total Revenue
│
├── Booking Management
│   ├── View All Bookings
│   ├── Filter by Status
│   │   - Pending
│   │   - Approved
│   │   - In Queue
│   │   - In Progress
│   │   - Completed
│   │   - Cancelled
│   │
│   ├── Send Quotations
│   │   - Calculate costs
│   │   - Add line items
│   │   - Set grand total
│   │
│   ├── Update Status
│   │   - Change booking status
│   │   - Add notes
│   │
│   ├── Confirm Payments
│   │   - Review payment proof
│   │   - Approve/Reject payment
│   │
│   └── Manage Deliveries
│       - Schedule pickup/delivery
│       - Update addresses
│
├── Customer Management
│   ├── View Customer List
│   ├── View Customer Details
│   ├── View Customer History
│   └── Customer Communication
│
├── Service Management
│   ├── Add/Edit Services
│   ├── Set Pricing
│   ├── Manage Categories
│   └── Service Availability
│
├── Reports & Analytics
│   ├── Revenue Reports
│   ├── Booking Statistics
│   ├── Service Performance
│   └── Customer Analytics
│
└── Profile Management
    ├── Business Information
    ├── Contact Details
    └── Change Password
```

---

## 💰 Payment Process

### Payment Flow

```
1. Customer Receives Quotation
   ↓
2. Customer Accepts Quotation
   ↓
3. Payment Method Selection
   
   OPTION A: Full Payment
   ├── Upload proof of payment
   ├── Admin verifies payment
   └── Status: "Paid Full"
   
   OPTION B: Partial Payment (50% Down)
   ├── Calculate 50% of total
   ├── Upload proof of down payment
   ├── Admin verifies payment
   ├── Status: "Partially Paid"
   └── Remaining balance due on completion
   
   OPTION C: Cash on Delivery (COD)
   ├── No upfront payment
   ├── Status: "Pending Payment"
   └── Pay when item is delivered
   ↓
4. Payment Verification
   ├── Admin reviews proof of payment
   ├── Admin confirms/rejects payment
   └── If rejected: customer re-uploads proof
   ↓
5. Payment Confirmed
   ├── Booking status updated
   ├── Service work begins
   └── Customer notified
   ↓
6. Completion Payment (if applicable)
   ├── Remaining balance due
   ├── Customer pays remaining amount
   ├── Admin confirms final payment
   └── Status: "Delivered and Paid"
   ↓
7. Receipt Generation
   ├── Customer requests receipt
   ├── System generates receipt
   └── Customer downloads receipt
```

### Payment Status Types
- **unpaid** - No payment made
- **pending** - Payment uploaded, awaiting verification
- **paid_partial** - 50% down payment confirmed
- **paid_full_cash** - Full payment confirmed
- **paid_on_delivery_cod** - COD option selected
- **paid** - Fully paid

---

## 🔑 Key Features

### For Customers

1. **Easy Booking Process**
   - Step-by-step form with validation
   - Multiple service options
   - Upload photos of damaged items
   - Choose pickup/delivery options

2. **Real-time Tracking**
   - View booking status
   - Track service progress
   - Receive notifications

3. **Transparent Pricing**
   - Detailed quotations
   - Breakdown of costs
   - Accept/decline quotes

4. **Payment Flexibility**
   - Multiple payment options
   - Upload payment proofs
   - Download receipts

5. **Booking History**
   - View past bookings
   - Download receipts
   - Rebook services

### For Admins

1. **Comprehensive Booking Management**
   - View all bookings in one place
   - Filter and search bookings
   - Update status in real-time

2. **Quotation System**
   - Create detailed quotations
   - Calculate costs automatically
   - Send quotes to customers

3. **Payment Management**
   - Verify payment proofs
   - Track payment status
   - Manage partial payments

4. **Business Analytics**
   - Revenue tracking
   - Booking statistics
   - Performance metrics

5. **Customer Communication**
   - View customer details
   - Track customer history
   - Manage customer relationships

### For Super Admin

1. **Admin Verification System**
   - Review admin applications
   - Send verification codes
   - Approve/reject registrations
   - View business permits

2. **System Management**
   - Control panel access
   - System settings
   - User management
   - Security controls

---

## 🏗️ Technical Architecture

### Technology Stack

**Frontend:**
- HTML5, CSS3, JavaScript
- Bootstrap 4.6 (UI Framework)
- Font Awesome (Icons)
- jQuery (DOM Manipulation)
- AJAX (Asynchronous Requests)

**Backend:**
- PHP 7.4+ (Server-side Language)
- MVC Architecture Pattern
- Custom Framework
  - Controllers (Business Logic)
  - Models (Data Layer)
  - Views (Presentation Layer)

**Database:**
- MySQL/MariaDB
- PDO (Database Connection)

**Security:**
- Password Hashing (password_hash)
- SQL Injection Prevention (Prepared Statements)
- XSS Protection (htmlspecialchars)
- CSRF Protection
- Session Management
- File Upload Validation

### Directory Structure

```
UphoCare/
├── controllers/          # Business logic controllers
│   ├── AuthController.php
│   ├── CustomerController.php
│   ├── AdminController.php
│   └── ControlPanelController.php
│
├── models/              # Data models
│   ├── User.php
│   ├── Booking.php
│   ├── Service.php
│   └── Payment.php
│
├── views/               # View templates
│   ├── auth/           # Authentication pages
│   ├── customer/       # Customer pages
│   ├── admin/          # Admin pages
│   ├── control_panel/  # Super admin pages
│   └── layouts/        # Shared layouts
│
├── core/               # Core framework files
│   ├── Database.php
│   ├── Controller.php
│   ├── Model.php
│   └── Router.php
│
├── config/             # Configuration files
│   └── config.php
│
├── assets/             # Static assets
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
│
└── index.php           # Entry point
```

### Database Schema

**Key Tables:**

1. **users**
   - id, fullname, email, phone, password
   - role (customer/admin/super_admin)
   - status, created_at, updated_at

2. **admin_profiles**
   - id, user_id, business_name
   - business_address, city, province
   - business_permit_path, verification_code
   - verification_status, verified_at

3. **bookings**
   - id, user_id, service_id
   - status, payment_status
   - pickup_date, delivery_date
   - pickup_address, delivery_address
   - total_amount, grand_total
   - created_at, updated_at

4. **services**
   - id, service_name, category_id
   - description, price
   - service_type, status

5. **quotations**
   - id, booking_id
   - labor_fee, materials_cost
   - fabric_cost, service_fee
   - pickup_fee, delivery_fee
   - grand_total, status

6. **payments**
   - id, booking_id, user_id
   - amount, payment_method
   - payment_proof_path
   - status, verified_at

### Booking Status Flow

```
pending
  ↓
quotation_sent
  ↓
approved (customer accepts)
  ↓
confirmed (payment received)
  ↓
in_queue
  ↓
in_progress
  ↓
quality_check
  ↓
ready_for_pickup
  ↓
completed
  ↓
delivered_and_paid
```

### Authentication Flow

```
1. User enters credentials
   ↓
2. System validates input
   ↓
3. Query database for user
   ↓
4. Verify password hash
   ↓
5. Check user status/role
   ↓
6. Create session
   ↓
7. Redirect to appropriate dashboard
   - Customer → /customer/dashboard
   - Admin → /admin/dashboard
   - Super Admin → /control-panel/dashboard
```

---

## 🔒 Security Features

1. **Password Security**
   - Passwords hashed using `password_hash()`
   - Minimum 6 characters required
   - Password confirmation validation

2. **SQL Injection Prevention**
   - All queries use prepared statements
   - Input sanitization
   - PDO parameter binding

3. **File Upload Security**
   - File type validation
   - File size restrictions
   - MIME type checking
   - Secure file storage

4. **Session Security**
   - Secure session handling
   - Session timeout
   - Role-based access control

5. **Input Validation**
   - Frontend validation (JavaScript)
   - Backend validation (PHP)
   - Data sanitization
   - XSS prevention

---

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

**Key Responsive Features:**
- Mobile-friendly navigation
- Touch-optimized forms
- Responsive tables
- Adaptive layouts

---

## 🎨 Design System

**Color Scheme:**
- Primary: Brown (#8B4513, #654321, #A0522D)
- Success: Green (#1cc88a)
- Warning: Orange (#f6c23e)
- Danger: Red (#e74c3c)
- Info: Blue (#36b9cc)

**Typography:**
- Font Family: 'Nunito', sans-serif
- Clean, modern interface
- Consistent spacing

**UI Components:**
- Bootstrap-based components
- Custom styled cards
- Modern buttons with gradients
- Icon-based navigation

---

## 📊 Booking ID System

**Format:** `BK-XXXXXX`

- **BK** - Prefix for "Booking"
- **XXXXXX** - 6-digit number with leading zeros

**Examples:**
- Booking 1 → BK-000001
- Booking 25 → BK-000025
- Booking 1234 → BK-001234

Generated automatically from database ID using SQL:
```sql
CONCAT('BK-', LPAD(b.id, 6, '0'))
```

---

## 🚀 Getting Started

### For Customers:
1. Visit landing page
2. Click "Sign Up"
3. Select "Customer" role
4. Complete registration form
5. Login and start booking

### For Business Owners (Admins):
1. Visit landing page
2. Click "Sign Up"
3. Select "Admin" role
4. Complete registration with business details
5. Upload business permit
6. Wait for verification code from Super Admin
7. Enter verification code
8. Wait for final approval
9. Login and manage bookings

### For Super Admins:
1. Access control panel
2. Review admin applications
3. Send verification codes
4. Approve/reject registrations
5. Manage system settings

---

## 📞 Support & Contact

For system support or inquiries:
- **Email:** info@uphocare.com
- **Phone:** +63 XXX XXX XXXX
- **Location:** Manila, Philippines

---

## 📄 License

Copyright © 2025 UpholCare. All rights reserved.

---

## 🔄 Version History

**Current Version:** 1.0.0

**Recent Updates:**
- ✅ Phone validation (11 digits only)
- ✅ Booking ID system implemented
- ✅ Services section removed from customer dashboard
- ✅ Role selection before registration
- ✅ Unified background design across auth pages
- ✅ Admin verification system with business permit upload
- ✅ Customer and Admin separate registration flows

---

**End of Documentation**

