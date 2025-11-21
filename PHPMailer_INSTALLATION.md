# PHPMailer Installation Guide

## Overview

The UphoCare system now uses PHPMailer for sending verification codes via email. PHPMailer provides better SMTP support and more reliable email delivery compared to PHP's built-in `mail()` function.

## Installation Methods

### Method 1: Using Composer (Recommended)

1. **Install Composer** (if not already installed):
   - Download from: https://getcomposer.org/download/
   - Or use: `php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"`
   - Run: `php composer-setup.php`

2. **Install PHPMailer**:
   ```bash
   cd C:\xampp\htdocs\UphoCare
   composer install
   ```

   This will install PHPMailer in the `vendor/` directory.

### Method 2: Manual Installation

1. **Download PHPMailer**:
   - Visit: https://github.com/PHPMailer/PHPMailer/releases
   - Download the latest release (ZIP file)

2. **Extract to vendor directory**:
   - Extract the ZIP file
   - Copy the `PHPMailer` folder to: `C:\xampp\htdocs\UphoCare\vendor\phpmailer\phpmailer\`
   - The structure should be: `vendor/phpmailer/phpmailer/src/PHPMailer.php`

## Email Configuration

After installing PHPMailer, configure your email settings in `config/email.php`:

```php
// Email Configuration
define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com'); // Your email
define('EMAIL_SMTP_PASSWORD', 'your-app-password'); // Your app password
define('EMAIL_FROM_ADDRESS', 'noreply@uphocare.com');
define('EMAIL_FROM_NAME', 'UphoCare System');
```

### Gmail Setup

1. **Enable 2-Factor Authentication** on your Google Account
2. **Generate App Password**:
   - Go to Google Account → Security → 2-Step Verification
   - Click "App passwords"
   - Generate a new app password for "Mail"
   - Use this 16-character password in `EMAIL_SMTP_PASSWORD`

## How It Works

### Automatic Email Verification Flow

1. **Admin Registers**:
   - Admin fills out registration form
   - System automatically generates a 4-digit verification code
   - Code is stored in `admin_registrations` table
   - Email is automatically sent via PHPMailer with the verification code

2. **Admin Receives Email**:
   - Admin receives email with verification code
   - Email contains a link to the verification page

3. **Admin Verifies Code**:
   - Admin visits verification page
   - Enters the 4-digit code from email
   - System verifies the code

4. **Admin Can Login**:
   - After verification, admin account is activated
   - Admin can now login to the system

### Fallback Behavior

If PHPMailer is not installed, the system will automatically fall back to PHP's `mail()` function. However, PHPMailer is recommended for better reliability.

## Testing

1. **Test Email Configuration**:
   - Register a new admin account
   - Check if verification code email is received
   - Verify the code works on the verification page

2. **Check Email Logs**:
   - Email attempts are logged in: `logs/email_notifications.log`
   - Check this file if emails are not being sent

## Troubleshooting

### Emails Not Sending

1. **Check PHPMailer Installation**:
   - Verify `vendor/phpmailer/phpmailer/src/PHPMailer.php` exists
   - Check Composer autoload: `vendor/autoload.php`

2. **Check Email Configuration**:
   - Verify SMTP credentials in `config/email.php`
   - Test with Gmail App Password (not regular password)

3. **Check Email Logs**:
   - Review `logs/email_notifications.log` for error messages
   - Check PHP error logs for exceptions

4. **Test Mode**:
   - Set `EMAIL_TEST_MODE = true` in `config/email.php` to test without sending actual emails

### Common Issues

- **"Class PHPMailer not found"**: PHPMailer is not installed. Install via Composer or manually.
- **"SMTP authentication failed"**: Check your email credentials and app password.
- **"Connection timeout"**: Check firewall settings and SMTP port (587 for Gmail).

## Support

For more information:
- PHPMailer Documentation: https://github.com/PHPMailer/PHPMailer
- Gmail App Passwords: https://support.google.com/accounts/answer/185833

