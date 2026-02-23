# 🔧 Quick Fix: Email Not Sending Verification Codes

## Problem

The email `merlyn.lagrimas122021@gmail.com` is not receiving verification codes. All email attempts are failing.

## Solution Steps

### Step 1: Test SMTP Authentication

Open in your browser:

```
http://localhost/UphoCare/fix_email_auth.php
```

This will test if your Gmail App Password is working correctly.

### Step 2: Send Test Verification Code

Open in your browser:

```
http://localhost/UphoCare/send_test_verification_code.php?email=merlyn.lagrimas122021@gmail.com&name=Merlyn Lagrimas
```

This will:

- Test SMTP connection
- Send a test verification code to the email
- Show if the email was sent successfully

### Step 3: Check Email Configuration

**File:** `config/email.php`

**Current Configuration:**

- EMAIL_SMTP_USERNAME: `merlyn.batonghinog@gmail.com`
- EMAIL_SMTP_PASSWORD: `kfkvbpcrsxpqoakl` (16 characters ✅)

**If authentication is failing:**

1. Go to: https://myaccount.google.com/apppasswords
2. Generate a new App Password for "Mail"
3. Copy the 16-character password (remove spaces)
4. Update `EMAIL_SMTP_PASSWORD` in `config/email.php`

### Step 4: Check Email Logs

**File:** `logs/email_notifications.log`

Recent entries show:

- All emails are failing
- Status: "FAILED (mail())" - means PHPMailer failed and fell back to mail()

**This means:**

- PHPMailer authentication is likely failing
- System is falling back to PHP's mail() function
- mail() doesn't work on localhost/XAMPP

### Step 5: Verify PHPMailer is Working

1. Run `fix_email_auth.php` to test authentication
2. If authentication fails, generate a new Gmail App Password
3. Update `config/email.php` with the new password
4. Test again

## Common Issues

### Issue 1: Authentication Failed

**Symptom:** "Could not authenticate" error
**Solution:**

- Generate a new Gmail App Password
- Make sure 2-Step Verification is enabled
- Update `EMAIL_SMTP_PASSWORD` in `config/email.php`

### Issue 2: Connection Failed

**Symptom:** "SMTP connect() failed"
**Solution:**

- Check internet connection
- Check firewall settings
- Verify EMAIL_SMTP_HOST and EMAIL_SMTP_PORT

### Issue 3: Falling Back to mail()

**Symptom:** Logs show "FAILED (mail())"
**Solution:**

- Fix PHPMailer authentication
- Don't rely on mail() fallback
- Ensure PHPMailer is working correctly

## Testing

After fixing the configuration:

1. **Test Authentication:**

   ```
   http://localhost/UphoCare/fix_email_auth.php
   ```

2. **Send Test Email:**

   ```
   http://localhost/UphoCare/send_test_verification_code.php?email=merlyn.lagrimas122021@gmail.com
   ```

3. **Register New Admin:**
   - Register with `merlyn.lagrimas122021@gmail.com`
   - Check email inbox for verification code
   - Check spam folder if not in inbox

## Expected Result

After fixing:

- ✅ SMTP authentication succeeds
- ✅ Email is sent via PHPMailer (not mail())
- ✅ Verification code is received in email inbox
- ✅ Admin can verify account and login

## Next Steps

1. Run `fix_email_auth.php` to test authentication
2. If authentication fails, generate new Gmail App Password
3. Update `config/email.php`
4. Run `send_test_verification_code.php` to test email sending
5. Register a new admin account to verify it works
