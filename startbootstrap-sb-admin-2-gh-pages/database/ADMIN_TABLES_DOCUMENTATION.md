# Admin Tables for UphoCare Email Notification System

## Overview

This script creates only the admin-specific tables needed for the email notification system to work with your existing `db_upholcare` database.

## Admin Tables Created

### 1. `booking_numbers` Table

**Purpose:** Admin-managed booking numbers

```sql
CREATE TABLE `booking_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`)
);
```

- Stores pre-generated booking numbers
- Admin can add more numbers as needed
- Prevents duplicate booking numbers

### 2. `email_logs` Table

**Purpose:** Track all email notifications sent

```sql
CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `customer_email` varchar(100) NOT NULL,
  `email_type` enum('approval','rejection','test') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

- Logs every email sent to customers
- Tracks success/failure status
- Stores error messages for debugging

### 3. `admin_settings` Table

**Purpose:** Store admin configuration settings

```sql
CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

- Stores email configuration settings
- Allows runtime configuration changes
- Includes default settings for email system

### 4. `admin_activity_log` Table

**Purpose:** Track admin actions for audit trail

```sql
CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

- Logs all admin actions (accept/reject reservations)
- Tracks IP address and user agent
- Provides audit trail for security

### 5. `reservation_queue` Table

**Purpose:** Manage pending reservations for admin review

```sql
CREATE TABLE `reservation_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `assigned_admin_id` int(11) DEFAULT NULL,
  `status` enum('pending','in_review','approved','rejected') DEFAULT 'pending',
  `review_notes` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

- Queues pending reservations for admin review
- Allows priority assignment
- Tracks review status and notes

## Database Views Created

### 1. `admin_dashboard_stats` View

**Purpose:** Provides dashboard statistics

- Total bookings count
- Pending bookings count
- Revenue totals
- Customer counts
- Daily email statistics

### 2. `admin_booking_details` View

**Purpose:** Complete booking information for admin

- All booking details
- Customer information
- Service information
- Queue status
- Review notes

## Enhanced Existing Tables

### `bookings` Table Additions

- `booking_number_id` - Links to booking numbers
- `total_amount` - Booking total amount
- `payment_status` - Payment status tracking
- `updated_at` - Last modification timestamp

### `services` Table Additions

- `category_id` - Links to service categories
- Foreign key relationship to service_categories

## Setup Instructions

### 1. Run the Setup Script

```bash
cd database/
php setup_admin_tables.php
```

### 2. Or Run SQL Manually

```bash
mysql -u your_username -p db_upholcare < create_admin_tables.sql
```

### 3. Verify Setup

```sql
-- Check if tables were created
SHOW TABLES LIKE '%admin%';
SHOW TABLES LIKE '%booking_numbers%';
SHOW TABLES LIKE '%email_logs%';

-- Check views
SHOW TABLES LIKE '%admin_dashboard%';
SHOW TABLES LIKE '%admin_booking%';
```

## Default Data Inserted

### Booking Numbers

- 20 pre-generated booking numbers (BKG-20250115-0001 to BKG-20250115-0020)
- Admin can add more as needed

### Admin Settings

- Email enabled/disabled setting
- Test mode setting
- SMTP configuration placeholders
- Email template settings

### Service Categories

- Existing services linked to appropriate categories
- Vehicle Upholstery → Category 1
- Bedding → Category 2
- Furniture → Category 3

## Admin Features Enabled

### Email Notifications

- Automatic approval emails
- Automatic rejection emails
- Email logging and tracking
- Test email functionality

### Reservation Management

- Queue-based reservation review
- Priority assignment
- Review notes and tracking
- Admin activity logging

### Dashboard Statistics

- Real-time booking statistics
- Revenue tracking
- Email delivery statistics
- Customer counts

## Security Features

### Audit Trail

- All admin actions logged
- IP address tracking
- User agent logging
- Timestamp tracking

### Data Integrity

- Foreign key constraints
- Unique constraints
- Proper indexing
- Cascade delete rules

## Maintenance

### Adding More Booking Numbers

```sql
INSERT INTO booking_numbers (booking_number) VALUES ('BKG-20250115-0021');
```

### Updating Email Settings

```sql
UPDATE admin_settings SET setting_value = 'your-smtp-host.com' WHERE setting_key = 'email_smtp_host';
```

### Viewing Email Logs

```sql
SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 10;
```

## Troubleshooting

### Common Issues

1. **Foreign key constraint errors:** Ensure existing data is valid
2. **Permission errors:** Check database user permissions
3. **Duplicate key errors:** Some data may already exist

### Verification Queries

```sql
-- Check table counts
SELECT COUNT(*) as booking_numbers FROM booking_numbers;
SELECT COUNT(*) as email_logs FROM email_logs;
SELECT COUNT(*) as admin_settings FROM admin_settings;

-- Check enhanced bookings table
DESCRIBE bookings;

-- Check foreign key relationships
SELECT * FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'db_upholcare' AND TABLE_NAME = 'bookings';
```

This setup provides a complete admin management system for the email notification functionality while preserving your existing customer data.
