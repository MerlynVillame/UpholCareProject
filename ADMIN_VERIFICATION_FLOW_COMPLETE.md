# Admin Verification Flow - Complete Guide

## ✅ Complete Flow After Admin Clicks Register Button

### Step 1: Admin Clicks Register Button

- Admin fills out registration form
- Clicks "Register" button
- System validates input

### Step 2: System Automatically Sends Verification Code

- System generates 4-digit verification code
- Code is stored in database
- **Email is automatically sent** to admin's email address via PHPMailer
- Code is sent immediately after registration

### Step 3: Admin is Redirected to Verification Page (NOT Login Page)

- Admin is **automatically redirected** to: `auth/verifyCode?email=admin@email.com`
- Admin sees verification page with instructions
- **Admin cannot go to login page yet** - verification is required

### Step 4: Admin Enters Verification Code

- Admin checks email inbox (and spam folder)
- Admin finds verification code in email
- Admin enters 4-digit code on verification page
- Admin clicks "Verify Code" button

### Step 5: System Verifies Code

- System checks if code matches
- If correct: Account is activated immediately
- Admin is automatically logged in
- Admin is redirected to dashboard

### Step 6: Admin Can Now Login

- After verification, admin account is active
- Admin can login normally using email and password
- Login is **blocked** until verification is complete

## 🔒 Security Features

### Login is Blocked Until Verification

- If admin tries to login before verification:
  - Login attempt is **blocked**
  - Error message: "Please verify your email code before logging in"
  - Link provided to verification page
  - Admin **must** verify code first

### Verification Code Requirements

- Code is **only** sent via email (not displayed on page)
- Code expires after 24 hours
- Maximum 5 verification attempts
- Code is unique per admin registration

## 📧 Email Flow

### What Happens with Email

1. **Sender Email** (configured in `config/email.php`):

   - This is the system email account that sends emails
   - Example: `uphocare.system@gmail.com`
   - Only **one** sender email is needed

2. **Recipient Email** (admin's email during registration):

   - Can be **any email address** (Gmail, Yahoo, Outlook, etc.)
   - Example: `merlyn.lagrimas122021@gmail.com`
   - System sends verification code to **this email**

3. **Email Sending**:
   - System automatically sends email after registration
   - Email contains verification code
   - Email includes link to verification page
   - Uses PHPMailer for reliable delivery

## 🎯 Current Implementation

### Registration Process (`processRegisterAdmin`)

1. ✅ Validates input
2. ✅ Creates admin account with status `pending_verification`
3. ✅ Generates 4-digit verification code
4. ✅ Stores code in database
5. ✅ **Automatically sends email** via PHPMailer
6. ✅ **Redirects to verification page** (NOT login page)

### Verification Page (`verifyCode`)

- ✅ Shows instructions to check email
- ✅ Displays email address where code was sent
- ✅ Shows when code was sent
- ✅ **Clear message**: "You Must Verify Before Login"
- ✅ Input field for verification code
- ✅ **No code displayed on page** (security)

### Login Process (`processLogin`)

- ✅ Checks if admin has verified code
- ✅ **Blocks login** if code not verified
- ✅ Shows error message with link to verification page
- ✅ Only allows login after verification

### Verification Process (`processVerifyCode`)

- ✅ Validates verification code
- ✅ Checks code expiration (24 hours)
- ✅ Checks verification attempts (max 5)
- ✅ Activates account immediately after verification
- ✅ Automatically logs in admin after verification
- ✅ Redirects to dashboard

## 📋 Flow Diagram

```
Admin Clicks Register
        ↓
System Validates Input
        ↓
System Creates Account (status: pending_verification)
        ↓
System Generates 4-Digit Code
        ↓
System Stores Code in Database
        ↓
System Sends Email via PHPMailer (automatic)
        ↓
Admin Redirected to Verification Page (NOT login page)
        ↓
Admin Checks Email Inbox
        ↓
Admin Finds Verification Code
        ↓
Admin Enters Code on Verification Page
        ↓
System Verifies Code
        ↓
Account Activated (status: active)
        ↓
Admin Automatically Logged In
        ↓
Admin Redirected to Dashboard
        ↓
Admin Can Now Login Normally
```

## ⚠️ Important Notes

1. **Admin is ALWAYS redirected to verification page** after registration
2. **Login is BLOCKED** until verification code is entered
3. **Verification code is ONLY in email** (not displayed on page)
4. **Email is sent automatically** - no manual intervention needed
5. **Works with any email address** - not limited to Gmail

## 🚫 What Admin Cannot Do

- ❌ Cannot login before verification
- ❌ Cannot see verification code on page (only in email)
- ❌ Cannot skip verification step
- ❌ Cannot use system until verification is complete

## ✅ What Admin Can Do

- ✅ Register with any email address
- ✅ Receive verification code automatically via email
- ✅ Enter verification code on verification page
- ✅ Login after verification is complete
- ✅ Use system normally after verification

## 📝 Summary

**After admin clicks register button:**

1. ✅ System automatically sends verification code to admin's email
2. ✅ Admin is redirected to **verification page** (NOT login page)
3. ✅ Admin must enter verification code from email
4. ✅ After verification, admin can login and use the system
5. ✅ Login is blocked until verification is complete

The system is fully automated - no manual intervention needed. The verification code is sent automatically, and the admin must verify before they can login.
