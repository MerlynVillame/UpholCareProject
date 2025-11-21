# Email Configuration Guide for Automatic Verification Notifications

## Overview
The system automatically sends verification codes to admin email addresses via Gmail when they register. This guide will help you configure the email settings properly.

## Gmail SMTP Configuration

### Step 1: Enable 2-Step Verification on Gmail
1. Go to your Google Account: https://myaccount.google.com/
2. Navigate to **Security** section
3. Enable **2-Step Verification**

### Step 2: Generate App Password
1. After enabling 2-Step Verification, go to **App passwords**
2. Select **Mail** as the app
3. Select **Other (Custom name)** as the device
4. Enter "UphoCare System" as the name
5. Click **Generate**
6. Copy the 16-character password (you'll need this for configuration)

### Step 3: Configure Email Settings
1. Open `config/email.php`
2. Update the following settings:

```php
define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com'); // Your Gmail address
define('EMAIL_SMTP_PASSWORD', 'your-16-char-app-password'); // The app password from Step 2
define('EMAIL_FROM_ADDRESS', 'your-email@gmail.com'); // Your Gmail address
define('EMAIL_FROM_NAME', 'UphoCare System');
define('EMAIL_ENABLED', true); // Must be true
define('EMAIL_TEST_MODE', false); // Set to false for production
```

### Step 4: Test Email Configuration
1. Register a test admin account
2. Check if the verification email is received
3. Check the error logs at `logs/email_notifications.log` if emails are not being sent

## Important Notes

- **App Password Required**: You cannot use your regular Gmail password. You must use an App Password.
- **Automatic Sending**: The verification code is automatically sent immediately after admin registration - no manual action required.
- **Email Logs**: All email attempts are logged in `logs/email_notifications.log` for debugging.
- **Test Mode**: When `EMAIL_TEST_MODE` is set to `true`, emails are logged but not actually sent.

## Troubleshooting

### Emails not being sent?
1. Check that `EMAIL_ENABLED` is set to `true`
2. Verify the App Password is correct (16 characters, no spaces)
3. Check that 2-Step Verification is enabled on your Gmail account
4. Review the error logs at `logs/email_notifications.log`
5. Check PHP error logs for any SMTP connection errors

### Email in spam folder?
- This is normal for automated emails
- Inform users to check their spam folder
- Consider using a custom domain email address for better deliverability

## Alternative: Using XAMPP Sendmail (Local Development)

For local development, you can configure XAMPP's sendmail:

1. Open `php.ini` in XAMPP
2. Find `[mail function]`
3. Configure sendmail:

```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

4. Configure `sendmail.ini`:

```ini
[sendmail]
smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=your-email@gmail.com
auth_password=your-app-password
force_sender=your-email@gmail.com
```

## Current Implementation

The system currently uses PHP's `mail()` function. For production environments, consider:
- Using PHPMailer library for better SMTP support
- Using a professional email service (SendGrid, Mailgun, etc.)
- Setting up proper SPF, DKIM, and DMARC records for your domain

## Automatic Notification Flow

1. Admin submits registration form
2. System creates user account with status `pending_verification`
3. System automatically generates 6-digit verification code
4. System immediately sends email via Gmail with verification code
5. Admin receives email with code and verification link
6. Admin enters code on verification page
7. After verification, status changes to `pending` (waiting for super admin approval)

