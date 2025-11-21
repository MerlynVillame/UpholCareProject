# Email Notification System Setup Guide

## Overview

The UphoCare system now includes automatic email notifications that are sent to customers when their reservations are approved or rejected by administrators.

## Features

- ✅ **Automatic Notifications**: Customers receive emails when reservations are approved/rejected
- ✅ **Professional Templates**: Beautiful HTML email templates with company branding
- ✅ **Test Mode**: Safe testing without sending actual emails
- ✅ **Email Logging**: Track all email attempts for debugging
- ✅ **Admin Management**: Dedicated admin page for email configuration and testing

## Setup Instructions

### 1. Email Configuration

Edit the file `config/email.php` and update the following settings:

```php
// Email Configuration
define('EMAIL_SMTP_HOST', 'smtp.gmail.com'); // Your SMTP server
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com'); // Your email
define('EMAIL_SMTP_PASSWORD', 'your-app-password'); // Your app password
define('EMAIL_FROM_ADDRESS', 'noreply@uphocare.com');
define('EMAIL_FROM_NAME', 'UphoCare System');

// Email Settings
define('EMAIL_ENABLED', true); // Set to false to disable emails
define('EMAIL_TEST_MODE', false); // Set to true for testing
```

### 2. Gmail Setup (Recommended)

#### Step 1: Enable 2-Factor Authentication

1. Go to your Google Account settings
2. Navigate to Security
3. Enable 2-Step Verification

#### Step 2: Generate App Password

1. In Security settings, find "App passwords"
2. Generate a new app password for "Mail"
3. Use this password in `EMAIL_SMTP_PASSWORD`

#### Step 3: Update Configuration

```php
define('EMAIL_SMTP_USERNAME', 'your-gmail@gmail.com');
define('EMAIL_SMTP_PASSWORD', 'your-16-character-app-password');
```

### 3. Other Email Providers

#### Outlook/Hotmail

```php
define('EMAIL_SMTP_HOST', 'smtp-mail.outlook.com');
define('EMAIL_SMTP_PORT', 587);
```

#### Yahoo Mail

```php
define('EMAIL_SMTP_HOST', 'smtp.mail.yahoo.com');
define('EMAIL_SMTP_PORT', 587);
```

#### Custom SMTP Server

```php
define('EMAIL_SMTP_HOST', 'your-smtp-server.com');
define('EMAIL_SMTP_PORT', 587); // or 465 for SSL
```

### 4. Testing the Configuration

#### Method 1: Admin Panel

1. Login as admin
2. Go to "Email Notifications" in the sidebar
3. Enter a test email address
4. Click "Send Test Email"

#### Method 2: Test Mode

1. Set `EMAIL_TEST_MODE` to `true` in config
2. Accept/reject a reservation
3. Check the email logs (no actual emails sent)

### 5. Email Templates

The system includes two professional email templates:

#### Approval Email

- **Subject**: "Reservation Approved - [Booking Number]"
- **Content**: Confirmation message with booking details
- **Design**: Green theme with success indicators

#### Rejection Email

- **Subject**: "Reservation Update - [Booking Number]"
- **Content**: Rejection message with reason
- **Design**: Red theme with clear explanation

### 6. Email Logs

All email attempts are logged to `logs/email_notifications.log`:

```json
{
  "timestamp": "2024-01-15 10:30:00",
  "to": "customer@example.com",
  "subject": "Reservation Approved - BKG-20240115-0001",
  "success": "YES",
  "status": "SUCCESS"
}
```

### 7. Troubleshooting

#### Common Issues

**Emails not sending:**

1. Check `EMAIL_ENABLED` is set to `true`
2. Verify SMTP credentials
3. Check server mail() function is enabled
4. Review email logs for errors

**Gmail authentication errors:**

1. Ensure 2FA is enabled
2. Use App Password, not regular password
3. Check "Less secure app access" settings

**Emails going to spam:**

1. Set up SPF records for your domain
2. Use a professional "From" address
3. Avoid spam trigger words

#### Debug Mode

Enable test mode to safely test without sending emails:

```php
define('EMAIL_TEST_MODE', true);
```

### 8. Security Considerations

- **Credentials**: Never commit email passwords to version control
- **Environment Variables**: Consider using environment variables for production
- **Rate Limiting**: Implement rate limiting for email sending
- **Validation**: Always validate email addresses before sending

### 9. Production Deployment

#### Environment-Specific Configuration

```php
// Production
define('EMAIL_ENABLED', true);
define('EMAIL_TEST_MODE', false);

// Development
define('EMAIL_ENABLED', false);
define('EMAIL_TEST_MODE', true);
```

#### Server Requirements

- PHP `mail()` function enabled
- SMTP server access
- Proper DNS records (SPF, DKIM)

### 10. Monitoring

#### Email Delivery Monitoring

- Check email logs regularly
- Monitor bounce rates
- Set up email delivery alerts

#### Performance Monitoring

- Track email sending times
- Monitor server resources
- Log failed attempts

## Usage

### For Administrators

1. **Accept Reservation**: Customer automatically receives approval email
2. **Reject Reservation**: Customer receives rejection email with reason
3. **Monitor Logs**: Check email delivery status in admin panel

### For Customers

1. **Reservation Approved**: Receive confirmation email with booking details
2. **Reservation Rejected**: Receive notification with rejection reason
3. **Email Actions**: Click links to view reservations or make new bookings

## Support

If you encounter issues:

1. Check the email logs in the admin panel
2. Verify SMTP configuration
3. Test with a simple email first
4. Check server error logs

## Future Enhancements

- SMS notifications
- Push notifications
- Email template customization
- Bulk email management
- Advanced email analytics
