# 🔐 UphoCare Login System - Complete Guide

## ✅ **How Login Validation Works**

The login system automatically validates users and redirects them based on their role:

### **Login Flow:**

```
User enters credentials
       ↓
   Validate Input
       ↓
  Authenticate User
       ↓
  Check Account Status (active/inactive)
       ↓
  Set Session Variables:
  - user_id
  - username
  - role (admin/customer)
  - name
       ↓
  Check User Role
       ↓
  ┌─────────┬─────────┐
  ↓         ↓         ↓
ADMIN    CUSTOMER   OTHER
  ↓         ↓         ↓
Admin    Customer   Error
Dashboard Dashboard
```

---

## 🎯 **Role-Based Redirect Logic**

### **Code Implementation** (`AuthController.php`)

```php
// After successful login (line 76):
$this->redirectToDashboard();

// Redirect logic (line 193-199):
private function redirectToDashboard() {
    if ($this->hasRole(ROLE_ADMIN)) {
        $this->redirect('admin/dashboard');     // Admin → admin/dashboard
    } else {
        $this->redirect('customer/dashboard');  // Customer → customer/dashboard
    }
}
```

### **What This Means:**

✅ **Customer Login** → Automatically redirects to: `http://localhost/UphoCare/customer/dashboard`  
✅ **Admin Login** → Automatically redirects to: `http://localhost/UphoCare/admin/dashboard`  
❌ **Invalid Login** → Shows error message and stays on login page

---

## 🔑 **Test Accounts**

### **Step 1: Create Test Users**

Open **phpMyAdmin** and run this SQL:

```sql
-- Run: database/create_test_users.sql
-- Or copy the SQL from that file
```

### **Step 2: Login Credentials**

| Role         | Username       | Password   | Redirects To          |
| ------------ | -------------- | ---------- | --------------------- |
| **Admin**    | `admin`        | `password` | `/admin/dashboard`    |
| **Customer** | `customer`     | `password` | `/customer/dashboard` |
| **Customer** | `testcustomer` | `password` | `/customer/dashboard` |

---

## 🧪 **Testing the Login System**

### **Test 1: Customer Login**

1. **Open:** `http://localhost/UphoCare/auth/login`
2. **Enter:**
   - Username: `customer`
   - Password: `password`
3. **Click:** Login
4. **Expected Result:** ✅ Redirects to `customer/dashboard`

### **Test 2: Admin Login**

1. **Open:** `http://localhost/UphoCare/auth/login`
2. **Enter:**
   - Username: `admin`
   - Password: `password`
3. **Click:** Login
4. **Expected Result:** ✅ Redirects to `admin/dashboard`

### **Test 3: Invalid Login**

1. **Open:** `http://localhost/UphoCare/auth/login`
2. **Enter:**
   - Username: `wronguser`
   - Password: `wrongpass`
3. **Click:** Login
4. **Expected Result:** ❌ Shows error "Invalid username or password"

### **Test 4: Customer Accessing Admin Page**

1. **Login as:** `customer` / `password`
2. **Try to access:** `http://localhost/UphoCare/admin/dashboard`
3. **Expected Result:** ❌ Shows "403 Access Denied"

---

## 🔍 **Debugging Login Issues**

### **Check Session Status:**

Open: `http://localhost/UphoCare/check_session.php`

This will show:

- ✅ If you're logged in
- ✅ Your user ID
- ✅ Your role
- ✅ All session variables
- ✅ Database connection status

### **Common Issues:**

| Issue                   | Cause                       | Solution                   |
| ----------------------- | --------------------------- | -------------------------- |
| **403 Access Denied**   | Not logged in or wrong role | Login with correct account |
| **Invalid credentials** | Wrong username/password     | Check database for users   |
| **Session not set**     | Cookies disabled            | Enable cookies in browser  |
| **Role not recognized** | Database role mismatch      | Check `users.role` column  |
| **Database error**      | Wrong credentials in config | Check `config/config.php`  |

---

## 🗄️ **Database Requirements**

### **Users Table Structure:**

