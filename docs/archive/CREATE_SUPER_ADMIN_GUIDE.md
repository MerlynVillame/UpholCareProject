# Super Admin Account Setup Guide

## 🚨 Important Notice

**Super admin registration is intentionally restricted for security reasons.** Super admin accounts must be created directly in the database to prevent unauthorized access.

---

## 📋 Quick Start

### Method 1: Using the SQL Script (Recommended)

1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. Select your **UphoCare** database
3. Click on the **SQL** tab
4. Open the file: `database/create_super_admin.sql`
5. **IMPORTANT:** Edit these lines before running:
   ```sql
   'superadmin@uphocare.com',        -- Change this to your email
   '$2y$10$...',                      -- Change this to your password hash
   'Super Administrator',             -- Change this to your name
   ```
6. Click **Go** to execute the script
7. Done! You can now login at: `http://localhost/UphoCare/control-panel/login`

---

## 🔐 How to Generate a Password Hash

### Option A: Using PHP Command Line (Easiest)

1. Open your terminal/command prompt
2. Navigate to your UphoCare folder:
   ```bash
   cd C:\xampp\htdocs\UphoCare
   ```
3. Run this command (replace `YourPasswordHere` with your desired password):
   ```bash
   php -r "echo password_hash('YourPasswordHere', PASSWORD_DEFAULT);"
   ```
4. Copy the generated hash
5. Paste it in the SQL script

### Option B: Using a PHP File

1. Create a file called `generate_hash.php` in your UphoCare folder
2. Add this code:
   ```php
   <?php
   $password = "YourPasswordHere";  // Change this
   echo password_hash($password, PASSWORD_DEFAULT);
   ?>
   ```
3. Open in browser: `http://localhost/UphoCare/generate_hash.php`
4. Copy the hash shown
5. Delete the file after use (for security)

### Option C: Using Online Generator

1. Visit: https://bcrypt-generator.com/
2. Enter your desired password
3. Copy the generated hash
4. Use it in your SQL script

---

## 📝 Manual Database Entry

If you prefer to enter data directly in phpMyAdmin:

1. Open **phpMyAdmin**
2. Select your **UphoCare** database
3. Click on **control_panel_admins** table
4. Click **Insert** tab
5. Fill in the form:
   - **email**: your-email@example.com
   - **password**: (paste your password hash here)
   - **fullname**: Your Name
   - **role**: super_admin
   - **status**: active
   - **created_at**: CURRENT_TIMESTAMP
   - **updated_at**: CURRENT_TIMESTAMP
6. Click **Go**

---

## ✅ Verify Your Account

Run this query in phpMyAdmin SQL tab:

```sql
SELECT * FROM control_panel_admins WHERE role = 'super_admin';
```

You should see your account listed with:
- Your email
- Password hash (long encrypted string)
- Status: active
- Role: super_admin

---

## 🔑 Forgot Password Feature

Now you have a **Forgot Password** option on the login page:

1. Click "Forgot Password?" on the login page
2. Enter your email address
3. A reset code will be displayed on screen
4. Enter the code and set your new password
5. Done! Login with your new password

**Note:** Reset codes expire in 1 hour for security.

---

## 🎯 Default Test Credentials

For testing purposes, use these credentials:

**Email:** `superadmin@uphocare.com`  
**Password:** `password`

**⚠️ IMPORTANT:** Change this password immediately after first login!

---

## 🔒 Security Best Practices

1. **Never share** your super admin credentials
2. **Use a strong password** (minimum 8 characters, mix of upper/lower case, numbers, symbols)
3. **Change default passwords** immediately
4. **Delete** any password generation files after use
5. **Limit** super admin accounts (1-2 maximum)
6. **Enable** two-factor authentication (if available)
7. **Monitor** login logs regularly in the control panel

---

## 🐛 Troubleshooting

### Problem: "Invalid email or password"

**Solution:**
- Verify email is exactly as entered in database
- Ensure password hash was generated correctly
- Check that status is 'active'
- Check that role is 'super_admin'

### Problem: Can't access control panel

**Solution:**
- Clear browser cache and cookies
- Try incognito/private browsing mode
- Verify database connection
- Check error logs

### Problem: Forgot password not working

**Solution:**
- Ensure reset_code columns exist in database (run the SQL script)
- Check that email matches database exactly
- Reset code expires in 1 hour - request new one if expired

---

## 📞 Need Help?

If you're still having issues:

1. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
2. Check Apache error logs: `C:\xampp\apache\logs\error.log`
3. Verify database table structure
4. Ensure all migrations have run

---

## 🎉 Quick Setup Summary

```bash
# 1. Open phpMyAdmin
# 2. Select UphoCare database
# 3. Run this SQL:

ALTER TABLE control_panel_admins 
ADD COLUMN IF NOT EXISTS reset_code VARCHAR(10) NULL,
ADD COLUMN IF NOT EXISTS reset_code_expires DATETIME NULL;

INSERT INTO control_panel_admins (email, password, fullname, role, status, created_at)
VALUES (
    'admin@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System Administrator',
    'super_admin',
    'active',
    NOW()
);

# 4. Login at: http://localhost/UphoCare/control-panel/login
# Email: admin@uphocare.com
# Password: password

# 5. IMPORTANT: Change password immediately using forgot password feature!
```

---

**You're all set!** 🚀

After creating your super admin account, you can:
- Approve admin registrations
- Manage system users
- View login logs
- Access all control panel features
- Use the forgot password feature anytime

