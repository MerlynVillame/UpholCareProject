# 🚀 UphoCare - Localhost Setup Guide

## ✅ **System is Configured for Localhost**

All settings are already configured to run on your local machine!

---

## 📋 **Prerequisites**

### **Required Software:**

- ✅ **XAMPP** - Apache + MySQL + PHP
- ✅ **Web Browser** - Chrome, Firefox, or Edge
- ✅ **Text Editor** - VSCode, Sublime, etc. (optional)

---

## 🔧 **Current Configuration**

### **Base URL:**

```php
http://localhost/UphoCare/
```

### **Database Settings:**

```
Host:     localhost
Database: db_upholcare
User:     uphocare
Password: up
```

### **File Location:**

```
C:\xampp\htdocs\UphoCare\
```

---

## 🚀 **How to Run Your Project**

### **Step 1: Start XAMPP**

1. **Open XAMPP Control Panel**
   - Location: `C:\xampp\xampp-control.exe`
2. **Start Apache**
   - Click "Start" button next to Apache
   - Wait for green "Running" status
3. **Start MySQL**
   - Click "Start" button next to MySQL
   - Wait for green "Running" status

**Status Should Show:**

```
Apache  | Running | Port: 80, 443
MySQL   | Running | Port: 3306
```

---

### **Step 2: Setup Database**

1. **Open phpMyAdmin**

   ```
   http://localhost/phpmyadmin
   ```

2. **Select Database**

   - Click on `db_upholcare` database (left sidebar)
   - If it doesn't exist, create it first

3. **Setup Database**
   - Click "SQL" tab
   - Copy and paste content from: `database/setup_empty_database.sql`
   - Click "Go"
   - Database will be created (empty, no test users)

---

### **Step 3: Access Your Project**

#### **Main URLs:**

| Page                   | URL                                            | Description          |
| ---------------------- | ---------------------------------------------- | -------------------- |
| **Home/Login**         | `http://localhost/UphoCare/`                   | Landing page         |
| **Login Page**         | `http://localhost/UphoCare/auth/login`         | User login           |
| **Register**           | `http://localhost/UphoCare/auth/register`      | New account          |
| **Customer Dashboard** | `http://localhost/UphoCare/customer/dashboard` | After customer login |
| **Admin Dashboard**    | `http://localhost/UphoCare/admin/dashboard`    | After admin login    |

---

### **Step 4: Test Login**

#### **Customer Login:**

1. Open: `http://localhost/UphoCare/auth/login`
2. Enter credentials:
   - **Username:** `customer`
   - **Password:** `password`
3. Click "Login"
4. **Result:** Redirects to Customer Dashboard

#### **Admin Login:**

1. Open: `http://localhost/UphoCare/auth/login`
2. Enter credentials:
   - **Username:** `admin`
   - **Password:** `password`
3. Click "Login"
4. **Result:** Redirects to Admin Dashboard

---

## 🧪 **Testing Tools**

### **1. Visual Test Page**

```
http://localhost/UphoCare/test_login.php
```

**Features:**

- 📊 Login flow diagram
- 🧪 Test account credentials
- 🔧 Quick action buttons
- 📝 Setup instructions

### **2. Session Debug Tool**

```
http://localhost/UphoCare/check_session.php
```

**Shows:**

- ✅ Login status
- 👤 User information
- 🔍 Session variables
- 💾 Database connection

### **3. phpMyAdmin**

```
http://localhost/phpmyadmin
```

**For:**

- 📊 View database tables
- 👥 Manage users
- 📝 Run SQL queries
- 🔍 Debug data

---

## 📂 **Project Structure**

```
C:\xampp\htdocs\UphoCare\
├── config\
│   ├── config.php          ← Localhost settings here
│   └── database.php        ← Database connection
├── controllers\
│   ├── AuthController.php  ← Login logic
│   ├── CustomerController.php
│   └── AdminController.php
├── models\
│   ├── User.php
│   ├── Booking.php
│   └── Service.php
├── views\
│   ├── auth\               ← Login/Register pages
│   ├── customer\           ← Customer pages
│   └── admin\              ← Admin pages
├── assets\
│   ├── css\
│   └── js\
├── database\
│   └── create_test_users.sql
├── .htaccess               ← URL routing (important!)
└── index.php               ← Entry point
```

---

## 🌐 **How URLs Work on Localhost**

### **URL Structure:**

```
http://localhost/UphoCare/{controller}/{method}/{parameters}
```

### **Examples:**

| URL                                             | What It Does              |
| ----------------------------------------------- | ------------------------- |
| `http://localhost/UphoCare/`                    | Home (redirects to login) |
| `http://localhost/UphoCare/auth/login`          | Shows login page          |
| `http://localhost/UphoCare/customer/dashboard`  | Customer dashboard        |
| `http://localhost/UphoCare/customer/bookings`   | Customer bookings         |
| `http://localhost/UphoCare/customer/newBooking` | Create new booking        |

### **How It Works:**

```
localhost/UphoCare/customer/dashboard
    ↓          ↓         ↓         ↓
  Domain    Folder   Controller Method
```

---

## 🔧 **Configuration Details**

### **config/config.php**

```php
// Base URL (already set for localhost)
define('BASE_URL', 'http://localhost/UphoCare/');

// Database (already configured)
define('DB_HOST', 'localhost');
define('DB_USER', 'uphocare');
define('DB_PASS', 'up');
define('DB_NAME', 'upholcare');
```

