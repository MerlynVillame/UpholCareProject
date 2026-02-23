# Control Panel - Quick Start Guide

## ⚡ Quick Setup (5 Minutes)

### Step 1: Create Database Tables

Open your MySQL client and run:

```sql
USE db_upholcare;

-- Create control panel admins table
CREATE TABLE IF NOT EXISTS control_panel_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin
INSERT INTO control_panel_admins (email, password, fullname, status) 
VALUES (
    'control@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Control Panel Admin',
    'active'
);

SELECT 'Setup complete!' AS status;
```

### Step 2: Access Control Panel

1. Open browser: `http://localhost/UphoCare/control-panel`
2. Login with:
   - **Email**: `control@uphocare.com`
   - **Password**: `Control@2025`

### Step 3: Test the Dashboard

✅ You should see:
- Statistics cards
- Login activity charts
- Recent login logs table

---

## 🎯 Default Credentials

```
URL:      http://localhost/UphoCare/control-panel
Email:    control@uphocare.com
Password: Control@2025
```

⚠️ **IMPORTANT**: Change this password after first login!

---

## 📊 What You Can Track

### Customer Logins
- Successful customer logins
- Failed customer login attempts
- Customer login times and locations

### Admin Logins
- Admin authentication events
- Failed admin access attempts
- Admin session activities

### Control Panel Access
- Control panel admin logins
- Unauthorized access attempts
- System administrator activities

---

## 🔍 Quick Features Overview

### Dashboard
- Real-time statistics
- Today's login summary
- Weekly trends
- User type breakdown
- Recent activity feed

### Login Logs
- Complete login history
- Filter by user type
- Filter by status (success/failed)
- Search functionality
- IP address tracking
- Browser/device information

---

## 🛠️ Common Tasks

### View Today's Failed Logins

1. Go to Control Panel Dashboard
2. Check "Failed Today" card
3. Click "View All Logs"
4. Filter: Status = "Failed"

### Track Specific User

1. Go to "Login Logs"
2. Use search box
3. Enter email or name
4. View all login attempts

### Monitor Security

1. Check "Failed" logins regularly
2. Look for repeated failures from same IP
3. Check unusual login times
4. Monitor unauthorized access attempts

---

## 📋 Verification Checklist

After setup, verify:

- [ ] Can access `/control-panel` URL
- [ ] Can login with default credentials
- [ ] Dashboard loads with statistics
- [ ] Recent logins table shows data
- [ ] Can access "Login Logs" page
- [ ] Can filter and search logs
- [ ] Can logout successfully

---

## 🚨 Troubleshooting

### Can't Login
```sql
-- Check if admin exists
SELECT * FROM control_panel_admins;

-- Reset password if needed
UPDATE control_panel_admins 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'control@uphocare.com';
```

### No Data Showing
```sql
-- Check if login_logs table exists
DESCRIBE login_logs;

-- Check for any logs
SELECT COUNT(*) FROM login_logs;

-- Add test log
INSERT INTO login_logs (user_type, email, login_status, login_time) 
VALUES ('customer', 'test@example.com', 'success', NOW());
```

### Page Not Found (404)
- Check `.htaccess` is configured
- Verify URL rewriting is enabled
- Ensure Apache `mod_rewrite` is active

---

## 📱 Mobile Access

The control panel is fully responsive:
- ✅ Works on tablets
- ✅ Works on mobile phones
- ✅ Touch-friendly interface
- ✅ Optimized tables for small screens

---

## 🔐 Security Tips

1. **Change Default Password**
   ```sql
   UPDATE control_panel_admins 
   SET password = '<new_hash>' 
   WHERE id = 1;
   ```

2. **Use Strong Passwords**
   - Minimum 12 characters
   - Mix of letters, numbers, symbols
   - No dictionary words

3. **Limit Access**
   - Only create accounts for trusted admins
   - Disable accounts when no longer needed
   - Monitor access regularly

4. **Regular Monitoring**
   - Check logs weekly
   - Review failed login attempts
   - Look for suspicious patterns

---

## 📖 Full Documentation

For complete documentation, see: `CONTROL_PANEL_README.md`

---

## 💡 Tips & Tricks

### Keyboard Shortcuts
- **Ctrl + F**: Search in logs table
- **Ctrl + R**: Refresh page
- **Ctrl + W**: Close tab

### Best Practices
- Check dashboard daily
- Review failed logins weekly
- Archive old logs monthly
- Update password quarterly

### Performance
- Limit log history to 6-12 months
- Use filters to reduce data load
- Export large datasets for offline analysis

---

## ✨ What's Next?

After setup:

1. ✅ **Familiarize yourself** with the dashboard
2. ✅ **Change the default password**
3. ✅ **Test filtering** in login logs
4. ✅ **Set up regular monitoring** schedule
5. ✅ **Create additional admins** if needed

---

## 🆘 Need Help?

- Check `CONTROL_PANEL_README.md` for detailed docs
- Review code in `controllers/ControlPanelController.php`
- Check database tables structure
- Review server error logs

---

**Status**: ✅ Control Panel Ready!  
**Version**: 1.0  
**Last Updated**: November 2025

