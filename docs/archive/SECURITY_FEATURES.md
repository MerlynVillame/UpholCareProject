# Security Features - UphoCare System

## Overview

This document outlines the security measures implemented to prevent unauthorized admin access and ensure proper role-based authentication.

## Security Features Implemented

### 1. Admin Registration Protection

**Admin Verification Key System**

- Location: `config/config.php`
- Key: `ADMIN_REGISTRATION_KEY` constant
- Default Value: `UphoCare2024Admin!Secure@Key#987`

**How it works:**

- Admin registration requires a special verification key
- The key is checked server-side in `AuthController::processRegister()`
- Invalid keys are rejected and logged as security alerts
- Users without the key can only register as customers

**Implementation:**

```php
// Check admin key
if ($role === 'admin' && $adminKey !== ADMIN_REGISTRATION_KEY) {
    // Reject registration
    error_log("SECURITY ALERT: Failed admin registration attempt");
}
```

### 2. Registration Form Protection

**UI Changes:**

- Admin role option is available but requires verification key
- When "Admin" is selected, an admin key field appears
- Clear warnings about needing the special key
- JavaScript validation enforces required field

**File:** `views/auth/register.php`

### 3. Login Security

**Role Verification on Login**

- Verifies user role is either 'admin' or 'customer'
- Invalid roles are rejected immediately
- All login attempts are logged (successful and failed)
- Session contains verified role information

**Implementation:** `controllers/AuthController::processLogin()`

### 4. Admin Access Protection

**Multiple Layers of Security:**

1. **requireAdmin() Method** - Core validation

   - Checks user is logged in
   - Verifies role is 'admin'
   - Logs unauthorized access attempts
   - Redirects to login if unauthorized

2. **verifyRoleIntegrity()** - Database validation

   - Compares session role with database role
   - Prevents session manipulation
   - Automatically logs out if mismatch detected

3. **AdminController Protection**
   - All admin controllers use both methods
   - Runs on every request
   - Provides defense in depth

**Files:** `core/Controller.php`, `controllers/AdminController.php`

### 5. Security Logging

**All security events are logged:**

- Failed admin registration attempts
- Role mismatch detection
- Unauthorized access attempts
- Successful/failed logins
- User logout events

**Log Format:**

```
SECURITY ALERT: Failed admin registration attempt with username: xxx, email: xxx
SECURITY ALERT: Role mismatch for user xxx. Session: admin, DB: customer
SUCCESS: User xxx (admin) logged in successfully
```

## How It Prevents Customer-to-Admin Escalation

### Scenario 1: Customer tries to register as admin

1. Customer selects "Admin" role
2. Admin key field appears
3. Without correct key → Registration rejected
4. Attempt is logged as security alert

### Scenario 2: Customer tries to login as admin

1. Customer has "admin" role in database (somehow)
2. Login process checks role validity
3. Invalid role → Access denied
4. Attempt logged

### Scenario 3: Session manipulation

1. User tries to modify session to "admin"
2. AdminController verifies with database
3. Role mismatch detected
4. User auto-logged out
5. Attempt logged

### Scenario 4: Direct admin page access

1. Customer (or unauthorized user) tries to access admin page
2. requireAdmin() checks role
3. Not admin → Redirected to login
4. Attempt logged

## Security Best Practices

### For System Administrators:

1. **Change Admin Key**

   - Edit `config/config.php`
   - Update `ADMIN_REGISTRATION_KEY` constant
   - Use a strong, unique key

2. **Monitor Logs**

   - Check error logs regularly
   - Watch for "SECURITY ALERT" messages
   - Investigate unauthorized attempts

3. **Limit Admin Creation**
   - Only share admin key with trusted personnel
   - Revoke keys if compromised
   - Regular audit of admin users

### Admin Key Format Recommendation:

- Minimum 16 characters
- Include uppercase, lowercase, numbers, and symbols
- Example: `MyCompany2024Secure@#Key!`

## User Model Methods

**New Method Added:**

- `findById($id)` - Retrieve user by ID for role verification

**Existing Methods Enhanced:**

- `authenticate()` - Now validates role integrity
- `register()` - Enforces admin key requirement

## Testing the Security

### Test 1: Customer Registration

1. Go to registration page
2. Select "Customer" role
3. Fill required fields
4. Should register successfully

### Test 2: Admin Registration Without Key

1. Go to registration page
2. Select "Admin" role
3. Leave admin key empty
4. Should show error: "Admin verification key is required"

### Test 3: Admin Registration With Wrong Key

1. Go to registration page
2. Select "Admin" role
3. Enter wrong key
4. Should show error: "Invalid admin verification key"
5. Check logs for security alert

### Test 4: Customer Accessing Admin Pages

1. Login as customer
2. Try to access: `/admin/dashboard`
3. Should redirect to login page
4. Check logs for unauthorized access alert

## Important Notes

- Admin key is in plain text in config file (by design for flexibility)
- For production, consider using environment variables
- All security features log to PHP error log
- Session timeout is 1 hour (configurable in config.php)
- Passwords are hashed using PHP's `password_hash()`

## Configuration

**Location:** `config/config.php`

```php
// Change this to your own secure key
define('ADMIN_REGISTRATION_KEY', 'YourSecureKeyHere');

// Session timeout (seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CUSTOMER', 'customer');
```

## Support

For security-related issues or questions, consult with the development team or refer to PHP documentation on password hashing and session security.
