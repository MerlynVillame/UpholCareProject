# Quick Email Setup Guide

## ✅ PHPMailer is Already Installed!

Now you just need to configure your Gmail credentials.

## Step 1: Get Gmail App Password

1. **Go to Google Account:**
   - Visit: https://myaccount.google.com/security
   - Or: https://myaccount.google.com/ → Click "Security"

2. **Enable 2-Step Verification** (if not already enabled):
   - Under "Signing in to Google", click **2-Step Verification**
   - Follow the steps to enable it

3. **Generate App Password:**
   - Go back to **Security** page
   - Under "Signing in to Google", find **App passwords**
   - Click **App passwords**
   - Select **Mail** as the app
   - Select **Other (Custom name)** as device
   - Enter name: "UphoCare System"
   - Click **Generate**
   - **Copy the 16-character password** (it looks like: `abcd efgh ijkl mnop`)
   - **Important:** Remove all spaces when using it

## Step 2: Update config/email.php

Open the file: `config/email.php`

Find these lines (around line 10-11):
```php
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com'); // Change this to your email
define('EMAIL_SMTP_PASSWORD', 'your-app-password'); // Change this to your app password
```

Replace with your actual Gmail credentials:
```php
define('EMAIL_SMTP_USERNAME', 'your-actual-gmail@gmail.com'); // Your Gmail address
define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // Your 16-character App Password (no spaces)
```

**Example:**
```php
define('EMAIL_SMTP_USERNAME', 'merlyn.lagrimas122021@gmail.com'); // Your Gmail
define('EMAIL_SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // App Password (remove spaces)
```

## Step 3: Test Email Configuration

1. **Run the test script:**
   - Open in browser: `http://localhost/UphoCare/test_email_config.php`
   - This will test your email configuration

2. **Or test by registering:**
   - Register a new admin account
   - Check if verification code email is received

## Common Issues

### ❌ "Authentication failed"
- Make sure you're using **App Password**, not your regular Gmail password
- Verify 2-Step Verification is enabled
- Check that the App Password has no spaces

### ❌ "SMTP connect() failed"
- Check your internet connection
- Verify firewall settings
- Make sure port 587 is not blocked

### ❌ Email not received
- Check spam/junk folder
- Verify email address is correct
- Check email logs: `logs/email_notifications.log`

## After Configuration

Once you've updated `config/email.php`:
1. Save the file
2. Try registering a new admin account
3. Check your email inbox (and spam folder) for the verification code

The verification code will be automatically sent to the email address you use during registration!

