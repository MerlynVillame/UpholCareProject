# Email System - How It Works

## Important: Understanding Email Configuration

### How the Email System Works

The email configuration in `config/email.php` defines the **sender's email account** (the account that sends emails). This is **NOT** the recipient's email address.

**Key Points:**
- `EMAIL_SMTP_USERNAME` = **Sender's email** (your Gmail account that sends emails)
- `EMAIL_SMTP_PASSWORD` = **Sender's password** (Gmail App Password for the sender account)
- **Recipient emails** = Any email address (admins, customers, etc.)

### Example Scenario

**Configuration in `config/email.php`:**
```php
define('EMAIL_SMTP_USERNAME', 'uphocare.system@gmail.com'); // Sender's Gmail
define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // Sender's App Password
```

**Admin Registration:**
- Admin registers with: `merlyn.lagrimas122021@gmail.com`
- Admin registers with: `admin@example.com`
- Admin registers with: `john.doe@company.com`

**What Happens:**
- ✅ System sends email **FROM**: `uphocare.system@gmail.com`
- ✅ System sends email **TO**: `merlyn.lagrimas122021@gmail.com` (or any admin email)
- ✅ Verification code is sent to the admin's email address
- ✅ Works with **ANY** email address (Gmail, Yahoo, Outlook, etc.)

## Answer to Your Question

### "What if the admin uses another email?"

**✅ It works perfectly!** The system can send verification codes to **any email address**, regardless of:
- Email provider (Gmail, Yahoo, Outlook, etc.)
- Domain (gmail.com, company.com, etc.)
- The sender's email address

### How It Works:

1. **You configure ONE sender email** in `config/email.php`:
   ```php
   define('EMAIL_SMTP_USERNAME', 'your-gmail@gmail.com'); // This sends emails
   define('EMAIL_SMTP_PASSWORD', 'your-app-password'); // Password for sender
   ```

2. **Admins register with ANY email address**:
   - `merlyn.lagrimas122021@gmail.com`
   - `admin@company.com`
   - `john.doe@yahoo.com`
   - `any.email@any-domain.com`

3. **System sends verification code**:
   - **From**: Your configured Gmail account
   - **To**: The admin's email address (whatever they registered with)

## Important Notes

### 1. Sender Email (EMAIL_SMTP_USERNAME)
- This is **your system's email account** (the account that sends emails)
- Should be a Gmail account with App Password enabled
- Used to send emails to **all** recipients (admins, customers, etc.)
- Only **ONE** sender email is needed

### 2. Recipient Emails
- Can be **any email address** (Gmail, Yahoo, Outlook, etc.)
- Each admin/customer can use their own email
- No configuration needed for recipient emails
- System automatically sends to the email address they registered with

### 3. Gmail Limitations

**Gmail sending limits:**
- **Free Gmail**: 500 emails per day
- **Google Workspace**: 2,000 emails per day

If you need to send more emails, consider:
- Using a dedicated email service (SendGrid, Mailgun, etc.)
- Using Google Workspace (paid Gmail)
- Using a different SMTP server

## Configuration Example

### Scenario: System sends emails to multiple admins

**Configuration (`config/email.php`):**
```php
// Sender's email (system email account)
define('EMAIL_SMTP_USERNAME', 'uphocare.system@gmail.com');
define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // App Password
define('EMAIL_FROM_ADDRESS', 'noreply@uphocare.com');
define('EMAIL_FROM_NAME', 'UphoCare System');
```

**Admin Registrations:**
1. Admin 1 registers with: `merlyn.lagrimas122021@gmail.com`
   - ✅ Verification code sent to: `merlyn.lagrimas122021@gmail.com`

2. Admin 2 registers with: `admin@company.com`
   - ✅ Verification code sent to: `admin@company.com`

3. Admin 3 registers with: `john.doe@yahoo.com`
   - ✅ Verification code sent to: `john.doe@yahoo.com`

**All emails are sent FROM:** `uphocare.system@gmail.com`  
**All emails are sent TO:** The admin's registered email address

## Troubleshooting

### Issue: Emails not received by some recipients

**Possible causes:**
1. **Spam folder**: Check spam/junk folder
2. **Email provider blocking**: Some providers block emails from unknown senders
3. **Gmail sending limits**: Free Gmail has daily sending limits
4. **Invalid email address**: Check if email address is correct

### Issue: Can only send to Gmail addresses

**Solution:**
- Gmail can send to **any email provider** (Yahoo, Outlook, etc.)
- If emails are not received, check spam folders
- Verify the recipient's email address is correct

## Summary

✅ **One sender email** configured in `config/email.php`  
✅ **Multiple recipient emails** (any email address)  
✅ **Works with any email provider** (Gmail, Yahoo, Outlook, etc.)  
✅ **No additional configuration** needed for recipient emails  
✅ **Automatic sending** to the email address used during registration

The system is designed to send verification codes to **any email address** that admins use during registration. You only need to configure **one sender email account** in `config/email.php`.

