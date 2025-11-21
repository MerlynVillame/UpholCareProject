# Admin Email Verification Flow

## Complete Registration and Verification Process

### Step 1: Admin Registration
1. Admin fills out registration form at `/auth/registerAdmin`
   - Full Name
   - Email Address
   - Password
   - Phone (optional)
2. Admin clicks "Register & Verify Admin Key"
3. Account is created with status `pending_verification`
4. Record appears in Super Admin's Admin Registrations page immediately
5. Admin is redirected to login page with success message

### Step 2: Super Admin Sends Verification Code
1. Super admin logs in to control panel
2. Goes to "Admin Registrations" page
3. Sees new registration with status "Waiting for Verification"
4. Clicks "Send Verification Code via Gmail" button
5. System generates a 6-digit verification code
6. Code is sent to admin's email address via Gmail
7. Registration status shows "Code Sent" with timestamp

### Step 3: Admin Receives Email
- Admin receives email in their Gmail inbox
- Email contains:
  - 6-digit verification code
  - Instructions on how to use it
  - Direct link to verification page
  - Expiration notice (24 hours)

### Step 4: Admin Verifies Code
1. Admin receives email with verification code
2. Admin goes to verification page:
   - Direct link from email, OR
   - Login page → Click "Enter Verification Code" link, OR
   - Visit `/auth/verifyCode?email=admin@example.com`
3. Admin enters 6-digit code
4. System validates code:
   - Checks if code matches
   - Checks if code has expired (24 hours)
   - Checks verification attempts (max 5)
5. If valid:
   - Registration status changes to `pending`
   - User status changes to `pending`
   - Admin is redirected to login with success message
6. If invalid:
   - Error message shown
   - Verification attempts incremented
   - Admin can try again

### Step 5: Super Admin Approval
1. Super admin sees verified registration (status = `pending`)
2. Super admin reviews registration details
3. Super admin can:
   - **Approve**: Account activated, moved to admin list
   - **Reject**: Registration rejected with reason
4. On approval:
   - User status changes to `active`
   - Admin account created in `control_panel_admins` table
   - Registration status changes to `approved`
   - Admin can now login

## Email Configuration

### Setup Gmail
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

## Database Status Flow

```
pending_verification → pending → approved
     (waiting)      (verified)  (activated)
```

- **pending_verification**: Waiting for super admin to send code
- **pending**: Code verified, waiting for super admin approval
- **approved**: Account activated and can login

## Security Features

1. **Code Expiration**: Verification codes expire after 24 hours
2. **Attempt Limit**: Maximum 5 verification attempts
3. **Code Validation**: 6-digit numeric code
4. **Email Verification**: Code sent only via Gmail
5. **Status Tracking**: Each step tracked in database

## User Experience

- Clear instructions at each step
- Email notifications with verification links
- Helpful error messages
- Resend code option for super admin
- Status indicators in admin registrations page

## Testing Checklist

- [ ] Admin registers successfully
- [ ] Registration appears in super admin dashboard
- [ ] Super admin can send verification code
- [ ] Email is received in Gmail inbox
- [ ] Verification code is correct
- [ ] Admin can enter and verify code
- [ ] Status updates correctly after verification
- [ ] Super admin can approve/reject
- [ ] Approved admin can login

