<?php
/**
 * Payment Controller
 * Handles all payment-related operations
 */

require_once '../config/Database.php';

class PaymentController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all payments for a customer
     */
    public function getCustomerPayments($customerId) {
        $sql = "SELECT p.*, b.booking_date, b.booking_time, s.service_name, c.first_name, c.last_name 
                FROM payments p 
                JOIN bookings b ON p.booking_id = b.booking_id 
                JOIN services s ON b.service_id = s.service_id 
                JOIN customers c ON b.customer_id = c.customer_id 
                WHERE b.customer_id = :customer_id 
                ORDER BY p.payment_date DESC";
        return $this->db->fetchAll($sql, ['customer_id' => $customerId]);
    }
    
    /**
     * Get payment by ID
     */
    public function getPaymentById($id) {
        $sql = "SELECT p.*, b.booking_date, b.booking_time, s.service_name, 
                       c.first_name, c.last_name, c.email 
                FROM payments p 
                JOIN bookings b ON p.booking_id = b.booking_id 
                JOIN services s ON b.service_id = s.service_id 
                JOIN customers c ON b.customer_id = c.customer_id 
                WHERE p.payment_id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Create new payment
     */
    public function createPayment($data) {
        return $this->db->insert('payments', $data);
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, $status, $transactionId = null) {
        $data = ['payment_status' => $status];
        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }
        if ($status === 'paid') {
            $data['payment_date'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->update('payments', $data, 'payment_id = :id', ['id' => $id]);
    }
    
    /**
     * Get pending payments count
     */
    public function getPendingPaymentsCount($customerId = null) {
        if ($customerId) {
            $sql = "SELECT COUNT(*) as count FROM payments p 
                    JOIN bookings b ON p.booking_id = b.booking_id 
                    WHERE b.customer_id = :customer_id AND p.payment_status = 'pending'";
            $result = $this->db->fetchOne($sql, ['customer_id' => $customerId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'pending'";
            $result = $this->db->fetchOne($sql);
        }
        return $result['count'];
    }
    
    /**
     * Get payments by date range
     */
    public function getPaymentsByDateRange($startDate, $endDate) {
        $sql = "SELECT p.*, b.booking_date, s.service_name, c.first_name, c.last_name 
                FROM payments p 
                JOIN bookings b ON p.booking_id = b.booking_id 
                JOIN services s ON b.service_id = s.service_id 
                JOIN customers c ON b.customer_id = c.customer_id 
                WHERE DATE(p.payment_date) BETWEEN :start_date AND :end_date 
                ORDER BY p.payment_date DESC";
        return $this->db->fetchAll($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
    
    /**
     * Get total revenue
     */
    public function getTotalRevenue($startDate = null, $endDate = null) {
        $sql = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'paid'";
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " AND DATE(payment_date) BETWEEN :start_date AND :end_date";
            $params['start_date'] = $startDate;
            $params['end_date'] = $endDate;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Process payment
     */
    public function processPayment($paymentId, $paymentMethod, $transactionId = null) {
        try {
            $this->db->beginTransaction();
            
            // Update payment status
            $this->updatePaymentStatus($paymentId, 'paid', $transactionId);
            
            // Get payment details
            $payment = $this->getPaymentById($paymentId);
            
            // Update booking status to confirmed
            $this->db->update('bookings', 
                ['status' => 'confirmed'], 
                'booking_id = :booking_id', 
                ['booking_id' => $payment['booking_id']]
            );
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Get purchase history for customer
     */
    public function getPurchaseHistory($customerId) {
        $sql = "SELECT ph.*, s.service_name, c.first_name, c.last_name 
                FROM purchase_history ph 
                JOIN services s ON ph.service_id = s.service_id 
                JOIN customers c ON ph.customer_id = c.customer_id 
                WHERE ph.customer_id = :customer_id 
                ORDER BY ph.purchase_date DESC";
        return $this->db->fetchAll($sql, ['customer_id' => $customerId]);
    }
}

// Initialize controller
$paymentController = new PaymentController();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_payment':
            $data = [
                'booking_id' => $_POST['booking_id'],
                'amount' => $_POST['amount'],
                'payment_method' => $_POST['payment_method'],
                'payment_status' => 'pending',
                'notes' => $_POST['notes'] ?? ''
            ];
            
            try {
                $paymentId = $paymentController->createPayment($data);
                echo json_encode(['success' => true, 'payment_id' => $paymentId]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'process_payment':
            $paymentId = $_POST['payment_id'];
            $paymentMethod = $_POST['payment_method'];
            $transactionId = $_POST['transaction_id'] ?? null;
            
            try {
                $paymentController->processPayment($paymentId, $paymentMethod, $transactionId);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'get_revenue':
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            
            $revenue = $paymentController->getTotalRevenue($startDate, $endDate);
            echo json_encode(['revenue' => $revenue]);
            exit;
    }
}

// Get payments data for display
$payments = $paymentController->getCustomerPayments(1); // Default to customer ID 1 for demo
$pendingCount = $paymentController->getPendingPaymentsCount(1);
$totalRevenue = $paymentController->getTotalRevenue();
$purchaseHistory = $paymentController->getPurchaseHistory(1);

// Include the view
require_once "../views/cus_payment.php";