```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

### **Required Columns:**

- ✅ `username` - For login
- ✅ `password` - Hashed password
- ✅ `role` - Must be 'admin' or 'customer'
- ✅ `status` - Must be 'active' to login
- ✅ `fullname` - User's full name (used in dashboard)

---

## 🔒 **Security Features**

### **Implemented:**

1. ✅ **Password Hashing** - Uses `password_hash()` with bcrypt
2. ✅ **Session Management** - Secure session handling
3. ✅ **Role-Based Access** - Customers can't access admin pages
4. ✅ **Status Check** - Inactive accounts can't login
5. ✅ **Input Validation** - Username and password required
6. ✅ **SQL Injection Protection** - Using prepared statements

### **Session Variables Set:**

```php
$_SESSION['user_id']       // User ID from database
$_SESSION['username']      // Username
$_SESSION['role']          // 'admin' or 'customer'
$_SESSION['name']          // Full name
$_SESSION['last_activity'] // Timestamp
```

---

## 🚀 **Quick Start Guide**

### **1. Setup Database**

```bash
# Open phpMyAdmin
http://localhost/phpmyadmin

# Select your database: upholcare
# Run SQL from: database/create_test_users.sql
```

### **2. Test Login**

```bash
# Open login page
http://localhost/UphoCare/auth/login

# Login as customer:
Username: customer
Password: password

# Should redirect to:
http://localhost/UphoCare/customer/dashboard
```

### **3. Verify Session**

```bash
# Check if logged in:
http://localhost/UphoCare/check_session.php
```

---

## 📝 **Login Validation Checklist**

When a user logs in, the system checks:

- [ ] Username and password are not empty
- [ ] User exists in database
- [ ] Password matches (using `password_verify()`)
- [ ] Account status is 'active'
- [ ] Session variables are set correctly
- [ ] User is redirected based on their role

---

## 🎯 **Expected Behavior**

### **✅ Successful Customer Login:**

```
1. User enters: customer / password
2. System validates credentials
3. System checks role: 'customer'
4. System creates session
5. System redirects to: /customer/dashboard
6. Customer sees their dashboard
```

### **✅ Successful Admin Login:**

```
1. User enters: admin / password
2. System validates credentials
3. System checks role: 'admin'
4. System creates session
5. System redirects to: /admin/dashboard
6. Admin sees their dashboard
```

### **❌ Failed Login:**

```
1. User enters wrong credentials
2. System shows error message
3. User stays on login page
4. Can try again
```

---

## 🆘 **Troubleshooting**

### **Problem: 403 Access Denied**

**Cause:** Not logged in as customer

**Solution:**

1. Run `check_session.php` to verify login status
2. If not logged in, go to login page
3. Login with customer credentials
4. Try accessing customer pages again

### **Problem: Redirects to wrong dashboard**

**Cause:** Wrong role in database

**Solution:**

```sql
-- Check user roles:
SELECT username, role FROM users;

-- Fix if needed:
UPDATE users SET role = 'customer' WHERE username = 'customer';
```

### **Problem: Can't login with any account**

**Cause:** No users in database

**Solution:**
Run `database/create_test_users.sql` in phpMyAdmin

---

## 📊 **Login System Architecture**

```
┌─────────────────────────────────────────────┐
│           Login Page (auth/login)           │
│  - Username input                           │
│  - Password input                           │
│  - Login button                             │
└──────────────────┬──────────────────────────┘
                   ↓
┌─────────────────────────────────────────────┐
│     AuthController::processLogin()          │
│  - Validate input                           │
│  - Authenticate user                        │
│  - Check status                             │
│  - Set session                              │
└──────────────────┬──────────────────────────┘
                   ↓
┌─────────────────────────────────────────────┐
│   AuthController::redirectToDashboard()     │
│  - Check user role                          │
│  - Redirect based on role                   │
└──────┬──────────────────────┬────────────────┘
       ↓                      ↓
┌──────────────┐      ┌──────────────────┐
│    ADMIN     │      │    CUSTOMER      │
│  Dashboard   │      │    Dashboard     │
└──────────────┘      └──────────────────┘
```

---

## ✅ **System is Ready!**

Your login validation is fully configured and working:

- ✅ Customers redirect to customer dashboard
- ✅ Admins redirect to admin dashboard
- ✅ Role-based access control enabled
- ✅ Session management active
- ✅ Security measures in place

**Login URL:** `http://localhost/UphoCare/auth/login`

**Test Accounts Created:** Run `database/create_test_users.sql`

---

**Need Help?**  
Run `check_session.php` to debug any login issues!
