    /**
     * Assign Booking Number to Customer
     */
    public function assignBookingNumber() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/bookingNumbers');
        }
        
        $customerId = $_POST['customer_id'] ?? null;
        $bookingNumberId = $_POST['booking_number_id'] ?? null;
        
        if (!$customerId || !$bookingNumberId) {
            $_SESSION['error'] = 'Customer and booking number are required.';
            $this->redirect('admin/bookingNumbers');
        }
        
        require_once ROOT . DS . 'core' . DS . 'BookingNumberAssignment.php';
        $assignment = new BookingNumberAssignment();
        
        $adminId = $this->currentUser()['id'];
        $result = $assignment->assignBookingNumberToCustomer($customerId, $bookingNumberId, $adminId);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect('admin/bookingNumbers');
    }
    
    /**
     * Get Available Booking Numbers (AJAX)
     */
    public function getAvailableBookingNumbers() {
        header('Content-Type: application/json');
        
        require_once ROOT . DS . 'core' . DS . 'BookingNumberAssignment.php';
        $assignment = new BookingNumberAssignment();
        $availableNumbers = $assignment->getAvailableBookingNumbers();
        
        echo json_encode([
            'success' => true,
            'data' => $availableNumbers
        ]);
        exit;
    }
    
    /**
     * Manage Repair Items (Customer Reservations)
     */
    public function repairItems() {
        $db = Database::getInstance()->getConnection();
        
        // Get all repair items with customer details
        $sql = "SELECT ri.*, cbn.id as customer_booking_number_id, bn.booking_number,
                u.fullname as customer_name, u.email, u.phone,
                admin.fullname as assigned_by_admin, cbn.assigned_at
                FROM repair_items ri
                LEFT JOIN customer_booking_numbers cbn ON ri.customer_booking_number_id = cbn.id
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
                LEFT JOIN users u ON ri.customer_id = u.id
                LEFT JOIN users admin ON cbn.assigned_by_admin_id = admin.id
                ORDER BY ri.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $repairItems = $stmt->fetchAll();
        
        $data = [
            'title' => 'Repair Items - ' . APP_NAME,
            'user' => $this->currentUser(),
            'repairItems' => $repairItems
        ];
        
        $this->view('admin/repair_items', $data);
    }
    
    /**
     * Accept Repair Item Reservation
     */
    public function acceptRepairItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $repairItemId = $_POST['repair_item_id'] ?? null;
        $adminNotes = $_POST['admin_notes'] ?? '';
        $bookingNumberId = $_POST['booking_number_id'] ?? null;
        
        if (!$repairItemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Repair item ID is required']);
            return;
        }
        
        if (!$bookingNumberId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking number is required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Get repair item details
        $repairItem = $this->getRepairItemDetails($repairItemId);
        if (!$repairItem) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Repair item not found']);
            return;
        }
        
        // Get booking number details
        $stmt = $db->prepare("SELECT * FROM booking_numbers WHERE id = ?");
        $stmt->execute([$bookingNumberId]);
        $bookingNumber = $stmt->fetch();
        
        if (!$bookingNumber) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking number not found']);
            return;
        }
        
        // Update customer_booking_numbers to link booking number with this repair item
        // First check if customer already has this booking number assigned
        $stmt = $db->prepare("
            SELECT id FROM customer_booking_numbers 
            WHERE customer_id = ? AND booking_number_id = ? AND status = 'active'
        ");
        $stmt->execute([$repairItem['customer_id'], $bookingNumberId]);
        $existingAssignment = $stmt->fetch();
        
        if (!$existingAssignment) {
            // Assign booking number to customer if not already assigned
            require_once ROOT . DS . 'core' . DS . 'BookingNumberAssignment.php';
            $assignment = new BookingNumberAssignment();
            $adminId = $this->currentUser()['id'];
            $assignResult = $assignment->assignBookingNumberToCustomer(
                $repairItem['customer_id'], 
                $bookingNumberId, 
                $adminId
            );
            
            if (!$assignResult['success']) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $assignResult['message']]);
                return;
            }
        }
        
        // Update repair item status to approved and link booking number
        $stmt = $db->prepare("
            UPDATE repair_items 
            SET status = 'approved', admin_notes = ?, updated_at = NOW() 
            WHERE id = ? AND status = 'pending'
        ");
        
        $result = $stmt->execute([$adminNotes, $repairItemId]);
        
        if ($result) {
            // Get updated repair item with booking number
            $updatedRepairItem = $this->getRepairItemDetails($repairItemId);
            
            if ($updatedRepairItem) {
                // Send approval notification with booking number via email
                $this->sendRepairItemApprovalNotification($updatedRepairItem);
                
                // Send booking number assignment email
                require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                $notificationService = new NotificationService();
                $notificationService->sendBookingNumberAssignment(
                    $repairItem['customer_email'],
                    $repairItem['customer_name'],
                    $bookingNumber['booking_number'],
                    $repairItem
                );
                
                // Notify customer in database
                $this->notifyCustomerAboutBookingNumber($repairItem['customer_id'], $bookingNumber['booking_number'], $repairItem);
                
                // Log admin activity
                $this->logAdminActivity('accept_repair_item', 'repair_item', $repairItemId, 'Accepted repair item reservation and assigned booking number: ' . $bookingNumber['booking_number']);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Repair item accepted successfully. Booking number assigned and customer has been notified.',
                'new_status' => 'approved',
                'booking_number' => $bookingNumber['booking_number']
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to accept repair item']);
        }
    }
    
    /**
     * Notify customer about booking number assignment
     */
    private function notifyCustomerAboutBookingNumber($customerId, $bookingNumber, $repairItem) {
        $db = Database::getInstance()->getConnection();
        
        // Create notification for customer
        $stmt = $db->prepare("
            INSERT INTO notifications 
            (user_id, title, message, type, is_read, created_at) 
            VALUES (?, ?, ?, 'success', 0, NOW())
        ");
        
        $title = "Reservation Confirmed - Booking Number Assigned";
        $message = "Your repair reservation for '{$repairItem['item_name']}' has been confirmed! Your booking number is: {$bookingNumber}. Click to view your receipt.";
        
        $stmt->execute([
            $customerId,
            $title,
            $message
        ]);
        
        return true;
    }
    
    /**
     * Reject Repair Item Reservation
     */
    public function rejectRepairItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $repairItemId = $_POST['repair_item_id'] ?? null;
        $rejectionReason = $_POST['rejection_reason'] ?? 'No reason provided';
        
        if (!$repairItemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Repair item ID is required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Update repair item status to cancelled
        $stmt = $db->prepare("
            UPDATE repair_items 
            SET status = 'cancelled', admin_notes = ?, updated_at = NOW() 
            WHERE id = ? AND status = 'pending'
        ");
        
        $result = $stmt->execute([$rejectionReason, $repairItemId]);
        
        if ($result) {
            // Get repair item details for email notification
            $repairItem = $this->getRepairItemDetails($repairItemId);
            
            if ($repairItem) {
                // Send rejection notification
                $this->sendRepairItemRejectionNotification($repairItem, $rejectionReason);
                
                // Log admin activity
                $this->logAdminActivity('reject_repair_item', 'repair_item', $repairItemId, 'Rejected repair item: ' . $rejectionReason);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Repair item rejected successfully. Customer has been notified.',
                'new_status' => 'cancelled'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to reject repair item']);
        }
    }
    
    /**
     * Get repair item details with customer information
     */
    private function getRepairItemDetails($repairItemId) {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT ri.*, cbn.id as customer_booking_number_id, bn.booking_number,
                u.fullname as customer_name, u.email as customer_email, u.phone
                FROM repair_items ri
                LEFT JOIN customer_booking_numbers cbn ON ri.customer_booking_number_id = cbn.id
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
                LEFT JOIN users u ON ri.customer_id = u.id
                WHERE ri.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$repairItemId]);
        return $stmt->fetch();
    }
    
    /**
     * Send repair item approval notification
     */
    private function sendRepairItemApprovalNotification($repairItem) {
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $subject = "Repair Item Approved - " . $repairItem['booking_number'];
            
            $message = $this->getRepairApprovalEmailTemplate($repairItem);
            
            $result = $notificationService->sendEmail(
                $repairItem['customer_email'],
                $subject,
                $message
            );
            
            if (!$result) {
                error_log("Failed to send repair approval notification for item ID: " . $repairItem['id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error sending repair approval notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send repair item rejection notification
     */
    private function sendRepairItemRejectionNotification($repairItem, $reason) {
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $subject = "Repair Item Update - " . $repairItem['booking_number'];
            
            $message = $this->getRepairRejectionEmailTemplate($repairItem, $reason);
            
            $result = $notificationService->sendEmail(
                $repairItem['customer_email'],
                $subject,
                $message
            );
            
            if (!$result) {
                error_log("Failed to send repair rejection notification for item ID: " . $repairItem['id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error sending repair rejection notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get repair approval email template
     */
    private function getRepairApprovalEmailTemplate($repairItem) {
        $bookingNumber = htmlspecialchars($repairItem['booking_number']);
        $itemName = htmlspecialchars($repairItem['item_name']);
        $itemDescription = htmlspecialchars($repairItem['item_description']);
        $customerName = htmlspecialchars($repairItem['customer_name']);
        $createdDate = date('F d, Y', strtotime($repairItem['created_at']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Repair Item Approved</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1cc88a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .item-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #1cc88a; }
                .status-badge { background: #1cc88a; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔧 Repair Item Approved!</h1>
                    <p>Your repair request has been accepted</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <p>Great news! Your repair item has been <span class='status-badge'>APPROVED</span> and we will proceed with the repair.</p>
                    
                    <div class='item-details'>
                        <h3>📋 Repair Details</h3>
                        <p><strong>Booking Number:</strong> {$bookingNumber}</p>
                        <p><strong>Item:</strong> {$itemName}</p>
                        <p><strong>Description:</strong> {$itemDescription}</p>
                        <p><strong>Request Date:</strong> {$createdDate}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>APPROVED</span></p>
                    </div>
                    
                    <p>We will contact you soon to discuss the repair process and provide a detailed quotation.</p>
                    
                    <p>Thank you for choosing UphoCare for your repair needs!</p>
                </div>
                
                <div class='footer'>
                    <p>© " . date('Y') . " UphoCare. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get repair rejection email template
     */
    private function getRepairRejectionEmailTemplate($repairItem, $reason) {
        $bookingNumber = htmlspecialchars($repairItem['booking_number']);
        $itemName = htmlspecialchars($repairItem['item_name']);
        $customerName = htmlspecialchars($repairItem['customer_name']);
        $rejectionReason = htmlspecialchars($reason);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Repair Item Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #e74a3b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 8px 8px; }
                .item-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #e74a3b; }
                .status-badge { background: #e74a3b; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
                .reason-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin: 15px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📋 Repair Item Update</h1>
                    <p>Important information about your repair request</p>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$customerName}</strong>,</p>
                    
                    <p>We regret to inform you that your repair request has been <span class='status-badge'>CANCELLED</span>.</p>
                    
                    <div class='item-details'>
                        <h3>📋 Repair Details</h3>
                        <p><strong>Booking Number:</strong> {$bookingNumber}</p>
                        <p><strong>Item:</strong> {$itemName}</p>
                        <p><strong>Status:</strong> <span class='status-badge'>CANCELLED</span></p>
                    </div>
                    
                    <div class='reason-box'>
                        <h4>📝 Reason for Cancellation:</h4>
                        <p><em>{$rejectionReason}</em></p>
                    </div>
                    
                    <p>We apologize for any inconvenience. If you have questions or would like to discuss alternative options, please contact us.</p>
                </div>
                
                <div class='footer'>
                    <p>© " . date('Y') . " UphoCare. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Log admin activity
     */
    private function logAdminActivity($action, $targetType, $targetId, $details) {
        $db = Database::getInstance()->getConnection();
        $adminId = $this->currentUser()['id'];
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $db->prepare("
            INSERT INTO admin_activity_log 
            (admin_id, action, target_type, target_id, details, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([$adminId, $action, $targetType, $targetId, $details, $ipAddress, $userAgent]);
    }
