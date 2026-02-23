# Admin Email Verification Implementation

## Overview

The UphoCare system now automatically sends verification codes via email when an admin creates an account. The admin must enter this verification code before they can log in.

## What Was Implemented

### 1. PHPMailer Integration

- **Updated `core/NotificationService.php`**:
  - Modified `sendEmail()` method to use PHPMailer when available
  - Falls back to PHP's `mail()` function if PHPMailer is not installed
  - Supports SMTP authentication with configurable settings

### 2. Automatic Verification Code Generation

- **Updated `controllers/AuthController.php`**:
  - Modified `processRegisterAdmin()` method to automatically:
    - Generate a 4-digit verification code when admin registers
    - Store the code in `admin_registrations` table
    - Send the code via email using PHPMailer
    - Redirect admin to verification page immediately after registration

### 3. Login Verification Check

- **Login Process** (already implemented):
  - `processLogin()` method checks if admin has verified their code
  - Blocks login if verification code is not verified
  - Redirects to verification page with helpful message

### 4. Updated Verification Page

- **Updated `views/auth/verify_code.php`**:
  - Changed messaging to reflect automatic code sending
  - Updated UI to show that code was automatically sent via email
  - Improved user experience with clearer instructions

## How It Works

### Registration Flow

1. **Admin Registers**:
   ```
   Admin fills registration form
   ↓
   System creates account with status 'pending_verification'
   ↓
   System generates 4-digit verification code
   ↓
   Code is stored in admin_registrations table
   ↓
   Email is automatically sent via PHPMailer with verification code
   ↓
   Admin is redirected to verification page
   ```

2. **Email Sent**:
   - Email contains verification code
   - Email includes link to verification page
   - Email is sent using PHPMailer (or mail() fallback)

3. **Admin Verifies**:
   ```
   Admin receives email with code
   ↓
   Admin visits verification page
   ↓
   Admin enters 4-digit code
   ↓
   System verifies code
   ↓
   Account status updated to 'pending' (waiting for super admin approval)
   ↓
   Admin can now login (after super admin approval)
   ```

### Login Flow

1. **Admin Attempts Login**:
   - System checks if account exists
   - System checks if account is verified
   - If not verified, redirects to verification page
   - If verified, allows login (if super admin approved)

## Files Modified

1. **`core/NotificationService.php`**:
   - Updated `sendEmail()` to use PHPMailer
   - Added PHPMailer detection and fallback logic

2. **`controllers/AuthController.php`**:
   - Updated `processRegisterAdmin()` to automatically generate and send verification code
   - Added automatic email sending after registration

3. **`views/auth/verify_code.php`**:
   - Updated messaging to reflect automatic code sending
   - Improved user experience

4. **`composer.json`** (new):
   - Added PHPMailer dependency

5. **`PHPMailer_INSTALLATION.md`** (new):
   - Installation and configuration guide

## Configuration

### Email Settings

Configure email in `config/email.php`:

```php
define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com');
define('EMAIL_SMTP_PASSWORD', 'your-app-password');
define('EMAIL_FROM_ADDRESS', 'noreply@uphocare.com');
define('EMAIL_FROM_NAME', 'UphoCare System');
```

### Gmail Setup

1. Enable 2-Factor Authentication on Google Account
2. Generate App Password:
   - Go to Google Account → Security → 2-Step Verification
   - Click "App passwords"
   - Generate new app password for "Mail"
   - Use 16-character password in `EMAIL_SMTP_PASSWORD`

## Installation

### Install PHPMailer

**Option 1: Using Composer (Recommended)**
```bash
cd C:\xampp\htdocs\UphoCare
composer install
```

**Option 2: Manual Installation**
- Download PHPMailer from: https://github.com/PHPMailer/PHPMailer/releases
- Extract to: `vendor/phpmailer/phpmailer/`

## Testing

1. **Register New Admin**:
   - Go to admin registration page
   - Fill out registration form
   - Submit registration
   - Check email inbox for verification code

2. **Verify Code**:
   - Visit verification page (auto-redirected after registration)
   - Enter 4-digit code from email
   - Submit verification

3. **Login**:
   - After verification, admin can login (if super admin approved)
   - System checks verification status before allowing login

## Troubleshooting

### Emails Not Sending

1. **Check PHPMailer Installation**:
   - Verify `vendor/phpmailer/phpmailer/src/PHPMailer.php` exists
   - Check if Composer autoload works

2. **Check Email Configuration**:
   - Verify SMTP credentials in `config/email.php`
   - Use Gmail App Password (not regular password)

3. **Check Email Logs**:
   - Review `logs/email_notifications.log` for errors
   - Check PHP error logs

4. **Test Mode**:
   - Set `EMAIL_TEST_MODE = true` to test without sending emails

### Common Issues

- **"Class PHPMailer not found"**: Install PHPMailer via Composer or manually
- **"SMTP authentication failed"**: Check email credentials and app password
- **"Connection timeout"**: Check firewall and SMTP port (587 for Gmail)

## Benefits

1. **Automatic Process**: No manual intervention needed - code is sent automatically
2. **Better Reliability**: PHPMailer provides better SMTP support than mail()
3. **User-Friendly**: Clear instructions and automatic redirects
4. **Secure**: Verification code required before login
5. **Fallback Support**: Works even if PHPMailer is not installed (uses mail())

## Next Steps

1. Install PHPMailer (see `PHPMailer_INSTALLATION.md`)
2. Configure email settings in `config/email.php`
3. Test registration and verification flow
4. Monitor email logs for any issues

## Support

For issues or questions:
- Check `PHPMailer_INSTALLATION.md` for installation help
- Review email logs in `logs/email_notifications.log`
- Check PHP error logs for exceptions

