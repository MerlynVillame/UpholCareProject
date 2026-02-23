# Control Panel Database Structure

## Overview

All Control Panel tables are created in the **db_upholcare** database, keeping everything centralized in one database for easier management.

---

## Database: `db_upholcare`

### Control Panel Tables (5 tables)

#### 1. `control_panel_admins`
**Purpose**: Store control panel administrator accounts (separate from regular admins)

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary key, auto-increment |
| `email` | VARCHAR(191) | Admin email address (unique) |
| `password` | VARCHAR(255) | Hashed password (bcrypt) |
| `fullname` | VARCHAR(100) | Full name of admin |
| `status` | ENUM | 'active' or 'inactive' |
| `last_login` | TIMESTAMP | Last successful login time |
| `created_at` | TIMESTAMP | Account creation time |
| `updated_at` | TIMESTAMP | Last update time |

**Indexes**: 
- PRIMARY KEY on `id`
- UNIQUE KEY on `email`
- INDEX on `status`

---

#### 2. `login_logs`
**Purpose**: Record all login attempts across the system

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary key, auto-increment |
| `user_id` | INT (NULL) | User ID from users table |
| `user_type` | ENUM | 'customer', 'admin', 'control_panel' |
| `email` | VARCHAR(191) | Email used for login attempt |
| `fullname` | VARCHAR(100) | Full name (if available) |
| `ip_address` | VARCHAR(45) | IP address of login attempt |
| `user_agent` | TEXT | Browser/device information |
| `login_status` | ENUM | 'success' or 'failed' |
| `failure_reason` | VARCHAR(255) | Reason for failure (if applicable) |
| `login_time` | TIMESTAMP | When the login occurred |

**Indexes**:
- PRIMARY KEY on `id`
- INDEX on `user_id`
- INDEX on `user_type`
- INDEX on `login_status`
- INDEX on `login_time`
- INDEX on `email`
- COMPOSITE INDEX on `user_type, login_status`

**Sample Query**:
```sql
-- Get today's failed login attempts
SELECT * FROM login_logs 
WHERE login_status = 'failed' 
AND DATE(login_time) = CURDATE()
ORDER BY login_time DESC;
```

---

#### 3. `system_activities`
**Purpose**: Track system activities and admin actions for audit trail

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary key, auto-increment |
| `admin_id` | INT (NULL) | Admin who performed the action |
| `activity_type` | ENUM | Type: 'user_created', 'user_modified', 'user_deleted', 'booking_modified', 'settings_changed', 'other' |
| `description` | TEXT | Human-readable description |
| `affected_table` | VARCHAR(100) | Database table affected |
| `affected_record_id` | INT | Record ID affected |
| `old_value` | TEXT | Previous value (JSON) |
| `new_value` | TEXT | New value (JSON) |
| `ip_address` | VARCHAR(45) | IP address of admin |
| `created_at` | TIMESTAMP | When action occurred |

**Indexes**:
- PRIMARY KEY on `id`
- INDEX on `admin_id`
- INDEX on `activity_type`
- INDEX on `created_at`

**Sample Query**:
```sql
-- Get all user modifications in last 7 days
SELECT * FROM system_activities 
WHERE activity_type = 'user_modified' 
AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC;
```

---

#### 4. `control_panel_sessions`
**Purpose**: Track active control panel sessions for security monitoring

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary key, auto-increment |
| `admin_id` | INT (FK) | References control_panel_admins.id |
| `session_id` | VARCHAR(191) | Unique session identifier |
| `ip_address` | VARCHAR(45) | Session IP address |
| `user_agent` | TEXT | Browser/device information |
| `last_activity` | TIMESTAMP | Last activity timestamp |
| `created_at` | TIMESTAMP | Session start time |
| `expires_at` | TIMESTAMP | Session expiration time |

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `session_id`
- INDEX on `admin_id`
- INDEX on `last_activity`
- FOREIGN KEY `admin_id` → `control_panel_admins(id)` ON DELETE CASCADE

**Sample Query**:
```sql
-- Get active sessions (within last 30 minutes)
SELECT * FROM control_panel_sessions 
WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
ORDER BY last_activity DESC;
```

---

#### 5. `system_statistics`
**Purpose**: Store daily/weekly/monthly statistics summaries

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary key, auto-increment |
| `stat_date` | DATE (UNIQUE) | Date of statistics |
| `total_logins` | INT | Total login attempts |
| `successful_logins` | INT | Successful logins |
| `failed_logins` | INT | Failed logins |
| `customer_logins` | INT | Customer logins |
| `admin_logins` | INT | Admin logins |
| `unique_users` | INT | Unique users who logged in |
| `new_users` | INT | New user registrations |
| `new_bookings` | INT | New bookings created |
| `completed_bookings` | INT | Bookings completed |
| `created_at` | TIMESTAMP | When stats were generated |

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `stat_date`
- INDEX on `stat_date`

