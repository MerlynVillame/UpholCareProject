# Admin Email Verification System

## Overview
The admin registration process now uses email-based verification. After an admin registers, the super admin sends a verification code via email. The admin enters the code to complete registration, then waits for final approval.

## Registration Flow

### Step 1: Admin Registration
1. Admin fills out registration form at `/auth/registerAdmin`
2. Admin account is created with status `pending_verification`
3. Record is created in `admin_registrations` table with status `pending_verification`
4. Admin is redirected to login page with success message

### Step 2: Super Admin Sends Verification Code
1. Super admin goes to Admin Registrations page
2. Super admin sees pending registrations with "Send Verification Code" button
3. Super admin clicks button → 6-digit code is generated and sent via email
4. Registration status remains `pending_verification` until code is verified

### Step 3: Admin Verifies Code
1. Admin receives email with verification code
2. Admin goes to `/auth/verifyCode?email=admin@example.com`
3. Admin enters the 6-digit verification code
4. If code is correct:
   - Registration status changes to `pending`
   - User status changes to `pending`
   - Admin is redirected to login with success message
5. If code is incorrect:
   - Verification attempts are incremented (max 5 attempts)
   - Error message is shown
   - Admin can try again

### Step 4: Super Admin Approval
1. Super admin sees verified registrations (status = `pending`)
2. Super admin can approve or reject the registration
3. On approval:
   - User status changes to `active`
   - Admin account is created in `control_panel_admins` table
   - Registration status changes to `approved`
   - Admin can now login

## Database Changes

### New Fields in `admin_registrations` Table
- `verification_code` (VARCHAR(10)) - 6-digit verification code
- `verification_code_sent_at` (TIMESTAMP) - When code was sent
- `verification_code_verified_at` (TIMESTAMP) - When code was verified
- `verification_attempts` (INT) - Number of verification attempts

### Updated Status Enum
- `pending_verification` - Waiting for super admin to send code
- `pending` - Code verified, waiting for super admin approval
- `approved` - Approved and activated
- `rejected` - Rejected by super admin

## Email Configuration

### Setup Instructions
1. Edit `config/email.php`:
   ```php
   define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
   define('EMAIL_SMTP_PORT', 587);
   define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com');
   define('EMAIL_SMTP_PASSWORD', 'your-app-password');
   define('EMAIL_FROM_ADDRESS', 'noreply@uphocare.com');
   define('EMAIL_FROM_NAME', 'UphoCare System');
   define('EMAIL_ENABLED', true);
   define('EMAIL_TEST_MODE', false);
   ```

2. For Gmail:
   - Enable 2-Factor Authentication
   - Generate App Password
   - Use App Password in `EMAIL_SMTP_PASSWORD`

## Database Migration

Run the following SQL script to add verification code fields:
```sql
-- File: database/add_verification_code_to_admin_registrations.sql
```

## New Routes

- `control-panel/sendVerificationCode/{id}` - Send verification code to admin
- `auth/verifyCode?email=...` - Show verification code entry page
- `auth/processVerifyCode` - Process verification code submission

## Security Features

1. **Code Expiration**: Verification codes expire after 24 hours
2. **Attempt Limit**: Maximum 5 verification attempts
3. **Code Validation**: 6-digit numeric code
4. **Email Verification**: Code is sent only via email
5. **Status Tracking**: Each step is tracked in database

## Testing

1. Register a new admin account
2. Login as super admin
3. Go to Admin Registrations page
4. Click "Send Verification Code" for the new registration
5. Check email for verification code
6. Go to verification page and enter code
7. Login as super admin and approve the registration
8. Verify admin can now login

## Troubleshooting

### Email Not Sending
- Check `EMAIL_ENABLED` is set to `true`
- Verify SMTP credentials
- Check email logs in `logs/email_notifications.log`
- Test email configuration in admin panel

### Verification Code Not Working
- Check if code has expired (24 hours)
- Verify code matches exactly (case-sensitive)
- Check verification attempts (max 5)
- Ensure email address matches registration

### Admin Not Appearing in Admin List
- Verify registration was approved
- Check `control_panel_admins` table for admin record
- Ensure admin status is `active`