### **.htaccess** (URL Rewriting)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
Options -Indexes
```

**This enables clean URLs:**

- ✅ `localhost/UphoCare/customer/dashboard`
- ❌ Without it: `localhost/UphoCare/index.php?url=customer/dashboard`

---

## 🗄️ **Database Setup on Localhost**

### **Option 1: Using phpMyAdmin** (Recommended)

1. Open: `http://localhost/phpmyadmin`
2. Click "SQL" tab
3. Run this SQL:

```sql
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS db_upholcare;
USE db_upholcare;

-- Then run: database/create_test_users.sql
```

### **Option 2: Using MySQL Command Line**

```bash
# Open XAMPP Shell
cd C:\xampp\mysql\bin

# Login to MySQL
mysql -u uphocare -p
# Enter password: up

# Create database
CREATE DATABASE IF NOT EXISTS db_upholcare;
USE db_upholcare;

# Exit
exit;
```

---

## 📊 **Localhost Access Points**

### **For Testing:**

1. **Login System**

   - `http://localhost/UphoCare/test_login.php`

2. **Session Status**

   - `http://localhost/UphoCare/check_session.php`

3. **Database Management**
   - `http://localhost/phpmyadmin`

### **For Users:**

1. **Customer Portal**

   - `http://localhost/UphoCare/auth/login`
   - Login as: `customer` / `password`
   - Access: Customer dashboard, bookings, services

2. **Admin Portal**
   - `http://localhost/UphoCare/auth/login`
   - Login as: `admin` / `password`
   - Access: Admin dashboard, manage all

---

## 🐛 **Common Localhost Issues**

### **Problem: "This site can't be reached"**

**Cause:** Apache not running

**Solution:**

1. Open XAMPP Control Panel
2. Click "Start" on Apache
3. Wait for green status
4. Try again

---

### **Problem: "Database connection failed"**

**Cause:** MySQL not running or wrong credentials

**Solution:**

1. Start MySQL in XAMPP
2. Verify credentials in `config/config.php`:
   ```php
   DB_USER: uphocare
   DB_PASS: up
   DB_NAME: upholcare
   ```
3. Create database user in phpMyAdmin if needed

---

### **Problem: "404 Not Found"**

**Cause:** .htaccess not working or Apache mod_rewrite disabled

**Solution:**

1. Check if `.htaccess` file exists
2. Enable mod_rewrite:
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Remove `#` to uncomment
   - Restart Apache

---

### **Problem: "403 Forbidden"**

**Cause:** Not logged in or accessing wrong pages

**Solution:**

1. Run: `http://localhost/UphoCare/check_session.php`
2. Login with correct credentials
3. Try accessing page again

---

## ✅ **Localhost Checklist**

Before running your project:

- [ ] XAMPP installed
- [ ] Apache running (green in XAMPP)
- [ ] MySQL running (green in XAMPP)
- [ ] Project in `C:\xampp\htdocs\UphoCare\`
- [ ] Database `db_upholcare` created
- [ ] Database created (empty)
- [ ] Can access `http://localhost/UphoCare/`

---

## 🚀 **Quick Start (Complete Process)**

### **1. Start Services**

```
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL
4. Both should show green "Running"
```

### **2. Setup Database**

```
1. Open: http://localhost/phpmyadmin
2. Run SQL: database/setup_empty_database.sql
3. Database created (empty, no test users)
4. Import your own SQL file if needed
```

### **3. Test Project**

```
1. Open: http://localhost/UphoCare/
2. Should redirect to login page
3. Login with: customer / password
4. Should see Customer Dashboard
```

---

## 🌐 **Localhost vs Production**

### **What Changes for Production:**

| Setting        | Localhost                    | Production                |
| -------------- | ---------------------------- | ------------------------- |
| **BASE_URL**   | `http://localhost/UphoCare/` | `https://yourdomain.com/` |
| **DB_HOST**    | `localhost`                  | `your-db-host`            |
| **DB_USER**    | `uphocare`                   | `production-user`         |
| **DB_PASS**    | `up`                         | `strong-password`         |
| **Debug Mode** | Enabled                      | Disabled                  |

### **For Now:**

✅ Keep localhost settings - perfect for development!

---

## 📝 **Default Test Accounts**

| Username       | Password   | Role     | Access               |
| -------------- | ---------- | -------- | -------------------- |
| `admin`        | `password` | Admin    | Full system access   |
| `customer`     | `password` | Customer | Customer portal only |
| `testcustomer` | `password` | Customer | Customer portal only |

---

## 🎯 **Your Localhost URLs (Bookmark These!)**

### **Main Application:**

- 🏠 Home: `http://localhost/UphoCare/`
- 🔐 Login: `http://localhost/UphoCare/auth/login`
- 📝 Register: `http://localhost/UphoCare/auth/register`

### **Customer Portal:**

- 📊 Dashboard: `http://localhost/UphoCare/customer/dashboard`
- 📅 Bookings: `http://localhost/UphoCare/customer/bookings`
- ➕ New Booking: `http://localhost/UphoCare/customer/newBooking`

### **Testing Tools:**

- 🧪 Test Page: `http://localhost/UphoCare/test_login.php`
- 🔍 Session Debug: `http://localhost/UphoCare/check_session.php`
- 💾 phpMyAdmin: `http://localhost/phpmyadmin`

---

## ✅ **You're Ready!**

Your UphoCare project is fully configured for localhost:

- ✅ Base URL: `http://localhost/UphoCare/`
- ✅ Database: `db_upholcare` on localhost
- ✅ Apache + MySQL required
- ✅ Clean URLs enabled (.htaccess)
- ✅ Role-based access working
- ✅ Empty database ready

**Start your project:**

1. Start XAMPP (Apache + MySQL)
2. Open: `http://localhost/UphoCare/`
3. Login and test!

---

**Happy coding! 🚀**
