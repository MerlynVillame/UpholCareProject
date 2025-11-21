# 🔧 Fix Email Password Issue

## Problem

The email `merlyn.lagrimas122021@gmail.com` is not receiving verification codes because the Gmail App Password is invalid.

## Current Configuration

**File:** `config/email.php`

```php
define('EMAIL_SMTP_USERNAME', 'merlyn.lagrimas122021@gmail.com');
define('EMAIL_SMTP_PASSWORD', '1234567'); // ❌ INVALID - Only 7 characters
```

## Issue

The password `'1234567'` is **NOT a valid Gmail App Password**:

- ❌ Only 7 characters long
- ✅ Gmail App Passwords are **16 characters** long
- ❌ This will cause authentication to fail

## Solution

### Step 1: Generate Gmail App Password

1. **Go to Google Account Security:**

   - Visit: https://myaccount.google.com/security
   - Or: Google Account → Security

2. **Enable 2-Step Verification:**

   - If not already enabled, enable 2-Step Verification
   - This is required to generate App Passwords

3. **Generate App Password:**

   - Scroll down to "App passwords"
   - Click "App passwords"
   - Select "Mail" as the app
   - Select "Other (Custom name)" as device
   - Enter name: "UphoCare System"
   - Click "Generate"

4. **Copy the 16-Character Password:**
   - Google will show a 16-character password
   - Example: `abcd efgh ijkl mnop`
   - **Remove all spaces** → `abcdefghijklmnop`
   - Copy this password

### Step 2: Update Email Configuration

**File:** `config/email.php`

**Change from:**

```php
define('EMAIL_SMTP_PASSWORD', '1234567'); // ❌ INVALID
```

**Change to:**

```php
define('EMAIL_SMTP_PASSWORD', 'your-16-character-app-password'); // ✅ Valid App Password
```

**Example:**

```php
define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // ✅ 16 characters, no spaces
```

### Step 3: Test Email Sending

1. **Run Test Script:**

   - Visit: `http://localhost/UphoCare/test_email_config.php`
   - This will test the email configuration

2. **Check Results:**

   - ✅ If successful: "Email sent successfully!"
   - ❌ If failed: Check error messages

3. **Check Email Logs:**
   - File: `logs/email_notifications.log`
   - Check for error messages

## Important Notes

### Gmail App Password Requirements:

- ✅ Must be **16 characters** long
- ✅ No spaces (remove spaces from Google's display)
- ✅ Generated from Google Account → Security → App passwords
- ✅ Different from your regular Gmail password

### Common Mistakes:

- ❌ Using regular Gmail password (won't work)
- ❌ Using password with spaces (remove spaces)
- ❌ Using password shorter than 16 characters
- ❌ Not enabling 2-Step Verification first

## Verification

After updating the password:

1. **Test Email:**

   - Register a new admin account
   - Check email inbox for verification code
   - Check spam folder if not in inbox

2. **Check Logs:**

   - Check `logs/email_notifications.log` for success/failure
   - Check PHP error logs for detailed errors

3. **Verify Configuration:**
   - Run `test_email_config.php` to verify setup
   - Should show "✅ Email sent successfully!"

## Troubleshooting

### If Email Still Not Sending:

1. **Check Password:**

   - Must be exactly 16 characters
   - No spaces
   - Generated from App passwords (not regular password)

2. **Check 2-Step Verification:**

   - Must be enabled to generate App Passwords
   - Go to: https://myaccount.google.com/security

3. **Check Email Logs:**

   - File: `logs/email_notifications.log`
   - Look for error messages

4. **Check PHP Error Logs:**

   - Look for PHPMailer errors
   - Check for authentication failures

5. **Test with PHPMailer:**
   - Run `test_email_config.php`
   - Check detailed error messages

## Summary

**Problem:** Invalid Gmail App Password (only 7 characters)  
**Solution:** Generate a 16-character Gmail App Password and update `config/email.php`  
**Result:** Emails will be sent successfully to `merlyn.lagrimas122021@gmail.com`

After fixing the password, the verification code will be sent automatically when an admin registers.
