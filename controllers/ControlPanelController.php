<?php
/**
 * Control Panel Controller
 * Handles super admin control panel functionality including login tracking
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';

class ControlPanelController extends Controller {
    
    protected $db;
    
    public function __construct() {
        // Base Controller doesn't have a constructor, so we don't call parent::__construct()
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Default Index - Redirect to Login
     */
    public function index() {
        $this->login();
    }
    
    /**
     * Control Panel Login Page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->isControlPanelLoggedIn()) {
            $this->redirect('control-panel/dashboard');
        }
        
        $data['title'] = 'Control Panel Login';
        $this->view('control_panel/login', $data);
    }
    
    /**
     * Process Control Panel Login
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('control-panel/login');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Validate input
        if (empty($email) || empty($password)) {
            $this->logLoginAttempt(null, 'control_panel', $email, null, $ipAddress, $userAgent, 'failed', 'Empty credentials');
            $_SESSION['error'] = 'Email and password are required.';
            $this->redirect('control-panel/login');
        }
        
        // Check credentials
        $stmt = $this->db->prepare("SELECT * FROM control_panel_admins WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            $this->logLoginAttempt(null, 'control_panel', $email, null, $ipAddress, $userAgent, 'failed', 'Invalid email');
            $_SESSION['error'] = 'Invalid email or password.';
            $this->redirect('control-panel/login');
        }
        
        // Verify password
        if (!password_verify($password, $admin['password'])) {
            $this->logLoginAttempt($admin['id'], 'control_panel', $email, $admin['fullname'], $ipAddress, $userAgent, 'failed', 'Invalid password');
            $_SESSION['error'] = 'Invalid email or password.';
            $this->redirect('control-panel/login');
        }
        
        // Login successful
        $_SESSION['control_panel_admin'] = [
            'id' => $admin['id'],
            'email' => $admin['email'],
            'fullname' => $admin['fullname']
        ];
        
        // Update last login
        $stmt = $this->db->prepare("UPDATE control_panel_admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$admin['id']]);
        
        // Log successful login
        $this->logLoginAttempt($admin['id'], 'control_panel', $email, $admin['fullname'], $ipAddress, $userAgent, 'success', null);
        
        $_SESSION['success'] = 'Welcome to Control Panel!';
        $this->redirect('control-panel/dashboard');
    }
    
    /**
     * Control Panel Dashboard
     */
    public function dashboard() {
        $this->requireControlPanelLogin();
        
        // Redirect super admins to their dashboard
        if ($this->isSuperAdmin()) {
            $this->redirect('control-panel/superAdminDashboard');
        }
        
        $data['title'] = 'Control Panel Dashboard';
        $data['page_title'] = 'Dashboard Overview';
        $data['page_subtitle'] = 'Monitor login activities and system access';
        $data['page_icon'] = 'fas fa-tachometer-alt';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = false;
        $data['pending_count'] = 0;
        
        // Get statistics
        $data['stats'] = $this->getLoginStats();
        
        // Get recent login logs
        $data['recent_logins'] = $this->getRecentLogins(50);
        
        $this->view('control_panel/dashboard', $data);
    }
    
    /**
     * View all login logs with filtering
     */
    public function loginLogs() {
        $this->requireControlPanelLogin();
        
        $userType = $_GET['type'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        $limit = (int)($_GET['limit'] ?? 100);
        
        $data['title'] = 'Login Logs';
        $data['page_title'] = 'Login Logs';
        $data['page_subtitle'] = 'View and filter all login activities';
        $data['page_icon'] = 'fas fa-history';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = $this->isSuperAdmin();
        $data['pending_count'] = 0;
        $data['login_logs'] = $this->getLoginLogs($userType, $status, $limit);
        $data['filter_type'] = $userType;
        $data['filter_status'] = $status;
        $data['filter_limit'] = $limit;
        
        $this->view('control_panel/login_logs', $data);
    }
    
    /**
     * Logout from control panel
     */
    public function logout() {
        unset($_SESSION['control_panel_admin']);
        $_SESSION['success'] = 'Logged out successfully.';
        $this->redirect('control-panel/login');
    }
    
    /**
     * Get login statistics
     */
    private function getLoginStats() {
        $stats = [];
        
        // Total logins today
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN login_status = 'success' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN login_status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM login_logs 
            WHERE DATE(login_time) = CURDATE()
        ");
        $stats['today'] = $stmt->fetch();
        
        // Total logins this week
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN login_status = 'success' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN login_status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM login_logs 
            WHERE YEARWEEK(login_time, 1) = YEARWEEK(CURDATE(), 1)
        ");
        $stats['week'] = $stmt->fetch();
        
        // By user type
        $stmt = $this->db->query("
            SELECT 
                user_type,
                COUNT(*) as total,
                SUM(CASE WHEN login_status = 'success' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN login_status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM login_logs 
            GROUP BY user_type
        ");
        $stats['by_type'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Get recent login logs
     */
    private function getRecentLogins($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT * FROM login_logs 
            ORDER BY login_time DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get login logs with filters
     */
    private function getLoginLogs($userType = 'all', $status = 'all', $limit = 100) {
        $sql = "SELECT * FROM login_logs WHERE 1=1";
        $params = [];
        
        if ($userType !== 'all') {
            $sql .= " AND user_type = ?";
            $params[] = $userType;
        }
        
        if ($status !== 'all') {
            $sql .= " AND login_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY login_time DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Log login attempt
     */
    public function logLoginAttempt($userId, $userType, $email, $fullname, $ipAddress, $userAgent, $status, $failureReason = null) {
        $stmt = $this->db->prepare("
            INSERT INTO login_logs 
            (user_id, user_type, email, fullname, ip_address, user_agent, login_status, failure_reason) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $userType,
            $email,
            $fullname,
            $ipAddress,
            $userAgent,
            $status,
            $failureReason
        ]);
    }
    
    /**
     * Check if control panel admin is logged in
     */
    private function isControlPanelLoggedIn() {
        return isset($_SESSION['control_panel_admin']) && !empty($_SESSION['control_panel_admin']['id']);
    }
    
    /**
     * Require control panel login
     */
    private function requireControlPanelLogin() {
        if (!$this->isControlPanelLoggedIn()) {
            $_SESSION['error'] = 'Please login to access the control panel.';
            $this->redirect('control-panel/login');
        }
    }
    
    /**
     * Check if current user is a Super Admin
     */
    private function isSuperAdmin() {
        if (!$this->isControlPanelLoggedIn()) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT role FROM control_panel_admins WHERE id = ?");
        $stmt->execute([$_SESSION['control_panel_admin']['id']]);
        $admin = $stmt->fetch();
        
        return $admin && $admin['role'] === 'super_admin';
    }
    
    /**
     * Require Super Admin access
     */
    private function requireSuperAdmin() {
        $this->requireControlPanelLogin();
        
        if (!$this->isSuperAdmin()) {
            $_SESSION['error'] = 'Access denied. Super Admin privileges required.';
            $this->redirect('control-panel/dashboard');
        }
    }
    
    /**
     * Super Admin Dashboard
     */
    public function superAdminDashboard() {
        $this->requireSuperAdmin();
        
        $data['title'] = 'Super Admin Dashboard';
        $data['page_title'] = 'Super Admin Dashboard';
        $data['page_subtitle'] = 'Manage system and admin accounts';
        $data['page_icon'] = 'fas fa-crown';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        
        // Get statistics
        $data['stats'] = $this->getSuperAdminStats();
        
        // Get pending admin registrations
        $data['pending_admins'] = $this->getPendingAdmins();
        $adminPending = count($data['pending_admins']);
        
        // Get pending customer accounts (customers with inactive status or pending approval)
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'inactive'");
            $pendingCustomers = $stmt->fetch()['count'] ?? 0;
        } catch (Exception $e) {
            $pendingCustomers = 0;
        }
        
        // Get pending compliance reports count
        try {
            $complianceStats = $this->getComplianceReportsStats();
            $data['stats']['pending_compliance_reports'] = $complianceStats['pending'] ?? 0;
        } catch (Exception $e) {
            $data['stats']['pending_compliance_reports'] = 0;
        }
        
        $data['pending_count'] = $adminPending + $pendingCustomers;
        $data['pending_customers_count'] = $pendingCustomers;
        
        $this->view('control_panel/super_admin_dashboard', $data);
    }
    
    /**
     * Get Super Admin Statistics
     */
    private function getSuperAdminStats() {
        $stats = [];
        
        // Total customers
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'active'");
        $stats['total_customers'] = $stmt->fetch()['count'];
        
        // New customers today
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND DATE(created_at) = CURDATE()");
        $stats['new_customers_today'] = $stmt->fetch()['count'];
        
        // Total active admins
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM control_panel_admins WHERE status = 'active' AND role = 'admin'");
        $stats['total_active_admins'] = $stmt->fetch()['count'];
        
        // Pending admin registrations (includes pending_verification and pending)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM admin_registrations WHERE registration_status IN ('pending_verification', 'pending')");
        $stats['pending_admin_registrations'] = $stmt->fetch()['count'];
        
        // Pending customer accounts (inactive customers that need approval)
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'inactive'");
            $stats['pending_customer_accounts'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $stats['pending_customer_accounts'] = 0;
        }
        
        // Total bookings
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings");
            $stats['total_bookings'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $stats['total_bookings'] = 0;
        }
        
        // Pending bookings
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
            $stats['pending_bookings'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $stats['pending_bookings'] = 0;
        }
        
        // Total revenue (from bookings)
        try {
            $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM bookings WHERE payment_status = 'paid'");
            $stats['total_revenue'] = $stmt->fetch()['revenue'];
        } catch (Exception $e) {
            $stats['total_revenue'] = 0;
        }
        
        // Today's revenue
        try {
            $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM bookings WHERE payment_status = 'paid' AND DATE(created_at) = CURDATE()");
            $stats['today_revenue'] = $stmt->fetch()['revenue'];
        } catch (Exception $e) {
            $stats['today_revenue'] = 0;
        }
        
        // This month's revenue
        try {
            $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM bookings WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
            $stats['month_revenue'] = $stmt->fetch()['revenue'];
        } catch (Exception $e) {
            $stats['month_revenue'] = 0;
        }
        
        // Total super admins
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM control_panel_admins WHERE role = 'super_admin' AND status = 'active'");
        $stats['total_super_admins'] = $stmt->fetch()['count'];
        
        return $stats;
    }
    
    /**
     * Get Pending Admin Registrations
     */
    private function getPendingAdmins() {
        $stmt = $this->db->query("
            SELECT * FROM admin_registrations 
            WHERE registration_status IN ('pending_verification', 'pending')
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    
    /**
     * View All Registered Admins
     */
    public function adminRegistrations() {
        $this->requireSuperAdmin();
        
        $status = $_GET['status'] ?? 'all';
        
        $data['title'] = 'Admin Registrations';
        $data['page_title'] = 'Admin Registrations';
        $data['page_subtitle'] = 'Review and manage admin registration requests';
        $data['page_icon'] = 'fas fa-user-plus';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        $data['registrations'] = $this->getAdminRegistrations($status);
        $data['filter_status'] = $status;
        
        // Get pending count (admin registrations - includes pending_verification and pending)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM admin_registrations WHERE registration_status IN ('pending_verification', 'pending')");
        $adminPending = $stmt->fetch()['count'] ?? 0;
        
        // Get pending customer accounts
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'inactive'");
            $pendingCustomers = $stmt->fetch()['count'] ?? 0;
        } catch (Exception $e) {
            $pendingCustomers = 0;
        }
        
        $data['pending_count'] = $adminPending + $pendingCustomers;
        $data['pending_customers_count'] = $pendingCustomers;
        
        $this->view('control_panel/admin_registrations', $data);
    }
    
    /**
     * Get Admin Registrations with Filter
     */
    private function getAdminRegistrations($status = 'all') {
        if ($status === 'all') {
            $stmt = $this->db->query("
                SELECT * FROM admin_registrations 
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll();
        } else if ($status === 'pending') {
            // Show both pending_verification and pending statuses
            $stmt = $this->db->query("
                SELECT * FROM admin_registrations 
                WHERE registration_status IN ('pending_verification', 'pending')
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll();
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM admin_registrations 
                WHERE registration_status = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$status]);
            return $stmt->fetchAll();
        }
    }
    
    /**
     * Send verification code to admin
     */
    public function sendVerificationCode($id) {
        $this->requireSuperAdmin();
        
        try {
            // Get registration details
            $stmt = $this->db->prepare("SELECT * FROM admin_registrations WHERE id = ?");
            $stmt->execute([$id]);
            $registration = $stmt->fetch();
            
            if (!$registration) {
                $_SESSION['error'] = 'Registration not found.';
                $this->redirect('control-panel/adminRegistrations');
            }
            
            // Generate 4-digit verification code
            $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            // Update registration with verification code
            $updateStmt = $this->db->prepare("
                UPDATE admin_registrations 
                SET verification_code = ?, 
                    verification_code_sent_at = NOW(),
                    registration_status = 'pending_verification'
                WHERE id = ?
            ");
            $updateStmt->execute([$verificationCode, $id]);
            
            // Send verification code via email
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            $emailSent = $notificationService->sendAdminVerificationCode(
                $registration['email'],
                $registration['fullname'],
                $verificationCode
            );
            
            if ($emailSent) {
                // Log super admin activity (update action type if needed)
                try {
                    $stmt = $this->db->prepare("
                        INSERT INTO super_admin_activity 
                        (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                        VALUES (?, ?, 'admin_approved', ?, ?)
                    ");
                    $stmt->execute([
                        $_SESSION['control_panel_admin']['id'],
                        $_SESSION['control_panel_admin']['fullname'],
                        $registration['fullname'],
                        "Sent verification code to {$registration['fullname']} ({$registration['email']})"
                    ]);
                } catch (Exception $e) {
                    // If action_type doesn't support this, just log it
                    error_log("Activity log error: " . $e->getMessage());
                }
                
                $_SESSION['success'] = "Verification code sent successfully via Gmail to {$registration['email']}. The admin will receive a 4-digit code in their inbox.";
            } else {
                $_SESSION['error'] = "Failed to send verification code. Please check email configuration.";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to send verification code: ' . $e->getMessage();
            error_log("Error sending verification code: " . $e->getMessage());
        }
        
        $this->redirect('control-panel/adminRegistrations');
    }
    
    /**
     * Get Verification Code (AJAX endpoint)
     */
    public function getVerificationCode($id) {
        $this->requireSuperAdmin();
        
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->db->prepare("
                SELECT verification_code, verification_code_sent_at, email, fullname 
                FROM admin_registrations 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $registration = $stmt->fetch();
            
            if (!$registration) {
                echo json_encode(['success' => false, 'message' => 'Registration not found.']);
                return;
            }
            
            if (empty($registration['verification_code'])) {
                echo json_encode(['success' => false, 'message' => 'No verification code has been sent yet.']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'code' => $registration['verification_code'],
                'sent_at' => $registration['verification_code_sent_at'],
                'email' => $registration['email'],
                'fullname' => $registration['fullname']
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch verification code: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Approve Admin Registration
     * Automatically generates and sends verification code when approving
     */
    public function approveAdmin($id) {
        $this->requireSuperAdmin();
        
        try {
            // Get registration details
            $stmt = $this->db->prepare("SELECT * FROM admin_registrations WHERE id = ?");
            $stmt->execute([$id]);
            $registration = $stmt->fetch();
            
            if (!$registration) {
                $_SESSION['error'] = 'Registration not found.';
                $this->redirect('control-panel/adminRegistrations');
            }
            
            // Don't allow approval if already approved or rejected
            if ($registration['registration_status'] === 'approved' || $registration['registration_status'] === 'rejected') {
                $_SESSION['error'] = 'This registration has already been processed.';
                $this->redirect('control-panel/adminRegistrations');
            }
            
            // Start transaction
            $this->db->beginTransaction();
            
            // Check if verification_code column exists
            try {
                $checkColumnStmt = $this->db->query("
                    SELECT COUNT(*) as col_count 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'admin_registrations' 
                    AND COLUMN_NAME = 'verification_code'
                ");
                $columnExists = $checkColumnStmt->fetch()['col_count'] > 0;
            } catch (Exception $e) {
                // If we can't check, assume columns don't exist
                $columnExists = false;
                error_log("Could not check for verification_code column: " . $e->getMessage());
            }
            
            // Get verification code from admin_verification_codes table or generate new one
            $verificationCode = null;
            $codeFromTable = false;
            $codeId = null;
            
            // Check if admin_verification_codes table exists and get a code
            try {
                $checkTableStmt = $this->db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM INFORMATION_SCHEMA.TABLES 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'admin_verification_codes'
                ");
                $tableExists = $checkTableStmt->fetch()['table_exists'] > 0;
                
                if ($tableExists) {
                    // Get next available code from the table (using FOR UPDATE to lock the row)
                    // FOR UPDATE requires the query to be in a transaction (which we started above)
                    $getCodeStmt = $this->db->prepare("
                        SELECT id, verification_code 
                        FROM admin_verification_codes 
                        WHERE status = 'available' 
                        ORDER BY verification_code ASC 
                        LIMIT 1 
                        FOR UPDATE
                    ");
                    $getCodeStmt->execute();
                    $codeRecord = $getCodeStmt->fetch();
                    
                    if ($codeRecord && !empty($codeRecord['verification_code'])) {
                        $verificationCode = $codeRecord['verification_code'];
                        $codeId = $codeRecord['id'];
                        $codeFromTable = true;
                        
                        // Mark code as reserved (will be marked as used after successful assignment)
                        $reserveCodeStmt = $this->db->prepare("
                            UPDATE admin_verification_codes 
                            SET status = 'reserved',
                                updated_at = NOW()
                            WHERE id = ?
                        ");
                        $reserveCodeStmt->execute([$codeId]);
                        error_log("INFO: Reserved verification code {$verificationCode} (ID: {$codeId}) from admin_verification_codes table");
                    } else {
                        error_log("WARNING: No available codes found in admin_verification_codes table");
                    }
                }
            } catch (Exception $e) {
                error_log("Warning: Could not get code from admin_verification_codes table: " . $e->getMessage());
                $tableExists = false;
            }
            
            // If no code from table, generate a random one (fallback)
            if ($verificationCode === null) {
                // Generate random 4-digit code between 1000-9999
                $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                error_log("WARNING: Generated random verification code (admin_verification_codes table not available or empty). Using: " . $verificationCode);
            }
            
            // Update registration with verification code and set status to pending_verification
            // The admin will need to verify this code before final activation
            if ($columnExists) {
                // Columns exist - update with verification code
                try {
                    $updateCodeStmt = $this->db->prepare("
                        UPDATE admin_registrations 
                        SET verification_code = ?, 
                            verification_code_sent_at = NOW(),
                            registration_status = 'pending_verification',
                            approved_by = ?,
                            approved_at = NOW()
                        WHERE id = ?
                    ");
                    $updateCodeStmt->execute([
                        $verificationCode,
                        $_SESSION['control_panel_admin']['id'],
                        $id
                    ]);
                    
                    // If code came from table, update it to 'used' and link to registration
                    if ($codeFromTable && $tableExists && $codeId !== null) {
                        try {
                            $assignCodeStmt = $this->db->prepare("
                                UPDATE admin_verification_codes 
                                SET status = 'used',
                                    admin_registration_id = ?,
                                    assigned_to_email = ?,
                                    assigned_to_name = ?,
                                    assigned_by_super_admin_id = ?,
                                    assigned_at = NOW(),
                                    expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY),
                                    updated_at = NOW()
                                WHERE id = ?
            ");
                            $assignCodeStmt->execute([
                                $id,
                $registration['email'],
                                $registration['fullname'],
                                $_SESSION['control_panel_admin']['id'],
                                $codeId
                            ]);
                            error_log("INFO: Verification code {$verificationCode} (ID: {$codeId}) marked as 'used' and assigned to {$registration['email']}");
                        } catch (Exception $e) {
                            error_log("Warning: Could not update admin_verification_codes table: " . $e->getMessage());
                            // Try to release the reserved code if assignment fails
                            try {
                                $releaseStmt = $this->db->prepare("
                                    UPDATE admin_verification_codes 
                                    SET status = 'available',
                                        updated_at = NOW()
                                    WHERE id = ?
                                ");
                                $releaseStmt->execute([$codeId]);
                                error_log("INFO: Released reserved code {$verificationCode} (ID: {$codeId}) back to available status");
                            } catch (Exception $releaseError) {
                                error_log("ERROR: Could not release reserved code: " . $releaseError->getMessage());
                            }
                        }
                    }
                } catch (Exception $e) {
                    // If update fails, check if pending_verification status exists
                    $checkStatusStmt = $this->db->query("
                        SELECT COLUMN_TYPE 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'admin_registrations' 
                        AND COLUMN_NAME = 'registration_status'
                    ");
                    $statusType = $checkStatusStmt->fetch()['COLUMN_TYPE'] ?? '';
                    $newStatus = (strpos($statusType, 'pending_verification') !== false) ? 'pending_verification' : 'approved';
                    
                    // Try update without verification_code_sent_at if it doesn't exist
                    $updateCodeStmt = $this->db->prepare("
                        UPDATE admin_registrations 
                        SET verification_code = ?, 
                            registration_status = ?,
                            approved_by = ?,
                            approved_at = NOW()
                        WHERE id = ?
                    ");
                    $updateCodeStmt->execute([
                        $verificationCode,
                        $newStatus,
                        $_SESSION['control_panel_admin']['id'],
                        $id
                    ]);
                }
            } else {
                // Columns don't exist - update without verification code fields
                // Check what status values are available
                try {
                    $checkStatusStmt = $this->db->query("
                        SELECT COLUMN_TYPE 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'admin_registrations' 
                        AND COLUMN_NAME = 'registration_status'
                    ");
                    $statusRow = $checkStatusStmt->fetch();
                    $statusType = $statusRow['COLUMN_TYPE'] ?? '';
                    $newStatus = (strpos($statusType, 'pending_verification') !== false) ? 'pending_verification' : 'approved';
                } catch (Exception $e) {
                    $newStatus = 'approved';
                }
                
                // Update without verification_code columns
                $updateCodeStmt = $this->db->prepare("
                UPDATE admin_registrations 
                    SET registration_status = ?,
                    approved_by = ?,
                    approved_at = NOW()
                WHERE id = ?
            ");
                $updateCodeStmt->execute([
                    $newStatus,
                $_SESSION['control_panel_admin']['id'],
                $id
            ]);
            
                // Note: verification_code cannot be stored, but we can still approve
                $verificationCode = null;
            }
            
            // Send verification code via email if code was generated
            // This email will indicate that the account has been approved by super admin
            $emailSent = false;
            if ($verificationCode !== null) {
                try {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    $emailSent = $notificationService->sendAdminVerificationCode(
                        $registration['email'],
                        $registration['fullname'],
                        $verificationCode
                    );
                    
                    if ($emailSent) {
                        error_log("INFO: Verification code email sent successfully to {$registration['email']} after super admin approval");
                        // Update verification_code_sent_at if column exists
                        try {
                            $updateSentStmt = $this->db->prepare("
                                UPDATE admin_registrations 
                                SET verification_code_sent_at = NOW()
                                WHERE id = ?
                            ");
                            $updateSentStmt->execute([$id]);
                        } catch (Exception $e) {
                            // Column might not exist, ignore
                            error_log("Warning: Could not update verification_code_sent_at: " . $e->getMessage());
                        }
                    } else {
                        error_log("WARNING: Failed to send verification code email to {$registration['email']}");
                    }
                } catch (Exception $emailError) {
                    error_log("ERROR: Failed to send verification code email: " . $emailError->getMessage());
                    // Don't fail the approval if email fails - code is still stored in database
                }
            }
            
            // Geocode business address if business information exists
            if (!empty($registration['business_name']) && !empty($registration['business_address'])) {
                $latitude = $registration['business_latitude'] ?? null;
                $longitude = $registration['business_longitude'] ?? null;
                
                // If coordinates are not set, geocode the address
                if (empty($latitude) || empty($longitude)) {
                    try {
                        require_once ROOT . DS . 'core' . DS . 'GeocodingService.php';
                        
                        $coordinates = GeocodingService::geocodeAddressWithRetry(
                            $registration['business_address'],
                            $registration['business_city'] ?? 'Bohol',
                            $registration['business_province'] ?? 'Bohol'
                        );
                        
                        if ($coordinates !== null) {
                            // Update admin_registrations with geocoded coordinates
                            try {
                                $updateCoordsStmt = $this->db->prepare("
                                    UPDATE admin_registrations 
                                    SET business_latitude = ?, business_longitude = ?, updated_at = NOW()
                                    WHERE id = ?
                                ");
                                $updateCoordsStmt->execute([
                                    $coordinates['lat'],
                                    $coordinates['lng'],
                                    $id
                                ]);
                                error_log("INFO: Geocoded coordinates for admin during approval: {$registration['business_name']} - Lat: {$coordinates['lat']}, Lng: {$coordinates['lng']}");
                            } catch (Exception $coordError) {
                                error_log("WARNING: Could not update coordinates in admin_registrations during approval: " . $coordError->getMessage());
                            }
                        } else {
                            error_log("WARNING: Geocoding failed for {$registration['business_name']} during approval. Coordinates will be set during verification.");
                        }
                    } catch (Exception $geocodeError) {
                        error_log("WARNING: Geocoding error during approval: " . $geocodeError->getMessage());
                        // Don't fail approval if geocoding fails
                    }
                }
            }
            
            // Log super admin activity - code automatically sent on approval
            try {
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                    (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                    VALUES (?, ?, 'admin_approved', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $registration['fullname'],
                    "Approved admin registration after reviewing documents. Verification code automatically sent to {$registration['fullname']} ({$registration['email']})"
            ]);
            } catch (Exception $e) {
                error_log("Activity log error: " . $e->getMessage());
            }
            
            $this->db->commit();
            
            // Success message - verification code sent after approval
            if ($verificationCode !== null) {
                $codeSource = $codeFromTable ? "assigned from verification codes dataset" : "generated";
                if ($emailSent) {
                    $_SESSION['success'] = "✅ Admin registration approved! The admin's documents have been verified. Verification code <strong>{$verificationCode}</strong> has been automatically {$codeSource} and sent to <strong>{$registration['email']}</strong> via email. The admin will receive a notification that their account has been approved.";
                } else {
                    $_SESSION['success'] = "✅ Admin registration approved! The admin's documents have been verified. Verification code <strong>{$verificationCode}</strong> has been automatically {$codeSource}. Note: Email sending failed - please check email configuration. The admin can view the code on the verification page.";
                }
                error_log("INFO: Admin registration approved - Email: {$registration['email']}, Registration ID: {$id}, Verification Code: {$verificationCode}, Source: " . ($codeFromTable ? 'Dataset' : 'Random') . ", Email Sent: " . ($emailSent ? 'Yes' : 'No'));
            } else {
                $_SESSION['success'] = "Admin registration approved! Note: Please run the database migration scripts to enable verification code functionality: 1) database/add_verification_code_to_admin_registrations.sql 2) database/setup_verification_codes_complete.sql";
                error_log("INFO: Admin registration approved - Email: {$registration['email']}, Registration ID: {$id} (Verification code columns not available)");
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to approve registration: ' . $e->getMessage();
            error_log("Error approving admin registration: " . $e->getMessage());
        }
        
        // Check if request came from adminAccounts page
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'adminAccounts') !== false) {
            $this->redirect('control-panel/adminAccounts?view=pending');
        } else {
        $this->redirect('control-panel/adminRegistrations');
        }
    }
    
    /**
     * Reject Admin Registration
     */
    public function rejectAdmin($id) {
        $this->requireSuperAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method.';
            $this->redirect('control-panel/adminRegistrations');
        }
        
        $reason = trim($_POST['reason'] ?? '');
        
        if (empty($reason)) {
            $_SESSION['error'] = 'Rejection reason is required.';
            $this->redirect('control-panel/adminRegistrations');
        }
        
        try {
            // Get registration details
            $stmt = $this->db->prepare("SELECT * FROM admin_registrations WHERE id = ?");
            $stmt->execute([$id]);
            $registration = $stmt->fetch();
            
            if (!$registration) {
                $_SESSION['error'] = 'Registration not found.';
                $this->redirect('control-panel/adminRegistrations');
            }
            
            // Start transaction
            $this->db->beginTransaction();
            
            // Update registration status
            $stmt = $this->db->prepare("
                UPDATE admin_registrations 
                SET registration_status = 'rejected', 
                    rejection_reason = ?,
                    approved_by = ?,
                    approved_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $reason,
                $_SESSION['control_panel_admin']['id'],
                $id
            ]);
            
            // Update user status to inactive if user exists
            try {
                $userStmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
                $userStmt->execute([$registration['email']]);
                $user = $userStmt->fetch();
                
                if ($user) {
                    // Update user status to inactive
                    try {
                        $updateUserStmt = $this->db->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
                        $updateUserStmt->execute([$user['id']]);
                    } catch (Exception $e) {
                        error_log("Warning: Could not update user status: " . $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                error_log("Warning: Could not find user to update status: " . $e->getMessage());
            }
            
            // Send rejection email to admin
            $emailSent = false;
            try {
                require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                $notificationService = new NotificationService();
                $emailSent = $notificationService->sendAdminRejection(
                    $registration['email'],
                    $registration['fullname'],
                    $reason
                );
                
                if ($emailSent) {
                    error_log("INFO: Rejection email sent successfully to {$registration['email']}");
                } else {
                    error_log("WARNING: Failed to send rejection email to {$registration['email']}");
                }
            } catch (Exception $emailError) {
                error_log("ERROR: Failed to send rejection email: " . $emailError->getMessage());
                // Don't fail the rejection if email fails
            }
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'admin_rejected', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $registration['fullname'],
                "Rejected admin registration after reviewing documents. Reason: {$reason}"
            ]);
            
            $this->db->commit();
            
            if ($emailSent) {
                $_SESSION['success'] = "✅ Admin registration rejected. Rejection email with reason has been sent to <strong>{$registration['email']}</strong>.";
            } else {
                $_SESSION['success'] = "✅ Admin registration rejected. Note: Email sending failed - please check email configuration.";
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to reject registration: ' . $e->getMessage();
        }
        
        // Check if request came from adminAccounts page
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'adminAccounts') !== false) {
            $this->redirect('control-panel/adminAccounts?view=pending');
        } else {
        $this->redirect('control-panel/adminRegistrations');
        }
    }
    
    /**
     * View All Admin Accounts (Monitoring)
     */
    public function adminAccounts() {
        $this->requireSuperAdmin();
        
        $viewType = $_GET['view'] ?? 'active'; // 'active' or 'pending'
        $status = $_GET['status'] ?? 'all';
        
        $data['title'] = 'Admin Accounts';
        $data['page_title'] = 'Admin Accounts';
        $data['page_subtitle'] = 'Monitor all admin accounts';
        $data['page_icon'] = 'fas fa-user-shield';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        $data['view_type'] = $viewType;
        $data['filter_status'] = $status;
        
        // Always get both counts for badges
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM admin_registrations WHERE registration_status IN ('pending_verification', 'pending')");
        $data['pending_count'] = $stmt->fetch()['count'] ?? 0;
        
        // Get active admin count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM control_panel_admins WHERE role = 'admin'");
        $data['active_count'] = $stmt->fetch()['count'] ?? 0;
        
        if ($viewType === 'pending') {
            // Get pending admin registrations
            $data['pending_admins'] = $this->getPendingAdminRegistrations();
            $data['admins'] = []; // Empty for pending view
        } else {
            // Get active admin accounts
            $data['admins'] = $this->getAdminAccounts($status);
            $data['pending_admins'] = []; // Empty for active view
        }
        
        // Get pending customer counts for sidebar
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'inactive'");
            $pendingCustomers = $stmt->fetch()['count'] ?? 0;
        } catch (Exception $e) {
            $pendingCustomers = 0;
        }
        
        $data['pending_customers_count'] = $pendingCustomers;
        
        $this->view('control_panel/admin_accounts', $data);
    }
    
    /**
     * Get Pending Admin Registrations
     */
    private function getPendingAdminRegistrations() {
        $stmt = $this->db->query("
            SELECT * FROM admin_registrations 
            WHERE registration_status IN ('pending', 'pending_verification')
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Get Admin Accounts with Filter
     */
    private function getAdminAccounts($status = 'all') {
        // Get approved admins from control_panel_admins
        // Approved admins are moved here after approval in admin_registrations
        if ($status === 'all') {
            $stmt = $this->db->query("
                SELECT * FROM control_panel_admins 
                WHERE role = 'admin'
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll();
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM control_panel_admins 
                WHERE role = 'admin' AND status = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$status]);
            return $stmt->fetchAll();
        }
    }
    
    /**
     * View Customer Accounts
     */
    public function customerAccounts() {
        $this->requireSuperAdmin();
        
        $status = $_GET['status'] ?? 'all';
        
        $data['title'] = 'Customer Accounts';
        $data['page_title'] = 'Customer Accounts';
        $data['page_subtitle'] = 'Manage customer account approvals';
        $data['page_icon'] = 'fas fa-users';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        $data['customers'] = $this->getCustomerAccounts($status);
        $data['filter_status'] = $status;
        
        // Get pending counts (includes pending_verification and pending)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM admin_registrations WHERE registration_status IN ('pending_verification', 'pending')");
        $adminPending = $stmt->fetch()['count'] ?? 0;
        
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'inactive'");
            $pendingCustomers = $stmt->fetch()['count'] ?? 0;
        } catch (Exception $e) {
            $pendingCustomers = 0;
        }
        
        $data['pending_count'] = $adminPending + $pendingCustomers;
        $data['pending_customers_count'] = $pendingCustomers;
        
        $this->view('control_panel/customer_accounts', $data);
        }
        
    /**
     * Get Customer Accounts with Filter
     */
    private function getCustomerAccounts($status = 'all') {
        if ($status === 'all') {
            $stmt = $this->db->query("
                SELECT * FROM users 
                WHERE role = 'customer'
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll();
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM users 
                WHERE role = 'customer' AND status = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$status]);
        return $stmt->fetchAll();
        }
    }
    
    /**
     * Approve Customer Account
     */
    public function approveCustomer($id) {
        $this->requireSuperAdmin();
        
        try {
            // Get customer details
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$id]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                $_SESSION['error'] = 'Customer account not found.';
                $this->redirect('control-panel/customerAccounts');
            }
            
            // Update customer status to active (approved)
            $stmt = $this->db->prepare("
                UPDATE users 
                SET status = 'active', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'customer_approved', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $customer['fullname'],
                "Approved customer account for {$customer['fullname']} ({$customer['email']})"
            ]);
            
            $_SESSION['success'] = 'Customer account approved successfully!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to approve customer account: ' . $e->getMessage();
        }
        
        $this->redirect('control-panel/customerAccounts');
    }
    
    /**
     * Reject Customer Account
     */
    public function rejectCustomer($id) {
        $this->requireSuperAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method.';
            $this->redirect('control-panel/customerAccounts');
        }
        
        $reason = trim($_POST['reason'] ?? '');
        
        if (empty($reason)) {
            $_SESSION['error'] = 'Rejection reason is required.';
            $this->redirect('control-panel/customerAccounts');
        }
        
        try {
            // Get customer details
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$id]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                $_SESSION['error'] = 'Customer account not found.';
                $this->redirect('control-panel/customerAccounts');
            }
            
            // Update customer status to inactive (rejected)
            // Note: We keep the account but mark it as inactive
            $stmt = $this->db->prepare("
                UPDATE users 
                SET status = 'inactive', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'customer_rejected', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $customer['fullname'],
                "Rejected customer account for {$customer['fullname']} ({$customer['email']}): {$reason}"
            ]);
            
            $_SESSION['success'] = 'Customer account rejected.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to reject customer account: ' . $e->getMessage();
        }
        
        $this->redirect('control-panel/customerAccounts');
    }
    
    /**
     * Get All Active Admins
     */
    private function getAllActiveAdmins() {
        $stmt = $this->db->query("
            SELECT id, fullname, email 
            FROM control_panel_admins 
            WHERE status = 'active' AND role = 'admin'
            ORDER BY fullname
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Super Admin Registration Page
     */
    public function register() {
        // Check if there are any super admins already
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM control_panel_admins WHERE role = 'super_admin'");
        $superAdminCount = $stmt->fetch()['count'];
        
        // Allow registration if no super admins exist OR if logged in as super admin
        if ($superAdminCount > 0 && !$this->isSuperAdmin()) {
            $_SESSION['error'] = 'Super Admin registration is restricted.';
            $this->redirect('control-panel/login');
        }
        
        $data['title'] = 'Super Admin Registration';
        $this->view('control_panel/register', $data);
    }
    
    /**
     * Process Super Admin Registration
     */
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('control-panel/register');
        }
        
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($fullname)) {
            $errors[] = 'Full name is required.';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        
        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM control_panel_admins WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('control-panel/register');
        }
        
        try {
            // Create super admin account
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $this->db->prepare("
                INSERT INTO control_panel_admins 
                (email, password, fullname, role, status, created_at) 
                VALUES (?, ?, ?, 'super_admin', 'active', NOW())
            ");
            $stmt->execute([$email, $hashedPassword, $fullname]);
            
            $_SESSION['success'] = 'Super Admin account created successfully! You can now login.';
            $this->redirect('control-panel/login');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
            $this->redirect('control-panel/register');
        }
    }
    
    /**
     * Store Ratings Monitoring Page
     */
    public function storeRatings() {
        $this->requireSuperAdmin();
        
        $data['title'] = 'Store Ratings Monitoring';
        $data['page_title'] = 'Store Ratings Monitoring';
        $data['page_subtitle'] = 'Monitor store ratings and manage low-rated stores';
        $data['page_icon'] = 'fas fa-star';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        
        // Get rating threshold from query parameter (default: null to show all stores)
        $ratingThreshold = isset($_GET['threshold']) && $_GET['threshold'] !== '' ? floatval($_GET['threshold']) : null;
        $data['rating_threshold'] = $ratingThreshold;
        
        // Get stores with ratings (sorted by lowest rating first)
        $data['stores'] = $this->getStoresWithRatings($ratingThreshold);
        
        // Get statistics
        $data['stats'] = $this->getStoreRatingStats();
        
        $this->view('control_panel/store_ratings', $data);
    }
    
    /**
     * Compliance Reports Page
     */
    public function complianceReports() {
        $this->requireSuperAdmin();
        
        $data['title'] = 'Compliance Reports';
        $data['page_title'] = 'Compliance Reports';
        $data['page_subtitle'] = 'Review store compliance reports submitted by customers';
        $data['page_icon'] = 'fas fa-clipboard-check';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        
        // Get filter parameters
        $status = $_GET['status'] ?? 'all';
        $reportType = $_GET['type'] ?? 'all';
        
        // Get compliance reports
        $data['reports'] = $this->getComplianceReports($status, $reportType);
        
        // Get statistics
        $data['stats'] = $this->getComplianceReportsStats();
        $data['filter_status'] = $status;
        $data['filter_type'] = $reportType;
        
        $this->view('control_panel/compliance_reports', $data);
    }
    
    /**
     * Get compliance reports with filters
     */
    private function getComplianceReports($status = 'all', $reportType = 'all') {
        try {
            // Check if table exists
            $tableExists = false;
            try {
                $checkTableStmt = $this->db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_compliance_reports'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
            } catch (Exception $e) {
                $tableExists = false;
            }
            
            if (!$tableExists) {
                return [];
            }
            
            $sql = "
                SELECT scr.*, 
                       sl.store_name, sl.address, sl.city, sl.province,
                       u.fullname as customer_name, u.email as customer_email,
                       cpa.fullname as reviewed_by_name
                FROM store_compliance_reports scr
                LEFT JOIN store_locations sl ON scr.store_id = sl.id
                LEFT JOIN users u ON scr.customer_id = u.id
                LEFT JOIN control_panel_admins cpa ON scr.reviewed_by = cpa.id
                WHERE 1=1
            ";
            $params = [];
            
            if ($status !== 'all') {
                $sql .= " AND scr.status = ?";
                $params[] = $status;
            }
            
            if ($reportType !== 'all') {
                $sql .= " AND scr.report_type = ?";
                $params[] = $reportType;
            }
            
            $sql .= " ORDER BY scr.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $reports = $stmt->fetchAll();
            
            // Decode issue_types JSON for each report
            foreach ($reports as &$report) {
                $report['issue_types'] = json_decode($report['issue_types'], true) ?? [];
            }
            
            return $reports;
        } catch (Exception $e) {
            error_log('Error fetching compliance reports: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update Compliance Report Status (AJAX)
     */
    public function updateComplianceReportStatus() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $this->requireSuperAdmin();
        
        $reportId = intval($_POST['report_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        $superAdminId = $_SESSION['control_panel_admin']['id'];
        
        if ($reportId == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Report ID is required']);
            exit;
        }
        
        if (!in_array($status, ['reviewed', 'resolved', 'dismissed'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update report status
            $sql = "UPDATE store_compliance_reports SET status = ?, reviewed_by = ?, reviewed_at = NOW()";
            $params = [$status, $superAdminId];
            
            if ($status === 'resolved') {
                $sql .= ", resolved_at = NOW()";
            }
            
            if (!empty($adminNotes)) {
                $sql .= ", admin_notes = ?";
                $params[] = $adminNotes;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $reportId;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Log activity
            try {
                $reportStmt = $this->db->prepare("SELECT store_id FROM store_compliance_reports WHERE id = ?");
                $reportStmt->execute([$reportId]);
                $report = $reportStmt->fetch();
                
                if ($report) {
                    $storeStmt = $this->db->prepare("SELECT store_name FROM store_locations WHERE id = ?");
                    $storeStmt->execute([$report['store_id']]);
                    $store = $storeStmt->fetch();
                    $storeName = $store ? $store['store_name'] : 'Unknown Store';
                    
                    $activityStmt = $this->db->prepare("
                        INSERT INTO super_admin_activity 
                        (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                        VALUES (?, ?, 'compliance_report_updated', ?, ?)
                    ");
                    $activityStmt->execute([
                        $superAdminId,
                        $_SESSION['control_panel_admin']['fullname'],
                        $storeName,
                        "Updated compliance report #{$reportId} status to '{$status}'" . (!empty($adminNotes) ? ". Notes: {$adminNotes}" : "")
                    ]);
                }
            } catch (Exception $e) {
                // Activity logging is optional, don't fail the whole operation
                error_log('Warning: Could not log compliance report update activity: ' . $e->getMessage());
            }
            
            $this->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Report status updated successfully.'
            ]);
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error updating compliance report status: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating report status: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get compliance reports statistics
     */
    private function getComplianceReportsStats() {
        try {
            // Check if table exists
            $tableExists = false;
            try {
                $checkTableStmt = $this->db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_compliance_reports'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
            } catch (Exception $e) {
                $tableExists = false;
            }
            
            if (!$tableExists) {
                return [
                    'total' => 0,
                    'pending' => 0,
                    'reviewed' => 0,
                    'resolved' => 0,
                    'dismissed' => 0
                ];
            }
            
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed
                FROM store_compliance_reports
            ");
            $stats = $stmt->fetch();
            
            return [
                'total' => (int)$stats['total'],
                'pending' => (int)$stats['pending'],
                'reviewed' => (int)$stats['reviewed'],
                'resolved' => (int)$stats['resolved'],
                'dismissed' => (int)$stats['dismissed']
            ];
        } catch (Exception $e) {
            error_log('Error fetching compliance reports stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'reviewed' => 0,
                'resolved' => 0,
                'dismissed' => 0
            ];
        }
    }
    
    /**
     * Get stores with ratings
     */
    private function getStoresWithRatings($ratingThreshold = null) {
        try {
            // Check if store_ratings table exists
            $tableExists = false;
            try {
                $checkTable = $this->db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_ratings'
                ");
                $tableExists = $checkTable->fetch()['table_exists'] > 0;
            } catch (Exception $e) {
                error_log("DEBUG: Could not check if store_ratings table exists: " . $e->getMessage());
                $tableExists = false;
            }
            
            // Build query to get stores with ratings
            // Even if store_ratings table doesn't exist, we should still show stores
            if ($tableExists) {
                $sql = "
                    SELECT 
                        sl.id,
                        sl.store_name,
                        sl.address,
                        sl.city,
                        sl.province,
                        sl.latitude,
                        sl.longitude,
                        sl.status as store_status,
                        sl.rating as store_rating_field,
                        sl.banned_at,
                        sl.banned_until,
                        sl.ban_duration_days,
                        sl.ban_reason,
                        COUNT(DISTINCT sr.id) as total_ratings,
                        COALESCE(AVG(sr.rating), 0) as avg_rating_from_table,
                        COALESCE(MIN(sr.rating), 0) as min_rating,
                        COALESCE(MAX(sr.rating), 0) as max_rating
                    FROM store_locations sl
                    LEFT JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
                    WHERE sl.status = 'active'
                ";
            } else {
                // Table doesn't exist, just get stores without ratings
                error_log("DEBUG: store_ratings table does not exist, fetching stores without ratings");
                $sql = "
                    SELECT 
                        sl.id,
                        sl.store_name,
                        sl.address,
                        sl.city,
                        sl.province,
                        sl.latitude,
                        sl.longitude,
                        sl.status as store_status,
                        sl.rating as store_rating_field,
                        sl.banned_at,
                        sl.banned_until,
                        sl.ban_duration_days,
                        sl.ban_reason,
                        0 as total_ratings,
                        0 as avg_rating_from_table,
                        0 as min_rating,
                        0 as max_rating
                    FROM store_locations sl
                    WHERE sl.status = 'active'
                ";
            }
            
            $params = [];
            
            // Add GROUP BY only if we're joining with store_ratings table
            if ($tableExists) {
                // Simplified GROUP BY - only group by store_locations fields
                // Use subquery or aggregate functions for admin data to avoid GROUP BY issues
                $sql .= " GROUP BY sl.id, sl.store_name, sl.address, sl.city, sl.province, 
                            sl.latitude, sl.longitude, sl.rating, sl.status, sl.banned_at, 
                            sl.banned_until, sl.ban_duration_days, sl.ban_reason";
                
                // Add rating threshold filter in HAVING clause (after grouping and calculating average)
                if ($ratingThreshold !== null && $ratingThreshold !== '' && $ratingThreshold > 0) {
                    // Filter by calculated average rating from store_ratings, fallback to store_locations.rating
                    $sql .= " HAVING COALESCE(AVG(sr.rating), COALESCE(sl.rating, 0), 0) < ?";
                    $params[] = $ratingThreshold;
                } else {
                    // Show all stores - no threshold filter
                    $sql .= " HAVING 1=1";
                }
                
                // Order by calculated average rating (lowest first), then by total ratings (more ratings = more reliable)
                // Stores with no ratings (0.0) will appear last
                // Use calculated average from store_ratings, fallback to store_locations.rating
                $sql .= " ORDER BY 
                            CASE WHEN COUNT(DISTINCT sr.id) = 0 AND (sl.rating IS NULL OR sl.rating = 0) THEN 1 ELSE 0 END ASC,
                            COALESCE(AVG(sr.rating), COALESCE(sl.rating, 0), 0) ASC, 
                            COUNT(DISTINCT sr.id) DESC, 
                            sl.store_name ASC";
            } else {
                // No store_ratings table - no GROUP BY needed, just filter and order
                if ($ratingThreshold !== null && $ratingThreshold !== '' && $ratingThreshold > 0) {
                    $sql .= " AND COALESCE(sl.rating, 0) < ?";
                    $params[] = $ratingThreshold;
                }
                
                $sql .= " ORDER BY 
                            CASE WHEN sl.rating IS NULL OR sl.rating = 0 THEN 1 ELSE 0 END ASC,
                            COALESCE(sl.rating, 0) ASC, 
                            sl.store_name ASC";
            }
            
            // Debug: Log the SQL query
            error_log("DEBUG: Store Ratings Query SQL: " . $sql);
            error_log("DEBUG: Store Ratings Query Params: " . print_r($params, true));
            
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Debug: Log raw results
                error_log("DEBUG: Raw stores count from query: " . count($stores));
                
            } catch (PDOException $e) {
                error_log("ERROR: SQL Query failed: " . $e->getMessage());
                error_log("ERROR: SQL Query: " . $sql);
                error_log("ERROR: SQL Params: " . print_r($params, true));
                
                // Try a simpler fallback query
                error_log("DEBUG: Attempting fallback query");
                try {
                    $fallbackSql = "
                        SELECT 
                            sl.id,
                            sl.store_name,
                            sl.address,
                            sl.city,
                            sl.province,
                            sl.latitude,
                            sl.longitude,
                            sl.status as store_status,
                            sl.rating as store_rating_field,
                            COALESCE(sl.rating, 0) as rating,
                            COALESCE(sl.rating, 0) as avg_rating,
                            0 as total_ratings,
                            0 as min_rating,
                            0 as max_rating
                        FROM store_locations sl
                        WHERE sl.status = 'active'
                        ORDER BY sl.rating ASC, sl.store_name ASC
                    ";
                    
                    $fallbackStmt = $this->db->prepare($fallbackSql);
                    $fallbackStmt->execute();
                    $stores = $fallbackStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    error_log("DEBUG: Fallback query returned " . count($stores) . " stores");
                } catch (Exception $fallbackError) {
                    error_log("ERROR: Fallback query also failed: " . $fallbackError->getMessage());
                    return [];
                }
            }
            
            // Now get admin information separately to avoid GROUP BY issues
            if (!empty($stores)) {
                foreach ($stores as &$store) {
                    // Get admin info for this store
                    $adminSql = "
                        SELECT u.id as admin_id, u.fullname as admin_name, u.email as admin_email, 
                               u.status as admin_status, ar.id as admin_registration_id, 
                               ar.registration_status
                        FROM store_locations sl
                        LEFT JOIN admin_registrations ar ON (
                            (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                             AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                            OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                                AND ar.business_name IS NOT NULL 
                                AND ar.business_name != '')
                        )
                        LEFT JOIN users u ON ar.user_id = u.id AND u.role = 'admin'
                        WHERE sl.id = ?
                        LIMIT 1
                    ";
                    
                    try {
                        $adminStmt = $this->db->prepare($adminSql);
                        $adminStmt->execute([$store['id']]);
                        $adminData = $adminStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($adminData) {
                            $store['admin_id'] = $adminData['admin_id'];
                            $store['admin_name'] = $adminData['admin_name'];
                            $store['admin_email'] = $adminData['admin_email'];
                            $store['admin_status'] = $adminData['admin_status'];
                            $store['admin_registration_id'] = $adminData['admin_registration_id'];
                            $store['registration_status'] = $adminData['registration_status'];
                        } else {
                            $store['admin_id'] = null;
                            $store['admin_name'] = null;
                            $store['admin_email'] = null;
                            $store['admin_status'] = null;
                            $store['admin_registration_id'] = null;
                            $store['registration_status'] = null;
                        }
                    } catch (Exception $e) {
                        error_log("ERROR: Failed to get admin info for store ID " . $store['id'] . ": " . $e->getMessage());
                        // Set defaults if admin lookup fails
                        $store['admin_id'] = null;
                        $store['admin_name'] = null;
                        $store['admin_email'] = null;
                        $store['admin_status'] = null;
                        $store['admin_registration_id'] = null;
                        $store['registration_status'] = null;
                    }
                }
                unset($store);
            }
            
            // Post-process stores to ensure rating uses calculated average from store_ratings
            foreach ($stores as &$store) {
                // Calculate rating: use avg_rating_from_table from store_ratings if available, otherwise use store rating field
                $calculatedAvgRating = floatval($store['avg_rating_from_table'] ?? 0);
                $storeRatingField = floatval($store['store_rating_field'] ?? 0);
                $totalRatings = intval($store['total_ratings'] ?? 0);
                
                // If we have ratings from store_ratings table, use that average
                if ($totalRatings > 0 && $calculatedAvgRating > 0) {
                    $store['rating'] = $calculatedAvgRating;
                    $store['avg_rating'] = $calculatedAvgRating;
                } else {
                    // Fallback to store_locations.rating field
                    $store['rating'] = $storeRatingField > 0 ? $storeRatingField : 0;
                    $store['avg_rating'] = $storeRatingField > 0 ? $storeRatingField : 0;
                }
                
                // Ensure total_ratings is an integer
                $store['total_ratings'] = $totalRatings;
                
                // Ensure min/max ratings are set (only if we have ratings)
                if ($totalRatings > 0) {
                    $store['min_rating'] = floatval($store['min_rating'] ?? 0);
                    $store['max_rating'] = floatval($store['max_rating'] ?? 0);
                } else {
                    $store['min_rating'] = 0;
                    $store['max_rating'] = 0;
                }
            }
            unset($store);
            
            // Debug: Log stores found
            if (empty($stores)) {
                error_log("DEBUG: No stores found in getStoresWithRatings. Threshold: " . ($ratingThreshold ?? 'null'));
            } else {
                error_log("DEBUG: Found " . count($stores) . " stores in getStoresWithRatings");
                // Log first few stores for debugging
                foreach (array_slice($stores, 0, 5) as $idx => $store) {
                    error_log("DEBUG: Store " . ($idx + 1) . ": " . $store['store_name'] . 
                             " - Rating: " . $store['rating'] . 
                             " - Total Ratings: " . $store['total_ratings'] . 
                             " - Store Rating Field: " . ($store['store_rating_field'] ?? 'NULL'));
                }
            }
            
            return $stores;
            
        } catch (Exception $e) {
            error_log("Error getting stores with ratings: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get store rating statistics
     */
    private function getStoreRatingStats() {
        try {
            // Check if store_ratings table exists
            $checkTable = $this->db->query("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'store_ratings'
            ");
            $tableExists = $checkTable->fetch()['table_exists'] > 0;
            
            if (!$tableExists) {
                return [
                    'total_stores' => 0,
                    'stores_with_ratings' => 0,
                    'stores_below_threshold' => 0,
                    'avg_rating' => 0,
                    'total_ratings' => 0
                ];
            }
            
            // Get total stores
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM store_locations 
                WHERE status = 'active'
            ");
            $totalStores = $stmt->fetch()['count'];
            
            // Get stores with ratings
            $stmt = $this->db->query("
                SELECT COUNT(DISTINCT sl.id) as count
                FROM store_locations sl
                INNER JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
                WHERE sl.status = 'active'
            ");
            $storesWithRatings = $stmt->fetch()['count'];
            
            // Get stores below threshold (default: 2.0)
            $threshold = 2.0;
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM store_locations
                WHERE status = 'active'
                AND rating IS NOT NULL
                AND rating < ?
            ");
            $stmt->execute([$threshold]);
            $storesBelowThreshold = $stmt->fetch()['count'];
            
            // Get average rating
            $stmt = $this->db->query("
                SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings
                FROM store_ratings
                WHERE status = 'active'
            ");
            $ratingData = $stmt->fetch();
            $avgRating = $ratingData['avg_rating'] ?? 0;
            $totalRatings = $ratingData['total_ratings'] ?? 0;
            
            return [
                'total_stores' => $totalStores,
                'stores_with_ratings' => $storesWithRatings,
                'stores_below_threshold' => $storesBelowThreshold,
                'avg_rating' => round($avgRating, 2),
                'total_ratings' => $totalRatings
            ];
            
        } catch (Exception $e) {
            error_log("Error getting store rating stats: " . $e->getMessage());
            return [
                'total_stores' => 0,
                'stores_with_ratings' => 0,
                'stores_below_threshold' => 0,
                'avg_rating' => 0,
                'total_ratings' => 0
            ];
        }
    }
    
    /**
     * Ban Store and Admin (AJAX)
     */
    public function banStore() {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }
        
        $storeId = intval($_POST['store_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Low ratings');
        // Always ban admin when store is banned (regardless of POST value)
        $banAdmin = true;
        
        if ($storeId == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            exit;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Get store information
            // Link stores to admins via email (admin_registrations.email -> users.email)
            $stmt = $this->db->prepare("
                SELECT sl.*, ar.email as admin_email, u.id as admin_user_id, u.email as user_email
                FROM store_locations sl
                LEFT JOIN admin_registrations ar ON (
                    (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                     AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                    OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                        AND ar.business_name IS NOT NULL 
                        AND ar.business_name != '')
                )
                LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
                WHERE sl.id = ?
                LIMIT 1
            ");
            $stmt->execute([$storeId]);
            $store = $stmt->fetch();
            
            if (!$store) {
                throw new Exception('Store not found');
            }
            
            // Get ban duration from POST (default: null = permanent ban)
            $banDurationDays = isset($_POST['ban_duration_days']) && $_POST['ban_duration_days'] !== '' 
                ? intval($_POST['ban_duration_days']) 
                : null;
            
            // Calculate banned_until timestamp
            $bannedUntil = null;
            if ($banDurationDays !== null && $banDurationDays > 0) {
                $bannedUntil = date('Y-m-d H:i:s', strtotime("+{$banDurationDays} days"));
            }
            
            // Get current super admin ID
            $superAdminId = $_SESSION['control_panel_admin']['id'] ?? null;
            
            // 1. Remove store from map (set status to 'inactive') and record ban info
            // Check if ban tracking columns exist
            // IMPORTANT: Status must be set to 'inactive' when store is banned
            $hasBanColumns = $this->checkBanColumnsExist('store_locations');
            
            if ($hasBanColumns) {
                $stmt = $this->db->prepare("
                    UPDATE store_locations 
                    SET status = 'inactive',
                        banned_at = NOW(),
                        banned_until = ?,
                        ban_duration_days = ?,
                        ban_reason = ?,
                        banned_by = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $storeId]);
            } else {
                // Fallback: even without ban columns, ensure status is set to 'inactive'
                $stmt = $this->db->prepare("
                    UPDATE store_locations 
                    SET status = 'inactive',
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$storeId]);
                error_log("WARNING: Ban tracking columns do not exist. Only status was updated. Please run the migration: database/add_ban_fields_simple.php");
            }
            
            // 2. ALWAYS ban the admin account when store is banned
            if ($store['admin_user_id']) {
                $hasUserBanColumns = $this->checkBanColumnsExist('users');
                $hasAdminRegBanColumns = $this->checkBanColumnsExist('admin_registrations');
                
                // Ban admin user account
                // IMPORTANT: Status must be set to 'inactive' when admin is banned
                if ($hasUserBanColumns) {
                    $stmt = $this->db->prepare("
                        UPDATE users 
                        SET status = 'inactive',
                            banned_at = NOW(),
                            banned_until = ?,
                            ban_duration_days = ?,
                            ban_reason = ?,
                            banned_by = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $store['admin_user_id']]);
                } else {
                    // Even without ban columns, ensure status is set to 'inactive'
                    $stmt = $this->db->prepare("
                        UPDATE users 
                        SET status = 'inactive',
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$store['admin_user_id']]);
                }
                
                // Update admin_registrations status to 'banned'
                // IMPORTANT: registration_status must be set to 'banned' when admin is banned
                if ($hasAdminRegBanColumns) {
                    $stmt = $this->db->prepare("
                        UPDATE admin_registrations 
                        SET registration_status = 'banned',
                            banned_at = NOW(),
                            banned_until = ?,
                            ban_duration_days = ?,
                            ban_reason = ?,
                            banned_by = ?,
                            updated_at = NOW()
                        WHERE email = ?
                    ");
                    $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $store['admin_email']]);
                } else {
                    // Even without ban columns, ensure registration_status is set to 'banned'
                    $stmt = $this->db->prepare("
                        UPDATE admin_registrations 
                        SET registration_status = 'banned',
                            updated_at = NOW()
                        WHERE email = ?
                    ");
                    $stmt->execute([$store['admin_email']]);
                }
                
                // Remove ALL stores associated with this admin from map
                // Get all business names for this admin (link by email)
                if ($store['admin_email']) {
                    $stmt = $this->db->prepare("
                        SELECT business_name, business_address 
                        FROM admin_registrations 
                        WHERE email = ?
                    ");
                    $stmt->execute([$store['admin_email']]);
                    $adminBusinesses = $stmt->fetchAll();
                    
                    // Update all stores matching this admin's businesses
                    if (!empty($adminBusinesses)) {
                        foreach ($adminBusinesses as $business) {
                            $businessName = $business['business_name'];
                            $businessAddress = $business['business_address'];
                            
                            // Update stores matching business name and address
                            if ($hasBanColumns) {
                                $stmt = $this->db->prepare("
                                    UPDATE store_locations 
                                    SET status = 'inactive',
                                        banned_at = NOW(),
                                        banned_until = ?,
                                        ban_duration_days = ?,
                                        ban_reason = ?,
                                        banned_by = ?,
                                        updated_at = NOW()
                                    WHERE (LOWER(TRIM(store_name)) = LOWER(TRIM(?))
                                       OR store_name LIKE CONCAT('%', ?, '%'))
                                    AND (LOWER(TRIM(address)) = LOWER(TRIM(?))
                                       OR address LIKE CONCAT('%', ?, '%'))
                                ");
                                $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $businessName, $businessName, $businessAddress, $businessAddress]);
                            } else {
                                $stmt = $this->db->prepare("
                                    UPDATE store_locations 
                                    SET status = 'inactive',
                                        updated_at = NOW()
                                    WHERE (LOWER(TRIM(store_name)) = LOWER(TRIM(?))
                                       OR store_name LIKE CONCAT('%', ?, '%'))
                                    AND (LOWER(TRIM(address)) = LOWER(TRIM(?))
                                       OR address LIKE CONCAT('%', ?, '%'))
                                ");
                                $stmt->execute([$businessName, $businessName, $businessAddress, $businessAddress]);
                            }
                        }
                    }
                }
            }
            
            // Calculate ban duration text for logging
            $banDurationText = $banDurationDays 
                ? " for {$banDurationDays} day(s) (until " . date('Y-m-d', strtotime($bannedUntil)) . ")" 
                : " permanently";
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'store_banned', ?, ?)
            ");
            $adminName = $store['admin_email'] ?? 'Unknown Admin';
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $store['store_name'],
                "Banned store: {$store['store_name']} (Store ID: {$storeId}){$banDurationText}. Store status set to 'inactive'. Reason: {$reason}. Admin account ({$adminName}) also banned{$banDurationText}. Admin status set to 'inactive'."
            ]);
            
            $this->db->commit();
            
            $successMessage = 'Store banned successfully' . $banDurationText . '. Store status set to \'inactive\'.';
            if ($store['admin_user_id']) {
                $successMessage .= ' Admin account (' . $adminName . ') has been banned' . $banDurationText . '. Admin status set to \'inactive\'. Admin cannot log in.';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $successMessage
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error banning store: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to ban store: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Remove Store from Map (AJAX)
     */
    public function removeStoreFromMap() {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }
        
        $storeId = intval($_POST['store_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Low ratings');
        
        if ($storeId == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            exit;
        }
        
        try {
            // Get store information
            $stmt = $this->db->prepare("SELECT * FROM store_locations WHERE id = ?");
            $stmt->execute([$storeId]);
            $store = $stmt->fetch();
            
            if (!$store) {
                throw new Exception('Store not found');
            }
            
            // Remove store from map (set status to 'inactive')
            $stmt = $this->db->prepare("
                UPDATE store_locations 
                SET status = 'inactive', 
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$storeId]);
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'store_removed_from_map', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $store['store_name'],
                "Removed store from map: {$store['store_name']} (Store ID: {$storeId}). Reason: {$reason}"
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Store removed from map successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("Error removing store from map: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to remove store: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Ban Admin Account (AJAX)
     */
    public function banAdminAccount() {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }
        
        $adminId = intval($_POST['admin_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Low store ratings');
        
        if ($adminId == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Admin ID is required'
            ]);
            exit;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Get admin information (link by email since admin_registrations doesn't have user_id)
            $stmt = $this->db->prepare("
                SELECT u.*, ar.id as registration_id, ar.email as registration_email
                FROM users u
                LEFT JOIN admin_registrations ar ON u.email = ar.email
                WHERE u.id = ? AND u.role = 'admin'
            ");
            $stmt->execute([$adminId]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                throw new Exception('Admin not found');
            }
            
            // Get ban duration from POST (default: null = permanent ban)
            $banDurationDays = isset($_POST['ban_duration_days']) && $_POST['ban_duration_days'] !== '' 
                ? intval($_POST['ban_duration_days']) 
                : null;
            
            // Calculate banned_until timestamp
            $bannedUntil = null;
            if ($banDurationDays !== null && $banDurationDays > 0) {
                $bannedUntil = date('Y-m-d H:i:s', strtotime("+{$banDurationDays} days"));
            }
            
            // Get current super admin ID
            $superAdminId = $_SESSION['control_panel_admin']['id'] ?? null;
            
            // Check if ban columns exist
            $hasUserBanColumns = $this->checkBanColumnsExist('users');
            $hasAdminRegBanColumns = $this->checkBanColumnsExist('admin_registrations');
            
            // Ban admin user account
            if ($hasUserBanColumns) {
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET status = 'inactive',
                        banned_at = NOW(),
                        banned_until = ?,
                        ban_duration_days = ?,
                        ban_reason = ?,
                        banned_by = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $adminId]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET status = 'inactive',
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$adminId]);
            }
            
            // Update admin_registrations status (link by email)
            if ($admin['registration_email']) {
                if ($hasAdminRegBanColumns) {
                    $stmt = $this->db->prepare("
                        UPDATE admin_registrations 
                        SET registration_status = 'banned',
                            banned_at = NOW(),
                            banned_until = ?,
                            ban_duration_days = ?,
                            ban_reason = ?,
                            banned_by = ?,
                            updated_at = NOW()
                        WHERE email = ?
                    ");
                    $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $admin['registration_email']]);
                } else {
                    $stmt = $this->db->prepare("
                        UPDATE admin_registrations 
                        SET registration_status = 'banned',
                            updated_at = NOW()
                        WHERE email = ?
                    ");
                    $stmt->execute([$admin['registration_email']]);
                }
            }
            
            // Remove all stores associated with this admin from map
            // First, get all business names for this admin (link by email)
            $stmt = $this->db->prepare("
                SELECT business_name, business_address 
                FROM admin_registrations 
                WHERE email = ?
            ");
            $stmt->execute([$admin['email']]);
            $adminBusinesses = $stmt->fetchAll();
            
            // Update stores that match any of the admin's businesses
            if (!empty($adminBusinesses)) {
                foreach ($adminBusinesses as $business) {
                    $businessName = $business['business_name'];
                    $businessAddress = $business['business_address'];
                    
                    // Update stores matching business name and address
                    $hasStoreBanColumns = $this->checkBanColumnsExist('store_locations');
                    if ($hasStoreBanColumns) {
                        $stmt = $this->db->prepare("
                            UPDATE store_locations 
                            SET status = 'inactive',
                                banned_at = NOW(),
                                banned_until = ?,
                                ban_duration_days = ?,
                                ban_reason = ?,
                                banned_by = ?,
                                updated_at = NOW()
                            WHERE (LOWER(TRIM(store_name)) = LOWER(TRIM(?))
                               OR store_name LIKE CONCAT('%', ?, '%'))
                            AND (LOWER(TRIM(address)) = LOWER(TRIM(?))
                               OR address LIKE CONCAT('%', ?, '%'))
                        ");
                        $stmt->execute([$bannedUntil, $banDurationDays, $reason, $superAdminId, $businessName, $businessName, $businessAddress, $businessAddress]);
                    } else {
                        $stmt = $this->db->prepare("
                            UPDATE store_locations 
                            SET status = 'inactive',
                                updated_at = NOW()
                            WHERE (LOWER(TRIM(store_name)) = LOWER(TRIM(?))
                               OR store_name LIKE CONCAT('%', ?, '%'))
                            AND (LOWER(TRIM(address)) = LOWER(TRIM(?))
                               OR address LIKE CONCAT('%', ?, '%'))
                        ");
                        $stmt->execute([$businessName, $businessName, $businessAddress, $businessAddress]);
                    }
                }
            }
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'admin_banned', ?, ?)
            ");
            $banDurationText = $banDurationDays 
                ? " for {$banDurationDays} day(s) (until " . date('Y-m-d', strtotime($bannedUntil)) . ")" 
                : " permanently";
            
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $admin['fullname'],
                "Banned admin account: {$admin['fullname']} ({$admin['email']}){$banDurationText}. Reason: {$reason}. All associated stores removed from map."
            ]);
            
            $this->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Admin account banned successfully' . $banDurationText . ' and all stores removed from map'
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error banning admin: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to ban admin: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Banned Stores List Page
     */
    public function bannedStores() {
        $this->requireSuperAdmin();
        
        $data['title'] = 'Banned Stores List';
        $data['page_title'] = 'Banned Stores List';
        $data['page_subtitle'] = 'View all banned stores and their ban information';
        $data['page_icon'] = 'fas fa-ban';
        $data['admin'] = $_SESSION['control_panel_admin'];
        $data['is_super_admin'] = true;
        
        // Get banned stores
        $data['banned_stores'] = $this->getBannedStores();
        
        // Get statistics
        $data['stats'] = $this->getBannedStoresStats();
        
        $this->view('control_panel/banned_stores', $data);
    }
    
    /**
     * Get banned stores
     */
    private function getBannedStores() {
        try {
            // Check if ban columns exist
            $hasBanColumns = $this->checkBanColumnsExist('store_locations');
            
            if ($hasBanColumns) {
                $sql = "
                    SELECT 
                        sl.id,
                        sl.store_name,
                        sl.address,
                        sl.city,
                        sl.province,
                        sl.banned_at,
                        sl.banned_until,
                        sl.ban_duration_days,
                        sl.ban_reason,
                        sl.banned_by,
                        cpa.fullname as banned_by_name,
                        u.id as admin_user_id,
                        u.fullname as admin_name,
                        u.email as admin_email,
                        u.status as admin_status,
                        ar.email as admin_registration_email
                    FROM store_locations sl
                    LEFT JOIN control_panel_admins cpa ON sl.banned_by = cpa.id
                    LEFT JOIN admin_registrations ar ON (
                        (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                         AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                        OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                            AND ar.business_name IS NOT NULL 
                            AND ar.business_name != '')
                    )
                    LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
                    WHERE sl.status = 'inactive' 
                        AND sl.banned_at IS NOT NULL
                    ORDER BY sl.banned_at DESC
                ";
            } else {
                // Fallback: just get inactive stores if ban columns don't exist
                $sql = "
                    SELECT 
                        sl.id,
                        sl.store_name,
                        sl.address,
                        sl.city,
                        sl.province,
                        NULL as banned_at,
                        NULL as banned_until,
                        NULL as ban_duration_days,
                        NULL as ban_reason,
                        NULL as banned_by,
                        NULL as banned_by_name,
                        u.id as admin_user_id,
                        u.fullname as admin_name,
                        u.email as admin_email,
                        u.status as admin_status,
                        ar.email as admin_registration_email
                    FROM store_locations sl
                    LEFT JOIN admin_registrations ar ON (
                        (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                         AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                        OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                            AND ar.business_name IS NOT NULL 
                            AND ar.business_name != '')
                    )
                    LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
                    WHERE sl.status = 'inactive'
                    ORDER BY sl.updated_at DESC
                ";
                error_log("WARNING: Ban tracking columns do not exist. Showing all inactive stores. Please run migration: database/add_ban_fields_simple.php");
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $bannedStores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Post-process: Update admin status display based on actual database status
            // NOTE: We don't auto-ban admins here - we only display their actual status
            // Admins are only banned when store is banned, and must be explicitly unbanned
            foreach ($bannedStores as &$store) {
                if (!empty($store['admin_email'])) {
                    // Get actual admin status from database
                    try {
                        $userStatusStmt = $this->db->prepare("
                            SELECT status, banned_at FROM users WHERE email = ? AND role = 'admin'
                        ");
                        $userStatusStmt->execute([$store['admin_email']]);
                        $userStatus = $userStatusStmt->fetch();
                        
                        if ($userStatus) {
                            // Use actual database status
                            $store['admin_status'] = $userStatus['status'];
                            $store['admin_banned_at'] = $userStatus['banned_at'];
                        }
                    } catch (Exception $e) {
                        error_log("Error getting admin status: " . $e->getMessage());
                    }
                }
            }
            unset($store);
            
            return $bannedStores;
            
        } catch (Exception $e) {
            error_log("Error getting banned stores: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get banned stores statistics
     */
    private function getBannedStoresStats() {
        try {
            $stats = [];
            
            // Total banned stores
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM store_locations 
                WHERE status = 'inactive' 
                    AND banned_at IS NOT NULL
            ");
            $stats['total_banned_stores'] = $stmt->fetch()['count'];
            
            // Permanently banned stores
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM store_locations 
                WHERE status = 'inactive' 
                    AND banned_at IS NOT NULL
                    AND (banned_until IS NULL OR ban_duration_days IS NULL)
            ");
            $stats['permanently_banned'] = $stmt->fetch()['count'];
            
            // Temporarily banned stores
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM store_locations 
                WHERE status = 'inactive' 
                    AND banned_at IS NOT NULL
                    AND banned_until IS NOT NULL
                    AND banned_until > NOW()
            ");
            $stats['temporarily_banned'] = $stmt->fetch()['count'];
            
            // Expired bans (should be unbanned)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM store_locations 
                WHERE status = 'inactive' 
                    AND banned_at IS NOT NULL
                    AND banned_until IS NOT NULL
                    AND banned_until <= NOW()
            ");
            $stats['expired_bans'] = $stmt->fetch()['count'];
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error getting banned stores stats: " . $e->getMessage());
            return [
                'total_banned_stores' => 0,
                'permanently_banned' => 0,
                'temporarily_banned' => 0,
                'expired_bans' => 0
            ];
        }
    }
    
    /**
     * Unban Store (AJAX)
     */
    public function unbanStore() {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }
        
        $storeId = intval($_POST['store_id'] ?? 0);
        
        if ($storeId == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            exit;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Get store information
            $stmt = $this->db->prepare("SELECT * FROM store_locations WHERE id = ?");
            $stmt->execute([$storeId]);
            $store = $stmt->fetch();
            
            if (!$store) {
                throw new Exception('Store not found');
            }
            
            // Check if ban columns exist
            $hasBanColumns = $this->checkBanColumnsExist('store_locations');
            
            // Unban store (set status to 'active' and clear ban fields)
            // IMPORTANT: Status must be set to 'active' when store is unbanned
            if ($hasBanColumns) {
                $stmt = $this->db->prepare("
                    UPDATE store_locations 
                    SET status = 'active',
                        banned_at = NULL,
                        banned_until = NULL,
                        ban_duration_days = NULL,
                        ban_reason = NULL,
                        banned_by = NULL,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$storeId]);
            } else {
                // Even without ban columns, ensure status is set to 'active'
                $stmt = $this->db->prepare("
                    UPDATE store_locations 
                    SET status = 'active',
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$storeId]);
            }
            
            // NOTE: Admin account is NOT automatically unbanned when store is unbanned
            // This ensures admins remain banned until super admin explicitly unbans them
            // Admin can only be unbanned through the "Unban Admin Account" button
            // However, if all stores are unbanned, the super admin should unban the admin account separately
            
            // Log super admin activity
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'store_unbanned', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $store['store_name'],
                "Unbanned store: {$store['store_name']} (Store ID: {$storeId}). Store status set to 'active'. Note: Admin account remains banned and cannot log in until explicitly unbanned."
            ]);
            
            $this->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Store unbanned successfully. Store status set to \'active\' and restored to map. Note: The admin account remains banned and cannot log in until you explicitly unban the admin account.'
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error unbanning store: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to unban store: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Unban Admin Account (AJAX)
     * Allows super admin to explicitly unban an admin account
     */
    public function unbanAdmin() {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }
        
        $adminEmail = trim($_POST['admin_email'] ?? '');
        
        if (empty($adminEmail)) {
            echo json_encode([
                'success' => false,
                'message' => 'Admin email is required'
            ]);
            exit;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Get admin information
            $stmt = $this->db->prepare("
                SELECT u.id, u.email, u.fullname, u.status, u.role,
                       ar.email as reg_email, ar.registration_status
                FROM users u
                LEFT JOIN admin_registrations ar ON u.email = ar.email
                WHERE LOWER(u.email) = LOWER(?) AND u.role = 'admin'
            ");
            $stmt->execute([$adminEmail]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                throw new Exception('Admin account not found');
            }
            
            // Check if ban columns exist
            $hasUserBanColumns = $this->checkBanColumnsExist('users');
            $hasAdminRegBanColumns = $this->checkBanColumnsExist('admin_registrations');
            $hasStoreBanColumns = $this->checkBanColumnsExist('store_locations');
            
            // Unban user account
            // IMPORTANT: Status must be set to 'active' when admin is unbanned
            if ($hasUserBanColumns) {
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET status = 'active',
                        banned_at = NULL,
                        banned_until = NULL,
                        ban_duration_days = NULL,
                        ban_reason = NULL,
                        banned_by = NULL,
                        updated_at = NOW()
                    WHERE email = ? AND role = 'admin'
                ");
                $stmt->execute([$adminEmail]);
            } else {
                // Even without ban columns, ensure status is set to 'active'
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET status = 'active',
                        updated_at = NOW()
                    WHERE email = ? AND role = 'admin'
                ");
                $stmt->execute([$adminEmail]);
            }
            
            // Update admin_registrations
            // IMPORTANT: registration_status must be set to 'approved' when admin is unbanned
            if ($hasAdminRegBanColumns) {
                $stmt = $this->db->prepare("
                    UPDATE admin_registrations 
                    SET registration_status = 'approved',
                        banned_at = NULL,
                        banned_until = NULL,
                        ban_duration_days = NULL,
                        ban_reason = NULL,
                        banned_by = NULL,
                        updated_at = NOW()
                    WHERE email = ?
                ");
                $stmt->execute([$adminEmail]);
            } else {
                // Even without ban columns, ensure registration_status is set to 'approved'
                $stmt = $this->db->prepare("
                    UPDATE admin_registrations 
                    SET registration_status = 'approved',
                        updated_at = NOW()
                    WHERE email = ?
                ");
                $stmt->execute([$adminEmail]);
            }
            
            // Optional: If admin is unbanned and has no banned stores, automatically unban all their stores
            // Check if admin has any banned stores
            $bannedStoresCheck = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM store_locations sl
                INNER JOIN admin_registrations ar ON (
                    ar.email = ?
                    AND (
                        (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                         AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                        OR (LOWER(TRIM(sl.store_name)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                            AND ar.business_name IS NOT NULL 
                            AND TRIM(ar.business_name) != '')
                    )
                )
                WHERE sl.status = 'inactive'
                    AND (sl.banned_at IS NOT NULL AND sl.banned_at != '')
            ");
            $bannedStoresCheck->execute([$adminEmail]);
            $bannedStoresCount = $bannedStoresCheck->fetch()['count'];
            
            // If admin has banned stores, ask super admin if they want to unban stores too
            // For now, we'll unban all stores automatically when admin is unbanned
            if ($bannedStoresCount > 0) {
                // Get all stores linked to this admin
                $storesStmt = $this->db->prepare("
                    SELECT sl.id, sl.store_name
                    FROM store_locations sl
                    INNER JOIN admin_registrations ar ON (
                        ar.email = ?
                        AND (
                            (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                             AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                            OR (LOWER(TRIM(sl.store_name)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                                AND ar.business_name IS NOT NULL 
                                AND TRIM(ar.business_name) != '')
                        )
                    )
                    WHERE sl.status = 'inactive'
                        AND (sl.banned_at IS NOT NULL AND sl.banned_at != '')
                ");
                $storesStmt->execute([$adminEmail]);
                $storesToUnban = $storesStmt->fetchAll();
                
                // Unban all stores associated with this admin
                foreach ($storesToUnban as $store) {
                    if ($hasStoreBanColumns) {
                        $unbanStoreStmt = $this->db->prepare("
                            UPDATE store_locations 
                            SET status = 'active',
                                banned_at = NULL,
                                banned_until = NULL,
                                ban_duration_days = NULL,
                                ban_reason = NULL,
                                banned_by = NULL,
                                updated_at = NOW()
                            WHERE id = ?
                        ");
                        $unbanStoreStmt->execute([$store['id']]);
                    } else {
                        $unbanStoreStmt = $this->db->prepare("
                            UPDATE store_locations 
                            SET status = 'active',
                                updated_at = NOW()
                            WHERE id = ?
                        ");
                        $unbanStoreStmt->execute([$store['id']]);
                    }
                }
            }
            
            // Log super admin activity
            $storesUnbannedText = '';
            if (isset($bannedStoresCount) && $bannedStoresCount > 0) {
                $storesUnbannedText = " Also automatically unbanned {$bannedStoresCount} store(s) associated with this admin. Store status(es) set to 'active'.";
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO super_admin_activity 
                (super_admin_id, super_admin_name, action_type, target_admin_name, description) 
                VALUES (?, ?, 'admin_unbanned', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['control_panel_admin']['id'],
                $_SESSION['control_panel_admin']['fullname'],
                $admin['fullname'] ?? $adminEmail,
                "Unbanned admin account: {$adminEmail} (Admin: " . ($admin['fullname'] ?? 'Unknown') . "). Admin status set to 'active'.{$storesUnbannedText} Admin can now log in to the system."
            ]);
            
            $this->db->commit();
            
            $successMessage = 'Admin account unbanned successfully. Admin status set to \'active\'.';
            if (isset($bannedStoresCount) && $bannedStoresCount > 0) {
                $successMessage .= " Also automatically unbanned {$bannedStoresCount} store(s) associated with this admin. Store status(es) set to 'active'.";
            }
            $successMessage .= ' The admin can now log in to the system.';
            
            echo json_encode([
                'success' => true,
                'message' => $successMessage
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error unbanning admin: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to unban admin: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Check if ban tracking columns exist in a table
     */
    private function checkBanColumnsExist($table) {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as cnt 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = '$table' 
                AND COLUMN_NAME = 'banned_at'
            ");
            return $stmt->fetch()['cnt'] > 0;
        } catch (Exception $e) {
            error_log("Error checking ban columns: " . $e->getMessage());
            return false;
        }
    }
}
