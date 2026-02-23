# Verification Tables Documentation - db_upholcare

## Overview
Kini nga dokumento nag-explain kung unsa ang mga table nga gigamit para sa verification sa admin ug customer accounts.

---

## 📋 VERIFICATION TABLES

### 1. **admin_verification_codes** (Para sa Admin)
**Table Name:** `admin_verification_codes`  
**Location:** `db_upholcare` database  
**Purpose:** Nag-store sa pre-generated verification codes (1000-9999) para sa admin registrations

**Structure:**
```sql
CREATE TABLE admin_verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    verification_code VARCHAR(10) NOT NULL UNIQUE,  -- 4-digit code (1000-9999)
    status ENUM('available', 'reserved', 'used', 'expired') DEFAULT 'available',
    admin_registration_id INT NULL,
    assigned_to_email VARCHAR(191) NULL,
    assigned_to_name VARCHAR(100) NULL,
    assigned_by_super_admin_id INT NULL,
    assigned_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_registration_id) REFERENCES admin_registrations(id)
);
```

**How it works:**
- Nag-contain ug 9,000 codes gikan 1000 hangtod 9999
- Super Admin mag-assign ug code gikan niini nga table
- Ang code ma-mark as 'used' human ma-assign

---

### 2. **admin_registrations** (Para sa Admin)
**Table Name:** `admin_registrations`  
**Location:** `db_upholcare` database  
**Purpose:** Nag-store sa admin registration information ug verification code

**Verification Columns:**
- `verification_code` (VARCHAR(10)) - Ang 4-digit code nga gi-assign
- `verification_code_sent_at` (TIMESTAMP) - Kani-a nga time gi-send ang code
- `verification_code_verified_at` (TIMESTAMP) - Kani-a nga time na-verify ang code
- `verification_code_verified` (TINYINT) - 1 kung verified na, 0 kung wala pa

**How it works:**
- Human ma-approve sa Super Admin, ang code gikan sa `admin_verification_codes` ma-store dinhi
- Ang code ma-send via email sa admin
- Human ma-verify, ang `verification_code_verified_at` ma-update

---

### 3. **users** (Para sa Customer)
**Table Name:** `users`  
**Location:** `db_upholcare` database  
**Purpose:** Nag-store sa customer account information ug verification code

**Verification Columns:**
- `verification_code` (VARCHAR(10)) - Ang 4-digit code nga gi-generate
- `verification_code_sent_at` (TIMESTAMP) - Kani-a nga time gi-send ang code
- `verification_code_verified_at` (TIMESTAMP) - Kani-a nga time na-verify ang code
- `verification_attempts` (INT) - Pila ka attempts sa verification

**How it works:**
- Human mag-register ang customer, automatic mag-generate ug 4-digit code
- Ang code ma-store dinhi ug ma-send via email
- Human ma-verify, ang `verification_code_verified_at` ma-update
- Ang `status` ma-change gikan 'pending_verification' to 'active'

---

## 🔄 VERIFICATION FLOW

### Admin Verification Flow:
```
1. Admin mag-register
   ↓
2. Record ma-create sa admin_registrations (status: pending_verification)
   ↓
3. Super Admin mag-review ug approve
   ↓
4. System mag-select ug available code gikan sa admin_verification_codes
   ↓
5. Code ma-store sa admin_registrations.verification_code
   ↓
6. Code ma-send via email sa admin
   ↓
7. Admin mag-enter sa code
   ↓
8. System mag-verify ang code
   ↓
9. admin_registrations.verification_code_verified_at ma-update
   ↓
10. Account activated
```

### Customer Verification Flow:
```
1. Customer mag-register
   ↓
2. Account ma-create sa users table (status: pending_verification)
   ↓
3. System automatic mag-generate ug 4-digit code
   ↓
4. Code ma-store sa users.verification_code
   ↓
5. Code ma-send via email sa customer
   ↓
6. Customer mag-enter sa code
   ↓
7. System mag-verify ang code
   ↓
8. users.verification_code_verified_at ma-update
   ↓
9. users.status ma-change to 'active'
   ↓
10. Customer makalogin na
```

---

## 📊 TABLE SUMMARY

| Table Name | Purpose | Verification Columns |
|------------|---------|---------------------|
| **admin_verification_codes** | Pre-generated codes (1000-9999) | `verification_code`, `status` |
| **admin_registrations** | Admin registration info | `verification_code`, `verification_code_sent_at`, `verification_code_verified_at` |
| **users** | Customer account info | `verification_code`, `verification_code_sent_at`, `verification_code_verified_at`, `verification_attempts` |

---

## 🔍 QUERIES PARA MA-CHECK

### Check Admin Verification Codes:
```sql
-- Tan-awa ang available codes
SELECT COUNT(*) as available_codes 
FROM admin_verification_codes 
WHERE status = 'available';

-- Tan-awa ang used codes
SELECT verification_code, assigned_to_email, assigned_at 
FROM admin_verification_codes 
WHERE status = 'used' 
ORDER BY assigned_at DESC 
LIMIT 10;
```

### Check Admin Registrations:
```sql
-- Tan-awa ang pending verifications
SELECT id, email, fullname, verification_code, 
       verification_code_sent_at, verification_code_verified_at,
       registration_status
FROM admin_registrations 
WHERE registration_status = 'pending_verification'
ORDER BY created_at DESC;
```

### Check Customer Verifications:
```sql
-- Tan-awa ang pending customer verifications
SELECT id, email, fullname, verification_code, 
       verification_code_sent_at, verification_code_verified_at,
       status
FROM users 
WHERE role = 'customer' 
AND status = 'pending_verification'
ORDER BY created_at DESC;
```

---

## 📝 IMPORTANT NOTES

1. **Admin Verification:**
   - Ang codes gikan sa `admin_verification_codes` table (pre-generated)
   - Super Admin ang mag-assign sa code
   - Ang code ma-link sa `admin_registrations` table

2. **Customer Verification:**
   - Ang codes automatic mag-generate (random 4-digit)
   - Wala'y pre-generated codes para sa customers
   - Ang code diretso ma-store sa `users` table

3. **Code Expiration:**
   - Admin codes: 7 days gikan sa assignment
   - Customer codes: 24 hours gikan sa pag-send

4. **Security:**
   - Ang codes dili ma-display sa page (security)
   - Ang codes ma-send lang via email
   - Ang verification_attempts ma-track para sa security

---

## 🗂️ FILE LOCATIONS

**SQL Files:**
- `database/create_admin_verification_codes_table.sql` - Creates admin_verification_codes table
- `database/add_verification_code_to_admin_registrations.sql` - Adds verification columns to admin_registrations
- `database/add_customer_verification_columns.sql` - Adds verification columns to users table

**Controller Files:**
- `controllers/AuthController.php` - Handles verification process
- `controllers/ControlPanelController.php` - Handles admin code assignment

**View Files:**
- `views/auth/verify_code.php` - Verification page

---

**Generated:** December 2025  
**Database:** db_upholcare  
**Language:** Cebuano/Bisaya

