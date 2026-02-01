# 🚀 Super Admin Quick Setup

## ⚡ Fastest Way to Get Started (5 Minutes)

### Step 1: Generate Password Hash
1. Open your browser: `http://localhost/UphoCare/generate_password_hash.php`
2. Enter your desired password
3. Click "Generate Hash"
4. **Copy the hash** shown

### Step 2: Create Super Admin in Database
1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Select **UphoCare** database
3. Click **SQL** tab
4. Paste and run this code:

```sql
-- Add reset password columns
ALTER TABLE control_panel_admins 
ADD COLUMN IF NOT EXISTS reset_code VARCHAR(10) NULL,
ADD COLUMN IF NOT EXISTS reset_code_expires DATETIME NULL;

-- Create super admin (edit email, password hash, and name)
INSERT INTO control_panel_admins (email, password, fullname, role, status, created_at)
VALUES (
    'your-email@example.com',           -- ⬅️ CHANGE THIS
    'PASTE_YOUR_HASH_HERE',             -- ⬅️ PASTE HASH FROM STEP 1
    'Your Name',                        -- ⬅️ CHANGE THIS
    'super_admin',
    'active',
    NOW()
);
```

### Step 3: Login
1. Go to: `http://localhost/UphoCare/control-panel/login`
2. Enter your email and password
3. **Done!** ✅

### Step 4: Clean Up (Security)
Delete these files:
- `generate_password_hash.php`

---

## 🔐 Forgot Password Feature

Your control panel login now has a **"Forgot Password"** link!

**How to use:**
1. Click "Forgot Password?" on login page
2. Enter your email
3. Copy the reset code shown
4. Enter code and new password
5. Login with new password

---

## 🐛 Problem: Can't Login?

### Quick Fix:
Run this SQL to reset your password to "admin123":

```sql
UPDATE control_panel_admins 
SET password = '$2y$10$E.h3Cd8HpQKhCfKhCWqMaOv1jhKvJqI1oeEGfGT6F6XVxN.YPO8p2'
WHERE email = 'your-email@example.com';
```

Then login with:
- Email: your-email@example.com
- Password: admin123

**Change it immediately** using forgot password!

---

## 📝 Test Credentials (Quick Start)

Use these to test right away:

```sql
INSERT INTO control_panel_admins (email, password, fullname, role, status, created_at)
VALUES (
    'admin@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Test Admin',
    'super_admin',
    'active',
    NOW()
);
```

**Login:**
- Email: `admin@uphocare.com`
- Password: `password`

---

## ✅ Verify It Worked

Run this SQL:
```sql
SELECT * FROM control_panel_admins WHERE role = 'super_admin';
```

You should see your account!

---

## 🎯 Why This Error?

The error **"Super Admin registration is restricted"** is intentional! 

Super admins are too powerful to allow self-registration. They must be created directly in the database for security.

**This is correct behavior** - it prevents:
- ❌ Unauthorized access
- ❌ Multiple super admins without control
- ❌ Security breaches
- ❌ Account takeovers

---

## 🆘 Still Need Help?

Check the detailed guide: `CREATE_SUPER_ADMIN_GUIDE.md`

Or run the full SQL script: `database/create_super_admin.sql`

---

**That's it!** You're ready to manage your UphoCare system! 🎉