**Sample Query**:
```sql
-- Get last 30 days statistics
SELECT * FROM system_statistics 
WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY stat_date DESC;
```

---

## Database Relationships

```
db_upholcare
├── users (existing)
├── bookings (existing)
├── services (existing)
├── notifications (existing)
│
└── Control Panel Tables:
    ├── control_panel_admins (standalone)
    │
    ├── login_logs
    │   └── user_id → users.id (soft reference)
    │
    ├── system_activities
    │   └── admin_id (soft reference to admins)
    │
    ├── control_panel_sessions
    │   └── admin_id → control_panel_admins.id (FK)
    │
    └── system_statistics (standalone)
```

---

## Table Sizes Estimate

| Table | Initial Size | After 1 Year* |
|-------|-------------|---------------|
| control_panel_admins | ~1 KB | ~5 KB |
| login_logs | ~1 KB | ~50 MB |
| system_activities | ~1 KB | ~20 MB |
| control_panel_sessions | ~1 KB | ~10 MB |
| system_statistics | ~1 KB | ~100 KB |

*Estimates based on moderate usage (100 logins/day)

---

## Maintenance Queries

### Clean Old Login Logs (Keep last 6 months)
```sql
DELETE FROM login_logs 
WHERE login_time < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### Clean Expired Sessions
```sql
DELETE FROM control_panel_sessions 
WHERE expires_at < NOW() OR last_activity < DATE_SUB(NOW(), INTERVAL 1 DAY);
```

### Generate Daily Statistics
```sql
INSERT INTO system_statistics (
    stat_date, total_logins, successful_logins, failed_logins,
    customer_logins, admin_logins
)
SELECT 
    CURDATE(),
    COUNT(*),
    SUM(CASE WHEN login_status = 'success' THEN 1 ELSE 0 END),
    SUM(CASE WHEN login_status = 'failed' THEN 1 ELSE 0 END),
    SUM(CASE WHEN user_type = 'customer' THEN 1 ELSE 0 END),
    SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END)
FROM login_logs 
WHERE DATE(login_time) = CURDATE()
ON DUPLICATE KEY UPDATE
    total_logins = VALUES(total_logins),
    successful_logins = VALUES(successful_logins),
    failed_logins = VALUES(failed_logins),
    customer_logins = VALUES(customer_logins),
    admin_logins = VALUES(admin_logins);
```

---

## Backup Strategy

### Full Backup
```bash
mysqldump -u root db_upholcare > db_upholcare_backup.sql
```

### Control Panel Tables Only
```bash
mysqldump -u root db_upholcare \
    control_panel_admins \
    login_logs \
    system_activities \
    control_panel_sessions \
    system_statistics \
    > control_panel_backup.sql
```

---

## Security Considerations

1. **Passwords**: All passwords in `control_panel_admins` use bcrypt hashing
2. **Soft References**: `login_logs.user_id` doesn't have FK to allow logging even if user is deleted
3. **Session Management**: Old sessions auto-deleted via cleanup script
4. **IP Tracking**: All activities track IP addresses for security auditing
5. **Audit Trail**: `system_activities` provides complete audit log

---

## Quick Access Queries

### Check Control Panel Tables
```sql
USE db_upholcare;
SHOW TABLES LIKE '%control_panel%';
SHOW TABLES LIKE 'login_logs';
SHOW TABLES LIKE 'system_%';
```

### View All Control Panel Admins
```sql
SELECT id, email, fullname, status, last_login, created_at 
FROM control_panel_admins 
ORDER BY created_at DESC;
```

### Today's Login Summary
```sql
SELECT 
    user_type,
    login_status,
    COUNT(*) as count
FROM login_logs 
WHERE DATE(login_time) = CURDATE()
GROUP BY user_type, login_status;
```

### Failed Login Attempts (Last 24 Hours)
```sql
SELECT 
    email,
    user_type,
    failure_reason,
    ip_address,
    login_time
FROM login_logs 
WHERE login_status = 'failed' 
AND login_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY login_time DESC;
```

---

## Installation Commands

```sql
-- Use the main database
USE db_upholcare;

-- Run the creation script
source C:/xampp/htdocs/UphoCare/database/create_control_panel_tables.sql;

-- Verify tables
SHOW TABLES LIKE '%control%';
SHOW TABLES LIKE 'login_logs';
SHOW TABLES LIKE 'system_%';

-- Check admin account
SELECT * FROM control_panel_admins;
```

---

## Default Credentials

**Email**: `control@uphocare.com`  
**Password**: `Control@2025`

⚠️ **IMPORTANT**: Change this password immediately after first login!

---

## Status

✅ All tables created in `db_upholcare`  
✅ Default admin account configured  
✅ Indexes optimized for performance  
✅ Foreign keys properly set  
✅ Ready for production use  

---

**Last Updated**: November 6, 2025  
**Database**: db_upholcare  
**Total Control Panel Tables**: 5

