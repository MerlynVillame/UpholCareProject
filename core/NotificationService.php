<?php
/**
 * Notification Service
 * Handles email notifications for various system events
 */

class NotificationService {
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        // Load email configuration
        require_once ROOT . DS . 'config' . DS . 'email.php';
        
        $this->smtpHost = EMAIL_SMTP_HOST;
        $this->smtpPort = EMAIL_SMTP_PORT;
        $this->smtpUsername = EMAIL_SMTP_USERNAME;
        $this->smtpPassword = EMAIL_SMTP_PASSWORD;
        $this->fromEmail = EMAIL_FROM_ADDRESS;
        $this->fromName = EMAIL_FROM_NAME;
    }
    
    /**
     * Send reservation approval notification
     */
    public function sendReservationApproval($customerEmail, $customerName, $bookingData) {
        $subject = "Reservation Approved - " . $bookingData['booking_number'];
        
        $message = $this->getApprovalEmailTemplate($customerName, $bookingData);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Send booking confirmation with booking number for regular bookings
     */
    public function sendBookingConfirmation($customerEmail, $customerName, $bookingNumber, $booking) {
        $subject = "Booking Confirmed - " . $bookingNumber;
        
        $message = $this->getBookingConfirmationTemplate($customerName, $bookingNumber, $booking);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Send booking number assignment notification for repair reservation
     */
    public function sendBookingNumberAssignment($customerEmail, $customerName, $bookingNumber, $repairItem) {
        $subject = "Booking Number Assigned - " . $bookingNumber;
        
        $message = $this->getBookingNumberAssignmentTemplate($customerName, $bookingNumber, $repairItem);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Get booking confirmation email template
     */
    private function getBookingConfirmationTemplate($customerName, $bookingNumber, $booking) {
        $createdDate = !empty($booking['created_at']) ? date('F d, Y h:i A', strtotime($booking['created_at'])) : date('F d, Y');
        $serviceName = htmlspecialchars($booking['service_name'] ?? 'N/A');
        $categoryName = htmlspecialchars($booking['category_name'] ?? 'General');
        $serviceType = htmlspecialchars($booking['service_type'] ?? '—');
        $itemDescription = htmlspecialchars($booking['item_description'] ?? '—');
        $itemType = htmlspecialchars($booking['item_type'] ?? '—');
        $pickup = !empty($booking['pickup_date']) ? date('F d, Y', strtotime($booking['pickup_date'])) : '—';
        $status = htmlspecialchars(ucwords(str_replace('_', ' ', $booking['status'] ?? 'confirmed')));
        $paymentStatus = htmlspecialchars(ucfirst($booking['payment_status'] ?? 'unpaid'));
        $amount = number_format((float)($booking['total_amount'] ?? 0), 2);

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Confirmed</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 640px; margin: 0 auto; padding: 20px; }
                .header { background: #4e73df; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .booking-number { background: #4e73df; color: white; padding: 15px; border-radius: 8px; text-align: center; font-size: 1.25rem; font-weight: bold; margin: 20px 0; }
                .card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4e73df; }
                .table { width: 100%; border-collapse: collapse; }
                .table th, .table td { padding: 10px 12px; border: 1px solid #e3e6f0; text-align: left; }
                .table th { background: #f8f9fc; color: #2c3e50; }
                .badge { display: inline-block; padding: 6px 10px; border-radius: 12px; color: #fff; font-weight: 600; font-size: 12px; }
                .badge-status { background: #1cc88a; }
                .badge-paid { background: #1cc88a; }
                .badge-partial { background: #f6c23e; color: #2c3e50; }
                .badge-unpaid { background: #e74a3b; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Booking Confirmed</h1>
                    <p>Your reservation has been approved</p>
                </div>

                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    <p>Great news! Your booking has been <span class='badge badge-status'>{$status}</span> and a booking number has been assigned to you.</p>

                    <div class='booking-number'>Booking Number: {$bookingNumber}</div>

                    <div class='card'>
                        <h3>📋 Booking Details</h3>
                        <table class='table'>
                            <tr>
                                <th width='35%'>Booking Date</th>
                                <td>{$createdDate}</td>
                            </tr>
                            <tr>
                                <th>Service</th>
                                <td>{$serviceName}</td>
                            </tr>
                            <tr>
                                <th>Category / Type</th>
                                <td>{$categoryName} / {$serviceType}</td>
                            </tr>
                            <tr>
                                <th>Item Description</th>
                                <td>{$itemDescription}</td>
                            </tr>
                            <tr>
                                <th>Item Type</th>
                                <td>{$itemType}</td>
                            </tr>
                            <tr>
                                <th>Pickup Date</th>
                                <td>{$pickup}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>₱{$amount}</td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td><span class='badge " . (strtolower($paymentStatus) === 'paid' ? 'badge-paid' : (strtolower($paymentStatus) === 'partial' ? 'badge-partial' : 'badge-unpaid')) . "'>{$paymentStatus}</span></td>
                            </tr>
                        </table>
                    </div>

                    <p>Please keep your booking number for reference. You can view your booking details anytime in your account.</p>

                    <div style='text-align: center;'>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Bookings</a>
                    </div>
                </div>

                <div class='footer'>
                    <p>Thank you for choosing UphoCare!</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>© " . date('Y') . " UphoCare. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get booking number assignment email template
     */
    private function getBookingNumberAssignmentTemplate($customerName, $bookingNumber, $repairItem) {
        $itemName = htmlspecialchars($repairItem['item_name']);
        $itemDescription = htmlspecialchars(substr($repairItem['item_description'], 0, 100));
        $createdDate = date('F d, Y', strtotime($repairItem['created_at']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Number Assigned</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4e73df; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4e73df; }
                .booking-number { background: #4e73df; color: white; padding: 15px; border-radius: 8px; text-align: center; font-size: 1.5rem; font-weight: bold; margin: 20px 0; }
                .status-badge { background: #1cc88a; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Booking Number Assigned!</h1>
                    <p>Your repair reservation has been confirmed</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <p>Great news! Your repair reservation has been <span class='status-badge'>CONFIRMED</span> and a booking number has been assigned to you.</p>
                    
                    <div class='booking-number'>
                        Booking Number: {$bookingNumber}
                    </div>
                    
                    <div class='booking-details'>
                        <h3>📋 Reservation Details</h3>
                        <p><strong>Item:</strong> {$itemName}</p>
                        <p><strong>Description:</strong> {$itemDescription}...</p>
                        <p><strong>Request Date:</strong> {$createdDate}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>CONFIRMED</span></p>
                    </div>
                    
                    <p>Please keep your booking number for reference. You can view your receipt and reservation details in your account.</p>
                    
                    <div style='text-align: center;'>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Reservations</a>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Thank you for choosing UphoCare!</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>© " . date('Y') . " UphoCare. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Send reservation rejection notification
     */
    public function sendReservationRejection($customerEmail, $customerName, $bookingData, $rejectionReason) {
        $subject = "Reservation Update - " . $bookingData['booking_number'];
        
        $message = $this->getRejectionEmailTemplate($customerName, $bookingData, $rejectionReason);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Send admin verification code (after super admin approval)
     */
    public function sendAdminVerificationCode($adminEmail, $adminName, $verificationCode) {
        $subject = "Admin Account Approved - Verification Code - UphoCare";
        
        $message = $this->getAdminVerificationEmailTemplate($adminName, $verificationCode, $adminEmail, true);
        
        return $this->sendEmail($adminEmail, $subject, $message);
    }
    
    /**
     * Send admin rejection notification
     */
    public function sendAdminRejection($adminEmail, $adminName, $rejectionReason) {
        $subject = "Admin Account Registration Rejected - UphoCare";
        
        $message = $this->getAdminRejectionEmailTemplate($adminName, $rejectionReason);
        
        return $this->sendEmail($adminEmail, $subject, $message);
    }
    
    /**
     * Get admin verification email template
     * @param bool $approved - If true, indicates account was approved by super admin
     */
    private function getAdminVerificationEmailTemplate($adminName, $verificationCode, $adminEmail, $approved = false) {
        // Get base URL for verification link
        $baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/';
        $verificationLink = $baseUrl . 'auth/verifyCode?email=' . urlencode($adminEmail) . '&role=admin';
        
        $approvalMessage = $approved ? "
                    <div style='background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                        <strong>✅ Account Approved!</strong><br>
                        Your admin account has been reviewed and approved by the super admin. Your documents and information have been verified. You can now proceed with verification using the code below.
                    </div>
        " : "";
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .container {
                    background: #f9f9f9;
                    border-radius: 10px;
                    padding: 30px;
                    margin: 20px 0;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 20px;
                    border-radius: 10px 10px 0 0;
                    text-align: center;
                }
                .content {
                    background: white;
                    padding: 30px;
                    border-radius: 0 0 10px 10px;
                }
                .verification-code {
                    background: #f0f0f0;
                    border: 2px dashed #667eea;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    margin: 20px 0;
                    font-size: 32px;
                    font-weight: bold;
                    letter-spacing: 5px;
                    color: #667eea;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    color: #666;
                    font-size: 12px;
                }
                .warning {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Admin Account Verification</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$adminName}</strong>,</p>
                    
                    {$approvalMessage}
                    
                    <p>Your verification code has been generated. To complete your registration, please use the 4-digit verification code below:</p>
                    
                    <div class='verification-code'>
                        {$verificationCode}
                    </div>
                    
                    <p>This 4-digit verification code was sent to your email address. The code will expire after 24 hours for security purposes.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$verificationLink}' class='btn' style='display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>Verify My Account</a>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Important:</strong>
                        <ul>
                            <li>This code was automatically generated and sent - no manual action was required</li>
                            <li>Do not share this verification code with anyone</li>
                            <li>The code is valid for 24 hours</li>
                            <li>If you did not register for this account, please ignore this email</li>
                        </ul>
                    </div>
                    
                    <p>After verifying your code, your account will be activated and you can log in to the system.</p>
                    
                    <p>If you have any questions, please contact the system administrator.</p>
                    
                    <p>Best regards,<br>
                    <strong>UphoCare System</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get admin rejection email template
     */
    private function getAdminRejectionEmailTemplate($adminName, $rejectionReason) {
        $reason = htmlspecialchars($rejectionReason);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .container {
                    background: #f9f9f9;
                    border-radius: 10px;
                    padding: 30px;
                    margin: 20px 0;
                }
                .header {
                    background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
                    color: white;
                    padding: 20px;
                    border-radius: 10px 10px 0 0;
                    text-align: center;
                }
                .content {
                    background: white;
                    padding: 30px;
                    border-radius: 0 0 10px 10px;
                }
                .rejection-box {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .reason-box {
                    background: #f8f9fa;
                    border: 2px solid #dee2e6;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    color: #666;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>❌ Admin Account Registration Rejected</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$adminName}</strong>,</p>
                    
                    <div class='rejection-box'>
                        <strong>⚠️ Registration Rejected</strong><br>
                        We regret to inform you that your admin account registration has been rejected by the super admin.
                    </div>
                    
                    <p>After reviewing your registration and documents, the super admin has decided to reject your admin account registration.</p>
                    
                    <div class='reason-box'>
                        <h4>📝 Reason for Rejection:</h4>
                        <p><em>{$reason}</em></p>
                    </div>
                    
                    <p>If you believe this decision was made in error, or if you have additional information or documents that may change this decision, please contact the super admin or system administrator.</p>
                    
                    <p>You may submit a new registration request with corrected or additional information if needed.</p>
                    
                    <p>If you have any questions, please contact the system administrator.</p>
                    
                    <p>Best regards,<br>
                    <strong>UphoCare System</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Send email using PHPMailer (with fallback to mail() if PHPMailer not available)
     */
    private function sendEmail($to, $subject, $message) {
        // Check if email is disabled
        if (!EMAIL_ENABLED) {
            $this->logEmailAttempt($to, $subject, false, 'EMAIL_DISABLED');
            return false;
        }
        
        // Test mode - just log the email
        if (EMAIL_TEST_MODE) {
            $this->logEmailAttempt($to, $subject, true, 'TEST_MODE');
            return true;
        }
        
        // Check if email credentials are configured
        if ($this->smtpUsername === 'your-email@gmail.com' || $this->smtpPassword === 'your-app-password') {
            error_log("Email configuration not set - Please update config/email.php with your Gmail credentials");
            $this->logEmailAttempt($to, $subject, false, 'EMAIL_CONFIG_NOT_SET - Update config/email.php with Gmail credentials');
            return false;
        }
        
        // Try to use PHPMailer if available
        $phpmailerAvailable = false;
        try {
            // Check if PHPMailer is available via Composer autoload
            if (file_exists(ROOT . DS . 'vendor' . DS . 'autoload.php')) {
                require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
                $phpmailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');
            }
            // Also check if PHPMailer is in a manual location
            if (!$phpmailerAvailable && file_exists(ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'PHPMailer.php')) {
                require_once ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'PHPMailer.php';
                require_once ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'SMTP.php';
                require_once ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'Exception.php';
                $phpmailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');
            }
        } catch (Exception $e) {
            error_log("PHPMailer check failed: " . $e->getMessage());
        }
        
        if (!$phpmailerAvailable) {
            error_log("PHPMailer not available - Install via: composer install");
        }
        
        // Check for placeholder values first
        if ($this->smtpUsername === 'your-email@gmail.com' || $this->smtpPassword === 'your-app-password') {
            $errorMsg = "Email configuration not set: Please update EMAIL_SMTP_USERNAME and EMAIL_SMTP_PASSWORD in config/email.php";
            error_log("EMAIL ERROR: " . $errorMsg);
            $this->logEmailAttempt($to, $subject, false, 'CONFIGURATION ERROR: ' . $errorMsg);
            return false;
        }
        
        // Check for invalid password format (Gmail App Passwords are 16 characters, alphanumeric)
        $passwordLength = strlen($this->smtpPassword);
        if ($passwordLength !== 16) {
            $errorMsg = "Invalid Gmail App Password length: Password must be exactly 16 characters. Current password is " . $passwordLength . " characters.";
            error_log("EMAIL ERROR: " . $errorMsg);
            error_log("EMAIL ERROR: Please generate a Gmail App Password at: https://myaccount.google.com/apppasswords");
            error_log("EMAIL ERROR: Steps: 1) Enable 2-Step Verification, 2) Go to App passwords, 3) Generate password for 'Mail', 4) Copy the 16-character password (remove spaces)");
            $this->logEmailAttempt($to, $subject, false, 'INVALID PASSWORD LENGTH: ' . $errorMsg);
            return false;
        }
        
        // Check if password looks like a regular password (contains common patterns)
        // Gmail App Passwords are typically alphanumeric without spaces
        if (preg_match('/\s/', $this->smtpPassword)) {
            $errorMsg = "Invalid Gmail App Password: Password contains spaces. Remove all spaces from the App Password.";
            error_log("EMAIL ERROR: " . $errorMsg);
            $this->logEmailAttempt($to, $subject, false, 'INVALID PASSWORD FORMAT: ' . $errorMsg);
            return false;
        }
        
        if ($phpmailerAvailable) {
            // Use PHPMailer
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = $this->smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $this->smtpUsername;
                $mail->Password = $this->smtpPassword;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $this->smtpPort;
                $mail->CharSet = 'UTF-8';
                
                // Enable verbose debug output for troubleshooting (uncomment for debugging)
                // $mail->SMTPDebug = 2; // Enable detailed debug output
                // $mail->Debugoutput = function($str, $level) {
                //     error_log("PHPMailer Debug: " . $str);
                // };
                
                // Recipients
                $mail->setFrom($this->fromEmail, $this->fromName);
                $mail->addAddress($to);
                $mail->addReplyTo($this->fromEmail, $this->fromName);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = strip_tags($message);
                
                $mail->send();
                
                $this->logEmailAttempt($to, $subject, true, 'SUCCESS (PHPMailer)');
                error_log("EMAIL SUCCESS: Email sent successfully to {$to} via PHPMailer");
                return true;
                
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                $errorMsg = $e->getMessage();
                error_log("PHPMailer Error: " . $errorMsg);
                $this->logEmailAttempt($to, $subject, false, 'PHPMailer Error: ' . $errorMsg);
                
                // Check for common errors and provide helpful messages
                if (strpos($errorMsg, 'SMTP connect() failed') !== false) {
                    error_log("SMTP Connection Failed - Check EMAIL_SMTP_HOST and EMAIL_SMTP_PORT");
                }
                if (strpos($errorMsg, 'authentication failed') !== false || strpos($errorMsg, 'Invalid credentials') !== false || strpos($errorMsg, '535') !== false || strpos($errorMsg, 'Could not authenticate') !== false) {
                    error_log("=== SMTP AUTHENTICATION FAILED ===");
                    error_log("ERROR: Could not authenticate with Gmail SMTP server");
                    error_log("EMAIL_SMTP_USERNAME: " . $this->smtpUsername);
                    error_log("EMAIL_SMTP_PASSWORD length: " . strlen($this->smtpPassword) . " characters");
                    error_log("");
                    error_log("SOLUTION STEPS:");
                    error_log("1. Go to: https://myaccount.google.com/security");
                    error_log("2. Enable 2-Step Verification (if not already enabled)");
                    error_log("3. Go to 'App passwords' section: https://myaccount.google.com/apppasswords");
                    error_log("4. Generate a new app password for 'Mail'");
                    error_log("5. Copy the 16-character password (remove spaces)");
                    error_log("6. Update EMAIL_SMTP_PASSWORD in config/email.php");
                    error_log("");
                    error_log("IMPORTANT:");
                    error_log("- Use Gmail App Password (16 characters), NOT your regular Gmail password");
                    error_log("- Remove all spaces from the password");
                    error_log("- Password must be exactly 16 characters");
                    error_log("- If password was working before, it may have been revoked - generate a new one");
                }
                if (strpos($errorMsg, 'Could not instantiate mail function') !== false) {
                    error_log("Mail function not available - PHPMailer required");
                }
                
                // Don't fall through to mail() fallback for authentication errors
                // Return false so the error is clear
                return false;
            }
        }
        
        // Fallback to PHP mail() function if PHPMailer is not available or failed
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $headersString = implode("\r\n", $headers);
        
        try {
            $result = mail($to, $subject, $message, $headersString);
            
            // Log the email attempt
            $this->logEmailAttempt($to, $subject, $result, $result ? 'SUCCESS (mail())' : 'FAILED (mail())');
            
            return $result;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            $this->logEmailAttempt($to, $subject, false, 'EXCEPTION: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get approval email template
     */
    private function getApprovalEmailTemplate($customerName, $bookingData) {
        $bookingNumber = htmlspecialchars($bookingData['booking_number']);
        $serviceName = htmlspecialchars($bookingData['service_name']);
        $totalAmount = number_format($bookingData['total_amount'], 2);
        $bookingDate = date('F d, Y', strtotime($bookingData['created_at']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reservation Approved</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4e73df; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #1cc88a; }
                .status-badge { background: #1cc88a; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Reservation Approved!</h1>
                    <p>Your reservation has been confirmed</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <p>Great news! Your reservation has been <span class='status-badge'>APPROVED</span> and is now confirmed.</p>
                    
                    <div class='booking-details'>
                        <h3>📋 Reservation Details</h3>
                        <p><strong>Booking Number:</strong> {$bookingNumber}</p>
                        <p><strong>Service:</strong> {$serviceName}</p>
                        <p><strong>Total Amount:</strong> ₱{$totalAmount}</p>
                        <p><strong>Booking Date:</strong> {$bookingDate}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>CONFIRMED</span></p>
                    </div>
                    
                    <p>Your reservation is now confirmed and we will proceed with your service request. You will receive further updates about the progress of your service.</p>
                    
                    <p>If you have any questions or need to make changes to your reservation, please contact us immediately.</p>
                    
                    <div style='text-align: center;'>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Reservations</a>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Thank you for choosing UphoCare!</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>© " . date('Y') . " UphoCare. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get rejection email template
     */
    private function getRejectionEmailTemplate($customerName, $bookingData, $rejectionReason) {
        $bookingNumber = htmlspecialchars($bookingData['booking_number']);
        $serviceName = htmlspecialchars($bookingData['service_name']);
        $totalAmount = number_format($bookingData['total_amount'], 2);
        $bookingDate = date('F d, Y', strtotime($bookingData['created_at']));
        $reason = htmlspecialchars($rejectionReason);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reservation Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #e74a3b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #e74a3b; }
                .status-badge { background: #e74a3b; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
                .reason-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin: 15px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📋 Reservation Update</h1>
                    <p>Important information about your reservation</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <p>We regret to inform you that your reservation has been <span class='status-badge'>CANCELLED</span>.</p>
                    
                    <div class='booking-details'>
                        <h3>📋 Reservation Details</h3>
                        <p><strong>Booking Number:</strong> {$bookingNumber}</p>
                        <p><strong>Service:</strong> {$serviceName}</p>
                        <p><strong>Total Amount:</strong> ₱{$totalAmount}</p>
                        <p><strong>Booking Date:</strong> {$bookingDate}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>CANCELLED</span></p>
                    </div>
                    
                    <div class='reason-box'>
                        <h4>📝 Reason for Cancellation:</h4>
                        <p><em>{$reason}</em></p>
                    </div>
                    
                    <p>We apologize for any inconvenience this may cause. If you have any questions about this decision or would like to discuss alternative options, please don't hesitate to contact us.</p>
                    
                    <p>You can make a new reservation at any time through our online system.</p>
                    
                    <div style='text-align: center;'>
                        <a href='" . BASE_URL . "customer/new_booking' class='btn'>Make New Reservation</a>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Reservations</a>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Thank you for your understanding.</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>© " . date('Y') . " UphoCare. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Log email attempts for debugging
     */
    private function logEmailAttempt($to, $subject, $success, $status = '') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'subject' => $subject,
            'success' => $success ? 'YES' : 'NO',
            'status' => $status
        ];
        
        $logFile = NOTIFICATION_LOG_PATH;
        
        // Create logs directory if it doesn't exist
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Test email configuration
     */
    public function testEmailConfiguration() {
        $testEmail = 'test@example.com';
        $subject = 'Test Email - UphoCare System';
        $message = '<h1>Test Email</h1><p>This is a test email to verify email configuration.</p>';
        
        return $this->sendEmail($testEmail, $subject, $message);
    }
}
