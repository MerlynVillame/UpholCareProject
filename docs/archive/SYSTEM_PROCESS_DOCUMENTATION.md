# UphoCare System - Complete Process Documentation

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [System Architecture](#system-architecture)
3. [User Roles & Access Levels](#user-roles--access-levels)
4. [Core Features & Modules](#core-features--modules)
5. [Key Processes & Workflows](#key-processes--workflows)
6. [Database Structure](#database-structure)
7. [Technical Stack](#technical-stack)
8. [API Endpoints](#api-endpoints)
9. [Installation & Setup](#installation--setup)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 System Overview

**UphoCare** is a comprehensive web-based repair and restoration management system designed specifically for upholstery shops. It streamlines the entire process from customer booking to service completion, payment processing, and inventory management.

### Purpose

- Manage customer bookings and reservations
- Track service orders and repair workflows
- Handle payment processing
- Manage leather/fabric inventory
- Generate reports and analytics
- Monitor system-wide activities

### Key Benefits

- ✅ Centralized booking management
- ✅ Real-time inventory tracking
- ✅ Automated booking number assignment
- ✅ Two-stage email notifications:
  - Immediate confirmation email (no pricing)
  - Approval email with complete pricing details
- ✅ Mobile-responsive design
- ✅ Comprehensive reporting system
- ✅ Multi-level admin hierarchy

---

## 🏗️ System Architecture

### 3-Tier User Hierarchy

```
┌─────────────────────────────────────┐
│   Super Admin (Control Panel)      │
│   - System-wide oversight          │
│   - Admin approval/rejection       │
│   - Performance monitoring          │
└──────────────┬──────────────────────┘
               │
               ↓
┌─────────────────────────────────────┐
│   Regular Admins                    │
│   - Accept/reject bookings          │
│   - Process payments                 │
│   - Manage inventory                │
│   - View reports                    │
└──────────────┬──────────────────────┘
               │
               ↓
┌─────────────────────────────────────┐
│   Customers                         │
│   - Create bookings                 │
│   - View own bookings               │
│   - Manage profile                  │
└─────────────────────────────────────┘
```

### Application Structure

```
UphoCare/
├── controllers/          # Business logic controllers
│   ├── AdminController.php
│   ├── CustomerController.php
│   ├── AuthController.php
│   └── ControlPanelController.php
├── models/              # Data models
│   ├── Booking.php
│   ├── Inventory.php
│   ├── Service.php
│   └── User.php
├── views/               # Presentation layer
│   ├── admin/          # Admin interface
│   ├── customer/       # Customer interface
│   └── layouts/        # Shared layouts
├── core/               # Core framework
│   ├── Controller.php
│   ├── Model.php
│   └── Database.php
├── config/             # Configuration files
│   ├── config.php
│   └── database.php
└── assets/             # Static assets
    ├── css/
    └── js/
```

---

## 👥 User Roles & Access Levels

### 1. Customer (`role: 'customer'`)

**Access:**

- Customer Dashboard
- Booking Management
- Profile Management
- Service Catalog

**Capabilities:**

- Register and create account
- Browse available services
- Create booking requests
- View own booking history
- Update profile information
- Track booking status

**Login URL:** `http://localhost/UphoCare/auth/login`

---

### 2. Admin (`role: 'admin'`)

**Access:**

- Admin Dashboard
- All Bookings Management
- Inventory Management
- Services Management
- Reports & Analytics
- Booking Numbers Management

**Capabilities:**

- Accept/reject customer bookings
- Assign booking numbers
- Process payments
- Manage leather inventory
- Add/edit services
- View comprehensive reports
- Update booking statuses
- Send notifications to customers

**Login URL:** `http://localhost/UphoCare/auth/login`

**Registration:** Requires admin verification key and super admin approval

---

### 3. Super Admin / Control Panel Admin (`role: 'control_panel_admin'`)

**Access:**

- Control Panel Dashboard
- Admin Registration Management
- System-wide Statistics
- Login Activity Monitoring
- Admin Performance Tracking

**Capabilities:**

- Approve/reject admin registrations
- Monitor all admin activities
- View system-wide statistics
- Track login attempts
- Monitor admin sales performance
- Manage control panel settings

**Login URL:** `http://localhost/UphoCare/control-panel/login`

---

## 🚀 Core Features & Modules

### 1. Booking Management System

**Purpose:** Handle customer service requests from creation to completion

**Key Features:**

- Customer booking creation
- Admin approval/rejection workflow
- Automatic booking number assignment
- Status tracking (pending → approved → in_progress → completed)
- Payment status management
- Delivery/pickup date scheduling
- Booking notes and admin comments

**Process Flow:**

```
Customer Creates Booking
        ↓
Status: Pending
        ↓
⭐ EMAIL #1 SENT IMMEDIATELY:
- Booking Number (Queue Number) assigned
- Note: "Admin already received your reservation and will review it."
- NO price or total amount
        ↓
Admin Reviews Booking
        ↓
Admin Accepts → Status: Approved
Admin Rejects → Status: Cancelled
        ↓
⭐ EMAIL #2 SENT AFTER APPROVAL:
- Updated booking details
- Complete pricing breakdown
- TOTAL AMOUNT (Grand Total)
        ↓
Work Begins → Status: In Progress
        ↓
Service Completed → Status: Completed
        ↓
Payment Processed → Payment Status: Paid
```

---

### 2. Inventory Management System

**Purpose:** Track leather/fabric stock levels and manage inventory

**Key Features:**

- Auto-incrementing color codes (INV-001, INV-002, etc.)
- Color code and name management
- Color preview with hex codes
- Leather type categorization
- Quantity tracking (in rolls)
- Standard and premium pricing
- Stock status (in-stock, low-stock, out-of-stock)
- Real-time inventory updates

**Inventory Item Structure:**

- Color Code (auto-generated)
- Color Name
- Color Preview (hex code)
- Leather Type (Genuine, Faux, Suede, Nubuck, Vinyl)
- Quantity (rolls)
- Standard Price (₱)
- Premium Price (₱)
- Status (auto-calculated based on quantity)

**Process Flow:**

```
Admin Opens "Add Leather Stock" Modal
        ↓
Color Code Auto-Generated (e.g., INV-001)
        ↓
Admin Fills Form:
- Color Name
- Color Preview
- Leather Type
- Quantity
- Prices
        ↓
Form Submitted via AJAX
        ↓
Data Saved to Database
        ↓
Inventory Table Updated
        ↓
Summary Cards Refreshed
```

---

### 3. Services Management

**Purpose:** Manage available services offered by the shop

**Key Features:**

- Service creation and editing
- Service categorization
- Price management
- Service type classification
- Status management (active/inactive)
- Service description

**Service Structure:**

- Service Name
- Category
- Service Type
- Description
- Price (₱)
- Status

---

### 4. Reports & Analytics

**Purpose:** Generate insights and track business performance

**Key Features:**

- Monthly revenue breakdown
- Total orders tracking
- Revenue growth calculations
- Year-based filtering
- Chart visualizations (Chart.js)
- Export capabilities

**Report Types:**

- Monthly Revenue Reports
- Order Statistics
- Payment Status Reports
- Inventory Reports

---

### 5. Payment Processing

**Purpose:** Track and manage payment transactions

**Payment Statuses:**

- `unpaid` - No payment received
- `partial` - Partial payment received
- `paid` - Full payment received
- `paid_full_cash` - Paid in full with cash
- `paid_on_delivery_cod` - Cash on delivery

**Process Flow:**

```
Booking Created → Payment Status: Unpaid
        ↓
Customer Makes Payment
        ↓
Admin Updates Payment Status
        ↓
Payment Recorded in Database
        ↓
Booking Status Updated (if applicable)
```

---

### 6. Booking Number Assignment

**Purpose:** Automatically assign unique booking numbers to approved reservations

**Features:**

- Automatic assignment upon approval
- Queue position tracking
- Customer notification
- Booking number management

**Process:**

```
Admin Accepts Reservation
        ↓
System Finds Next Available Booking Number
        ↓
Booking Number Assigned to Booking
        ↓
Queue Position Calculated
        ↓
Customer Notified via Email
```

---

## 🔄 Key Processes & Workflows

### Process 1: Customer Booking Workflow

```
1. Customer Registration/Login
   ↓
2. Browse Services Catalog
   ↓
3. Select Service
   ↓
4. Fill Booking Form:
   - Service details
   - Item description
   - Pickup/delivery preferences
   - Special instructions
   ↓
5. Submit Booking Request
   ↓
6. Booking Status: Pending
   ↓
7. ⭐ EMAIL #1 SENT IMMEDIATELY:
   - Booking Number (Queue Number) assigned
   - Note: "Admin already received your reservation and will review it."
   - NO price or total amount included
   ↓
8. Admin Reviews Booking
   ↓
9. Admin Decision:
   ├─ Accept → Status: Approved
   └─ Reject → Status: Cancelled
   ↓
10. ⭐ EMAIL #2 SENT AFTER APPROVAL:
    - Updated booking details
    - Complete pricing breakdown:
      * Base Service Price
      * Labor Fee
      * Pickup/Delivery Fees
      * Gas/Travel Fees
      * Color/Material Price
      * TOTAL AMOUNT (Grand Total)
   ↓
11. Work Begins → Status: In Progress
   ↓
12. Service Completed → Status: Completed
   ↓
13. Payment Processed
   ↓
14. Booking Closed
```

---

### Process 2: Inventory Management Workflow

```
1. Admin Navigates to Inventory Page
   ↓
2. Clicks "Add Leather Stock" Button
   ↓
3. Modal Opens with Auto-Generated Color Code
   ↓
4. Admin Fills Form:
   - Color Name (required)
   - Color Preview (hex picker)
   - Leather Type (required)
   - Quantity (required)
   - Standard Price (required)
   - Premium Price (required)
   ↓
5. Form Validation
   ↓
6. AJAX POST Request to admin/createInventory
   ↓
7. Backend Validates Data
   ↓
8. Database Column Detection:
   - Checks for fabric_type or leather_type
   - Checks for price_per_unit or standard_price
   ↓
9. Data Inserted into Database
   ↓
10. Success Response
   ↓
11. Inventory Table Refreshed
   ↓
12. Summary Cards Updated:
    - Total Colors
    - Total Stock
    - Low Stock Count
```

---

### Process 3: Admin Approval Workflow

```
1. Admin Views Pending Bookings
   ↓
2. Clicks "Accept Reservation" Button
   ↓
3. Modal Opens with Booking Details
   ↓
4. Admin Reviews:
   - Customer Information
   - Service Details
   - Item Description
   - Special Instructions
   ↓
5. Admin Optionally Adds Notes
   ↓
6. Admin Clicks "Accept Reservation"
   ↓
7. System Processes:
   - Updates Booking Status to "Approved"
   - Queue Number already assigned (from initial booking)
   - Calculates Queue Position
   ↓
8. ⭐ APPROVAL EMAIL SENT TO CUSTOMER:
   - Updated booking details
   - Complete pricing breakdown:
     * Base Service Price
     * Labor Fee
     * Pickup/Delivery Fees
     * Gas/Travel Fees
     * Color/Material Price
     * TOTAL AMOUNT (Grand Total)
   - Status: Approved - Ready for Repair
   ↓
9. Booking Appears in "Approved" Section
   ↓
10. Customer Can View Updated Status with Pricing
```

---

### Process 4: Payment Processing Workflow

```
1. Admin Views Booking Details
   ↓
2. Admin Updates Payment Status:
   - Unpaid → Partial
   - Partial → Paid
   - Unpaid → Paid (Full)
   ↓
3. Payment Amount Recorded
   ↓
4. Payment Transaction Logged
   ↓
5. Booking Status Updated (if payment complete)
   ↓
6. Customer Notified (if applicable)
   ↓
7. Reports Updated with Payment Data
```

---

## 💾 Database Structure

### Core Tables

#### 1. `users`

Stores all user accounts (customers and admins)

**Key Columns:**

- `id` - Primary key
- `username` - Unique username
- `email` - Unique email
- `password` - Hashed password
- `fullname` - Full name
- `role` - 'admin' or 'customer'
- `status` - 'active' or 'inactive'
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp

---

#### 2. `bookings`

Stores all service bookings and reservations

**Key Columns:**

- `id` - Primary key
- `user_id` - Foreign key to users
- `service_id` - Foreign key to services
- `booking_number_id` - Foreign key to booking_numbers
- `selected_color_id` - Foreign key to inventory (optional)
- `booking_date` - Scheduled booking date
- `total_amount` - Total service cost
- `status` - pending, approved, in_progress, completed, cancelled
- `payment_status` - unpaid, partial, paid, paid_full_cash, paid_on_delivery_cod
- `notes` - Additional notes
- `created_at` - Booking creation timestamp
- `updated_at` - Last update timestamp

---

#### 3. `inventory`

Stores leather/fabric inventory items

**Key Columns:**

- `id` - Primary key
- `color_code` - Unique color code (e.g., INV-001)
- `color_name` - Color name
- `color_hex` - Hex color code
- `fabric_type` or `leather_type` - Type of material
- `price_per_unit` or `standard_price` - Standard price
- `premium_price` - Premium tier price
- `quantity` - Available quantity in rolls
- `status` - in-stock, low-stock, out-of-stock (auto-calculated)
- `store_location_id` - Foreign key to store_locations (optional)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

---

#### 4. `services`

Stores available services

**Key Columns:**

- `id` - Primary key
- `service_name` - Service name
- `category_id` - Foreign key to service_categories
- `service_type` - Type classification
- `description` - Service description
- `price` - Base price
- `status` - active or inactive
- `created_at` - Creation timestamp

---

#### 5. `booking_numbers`

Stores available booking numbers for assignment

**Key Columns:**

- `id` - Primary key
- `booking_number` - Unique booking number
- `status` - available, assigned, reserved
- `assigned_to_booking_id` - Foreign key to bookings
- `created_at` - Creation timestamp

---

#### 6. `payments`

Stores payment transactions

**Key Columns:**

- `id` - Primary key
- `booking_id` - Foreign key to bookings
- `amount` - Payment amount
- `payment_method` - Payment method used
- `payment_date` - Payment date
- `created_at` - Transaction timestamp

---

### Additional Tables

- `service_categories` - Service category definitions
- `store_locations` - Physical store locations
- `notifications` - User notifications
- `quotations` - Service quotations
- `repair_items` - Items being repaired
- `repair_quotations` - Repair quotes
- `admin_repair_stats` - Admin statistics
- `control_panel_admins` - Super admin accounts
- `admin_registrations` - Admin registration requests
- `login_logs` - Login attempt tracking

---

## 🛠️ Technical Stack

### Backend

- **Language:** PHP 7.4+
- **Framework:** Custom MVC Framework
- **Database:** MySQL/MariaDB
- **Server:** Apache (XAMPP)

### Frontend

- **HTML5/CSS3**
- **JavaScript (ES6+)**
- **Bootstrap 4** - UI Framework
- **jQuery** - DOM manipulation and AJAX
- **Chart.js v3** - Data visualization
- **DataTables** - Table enhancements
- **Font Awesome** - Icons

### Libraries & Tools

- **PHPMailer** - Email sending
- **Leaflet.js** - Maps (OpenStreetMap)
- **Bootstrap Modals** - Dialog boxes
- **AJAX/Fetch API** - Asynchronous requests

### Development Environment

- **XAMPP** - Local development server
- **phpMyAdmin** - Database management
- **Git** - Version control

---

## 🔌 API Endpoints

### Admin Endpoints

#### Inventory Management

```
GET  /admin/getInventory          - Get all inventory items
POST /admin/createInventory       - Create new inventory item
POST /admin/updateInventory       - Update inventory item
POST /admin/deleteInventory       - Delete inventory item
```

#### Booking Management

```
GET  /admin/getBookingDetails/{id} - Get booking details
POST /admin/acceptReservation     - Accept a reservation
POST /admin/updateBooking         - Update booking status
POST /admin/updatePaymentStatus   - Update payment status
```

#### Services Management

```
GET  /admin/services              - View all services
POST /admin/createService         - Create new service
POST /admin/updateService         - Update service
POST /admin/deleteService         - Delete service
```

#### Reports

```
GET  /admin/reports               - View reports dashboard
GET  /admin/reports/{year}        - View reports for specific year
```

### Customer Endpoints

```
GET  /customer/dashboard          - Customer dashboard
GET  /customer/bookings           - View customer bookings
POST /customer/createBooking     - Create new booking
GET  /customer/getBookingDetails/{id} - Get booking details
```

### Authentication Endpoints

```
GET  /auth/login                 - Login page
POST /auth/login                 - Process login
GET  /auth/register              - Registration page
POST /auth/register              - Process registration
GET  /auth/logout                - Logout
```

---

## 📦 Installation & Setup

### Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Edge)

### Step 1: Install XAMPP

1. Download and install XAMPP
2. Start Apache and MySQL services

### Step 2: Database Setup

1. Open phpMyAdmin (`http://localhost/phpmyadmin`)
2. Create database: `db_upholcare`
3. Import SQL file: `db_upholcare.sql`
4. Or run individual SQL scripts from `database/` folder

### Step 3: Configuration

1. Edit `config/config.php`:

   ```php
   define('BASE_URL', 'http://localhost/UphoCare/');
   define('DB_NAME', 'db_upholcare');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

2. Edit `config/database.php` if needed

### Step 4: Email Configuration (Optional)

1. Edit `config/email.php`
2. Configure SMTP settings for email notifications

### Step 5: Access Application

1. Open browser: `http://localhost/UphoCare`
2. Register as customer or admin
3. Login and start using the system

---

## 🔧 Key Technical Features

### 1. Auto-Incrementing Color Codes

- **Location:** `views/admin/inventory.php`
- **Function:** `generateNextColorCode()`
- **Format:** INV-001, INV-002, INV-003, etc.
- **Logic:** Finds highest existing number and increments

### 2. Dynamic Database Column Detection

- **Location:** `models/Inventory.php`
- **Purpose:** Handles different database schemas
- **Checks for:**
  - `fabric_type` or `leather_type`
  - `price_per_unit` or `standard_price`
- **Builds SQL dynamically based on available columns**

### 3. Modal Clickability Fix

- **Issue:** Modals being blocked by overlays
- **Solution:**
  - Backdrop with `pointer-events: none`
  - Modal content with `pointer-events: auto`
  - Proper z-index hierarchy
  - JavaScript monitoring and fixing

### 4. Responsive Design

- **Mobile-first approach**
- **Breakpoints:**
  - Mobile: < 576px
  - Tablet: 576px - 991px
  - Desktop: > 992px
- **Features:**
  - Touch-friendly buttons
  - Stacked layouts on mobile
  - Collapsible sidebar
  - Responsive tables

### 5. AJAX-Based Operations

- **Inventory loading:** `loadInventoryFromDatabase()`
- **Form submissions:** Fetch API with FormData
- **Real-time updates:** No page refresh required
- **Error handling:** Comprehensive try-catch blocks

---

## 🐛 Troubleshooting

### Common Issues

#### 1. Modal Not Clickable

**Symptoms:** Can't interact with form fields in modal

**Solutions:**

- Check browser console for z-index conflicts
- Ensure backdrop has `pointer-events: none`
- Verify modal content has `pointer-events: auto`
- Hard refresh page (Ctrl+Shift+R)

#### 2. Inventory Not Loading

**Symptoms:** Empty inventory table, console errors

**Solutions:**

- Check if `getInventory` endpoint exists
- Verify database connection
- Check if `inventory` table exists
- Review browser console for errors
- Check network tab for failed requests

#### 3. Database Column Errors

**Symptoms:** "Column not found" errors

**Solutions:**

- Run database migration scripts
- Check column names match code expectations
- Use dynamic column detection (already implemented)
- Verify table structure in phpMyAdmin

#### 4. Color Code Not Auto-Generating

**Symptoms:** Color code field is empty

**Solutions:**

- Check if `generateNextColorCode()` function exists
- Verify modal open event triggers code generation
- Check browser console for JavaScript errors
- Ensure inventory data is loaded before generation

#### 5. POST Request Returns GET Error

**Symptoms:** "Method not allowed. Expected POST, got GET"

**Solutions:**

- Verify fetch request uses `method: 'POST'`
- Check for form submission interference
- Add `redirect: 'manual'` to fetch options
- Ensure no redirects are changing the method

---

## 📊 System Statistics

### Current Capabilities

- ✅ Multi-user role system (Customer, Admin, Super Admin)
- ✅ Booking management with workflow
- ✅ Inventory management with auto-increment
- ✅ Payment processing
- ✅ Email notifications
- ✅ Reports and analytics
- ✅ Mobile responsive design
- ✅ Real-time updates via AJAX
- ✅ Secure authentication
- ✅ Activity logging

### Database

- **Total Tables:** 19+
- **Main Tables:** 14
- **Admin Tables:** 5+
- **Database Name:** `db_upholcare`

### Technology Versions

- PHP: 7.4+
- MySQL: 5.7+
- Bootstrap: 4.x
- jQuery: 3.x
- Chart.js: 3.9.1

---

## 🔐 Security Features

### Authentication

- Password hashing (bcrypt)
- Session management
- Role-based access control
- Login attempt logging
- Account status verification

### Data Protection

- SQL injection prevention (prepared statements)
- XSS protection (HTML escaping)
- CSRF protection (session tokens)
- Input validation
- Output sanitization

### Access Control

- Role verification on every request
- Admin-only endpoints protection
- Customer data isolation
- Super admin approval system

---

## 📝 Development Notes

### Code Organization

- **MVC Pattern:** Controllers, Models, Views separated
- **DRY Principle:** Reusable components
- **Error Handling:** Try-catch blocks with logging
- **Code Comments:** Comprehensive documentation

### Best Practices

- ✅ Prepared statements for database queries
- ✅ Output buffering for JSON responses
- ✅ Error logging for debugging
- ✅ Input validation on both client and server
- ✅ Responsive design principles
- ✅ Accessibility considerations

---

## 🚀 Future Enhancements

### Potential Features

- [ ] Mobile app integration
- [ ] SMS notifications
- [ ] Advanced reporting with PDF export
- [ ] Multi-store support
- [ ] Customer portal enhancements
- [ ] Inventory barcode scanning
- [ ] Automated reorder alerts
- [ ] Integration with payment gateways

---

## 📞 Support & Maintenance

### Log Files

- Email logs: `logs/email_notifications.log`
- PHP error logs: XAMPP error logs
- Browser console: F12 Developer Tools

### Debugging

1. Enable `DEBUG_MODE` in `config/config.php`
2. Check browser console (F12)
3. Review network tab for failed requests
4. Check PHP error logs
5. Verify database connectivity

---

## 📄 License & Credits

**System Name:** UphoCare  
**Version:** 1.0  
**Framework:** Custom PHP MVC  
**UI Framework:** Bootstrap SB Admin 2  
**Icons:** Font Awesome  
**Charts:** Chart.js

---

## 📚 Additional Documentation

- `DATABASE_STRUCTURE_AND_EMAILS.md` - Database details
- `LOGIN_GUIDE.md` - Authentication guide
- `CONTROL_PANEL_README.md` - Super admin guide
- `LOCALHOST_SETUP.md` - Setup instructions
- `SECURITY_FEATURES.md` - Security documentation

---

**Last Updated:** December 2, 2025  
**Documentation Version:** 1.0
