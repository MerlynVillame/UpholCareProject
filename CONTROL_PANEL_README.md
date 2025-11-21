# Control Panel Documentation

## Overview

The Control Panel is a super admin dashboard for monitoring and tracking all login activities across the UphoCare system. It provides centralized visibility into customer and admin login attempts, both successful and failed.

## Features

✅ **Secure Access** - Separate authentication system for control panel admins  
✅ **Login Tracking** - Monitors all login attempts (customers, admins, control panel)  
✅ **Real-time Statistics** - Dashboard with login metrics by day, week, and user type  
✅ **Detailed Logs** - View complete login history with IP addresses and user agents  
✅ **Advanced Filtering** - Filter logs by user type, status, and date ranges  
✅ **Security Monitoring** - Track failed login attempts and potential security issues

## Access Credentials

**Control Panel URL**: `http://localhost/UphoCare/control-panel`

### Default Login Credentials

```
Email: control@uphocare.com
Password: Control@2025
```

⚠️ **IMPORTANT**: Change the default password immediately after first login for security!

## Database Tables

### 1. `control_panel_admins`

Stores control panel administrator accounts (separate from regular admins).

| Column     | Type      | Description                |
| ---------- | --------- | -------------------------- |
| id         | INT       | Primary key                |
| email      | VARCHAR   | Admin email (unique)       |
| password   | VARCHAR   | Hashed password            |
| fullname   | VARCHAR   | Full name                  |
| status     | ENUM      | 'active' or 'inactive'     |
| last_login | TIMESTAMP | Last login timestamp       |
| created_at | TIMESTAMP | Account creation timestamp |

### 2. `login_logs`

Records all login attempts across the system.

| Column         | Type      | Description                          |
| -------------- | --------- | ------------------------------------ |
| id             | INT       | Primary key                          |
| user_id        | INT       | User ID (nullable)                   |
| user_type      | ENUM      | 'customer', 'admin', 'control_panel' |
| email          | VARCHAR   | Email used for login                 |
| fullname       | VARCHAR   | Full name of user                    |
| ip_address     | VARCHAR   | IP address of login attempt          |
| user_agent     | TEXT      | Browser/device user agent            |
| login_status   | ENUM      | 'success' or 'failed'                |
| failure_reason | VARCHAR   | Reason for failure (if applicable)   |
| login_time     | TIMESTAMP | When the login attempt occurred      |

## Installation

### Step 1: Create Database Tables

Run the SQL script to create the necessary tables:

```bash
mysql -u root db_upholcare < database/create_control_panel_tables.sql
```

Or run these commands directly:

```sql
-- Create control panel admins table
CREATE TABLE IF NOT EXISTS control_panel_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create login logs table
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    user_type ENUM('customer', 'admin', 'control_panel') NOT NULL,
    email VARCHAR(191) NOT NULL,
    fullname VARCHAR(100) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    login_status ENUM('success', 'failed') NOT NULL,
    failure_reason VARCHAR(255) NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_user_type (user_type),
    INDEX idx_login_status (login_status),
    INDEX idx_login_time (login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: Create Default Admin Account

Run the insert SQL to create the default control panel admin:

```sql
INSERT INTO control_panel_admins (email, password, fullname, status)
VALUES (
    'control@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Control Panel Admin',
    'active'
);
```

### Step 3: Access the Control Panel

Navigate to: `http://localhost/UphoCare/control-panel`

Login with:

- Email: `control@uphocare.com`
- Password: `Control@2025`

## File Structure

```
UphoCare/
├── controllers/
│   └── ControlPanelController.php    # Main controller for control panel
├── views/
│   └── control_panel/
│       ├── login.php                 # Login page
│       ├── dashboard.php             # Main dashboard
│       └── login_logs.php            # Detailed logs page
├── helpers/
│   └── LoginLogger.php               # Login logging utility
└── database/
    ├── create_control_panel_tables.sql
    ├── insert_control_admin.sql
    └── setup_control_panel_admin.php
```

## Usage

### Dashboard

The dashboard provides an overview of login activities:

- **Today's Statistics**: Total, successful, and failed logins today
- **Weekly Statistics**: Login metrics for the current week
- **By User Type**: Breakdown of login activities by user type
- **Recent Activity**: Latest 50 login attempts with details

### Login Logs

The login logs page offers advanced filtering:

