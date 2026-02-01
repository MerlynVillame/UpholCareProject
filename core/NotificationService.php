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
     * Send reservation approval email with "ready for repair" message
     */
    public function sendReservationApprovalEmail($customerEmail, $customerName, $bookingNumber, $booking, $queuePosition = null, $customersAhead = null) {
        $subject = "Reservation Approved - Ready for Repair - " . $bookingNumber;
        
        $message = $this->getReservationApprovalEmailTemplate($customerName, $bookingNumber, $booking, $queuePosition, $customersAhead);
        
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
     * Send booking number assignment email with queue position
     */
    public function sendBookingNumberAssignmentEmail($customerEmail, $customerName, $bookingNumber, $queuePosition, $customersAhead = null) {
        $subject = "Your Queue Number: " . $bookingNumber;
        
        $message = $this->getBookingNumberAssignmentEmailTemplate($customerName, $bookingNumber, $queuePosition, $customersAhead);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Send preview email to customer with booking details and receipt-style pricing
     */
    public function sendPreviewEmail($customerEmail, $customerName, $booking, $previewImage = null, $previewNotes = null) {
        $bookingNumber = $booking['booking_number'] ?? 'N/A';
        $subject = "Booking Preview - " . $bookingNumber;
        
        $message = $this->getPreviewEmailTemplate($customerName, $booking, $previewImage, $previewNotes);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Get booking confirmation email template
     * This email is sent immediately when customer submits booking
     * For PICKUP service option: NO PRICE included - pricing will be sent after inspection
     * For other service options: NO PRICE included - admin will review first
     */
    private function getBookingConfirmationTemplate($customerName, $bookingNumber, $booking) {
        $createdDate = !empty($booking['created_at']) ? date('F d, Y h:i A', strtotime($booking['created_at'])) : date('F d, Y');
        $serviceName = htmlspecialchars($booking['service_name'] ?? 'N/A');
        $categoryName = htmlspecialchars($booking['category_name'] ?? 'General');
        $serviceType = htmlspecialchars($booking['service_type'] ?? '—');
        $itemDescription = htmlspecialchars($booking['item_description'] ?? '—');
        $pickup = !empty($booking['pickup_date']) ? date('F d, Y', strtotime($booking['pickup_date'])) : '—';
        $status = htmlspecialchars(ucwords(str_replace('_', ' ', $booking['status'] ?? 'pending')));
        $serviceOption = strtolower($booking['service_option'] ?? 'pickup');
        
        // Special note for PICKUP service option
        $pickupNote = '';
        if ($serviceOption === 'pickup') {
            $pickupNote = "
            <div style='background: #fff3cd; border-left: 4px solid #f39c12; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0; font-size: 1.1rem; color: #856404;'><strong>📦 Important Note for Pickup Service:</strong></p>
                <p style='margin: 10px 0 0 0; color: #856404;'>Since you selected <strong>PICKUP</strong> service, our team will collect your item for inspection. <strong>Final pricing will be provided after we inspect the item</strong> at our shop, as accurate pricing requires physical measurements and damage assessment.</p>
                <p style='margin: 10px 0 0 0; color: #856404;'>You will receive another email with detailed pricing once the inspection is complete.</p>
            </div>";
        }

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
                .badge-status { background: #f6c23e; color: #2c3e50; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Reservation Received</h1>
                    <p>Your booking request has been submitted</p>
                </div>

                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    <p>Thank you! Your reservation has been received and your queue number has been assigned.</p>

                    <div class='queue-number' style='background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white; padding: 25px; border-radius: 8px; text-align: center; font-size: 2rem; font-weight: bold; margin: 25px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                        Your Queue Number: {$bookingNumber}
                    </div>

                    {$pickupNote}

                    <div style='background: #e7f3ff; border-left: 4px solid #4e73df; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 1.1rem; color: #004085;'><strong>📋 Important Note:</strong></p>
                        <p style='margin: 10px 0 0 0; color: #004085;'><strong>Admin already received your reservation and will review it.</strong></p>
                        <p style='margin: 10px 0 0 0; color: #004085;'>You will receive another email with updates as your booking progresses.</p>
                    </div>

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
                                <th>Status</th>
                                <td><span class='badge badge-status'>{$status}</span></td>
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
     * Get reservation approval email template with "ready for repair" message
     */
    private function getReservationApprovalEmailTemplate($customerName, $bookingNumber, $booking, $queuePosition = null, $customersAhead = null) {
        $createdDate = !empty($booking['created_at']) ? date('F d, Y h:i A', strtotime($booking['created_at'])) : date('F d, Y');
        $serviceName = htmlspecialchars($booking['service_name'] ?? 'N/A');
        $categoryName = htmlspecialchars($booking['category_name'] ?? 'General');
        $serviceType = htmlspecialchars($booking['service_type'] ?? '—');
        $bookingDate = !empty($booking['booking_date']) ? date('F d, Y', strtotime($booking['booking_date'])) : 'Not specified';
        $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
        $pickupDate = !empty($booking['pickup_date']) ? date('F d, Y', strtotime($booking['pickup_date'])) : 'Not specified';
        
        // Extract queue number from booking number
        $queueNum = $queuePosition;
        if (preg_match('/Queue #(\d+)/', $bookingNumber, $matches)) {
            $queueNum = (int)$matches[1];
        }
        
        $customersAheadText = '';
        if ($customersAhead !== null) {
            $customersAheadText = $customersAhead > 0 
                ? "<p style='margin: 10px 0; color: #856404;'><strong>There are {$customersAhead} customer" . ($customersAhead > 1 ? 's' : '') . " ahead of you in the queue.</strong></p>"
                : "<p style='margin: 10px 0; color: #856404;'><strong>You are first in line!</strong></p>";
        }
        
        // Calculate pricing details
        $baseServicePrice = number_format((float)($booking['total_amount'] ?? 0), 2);
        $laborFee = number_format((float)($booking['labor_fee'] ?? 0), 2);
        $pickupFee = number_format((float)($booking['pickup_fee'] ?? 0), 2);
        $deliveryFee = number_format((float)($booking['delivery_fee'] ?? 0), 2);
        $gasFee = number_format((float)($booking['gas_fee'] ?? 0), 2);
        $travelFee = number_format((float)($booking['travel_fee'] ?? 0), 2);
        $colorPrice = number_format((float)($booking['color_price'] ?? 0), 2);
        $totalAdditionalFees = number_format((float)($booking['total_additional_fees'] ?? 0), 2);
        
        // Recalculate grand total: labor fee + pickup fee + delivery fee + color price (EXCLUDE base service price)
        $grandTotal = (float)($booking['labor_fee'] ?? 0) + 
                      (float)($booking['pickup_fee'] ?? 0) + 
                      (float)($booking['delivery_fee'] ?? 0) + 
                      (float)($booking['color_price'] ?? 0);
        $grandTotal = number_format($grandTotal, 2);
        
        // Get service name and color name for display
        $serviceNameDisplay = htmlspecialchars($booking['service_name'] ?? 'Service');
        $colorNameDisplay = htmlspecialchars($booking['color_name'] ?? '');
        $colorTypeDisplay = htmlspecialchars($booking['color_type'] ?? 'standard');
        
        // Build pricing breakdown table with detailed descriptions
        // NOTE: Service Price is NOT included in the receipt - only fees and fabric price
        $pricingRows = '';
        if ($laborFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Labor Fee</td><td style='text-align: right; padding: 12px;'>₱{$laborFee}</td></tr>";
        }
        if ($pickupFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Pickup Fee</td><td style='text-align: right; padding: 12px;'>₱{$pickupFee}</td></tr>";
        }
        if ($deliveryFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Delivery Fee</td><td style='text-align: right; padding: 12px;'>₱{$deliveryFee}</td></tr>";
        }
        if ($gasFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Gas Fee</td><td style='text-align: right; padding: 12px;'>₱{$gasFee}</td></tr>";
        }
        if ($travelFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Travel Fee</td><td style='text-align: right; padding: 12px;'>₱{$travelFee}</td></tr>";
        }
        if ($colorPrice > 0) {
            $colorTypeText = $colorTypeDisplay === 'premium' ? ' (Premium)' : ' (Standard)';
            $colorDisplay = $colorNameDisplay ? "{$colorNameDisplay}{$colorTypeText}" : 'Selected Fabric';
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'><strong>Fabric/Color Price</strong><br><small style='color: #6c757d;'>{$colorDisplay}</small></td><td style='text-align: right; padding: 12px; vertical-align: middle;'>₱{$colorPrice}</td></tr>";
        }
        if ($totalAdditionalFees > 0 && ($pickupFee == 0 && $deliveryFee == 0 && $gasFee == 0 && $travelFee == 0)) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Additional Fees</td><td style='text-align: right; padding: 12px;'>₱{$totalAdditionalFees}</td></tr>";
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reservation Approved - Ready for Repair</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .approval-badge { background: #28a745; color: white; padding: 15px 25px; border-radius: 8px; text-align: center; font-size: 1.5rem; font-weight: bold; margin: 25px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .ready-badge { background: #fff3cd; border-left: 4px solid #28a745; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .booking-details { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
                .pricing-details { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f39c12; }
                .queue-number { background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; font-size: 1.3rem; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; font-weight: bold; }
                .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .table th, .table td { padding: 10px 12px; border: 1px solid #e3e6f0; text-align: left; }
                .table th { background: #f8f9fc; color: #2c3e50; font-weight: 600; }
                .total-row { background: #28a745; color: white; font-weight: bold; font-size: 1.1rem; }
                .total-row td { padding: 15px 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Reservation Approved!</h1>
                    <p>Your booking is ready for repair</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <div class='approval-badge'>
                        ✅ RESERVATION APPROVED
                    </div>
                    
                    " . ($serviceOption === 'pickup' || $serviceOption === 'both' 
                        ? "<div class='ready-badge'>
                            <p style='margin: 0;'><strong>📦 Pick Up Service Selected</strong></p>
                            <p style='margin: 10px 0 0 0;'>Your reservation has been approved and your booking status is now <strong>'For Pick Up'</strong>.</p>
                            " . ($pickupDate !== 'Not specified' ? "<p style='margin: 10px 0 0 0;'><strong>We will pick up your item on: {$pickupDate}</strong></p>" : "<p style='margin: 10px 0 0 0;'>We will contact you to schedule the pickup date.</p>") . "
                            <p style='margin: 10px 0 0 0;'>After we collect your item, we will perform a detailed inspection to determine the exact measurements, check for any damages, and identify the materials needed. Once the inspection is complete, we will send you a preview receipt with the total cost for your approval before we begin the repair work.</p>
                        </div>"
                        : ($serviceOption === 'delivery'
                            ? "<div class='ready-badge'>
                                <p style='margin: 0;'><strong>🚚 Delivery Service Selected</strong></p>
                                <p style='margin: 10px 0 0 0;'><strong>✅ Your reservation has been approved!</strong></p>
                                <p style='margin: 10px 0 0 0;'><strong>📋 IMPORTANT: Please bring your item to the shop on your scheduled date for inspection.</strong></p>
                                " . ($bookingDate !== 'Not specified' ? "<p style='margin: 10px 0 0 0;'><strong>📅 Scheduled Date to Bring Item: {$bookingDate}</strong></p>" : "<p style='margin: 10px 0 0 0;'><strong>📅 Please bring your item to the shop as soon as possible.</strong></p>") . "
                                <p style='margin: 15px 0 0 0;'><strong>What happens when you bring your item:</strong></p>
                                <ul style='margin: 10px 0; padding-left: 20px;'>
                                    <li>Our team will inspect your item for damages</li>
                                    <li>Take accurate measurements</li>
                                    <li>Determine materials needed</li>
                                    <li>Calculate labor cost and delivery fee</li>
                                    <li>Provide estimated timeline for repair</li>
                                </ul>
                                <p style='margin: 15px 0 0 0;'><strong>After inspection:</strong></p>
                                <ul style='margin: 10px 0; padding-left: 20px;'>
                                    <li>You will receive a <strong>Preview Receipt</strong> (Inspection Result) with estimated cost</li>
                                    <li>Review and approve the cost estimate</li>
                                    <li>If approved, repair work will begin</li>
                                    <li>If final cost changes after repair, you'll receive a <strong>Final Preview Receipt</strong></li>
                                    <li>After repair completion, we will schedule delivery to your address</li>
                                    <li>Payment can be made before or upon delivery</li>
                                    <li>After payment, you'll receive the <strong>Official Receipt</strong></li>
                                </ul>
                                <p style='margin: 15px 0 0 0; color: #856404;'><strong>⚠️ Note: No pricing is available yet. Pricing will be determined after inspection when you bring your item to the shop.</strong></p>
                            </div>"
                        : "<div class='ready-badge'>
                            <p style='margin: 0;'><strong>✅ Your reservation has been approved!</strong></p>
                            <p style='margin: 10px 0 0 0;'>Your booking status is now <strong>'Approved'</strong>. You can now track your repair progress.</p>
                            </div>")) . "
                    
                    <div class='queue-number'>
                        Your Booking: {$bookingNumber}
                    </div>
                    
                    {$customersAheadText}
                    
                    <div class='booking-details'>
                        <h3 style='margin-top: 0; color: #28a745;'>📋 Booking Details</h3>
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
                                <th>Preferred Date</th>
                                <td>{$bookingDate}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span style='background: #28a745; color: white; padding: 6px 12px; border-radius: 20px; font-weight: bold;'>APPROVED</span></td>
                            </tr>
                        </table>
                    </div>
                    
                    " . ($serviceOption !== 'delivery' 
                        ? "<div class='pricing-details'>
                        <h3 style='margin-top: 0; color: #f39c12;'>💰 Pricing Breakdown (Receipt)</h3>
                        <div style='background: white; border: 2px solid #f39c12; border-radius: 8px; padding: 20px; margin: 15px 0;'>
                            <table class='table' style='margin: 0; background: white;'>
                                <thead style='background: #f8f9fc;'>
                                    <tr>
                                        <th style='width: 60%; padding: 12px; border-bottom: 2px solid #dee2e6;'>Item</th>
                                        <th style='width: 40%; text-align: right; padding: 12px; border-bottom: 2px solid #dee2e6;'>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$pricingRows}
                                </tbody>
                                <tfoot style='background: #28a745; color: white;'>
                                    <tr>
                                        <td style='font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>TOTAL AMOUNT</td>
                                        <td style='text-align: right; font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>₱{$grandTotal}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        </div>"
                        : "<div class='pricing-details' style='background: #fff3cd; border-left: 4px solid #f39c12; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='margin-top: 0; color: #856404;'>💰 Pricing Information</h3>
                            <p style='margin: 10px 0; color: #856404;'><strong>Pricing will be determined after you bring your item to the shop for inspection.</strong></p>
                            <p style='margin: 10px 0; color: #856404;'>Once our team inspects your item, we will send you a <strong>Preview Receipt</strong> with the estimated cost breakdown including:</p>
                            <ul style='margin: 10px 0; padding-left: 20px; color: #856404;'>
                                <li>Labor fee</li>
                                <li>Materials needed</li>
                                <li>Delivery fee</li>
                                <li>Any additional charges</li>
                            </ul>
                            <p style='margin: 10px 0; color: #856404;'><em>This is not a receipt yet. No payment is required at this time.</em></p>
                        </div>") . "
                    
                    " . ($serviceOption === 'delivery'
                        ? "<div style='background: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <p style='margin: 0; font-size: 1.1rem; color: #004085;'><strong>📞 What's Next?</strong></p>
                            <ol style='margin: 10px 0 0 0; color: #004085; padding-left: 20px;'>
                                <li><strong>Bring your item to the shop</strong> on your scheduled date ({$bookingDate})</li>
                                <li>Our team will <strong>inspect your item</strong> and determine the repair requirements</li>
                                <li>You will receive a <strong>Preview Receipt</strong> with estimated cost for approval</li>
                                <li>After approval, repair work will begin</li>
                                <li>We will schedule <strong>delivery</strong> to your address after repair completion</li>
                                <li>Payment can be made <strong>before or upon delivery</strong></li>
                                <li>After payment, you'll receive the <strong>Official Receipt</strong></li>
                            </ol>
                            <p style='margin: 15px 0 0 0; color: #004085;'><strong>📍 Shop Address:</strong> [Your Shop Address Here]</p>
                            <p style='margin: 5px 0 0 0; color: #004085;'><strong>📞 Contact:</strong> [Your Contact Number]</p>
                        </div>"
                        : "<div style='background: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 1.1rem; color: #004085;'><strong>📞 What's Next?</strong></p>
                        <ul style='margin: 10px 0 0 0; color: #004085;'>
                            <li>Your reservation is approved and ready for repair</li>
                            <li>You can track your repair progress in your account</li>
                            <li>We will notify you when your item is being processed</li>
                            <li>You will receive updates as your repair progresses</li>
                        </ul>
                        </div>") . "
                    
                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Reservations</a>
                    </div>
                    
                    <p>Best regards,<br>
                    <strong>UphoCare Team</strong></p>
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
     * Get booking number assignment email template with queue position
     */
    private function getBookingNumberAssignmentEmailTemplate($customerName, $bookingNumber, $queuePosition, $customersAhead = null) {
        $assignedDate = date('F d, Y h:i A');
        
        // Extract queue number from booking number (e.g., "Queue #12" -> 12)
        $queueNum = $queuePosition;
        if (preg_match('/Queue #(\d+)/', $bookingNumber, $matches)) {
            $queueNum = (int)$matches[1];
        }
        
        // Calculate customers ahead if not provided
        if ($customersAhead === null) {
            $customersAhead = max(0, $queueNum - 1);
        }
        
        $customersAheadText = $customersAhead > 0 
            ? "There are <strong>{$customersAhead}</strong> customer" . ($customersAhead > 1 ? 's' : '') . " ahead of you."
            : "You are <strong>first</strong> in line!";
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Your Queue Number</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4e73df; color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .booking-details { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4e73df; }
                .queue-number { background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white; padding: 25px; border-radius: 8px; text-align: center; font-size: 2rem; font-weight: bold; margin: 25px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .queue-info { background: #fff3cd; border-left: 4px solid #f39c12; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .status-badge { background: #1cc88a; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; display: inline-block; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Your Booking is Confirmed!</h1>
                    <p>Your queue number has been assigned</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <p>Great news! Your reservation has been confirmed and your queue number has been assigned.</p>
                    
                    <div class='queue-number'>
                        Your Queue Number: {$bookingNumber}
                    </div>
                    
                    <div class='queue-info'>
                        <h3 style='margin-top: 0; color: #856404;'>📍 Your Position in Line</h3>
                        <p style='font-size: 1.1rem; margin-bottom: 0;'>{$customersAheadText}</p>
                        <p style='margin-top: 10px; margin-bottom: 0; color: #856404;'><small>We will notify you when your item is being processed.</small></p>
                    </div>
                    
                    <div class='booking-details'>
                        <h3 style='margin-top: 0;'>📋 Booking Details</h3>
                        <p><strong>Queue Number:</strong> <span style='font-weight: bold; color: #4e73df; font-size: 1.1rem;'>{$bookingNumber}</span></p>
                        <p><strong>Position in Queue:</strong> <span style='font-weight: bold; color: #f39c12;'>#{$queueNum}</span></p>
                        <p><strong>Assigned Date:</strong> {$assignedDate}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>ACTIVE</span></p>
                    </div>
                    
                    <p><strong>What this means:</strong></p>
                    <ul>
                        <li>Your queue number (<strong>{$bookingNumber}</strong>) shows your position in the processing line</li>
                        <li>The lower your number, the earlier you will be served</li>
                        <li>Queue numbers stay fixed - they don't change when other bookings are completed</li>
                        <li>You'll receive notifications when your item is ready for processing</li>
                    </ul>
                    
                    <div style='text-align: center; margin-top: 30px;'>
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
     * Send payment receipt email to customer
     */
    public function sendPaymentReceipt($customerEmail, $customerName, $bookingNumber, $booking, $totalAmount) {
        $subject = "Payment Receipt - " . $bookingNumber;
        
        $message = $this->getPaymentReceiptTemplate($customerName, $bookingNumber, $booking, $totalAmount);
        
        return $this->sendEmail($customerEmail, $subject, $message);
    }
    
    /**
     * Send quotation email after pickup and inspection (for PICKUP service option)
     * This is Email #2 - sent AFTER item is inspected with FINAL pricing
     */
    public function sendQuotationAfterInspection($customerEmail, $customerName, $bookingNumber, $booking) {
        $subject = "Final Quotation After Inspection - " . $bookingNumber;
        
        $message = $this->getQuotationAfterInspectionTemplate($customerName, $bookingNumber, $booking);
        
        return $this->sendEmail($customerEmail, $subject, $message);
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
     * Public method to allow sending custom emails
     */
    public function sendEmail($to, $subject, $message) {
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
        $bookingNumber = htmlspecialchars($bookingData['booking_number'] ?? 'N/A');
        $serviceName = htmlspecialchars($bookingData['service_name'] ?? 'N/A');
        $bookingDate = date('F d, Y', strtotime($bookingData['created_at'] ?? 'now'));
        
        // Calculate pricing details
        $baseServicePrice = number_format((float)($bookingData['total_amount'] ?? 0), 2);
        $laborFee = number_format((float)($bookingData['labor_fee'] ?? 0), 2);
        $pickupFee = number_format((float)($bookingData['pickup_fee'] ?? 0), 2);
        $deliveryFee = number_format((float)($bookingData['delivery_fee'] ?? 0), 2);
        $gasFee = number_format((float)($bookingData['gas_fee'] ?? 0), 2);
        $travelFee = number_format((float)($bookingData['travel_fee'] ?? 0), 2);
        $colorPrice = number_format((float)($bookingData['color_price'] ?? 0), 2);
        $totalAdditionalFees = number_format((float)($bookingData['total_additional_fees'] ?? 0), 2);
        
        // Recalculate grand total: labor fee + pickup fee + delivery fee + color price (EXCLUDE base service price)
        $grandTotal = (float)($bookingData['labor_fee'] ?? 0) + 
                      (float)($bookingData['pickup_fee'] ?? 0) + 
                      (float)($bookingData['delivery_fee'] ?? 0) + 
                      (float)($bookingData['color_price'] ?? 0);
        $grandTotal = number_format($grandTotal, 2);
        
        // Get service name and color name for display
        $serviceNameDisplay2 = htmlspecialchars($bookingData['service_name'] ?? 'Service');
        $colorNameDisplay2 = htmlspecialchars($bookingData['color_name'] ?? '');
        $colorTypeDisplay2 = htmlspecialchars($bookingData['color_type'] ?? 'standard');
        
        // Build pricing breakdown table
        // NOTE: Service Price is NOT included in the receipt - only fees and fabric price
        $pricingRows = '';
        if ($laborFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Labor Fee</td><td style='text-align: right; padding: 12px;'>₱{$laborFee}</td></tr>";
        }
        if ($pickupFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Pickup Fee</td><td style='text-align: right; padding: 12px;'>₱{$pickupFee}</td></tr>";
        }
        if ($deliveryFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Delivery Fee</td><td style='text-align: right; padding: 12px;'>₱{$deliveryFee}</td></tr>";
        }
        if ($colorPrice > 0) {
            $colorTypeText2 = $colorTypeDisplay2 === 'premium' ? ' (Premium)' : ' (Standard)';
            $colorDisplay2 = $colorNameDisplay2 ? "{$colorNameDisplay2}{$colorTypeText2}" : 'Selected Fabric';
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'><strong>Fabric/Color Price</strong><br><small style='color: #6c757d;'>{$colorDisplay2}</small></td><td style='text-align: right; padding: 12px; vertical-align: middle;'>₱{$colorPrice}</td></tr>";
        }
        
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
                .pricing-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f39c12; }
                .status-badge { background: #1cc88a; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .table th, .table td { padding: 10px 12px; border: 1px solid #e3e6f0; text-align: left; }
                .table th { background: #f8f9fc; color: #2c3e50; font-weight: 600; }
                .total-row { background: #1cc88a; color: white; font-weight: bold; font-size: 1.1rem; }
                .total-row td { padding: 15px 12px; }
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
                        <p><strong>Booking Date:</strong> {$bookingDate}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>CONFIRMED</span></p>
                    </div>
                    
                    <div class='pricing-details'>
                        <h3 style='margin-top: 0; color: #f39c12;'>💰 Pricing Breakdown (Receipt)</h3>
                        <div style='background: white; border: 2px solid #f39c12; border-radius: 8px; padding: 20px; margin: 15px 0;'>
                            <table class='table' style='margin: 0; background: white;'>
                                <thead style='background: #f8f9fc;'>
                                    <tr>
                                        <th style='width: 60%; padding: 12px; border-bottom: 2px solid #dee2e6;'>Item</th>
                                        <th style='width: 40%; text-align: right; padding: 12px; border-bottom: 2px solid #dee2e6;'>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$pricingRows}
                                </tbody>
                                <tfoot style='background: #28a745; color: white;'>
                                    <tr>
                                        <td style='font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>TOTAL AMOUNT</td>
                                        <td style='text-align: right; font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>₱{$grandTotal}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
     * Get payment receipt email template
     */
    private function getPaymentReceiptTemplate($customerName, $bookingNumber, $booking, $totalAmount) {
        $serviceName = $booking['service_name'] ?? 'Service';
        $paymentMethod = 'Cash on Delivery';
        if ($booking['payment_status'] === 'paid_full_cash') {
            $paymentMethod = 'Full Cash (Paid Before Service)';
        } elseif ($booking['payment_status'] === 'paid_on_delivery_cod') {
            $paymentMethod = 'Cash on Delivery (COD)';
        }
        
        $date = date('F j, Y', strtotime($booking['created_at'] ?? 'now'));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .receipt-box { background: white; border: 2px solid #e3e6f0; border-radius: 10px; padding: 20px; margin: 20px 0; }
                .receipt-header { text-align: center; margin-bottom: 20px; }
                .receipt-header h2 { color: #4e73df; margin: 0; }
                .receipt-details { margin: 20px 0; }
                .receipt-details table { width: 100%; border-collapse: collapse; }
                .receipt-details td { padding: 10px; border-bottom: 1px solid #eee; }
                .receipt-details td:first-child { font-weight: bold; width: 40%; }
                .total { background: #28a745; color: white; padding: 15px; text-align: center; border-radius: 5px; margin-top: 20px; font-size: 1.2em; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>UphoCare</h1>
                    <p>Upholstery Services</p>
                </div>
                <div class='content'>
                    <h2>Payment Receipt</h2>
                    <p>Dear {$customerName},</p>
                    <p>Your payment has been received and processed. Please find your receipt details below:</p>
                    
                    <div class='receipt-box'>
                        <div class='receipt-header'>
                            <h2>UphoCare</h2>
                            <p>Upholstery Services</p>
                            <p style='color: #28a745; font-weight: bold;'>Payment Receipt</p>
                        </div>
                        
                        <div class='receipt-details'>
                            <table>
                                <tr>
                                    <td>Booking ID:</td>
                                    <td>{$bookingNumber}</td>
                                </tr>
                                <tr>
                                    <td>Service:</td>
                                    <td>{$serviceName}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>{$date}</td>
                                </tr>
                                <tr>
                                    <td>Customer:</td>
                                    <td>{$customerName}</td>
                                </tr>
                                <tr>
                                    <td>Payment Method:</td>
                                    <td>{$paymentMethod}</td>
                                </tr>
                            </table>
                            
                            <div class='total'>
                                Total Amount: ₱" . number_format($totalAmount, 2) . "
                            </div>
                        </div>
                    </div>
                    
                    <p>Thank you for choosing UphoCare! This receipt confirms your payment has been received.</p>
                    <p>You can also view this receipt in your customer portal under Notifications.</p>
                    
                    <div class='footer'>
                        <p>This is an automated message. Please do not reply to this email.</p>
                        <p>&copy; " . date('Y') . " UphoCare. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get quotation email template after pickup and inspection
     * This is sent AFTER item is picked up and inspected (for PICKUP service option)
     * Contains FINAL pricing based on actual measurements and damage assessment
     */
    private function getQuotationAfterInspectionTemplate($customerName, $bookingNumber, $booking) {
        $createdDate = !empty($booking['created_at']) ? date('F d, Y h:i A', strtotime($booking['created_at'])) : date('F d, Y');
        $serviceName = htmlspecialchars($booking['service_name'] ?? 'N/A');
        $categoryName = htmlspecialchars($booking['category_name'] ?? 'General');
        $serviceType = htmlspecialchars($booking['service_type'] ?? '—');
        $itemDescription = htmlspecialchars($booking['item_description'] ?? '—');
        
        // Calculate pricing details
        $baseServicePrice = number_format((float)($booking['total_amount'] ?? 0), 2);
        $laborFee = number_format((float)($booking['labor_fee'] ?? 0), 2);
        $pickupFee = number_format((float)($booking['pickup_fee'] ?? 0), 2);
        $deliveryFee = number_format((float)($booking['delivery_fee'] ?? 0), 2);
        $gasFee = number_format((float)($booking['gas_fee'] ?? 0), 2);
        $travelFee = number_format((float)($booking['travel_fee'] ?? 0), 2);
        $colorPrice = number_format((float)($booking['color_price'] ?? 0), 2);
        
        // Get color name for display
        $colorNameDisplay = htmlspecialchars($booking['color_name'] ?? '');
        $colorTypeDisplay = htmlspecialchars($booking['color_type'] ?? 'standard');
        
        // Calculate grand total
        $grandTotal = (float)($booking['labor_fee'] ?? 0) + 
                      (float)($booking['pickup_fee'] ?? 0) + 
                      (float)($booking['delivery_fee'] ?? 0) + 
                      (float)($booking['color_price'] ?? 0);
        $grandTotal = number_format($grandTotal, 2);
        
        // Build pricing breakdown table
        $pricingRows = '';
        if ($laborFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Labor Fee</td><td style='text-align: right; padding: 12px;'>₱{$laborFee}</td></tr>";
        }
        if ($pickupFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Pickup Fee</td><td style='text-align: right; padding: 12px;'>₱{$pickupFee}</td></tr>";
        }
        if ($deliveryFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Delivery Fee</td><td style='text-align: right; padding: 12px;'>₱{$deliveryFee}</td></tr>";
        }
        if ($gasFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Gas Fee</td><td style='text-align: right; padding: 12px;'>₱{$gasFee}</td></tr>";
        }
        if ($travelFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Travel Fee</td><td style='text-align: right; padding: 12px;'>₱{$travelFee}</td></tr>";
        }
        if ($colorPrice > 0) {
            $colorTypeText = $colorTypeDisplay === 'premium' ? ' (Premium)' : ' (Standard)';
            $colorDisplay = $colorNameDisplay ? "{$colorNameDisplay}{$colorTypeText}" : 'Selected Fabric';
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'><strong>Fabric/Color Price</strong><br><small style='color: #6c757d;'>{$colorDisplay}</small></td><td style='text-align: right; padding: 12px; vertical-align: middle;'>₱{$colorPrice}</td></tr>";
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Final Quotation After Inspection</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 640px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .inspection-badge { background: #28a745; color: white; padding: 15px 25px; border-radius: 8px; text-align: center; font-size: 1.5rem; font-weight: bold; margin: 25px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .inspection-note { background: #d4edda; border-left: 4px solid #28a745; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .booking-details { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4e73df; }
                .pricing-details { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f39c12; }
                .queue-number { background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; font-size: 1.3rem; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; font-weight: bold; }
                .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .table th, .table td { padding: 10px 12px; border: 1px solid #e3e6f0; text-align: left; }
                .table th { background: #f8f9fc; color: #2c3e50; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Inspection Complete - Final Quotation</h1>
                    <p>Your item has been inspected</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <div class='inspection-badge'>
                        ✅ INSPECTION COMPLETED
                    </div>
                    
                    <div class='inspection-note'>
                        <p style='margin: 0; font-size: 1.1rem; color: #155724;'><strong>📦 Item Picked Up and Inspected</strong></p>
                        <p style='margin: 10px 0 0 0; color: #155724;'>Your item has been successfully picked up and our technicians have completed a thorough inspection. We have measured the item, assessed the damage, and calculated the exact material requirements.</p>
                        <p style='margin: 10px 0 0 0; color: #155724;'><strong>Below is your FINAL and ACCURATE quotation based on the actual condition of your item.</strong></p>
                    </div>
                    
                    <div class='queue-number'>
                        Booking Number: {$bookingNumber}
                    </div>
                    
                    <div class='booking-details'>
                        <h3 style='margin-top: 0; color: #4e73df;'>📋 Booking Details</h3>
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
                        </table>
                    </div>
                    
                    <div class='pricing-details'>
                        <h3 style='margin-top: 0; color: #f39c12;'>💰 FINAL PRICING (After Inspection)</h3>
                        <p style='color: #856404; background: #fff3cd; padding: 15px; border-radius: 6px; margin: 15px 0;'>
                            <strong>⚠️ Important:</strong> This pricing is based on the actual measurements, damage assessment, and material requirements determined during physical inspection.
                        </p>
                        <div style='background: white; border: 2px solid #f39c12; border-radius: 8px; padding: 20px; margin: 15px 0;'>
                            <table class='table' style='margin: 0; background: white;'>
                                <thead style='background: #f8f9fc;'>
                                    <tr>
                                        <th style='width: 60%; padding: 12px; border-bottom: 2px solid #dee2e6;'>Item</th>
                                        <th style='width: 40%; text-align: right; padding: 12px; border-bottom: 2px solid #dee2e6;'>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$pricingRows}
                                </tbody>
                                <tfoot style='background: #28a745; color: white;'>
                                    <tr>
                                        <td style='font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>TOTAL AMOUNT</td>
                                        <td style='text-align: right; font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>₱{$grandTotal}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div style='background: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 1.1rem; color: #004085;'><strong>📞 What's Next?</strong></p>
                        <ul style='margin: 10px 0 0 0; color: #004085;'>
                            <li><strong>Review the quotation above</strong></li>
                            <li>If you approve, we will proceed with the repair work</li>
                            <li>Payment can be made: Partial upfront or Full payment on completion</li>
                            <li>We will keep you updated on the progress</li>
                            <li>Contact us if you have any questions about the quotation</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Bookings</a>
                    </div>
                    
                    <p>Best regards,<br>
                    <strong>UphoCare Team</strong></p>
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
     * Get preview email template with booking details and receipt-style pricing
     */
    private function getPreviewEmailTemplate($customerName, $booking, $previewImage = null, $previewNotes = null) {
        $bookingNumber = htmlspecialchars($booking['booking_number'] ?? 'N/A');
        $createdDate = !empty($booking['created_at']) ? date('F d, Y h:i A', strtotime($booking['created_at'])) : date('F d, Y');
        $serviceName = htmlspecialchars($booking['service_name'] ?? 'N/A');
        $categoryName = htmlspecialchars($booking['category_name'] ?? 'General');
        $serviceType = htmlspecialchars($booking['service_type'] ?? '—');
        $itemDescription = htmlspecialchars($booking['item_description'] ?? '—');
        $pickup = !empty($booking['pickup_date']) ? date('F d, Y', strtotime($booking['pickup_date'])) : 'Not specified';
        $delivery = !empty($booking['delivery_date']) ? date('F d, Y', strtotime($booking['delivery_date'])) : 'Not specified';
        $status = htmlspecialchars(ucwords(str_replace('_', ' ', $booking['status'] ?? 'pending')));
        
        // Calculate pricing details
        $laborFee = number_format((float)($booking['labor_fee'] ?? 0), 2);
        $pickupFee = number_format((float)($booking['pickup_fee'] ?? 0), 2);
        $deliveryFee = number_format((float)($booking['delivery_fee'] ?? 0), 2);
        $colorPrice = number_format((float)($booking['color_price'] ?? 0), 2);
        
        // Get color name and type for display
        $colorNameDisplay = htmlspecialchars($booking['color_name'] ?? '');
        $colorTypeDisplay = htmlspecialchars($booking['color_type'] ?? 'standard');
        
        // Recalculate grand total: labor fee + pickup fee + delivery fee + color price
        $grandTotal = (float)($booking['labor_fee'] ?? 0) + 
                      (float)($booking['pickup_fee'] ?? 0) + 
                      (float)($booking['delivery_fee'] ?? 0) + 
                      (float)($booking['color_price'] ?? 0);
        $grandTotal = number_format($grandTotal, 2);
        
        // Build pricing breakdown table
        $pricingRows = '';
        if ($laborFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Labor Fee</td><td style='text-align: right; padding: 12px;'>₱{$laborFee}</td></tr>";
        }
        if ($pickupFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Pickup Fee</td><td style='text-align: right; padding: 12px;'>₱{$pickupFee}</td></tr>";
        }
        if ($deliveryFee > 0) {
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'>Delivery Fee</td><td style='text-align: right; padding: 12px;'>₱{$deliveryFee}</td></tr>";
        }
        if ($colorPrice > 0) {
            $colorTypeText = $colorTypeDisplay === 'premium' ? ' (Premium)' : ' (Standard)';
            $colorDisplay = $colorNameDisplay ? "{$colorNameDisplay}{$colorTypeText}" : 'Selected Fabric';
            $pricingRows .= "<tr style='border-bottom: 1px solid #e3e6f0;'><td style='padding: 12px;'><strong>Fabric/Color Price</strong><br><small style='color: #6c757d;'>{$colorDisplay}</small></td><td style='text-align: right; padding: 12px; vertical-align: middle;'>₱{$colorPrice}</td></tr>";
        }
        
        // Preview image HTML
        $previewImageHtml = '';
        if ($previewImage) {
            $previewImageHtml = "
                <div style='background: white; border: 2px solid #4e73df; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;'>
                    <h4 style='color: #4e73df; margin-top: 0;'>📷 Preview Image</h4>
                    <img src='" . BASE_URL . htmlspecialchars($previewImage) . "' style='max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);' alt='Preview Image'>
                </div>
            ";
        }
        
        // Preview notes HTML
        $previewNotesHtml = '';
        if ($previewNotes) {
            $previewNotesHtml = "
                <div style='background: #fff3cd; border-left: 4px solid #f39c12; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h4 style='margin-top: 0; color: #856404;'><strong>📝 Admin Notes:</strong></h4>
                    <p style='color: #856404; margin: 0;'>" . nl2br(htmlspecialchars($previewNotes)) . "</p>
                </div>
            ";
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Preview</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 640px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .booking-number { background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white; padding: 25px; border-radius: 8px; text-align: center; font-size: 2rem; font-weight: bold; margin: 25px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .card { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4e73df; }
                .pricing-card { background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f39c12; }
                .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .table th, .table td { padding: 10px 12px; border: 1px solid #e3e6f0; text-align: left; }
                .table th { background: #f8f9fc; color: #2c3e50; font-weight: 600; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #4e73df; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                .info-box { background: #e7f3ff; border-left: 4px solid #4e73df; padding: 20px; border-radius: 8px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📋 Booking Preview</h1>
                    <p>Your booking details and pricing breakdown</p>
                </div>

                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    <p>We have reviewed your booking request and prepared a preview of your booking details and pricing breakdown.</p>

                    <div class='booking-number'>
                        Booking Number: {$bookingNumber}
                    </div>

                    {$previewImageHtml}

                    {$previewNotesHtml}

                    <div class='card'>
                        <h3 style='margin-top: 0; color: #4e73df;'>📋 Booking Details</h3>
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
                                <th>Delivery Date</th>
                                <td>{$delivery}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span style='background: #f6c23e; color: #2c3e50; padding: 6px 12px; border-radius: 20px; font-weight: bold;'>{$status}</span></td>
                            </tr>
                        </table>
                    </div>

                    <div class='pricing-card'>
                        <h3 style='margin-top: 0; color: #f39c12;'>💰 Pricing Breakdown (Receipt)</h3>
                        <div style='background: white; border: 2px solid #f39c12; border-radius: 8px; padding: 20px; margin: 15px 0;'>
                            <table class='table' style='margin: 0; background: white;'>
                                <thead style='background: #f8f9fc;'>
                                    <tr>
                                        <th style='width: 60%; padding: 12px; border-bottom: 2px solid #dee2e6;'>Item</th>
                                        <th style='width: 40%; text-align: right; padding: 12px; border-bottom: 2px solid #dee2e6;'>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$pricingRows}
                                </tbody>
                                <tfoot style='background: #28a745; color: white;'>
                                    <tr>
                                        <td style='font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>TOTAL AMOUNT</td>
                                        <td style='text-align: right; font-size: 1.3rem; font-weight: bold; padding: 18px; border-top: 3px solid #1e7e34;'>₱{$grandTotal}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class='info-box'>
                        <p style='margin: 0; font-size: 1.1rem; color: #004085;'><strong>ℹ️ Next Steps:</strong></p>
                        <ul style='margin: 10px 0 0 0; color: #004085;'>
                            <li>Please review the booking details and pricing breakdown above</li>
                            <li>If everything looks correct, the admin will proceed with approval</li>
                            <li>You will receive another email once your booking is approved</li>
                            <li>If you have any questions or concerns, please contact us</li>
                        </ul>
                    </div>

                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='" . BASE_URL . "customer/bookings' class='btn'>View My Bookings</a>
                    </div>

                    <p>Best regards,<br>
                    <strong>UphoCare Team</strong></p>
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
     * Test email configuration
     */
    public function testEmailConfiguration() {
        $testEmail = 'test@example.com';
        $subject = 'Test Email - UphoCare System';
        $message = '<h1>Test Email</h1><p>This is a test email to verify email configuration.</p>';
        
        return $this->sendEmail($testEmail, $subject, $message);
    }
}
