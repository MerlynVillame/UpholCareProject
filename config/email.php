<?php
/**
 * Email Configuration
 * Configure your email settings here
 * 
 * IMPORTANT: 
 * - EMAIL_SMTP_USERNAME is the SENDER's email (your system email account that sends emails)
 * - This sender email will send verification codes to ANY recipient email address
 * - Admins can register with ANY email address (Gmail, Yahoo, Outlook, etc.)
 * - The system will send verification codes to whatever email the admin uses during registration
 * 
 * How to get Gmail App Password:
 * 1. Go to https://myaccount.google.com/security
 * 2. Enable 2-Step Verification (if not already enabled)
 * 3. Go to "App passwords"
 * 4. Generate new app password for "Mail"
 * 5. Copy the 16-character password (remove spaces)
 * 6. Use it in EMAIL_SMTP_PASSWORD below
 * 
 * Example:
 * - Sender (this config): uphocare.system@gmail.com
 * - Recipients (admin emails): merlyn.lagrimas122021@gmail.com, admin@company.com, etc.
 * - System sends FROM sender TO any recipient email address
 */

// Email Configuration
define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_USERNAME', 'merlyn.batonghinog@gmail.com'); // ⚠️ CHANGE THIS: Your Gmail address (e.g., merlyn.lagrimas122021@gmail.com)
define('EMAIL_SMTP_PASSWORD', 'kfkvbpcrsxpqoakl'); // ⚠️ CHANGE THIS: Your Gmail App Password (16 characters, no spaces)
define('EMAIL_FROM_ADDRESS', 'noreply@upholcare.com');
define('EMAIL_FROM_NAME', 'UpholCare System');

// Email Settings
define('EMAIL_ENABLED', true); // Set to false to disable email notifications
define('EMAIL_TEST_MODE', false); // Set to true to log emails instead of sending them

// Notification Settings
define('NOTIFICATION_LOG_PATH', ROOT . DS . 'logs' . DS . 'email_notifications.log');
