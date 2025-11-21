# Email Setup Instructions - Fix Verification Code Not Sending

## Problem
Verification codes are not being sent to email addresses (e.g., merlyn.lagrimas122021@gmail.com).

## Root Cause
The email configuration file has placeholder values that need to be replaced with actual Gmail credentials.

## Solution

### Step 1: Update Email Configuration

Edit the file: `config/email.php`

Replace these lines:
```php
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com'); // Change this to your email
define('EMAIL_SMTP_PASSWORD', 'your-app-password'); // Change this to your app password
```

With your actual Gmail credentials:
```php
define('EMAIL_SMTP_USERNAME', 'your-actual-email@gmail.com'); // Your Gmail address
define('EMAIL_SMTP_PASSWORD', 'your-16-character-app-password'); // Gmail App Password
```

### Step 2: Get Gmail App Password

**Important:** You cannot use your regular Gmail password. You need to generate an App Password.

1. **Go to your Google Account:**
   - Visit: https://myaccount.google.com/
   - Click on **Security** (left sidebar)

2. **Enable 2-Step Verification:**
   - Under "Signing in to Google", click **2-Step Verification**
   - Follow the steps to enable it (if not already enabled)

3. **Generate App Password:**
   - Go back to **Security** page
   - Under "Signing in to Google", click **App passwords**
   - Select **Mail** as the app
   - Select **Other (Custom name)** as device
   - Enter name: "UphoCare System"
   - Click **Generate**
   - **Copy the 16-character password** (it will look like: `abcd efgh ijkl mnop`)

4. **Update config/email.php:**
   ```php
   define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // Use the 16-character password (no spaces)
   ```

### Step 3: Install PHPMailer (Recommended)

PHPMailer provides better email delivery than PHP's mail() function.

**Option A: Using Composer (Recommended)**
```bash
cd C:\xampp\htdocs\UphoCare
composer install
```

**Option B: Manual Installation**
1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer/releases
2. Extract to: `vendor/phpmailer/phpmailer/`

### Step 4: Test Email Configuration

1. **Run the test script:**
   - Open in browser: `http://localhost/UphoCare/test_email_config.php`
   - This will test your email configuration and show any errors

2. **Check email logs:**
   - View: `logs/email_notifications.log`
   - This shows all email attempts and their status

### Step 5: Verify Configuration

After updating `config/email.php`, your file should look like:

```php
<?php
// Email Configuration
define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_USERNAME', 'your-actual-email@gmail.com'); // ✅ Your Gmail
define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // ✅ Your App Password
define('EMAIL_FROM_ADDRESS', 'noreply@uphocare.com');
define('EMAIL_FROM_NAME', 'UphoCare System');

// Email Settings
define('EMAIL_ENABLED', true);
define('EMAIL_TEST_MODE', false);
```

## Troubleshooting

### Issue: "SMTP connect() failed"
**Solution:** 
- Check your internet connection
- Verify EMAIL_SMTP_HOST is 'smtp.gmail.com'
- Verify EMAIL_SMTP_PORT is 587
- Check firewall settings

### Issue: "Authentication failed"
**Solution:**
- Make sure you're using App Password, not regular password
- Verify 2-Step Verification is enabled
- Check that the App Password is correct (no spaces)

### Issue: "PHPMailer not found"
**Solution:**
- Install PHPMailer: `composer install`
- Or download manually and place in `vendor/phpmailer/phpmailer/`

### Issue: Emails going to spam
**Solution:**
- Check spam/junk folder
- Add sender email to contacts
- Use a professional "From" address

## Quick Test

After configuration, test by registering a new admin account. The verification code should be sent automatically to the email address used during registration.

## Need Help?

1. Check `logs/email_notifications.log` for error messages
2. Check PHP error logs (usually in `C:\xampp\php\logs\php_error_log`)
3. Run `test_email_config.php` to diagnose issues

