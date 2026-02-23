# Admin Registration & Verification Flow - Complete Guide

## ✅ Complete Flow: Register → Verify → Login

### Step-by-Step Process

#### 1️⃣ Admin Clicks Register Button
- Admin fills out registration form:
  - Full Name
  - Email Address
  - Password
  - Confirm Password
  - Phone Number
  - Agrees to Terms
- Admin clicks **"Register"** button

#### 2️⃣ System Automatically Sends Verification Code
**What happens automatically:**
- ✅ System generates 4-digit verification code (e.g., 1234)
- ✅ Code is stored in database (`admin_registrations` table)
- ✅ **Email is automatically sent** via PHPMailer to admin's email address
- ✅ Email contains:
  - Verification code (4 digits)
  - Link to verification page
  - Instructions

#### 3️⃣ Admin is Redirected to Verification Page (NOT Login Page)
**After clicking register:**
- ✅ Admin is **automatically redirected** to: `auth/verifyCode?email=admin@email.com`
- ✅ Admin sees **verification page** (NOT login page)
- ✅ Page shows:
  - "Verification Required Before Login"
  - Email address where code was sent
  - Instructions to check email
  - Input field for verification code

#### 4️⃣ Admin Receives Email
- ✅ Admin checks email inbox
- ✅ Admin finds email with subject: "Admin Account Verification Code - UphoCare"
- ✅ Email contains 4-digit verification code
- ✅ Admin copies the code from email

#### 5️⃣ Admin Enters Verification Code
- ✅ Admin is on verification page (not login page)
- ✅ Admin enters 4-digit code from email
- ✅ Admin clicks **"Verify Code"** button

#### 6️⃣ System Verifies Code
- ✅ System checks if code matches
- ✅ System checks if code is not expired (24 hours)
- ✅ System checks verification attempts (max 5)

#### 7️⃣ Account Activated & Auto-Login
**If code is correct:**
- ✅ Account status changed to 'active'
- ✅ Admin is **automatically logged in**
- ✅ Admin is redirected to dashboard
- ✅ Admin can now use the system

**If code is incorrect:**
- ❌ Error message shown
- ❌ Admin stays on verification page
- ❌ Can try again (max 5 attempts)

#### 8️⃣ Admin Can Now Login
- ✅ After verification, admin account is active
- ✅ Admin can login normally using email and password
- ✅ Admin can use the system

## 🔒 Security: Login is Blocked Until Verification

### What Happens if Admin Tries to Login Before Verification?

**Scenario:** Admin tries to login before entering verification code

**Result:**
- ❌ Login attempt is **BLOCKED**
- ❌ Error message: "Please verify your email code before logging in"
- ❌ Link provided to verification page
- ❌ Admin **cannot** login until verification is complete

**Code Check:**
```php
// In processLogin() method:
if ($user['status'] === 'pending_verification' && $user['role'] === 'admin') {
    // BLOCK LOGIN - redirect to verification page
    $_SESSION['error'] = 'Please verify your email code before logging in...';
    $this->redirect('auth/login?tab=admin');
}
```

## 📧 Email Sending Process

### Automatic Email Sending

**When:** Immediately after admin clicks register button

**How:**
1. System generates 4-digit code
2. System stores code in database
3. System calls `NotificationService::sendAdminVerificationCode()`
4. PHPMailer sends email via SMTP
5. Email is delivered to admin's inbox

**Email Content:**
- Subject: "Admin Account Verification Code - UphoCare"
- Body: HTML email with verification code
- Code: 4-digit number (e.g., 1234)
- Link: Direct link to verification page

## 🎯 Key Points

### ✅ What Happens Automatically
1. **Code Generation**: Automatic (4-digit code)
2. **Email Sending**: Automatic (via PHPMailer)
3. **Redirect**: Automatic (to verification page)
4. **Account Activation**: Automatic (after verification)

### ✅ What Admin Must Do
1. **Check Email**: Admin must check inbox for verification code
2. **Enter Code**: Admin must enter code on verification page
3. **Verify**: Admin must click "Verify Code" button

### ✅ What is Blocked
1. **Login Before Verification**: ❌ BLOCKED
2. **Using System Before Verification**: ❌ BLOCKED
3. **Skipping Verification**: ❌ NOT POSSIBLE

## 📋 Flow Diagram

```
┌─────────────────────────────────┐
│  Admin Clicks Register Button   │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  System Validates Input         │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  System Creates Account         │
│  Status: pending_verification   │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  System Generates 4-Digit Code  │
│  Code: 1234                     │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  System Stores Code in Database │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  System Sends Email via PHPMailer│
│  To: admin@email.com            │
│  Code: 1234                     │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  Admin Redirected to            │
│  VERIFICATION PAGE               │
│  (NOT login page)               │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  Admin Checks Email Inbox       │
│  Finds Code: 1234               │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  Admin Enters Code on           │
│  Verification Page               │
│  Code: 1234                      │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  System Verifies Code           │
│  Code Matches: ✅                │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  Account Activated               │
│  Status: active                  │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  Admin Auto-Logged In           │
│  Redirected to Dashboard          │
└──────────────┬──────────────────┘
               │
               ▼
┌─────────────────────────────────┐
│  Admin Can Now Login & Use      │
│  the System                      │
└─────────────────────────────────┘
```

## 🔐 Login Blocking Flow

```
Admin Tries to Login
        ↓
System Checks Credentials
        ↓
Credentials Valid?
        ↓
    ┌───┴───┐
   YES     NO
    ↓       ↓
Check Status    ❌ Invalid Login
    ↓
Status = pending_verification?
    ↓
    ┌───┴───┐
   YES     NO
    ↓       ↓
❌ BLOCKED  ✅ Allow Login
    ↓
Redirect to Verification Page
```

## ✅ Verification Requirements

### Before Login is Allowed:
- ✅ Verification code must be entered
- ✅ Verification code must match
- ✅ Verification code must not be expired (24 hours)
- ✅ Account status must be 'active'

### What Happens After Verification:
- ✅ Account status: `pending_verification` → `active`
- ✅ Admin automatically logged in
- ✅ Admin redirected to dashboard
- ✅ Admin can now login normally

## 📝 Summary

**The Complete Flow:**
1. ✅ Admin clicks register → System sends code → Admin redirected to **verification page**
2. ✅ Admin receives email with code → Admin enters code on **verification page**
3. ✅ System verifies code → Account activated → Admin auto-logged in
4. ✅ Admin can now login and use the system

**Key Security:**
- ❌ Login is **BLOCKED** until verification
- ❌ Verification page is shown **BEFORE** login page
- ❌ Code is **ONLY** in email (not displayed on page)
- ❌ System **automatically** sends code after registration

The system is fully automated and secure. The verification page is the **first page** the admin sees after registration, and login is **blocked** until verification is complete.