- **Filter by User Type**: Customer, Admin, or Control Panel
- **Filter by Status**: All, Successful, or Failed
- **Limit Results**: Choose 50, 100, 250, 500, or 1000 records
- **Search**: DataTables search across all columns
- **Export**: Export logs for analysis

### Information Tracked

For each login attempt, the system records:

- User ID (if user exists)
- User type (customer/admin/control panel)
- Email address
- Full name
- IP address
- Browser/device information
- Success or failure status
- Failure reason (if applicable)
- Exact timestamp

## Integration with Existing Authentication

The `LoginLogger` helper class makes it easy to add login tracking to existing controllers:

```php
// In your authentication controller

// For successful login
LoginLogger::logSuccess(
    $userId,
    'customer',  // or 'admin'
    $email,
    $fullname
);

// For failed login
LoginLogger::logFailure(
    null,  // or $userId if user was found
    'customer',
    $email,
    null,  // or $fullname if available
    'Invalid password'  // reason for failure
);
```

## Security Features

1. **Separate Authentication**: Control panel has its own authentication system
2. **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
3. **Session Management**: Separate session variable for control panel
4. **IP Tracking**: Monitors IP addresses for suspicious activity
5. **Failed Login Monitoring**: Track potential brute force attempts
6. **Secure Access**: Hidden URL not exposed to regular users

## Adding New Control Panel Admins

To add a new control panel admin:

```php
<?php
// Create a temporary PHP file or use MySQL
$email = 'newadmin@uphocare.com';
$password = password_hash('SecurePassword123', PASSWORD_DEFAULT);
$fullname = 'New Admin Name';

// Execute this SQL
$sql = "INSERT INTO control_panel_admins (email, password, fullname, status)
        VALUES ('$email', '$password', '$fullname', 'active')";
```

Or via MySQL command line:

```sql
INSERT INTO control_panel_admins (email, password, fullname, status)
VALUES (
    'newadmin@uphocare.com',
    PASSWORD('SecurePassword123'),  -- Use PHP password_hash() instead
    'New Admin Name',
    'active'
);
```

## Changing Password

To change the control panel admin password:

```php
<?php
$newPassword = password_hash('NewSecurePassword', PASSWORD_DEFAULT);
$adminId = 1;

// Execute this SQL
$sql = "UPDATE control_panel_admins SET password = '$newPassword' WHERE id = $adminId";
```

## Troubleshooting

### Cannot Access Control Panel

1. **Check URL**: Ensure you're using `/control-panel` (with hyphen)
2. **Check Tables**: Verify tables exist:

   ```sql
   SHOW TABLES LIKE 'control_panel_admins';
   SHOW TABLES LIKE 'login_logs';
   ```

3. **Check Admin Account**: Verify admin exists:
   ```sql
   SELECT * FROM control_panel_admins;
   ```

### Login Tracking Not Working

1. **Check LoginLogger**: Ensure `helpers/LoginLogger.php` exists
2. **Check Database**: Verify `login_logs` table exists
3. **Check Permissions**: Ensure database user has INSERT permissions

### Password Not Working

The default password hash may need to be regenerated. Run this PHP script:

```php
<?php
echo password_hash('Control@2025', PASSWORD_DEFAULT);
```

Then update the database with the new hash.

## Best Practices

1. **Change Default Password**: Immediately change after first login
2. **Limit Access**: Only provide access to trusted administrators
3. **Monitor Regularly**: Check logs weekly for suspicious activity
4. **Backup Data**: Regular backups of login_logs table
5. **Clean Old Logs**: Archive or delete logs older than 6-12 months
6. **Secure Server**: Use HTTPS in production environment
7. **Strong Passwords**: Use complex passwords with special characters

## Future Enhancements

Potential features to add:

- [ ] Email alerts for suspicious login patterns
- [ ] Two-factor authentication for control panel
- [ ] Export logs to CSV/PDF
- [ ] Geolocation of IP addresses
- [ ] Login attempt rate limiting
- [ ] Real-time dashboard updates with websockets
- [ ] User lockout after failed attempts
- [ ] Session management and active session monitoring

## Support

For issues or questions about the control panel:

1. Check this README
2. Review the code in `controllers/ControlPanelController.php`
3. Check database tables and data
4. Review server error logs

## License

Part of the UphoCare system - all rights reserved.
