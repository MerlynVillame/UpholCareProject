# ⚠️ ACTION REQUIRED: Configure Email Settings

## Problem
Verification codes are not being sent because email configuration is not set up.

## Solution - Follow These Steps:

### Step 1: Get Gmail App Password

1. **Go to Google Account Security:**
   - Visit: https://myaccount.google.com/security
   - Or: https://myaccount.google.com/ → Click "Security"

2. **Enable 2-Step Verification:**
   - Under "Signing in to Google", click **2-Step Verification**
   - Follow the steps to enable it (if not already enabled)

3. **Generate App Password:**
   - Go back to **Security** page
   - Under "Signing in to Google", find **App passwords**
   - Click **App passwords**
   - Select **Mail** as the app
   - Select **Other (Custom name)** as device
   - Enter name: "UphoCare System"
   - Click **Generate**
   - **Copy the 16-character password** (it looks like: `abcd efgh ijkl mnop`)
   - **IMPORTANT:** Remove all spaces when using it (should be: `abcdefghijklmnop`)

### Step 2: Update config/email.php

Open the file: `config/email.php`

**Find these lines (around line 10-11):**
```php
define('EMAIL_SMTP_USERNAME', 'your-email@gmail.com');
define('EMAIL_SMTP_PASSWORD', 'your-app-password');
```

**Replace with your actual Gmail credentials:**
```php
define('EMAIL_SMTP_USERNAME', 'merlyn.lagrimas122021@gmail.com'); // Your Gmail address
define('EMAIL_SMTP_PASSWORD', 'abcdefghijklmnop'); // Your 16-character App Password (no spaces)
```

**Example:**
- If your Gmail is: `merlyn.lagrimas122021@gmail.com`
- And your App Password is: `abcd efgh ijkl mnop`
- Then use: `abcdefghijklmnop` (remove spaces)

### Step 3: Save and Test

1. **Save the file** `config/email.php`

2. **Test the configuration:**
   - Open in browser: `http://localhost/UphoCare/test_email_config.php`
   - This will show if configuration is correct

3. **Test by registering:**
   - Register a new admin account with email: `merlyn.lagrimas122021@gmail.com`
   - Check your email inbox (and spam folder) for the verification code

## What Should Happen After Configuration

1. ✅ Admin registers → System generates verification code
2. ✅ Code is automatically sent via email to `merlyn.lagrimas122021@gmail.com`
3. ✅ Admin receives email with verification code
4. ✅ Admin enters code to verify account
5. ✅ Account is activated and admin can login

## Troubleshooting

### ❌ Still not receiving emails?

1. **Check email logs:**
   - View: `logs/email_notifications.log`
   - Look for error messages

2. **Check spam folder:**
   - Gmail might put verification emails in spam
   - Check "Spam" or "Junk" folder

3. **Verify configuration:**
   - Run: `http://localhost/UphoCare/test_email_config.php`
   - Check if it shows ✅ or ❌ for each setting

4. **Common errors:**
   - "Authentication failed" → Use App Password, not regular password
   - "SMTP connect() failed" → Check internet connection
   - "PHPMailer not found" → Already installed, should be OK

## Need Help?

- Check `QUICK_EMAIL_SETUP.md` for detailed instructions
- Check `EMAIL_SETUP_INSTRUCTIONS.md` for troubleshooting
- View email logs: `logs/email_notifications.log`

