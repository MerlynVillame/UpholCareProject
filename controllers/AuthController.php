<?php
/**
 * Authentication Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';

class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    /**
     * Show role selection page
     */
    public function roleSelection() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }
        
        $data = [
            'title' => 'Select Role - ' . APP_NAME
        ];
        
        $this->view('auth/role_selection', $data);
    }
    
    /**
     * Show login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }
        
        $data = [
            'title' => 'Login - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        // Clear flash messages
        unset($_SESSION['error']);
        unset($_SESSION['success']);
        
        $this->view('auth/login', $data);
    }
    
    /**
     * Process login
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('home#login');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please enter both email address and password';
            $this->redirect('home#login');
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address';
            $this->redirect('home#login');
        }
        
        // 1. Find user first (to check lock status and valid email)
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            $user = $this->userModel->findByUsername($email);
        }

        // Email exists validation 
        if (!$user) {
            LoginLogger::logFailure(null, 'unknown', $email, null, 'Invalid email address or password');
            $_SESSION['error'] = 'Invalid email address or password';
            $this->redirect('home#login');
            return;
        }

        // 2. Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $lockTimeRemaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            LoginLogger::logFailure($user['id'], $user['role'], $email, $user['fullname'] ?? 'User', "Account locked until {$user['locked_until']}");
            $_SESSION['error'] = "Your account is temporarily locked due to multiple failed login attempts. Please try again in {$lockTimeRemaining} minutes.";
            $this->redirect('home#login');
            return;
        }

        // 3. Verify password
        if (!password_verify($password, $user['password'])) {
            // Increment failed attempts
            $fails = ($user['failed_login_attempts'] ?? 0) + 1;
            $updates = ['failed_login_attempts' => $fails];
            
            $lockMsg = '';
            if ($fails >= 5) {
                $lockedUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $updates['locked_until'] = $lockedUntil;
                $lockMsg = ' Account locked for 15 minutes.';
            }
            
            $this->userModel->updateUser($user['id'], $updates);
            
            LoginLogger::logFailure($user['id'], $user['role'], $email, $user['fullname'] ?? 'User', 'Invalid password.' . $lockMsg);
            $_SESSION['error'] = 'Invalid email address or password.' . ($fails >= 3 ? " Attempt {$fails} of 5 before lockout." : "");
            $this->redirect('home#login');
            return;
        }

        // 4. Success - Reset failed attempts and continue
        $this->userModel->updateUser($user['id'], [
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        if ($user) {
            // PRIORITY CHECK 1: For admins, FIRST check if account status is 'inactive' (BANNED)
            // This is the PRIMARY check - if admin account is banned (status = 'inactive'), block login immediately
            if ($user['role'] === 'admin' && $user['status'] === 'inactive') {
                $_SESSION['error'] = 'Your account has been banned by the administrator. You cannot log in to the system. Please contact the administrator if you have any questions.';
                $this->redirect('home#login');
                return;
            }
            
            // PRIORITY CHECK 2: For admins, check if they have banned stores (even if account status is 'active')
            // This ensures admins with banned stores cannot log in
            if ($user['role'] === 'admin') {
                $db = Database::getInstance()->getConnection();
                $hasBanColumns = false;
                try {
                    $checkStmt = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'store_locations' 
                        AND COLUMN_NAME = 'banned_at'
                    ");
                    $hasBanColumns = $checkStmt->fetch()['cnt'] > 0;
                } catch (Exception $e) {
                    // Ignore error
                }
                
                // Check if admin has any banned stores
                if ($hasBanColumns) {
                    // First, try to find stores by matching email in admin_registrations
                    $bannedStoresStmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM store_locations sl
                        INNER JOIN admin_registrations ar ON (
                            ar.email = ?
                            AND (
                                (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                                 AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                                OR (LOWER(TRIM(sl.store_name)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                                    AND ar.business_name IS NOT NULL 
                                    AND ar.business_name != ''
                                    AND TRIM(ar.business_name) != '')
                                OR (LOWER(TRIM(ar.business_name)) LIKE LOWER(CONCAT('%', TRIM(sl.store_name), '%'))
                                    AND sl.store_name IS NOT NULL 
                                    AND sl.store_name != ''
                                    AND TRIM(sl.store_name) != '')
                            )
                        )
                        WHERE sl.status = 'inactive'
                            AND sl.banned_at IS NOT NULL
                            AND sl.banned_at != ''
                    ");
                    $bannedStoresStmt->execute([$user['email']]);
                    $bannedStoresCount = $bannedStoresStmt->fetch()['count'];
                    
                    // Also check by store name directly if we know the store name (like "kyle store")
                    if ($bannedStoresCount == 0) {
                        // Try to find by store name that might be linked to this admin
                        $storeNameStmt = $db->prepare("
                            SELECT COUNT(*) as count
                            FROM store_locations sl
                            WHERE sl.status = 'inactive'
                                AND sl.banned_at IS NOT NULL
                                AND sl.banned_at != ''
                                AND EXISTS (
                                    SELECT 1 FROM admin_registrations ar
                                    WHERE ar.email = ?
                                    AND (
                                        LOWER(TRIM(sl.store_name)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                                        OR LOWER(TRIM(ar.business_name)) LIKE LOWER(CONCAT('%', TRIM(sl.store_name), '%'))
                                    )
                                )
                        ");
                        $storeNameStmt->execute([$user['email']]);
                        $bannedStoresCount = $storeNameStmt->fetch()['count'];
                    }
                    
                    if ($bannedStoresCount > 0) {
                        // Admin has banned stores - BLOCK LOGIN
                        // Only block login, don't update status here (status should already be set when store was banned)
                        $_SESSION['error'] = 'Your account has been banned. Your store(s) have been banned by the administrator. You cannot log in to the system. Please contact the administrator if you have any questions.';
                        $this->redirect('home#login');
                        return;
                    }
                } else {
                    // Fallback: Check by store status even if ban columns don't exist
                    $bannedStoresStmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM store_locations sl
                        INNER JOIN admin_registrations ar ON (
                            ar.email = ?
                            AND (
                                (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                                 AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                                OR (LOWER(TRIM(sl.store_name)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                                    AND ar.business_name IS NOT NULL 
                                    AND ar.business_name != '')
                            )
                        )
                        WHERE sl.status = 'inactive'
                    ");
                    $bannedStoresStmt->execute([$user['email']]);
                    $bannedStoresCount = $bannedStoresStmt->fetch()['count'];
                    
                    if ($bannedStoresCount > 0) {
                        // Admin has inactive stores - block login
                        // Only block login, don't update status here
                        $_SESSION['error'] = 'Your account has been banned. Your store(s) have been banned by the administrator. You cannot log in to the system. Please contact the administrator if you have any questions.';
                        $this->redirect('home#login');
                        return;
                    }
                }
            }
            
            // Check if user is active or pending
            // NOTE: This check happens AFTER the admin-specific checks above
            // For admins, if status is 'inactive', they were already blocked in the admin-specific check
            // This check is for other cases (non-admin users or admin status that wasn't caught above)
            if ($user['status'] === 'inactive') {
                if ($user['role'] === 'admin') {
                    $_SESSION['error'] = 'Your account has been banned by the administrator. You cannot log in to the system. Please contact the administrator if you have any questions.';
                } else {
                    $_SESSION['error'] = 'Your account has been deactivated. Please contact administrator.';
                }
                $this->redirect('home#login');
            }
            
            // Check if account is pending verification - BLOCK LOGIN until code is verified
            if ($user['status'] === 'pending_verification') {
                // Check if verification code has been sent and verified
                $db = Database::getInstance()->getConnection();
                
                if ($user['role'] === 'admin') {
                    // For admins, check admin_registrations table
                    $stmt = $db->prepare("SELECT verification_code_sent_at, verification_code_verified_at FROM admin_registrations WHERE email = ?");
                    $stmt->execute([$user['email']]);
                    $reg = $stmt->fetch();
                    
                    if ($reg && $reg['verification_code_sent_at'] && !$reg['verification_code_verified_at']) {
                        $_SESSION['error'] = 'Please verify your email code before logging in. A verification code was automatically sent to your email when you registered. <a href="' . BASE_URL . 'auth/verifyCode?email=' . urlencode($user['email']) . '&role=admin">Click here to enter verification code</a>';
                    } else if ($reg && !$reg['verification_code_sent_at']) {
                        $_SESSION['error'] = 'Your admin account is pending verification. A verification code should have been sent to your email. Please check your inbox or contact support.';
                    } else {
                        $_SESSION['error'] = 'Your admin account is pending verification. Please verify your email code before logging in.';
                    }
                    $this->redirect('auth/login?tab=admin');
                } else if ($user['role'] === 'customer') {
                    // For customers, check users table
                    $stmt = $db->prepare("SELECT verification_code_sent_at, verification_code_verified_at FROM users WHERE email = ? AND role = 'customer'");
                    $stmt->execute([$user['email']]);
                    $customerUser = $stmt->fetch();
                    
                    if ($customerUser && $customerUser['verification_code_sent_at'] && !$customerUser['verification_code_verified_at']) {
                        $_SESSION['error'] = 'Please verify your email code before logging in. A verification code was automatically sent to your email when you registered. <a href="' . BASE_URL . 'auth/verifyCode?email=' . urlencode($user['email']) . '&role=customer">Click here to enter verification code</a>';
                    } else if ($customerUser && !$customerUser['verification_code_sent_at']) {
                        $_SESSION['error'] = 'Your customer account is pending verification. A verification code should have been sent to your email. Please check your inbox or contact support.';
                    } else {
                        $_SESSION['error'] = 'Your customer account is pending verification. Please verify your email code before logging in.';
                    }
                    $this->redirect('auth/login?tab=customer');
                }
            }
            
            // Check if admin account is pending approval
            if ($user['status'] === 'pending' && $user['role'] === 'admin') {
                $_SESSION['error'] = 'Your admin account is pending approval by the super admin. You will be notified once your account is activated.';
                $this->redirect('home#login');
            }
            
            // Only allow active accounts to login
            if ($user['status'] !== 'active') {
                $_SESSION['error'] = 'Your account is not yet active. Please contact administrator.';
                $this->redirect('home#login');
            }
            
            // SECURITY: Verify role integrity
            // Ensure the role in database is valid
            if (!in_array($user['role'], [ROLE_ADMIN, ROLE_CUSTOMER, ROLE_CONTROL_PANEL_ADMIN])) {
                error_log("SECURITY ALERT: User {$user['username']} has invalid role: {$user['role']}");
                $_SESSION['error'] = 'Account error detected. Please contact administrator.';
                $this->redirect('home#login');
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'] ?? $user['email']; // Fallback to email if username doesn't exist
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['fullname'] ?? $user['full_name'] ?? 'User';
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $this->userModel->updateLastLogin($user['id']);
            
            // Log successful login to system audit trail
            LoginLogger::logSuccess($user['id'], $user['role'], $user['email'], $user['fullname'] ?? $user['full_name'] ?? 'User');
            
            // Log successful login with role
            error_log("SUCCESS: User {$user['email']} ({$user['role']}) logged in successfully");
            
            // Redirect to dashboard
            $this->redirectToDashboard();
        } else {
            // Log failed login attempt
            LoginLogger::logFailure(null, 'unknown', $email, null, 'Invalid email address or password');
            
            $_SESSION['error'] = 'Invalid email address or password';
            $this->redirect('home#login');
        }
    }
    
    /**
     * Show registration page (legacy - redirects to customer registration)
     */
    public function register() {
        $this->redirect('auth/registerCustomer');
    }
    
    /**
     * Show admin registration page
     */
    public function registerAdmin() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }
        
        // Get preserved form data if it exists (from validation errors)
        $formData = $_SESSION['registration_form_data'] ?? [];
        $fieldErrors = $_SESSION['registration_field_errors'] ?? [];
        
        $data = [
            'title' => 'Admin Registration - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
            'form_data' => $formData,
            'field_errors' => $fieldErrors
        ];
        
        // Get error/success messages before clearing
        $errorMessage = $_SESSION['error'] ?? null;
        $successMessage = $_SESSION['success'] ?? null;
        
        // Clear flash messages
        unset($_SESSION['error']);
        unset($_SESSION['success']);
        
        // Update data with messages (form_data and field_errors are already in $data)
        $data['error'] = $errorMessage;
        $data['success'] = $successMessage;
        
        // If there's a success message, clear form data (registration was successful)
        if ($successMessage) {
            unset($_SESSION['registration_form_data']);
            unset($_SESSION['registration_field_errors']);
            $data['form_data'] = [];
            $data['field_errors'] = [];
        }
        
        $this->view('auth/register_admin', $data);
    }
    
    /**
     * Show customer registration page
     */
    public function registerCustomer() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }
        
        $data = [
            'title' => 'Customer Registration - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        // Clear flash messages
        unset($_SESSION['error']);
        unset($_SESSION['success']);
        
        $this->view('auth/register_customer', $data);
    }
    
    /**
     * Process admin registration
     */
    public function processRegisterAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/registerAdmin');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $employeeId = trim($_POST['employee_id'] ?? '');
        $agreeTerms = isset($_POST['agree_terms']) ? true : true; // Default to true if not in post but required by design
        
        // Business Information
        $businessName = trim($_POST['business_name'] ?? '');
        $businessAddress = trim($_POST['business_address'] ?? '');
        $businessCity = trim($_POST['business_city'] ?? 'Bohol');
        $businessProvince = trim($_POST['business_province'] ?? 'Bohol');
        
        $role = 'admin'; // Always admin for this method
        
        // Validation
        $errors = [];
        
        // Personal Information Validation
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required';
        }
        
        // Phone validation
        if (!empty($phone)) {
            if (!preg_match('/^[0-9]{11}$/', $phone)) {
                $errors[] = 'Phone number must be exactly 11 digits';
            }
        }
        
        // Employee ID validation
        if (empty($employeeId)) {
            $errors[] = 'Employee ID / Admin ID is required';
        }
        
        // Business Information Validation
        if (empty($businessName)) {
            $errors[] = 'Business name is required';
        }
        
        if (empty($businessAddress)) {
            $errors[] = 'Business address is required';
        }
        
        if (empty($businessCity)) {
            $errors[] = 'Business city is required';
        }
        
        if (empty($businessProvince)) {
            $errors[] = 'Business province is required';
        }
        
        // Business Permit File Upload Validation
        $businessPermitPath = null;
        $businessPermitFilename = null;
        
        if (!isset($_FILES['business_permit']) || $_FILES['business_permit']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Business permit (PDF) is required';
        } else {
            $permitFile = $_FILES['business_permit'];
            
            // Check file type
            $fileExtension = strtolower(pathinfo($permitFile['name'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'pdf') {
                $errors[] = 'Business permit must be a PDF file';
            }
            
            // Check file size (5MB max)
            if ($permitFile['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Business permit file size must not exceed 5MB';
            }
            
            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $permitFile['tmp_name']);
            finfo_close($finfo);
            
            if ($mimeType !== 'application/pdf') {
                $errors[] = 'Invalid file type. Only PDF files are allowed.';
            }
        }
        
        if (!$agreeTerms) {
            $errors[] = 'You must agree to the terms and conditions';
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Email already exists';
        }
        
        // Store form data in session to preserve it on error (do this before redirect)
        $_SESSION['registration_form_data'] = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'employee_id' => $employeeId,
            'business_name' => $businessName,
            'business_address' => $businessAddress,
            'business_city' => $businessCity,
            'business_province' => $businessProvince,
            'agree_terms' => $agreeTerms,
            'business_permit_filename' => isset($_FILES['business_permit']) && $_FILES['business_permit']['error'] === UPLOAD_ERR_OK ? $_FILES['business_permit']['name'] : (isset($_SESSION['registration_form_data']['business_permit_filename']) ? $_SESSION['registration_form_data']['business_permit_filename'] : null)
        ];
        
        if (!empty($errors)) {
            // Store field-specific errors
            $_SESSION['registration_field_errors'] = $errors;
            
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('home#signup');
        }
        
        // Note: Don't clear form data here - we'll clear it only after successful registration
        // This ensures if there's a database error, the form data is still preserved
        
        // Handle business permit file upload
        if (isset($_FILES['business_permit']) && $_FILES['business_permit']['error'] === UPLOAD_ERR_OK) {
            $permitFile = $_FILES['business_permit'];
            $uploadDir = ROOT . DS . 'assets' . DS . 'uploads' . DS . 'business_permits' . DS;
            
            // Create upload directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $fileExtension = strtolower(pathinfo($permitFile['name'], PATHINFO_EXTENSION));
            $businessPermitFilename = $permitFile['name'];
            $uniqueFilename = 'permit_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $uniqueFilename;
            
            // Move uploaded file
            if (!move_uploaded_file($permitFile['tmp_name'], $uploadPath)) {
                $_SESSION['error'] = 'Failed to upload business permit. Please try again.';
                $this->redirect('home#signup');
            }
            
            // Store relative path for database
            $businessPermitPath = 'assets/uploads/business_permits/' . $uniqueFilename;
        }
        
        // Handle admin valid ID image upload
        $validIdPath = null;
        $validIdFilename = null;
        
        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
            $idFile = $_FILES['valid_id'];
            $allowedIdMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            
            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $idMime = finfo_file($finfo, $idFile['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($idMime, $allowedIdMimes)) {
                $_SESSION['error'] = 'Valid ID must be an image file (JPG, PNG, WEBP, or GIF).';
                $this->redirect('home#signup');
            }
            
            if ($idFile['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = 'Valid ID image must not exceed 5MB.';
                $this->redirect('home#signup');
            }
            
            $idUploadDir = ROOT . DS . 'assets' . DS . 'uploads' . DS . 'admin_ids' . DS;
            if (!file_exists($idUploadDir)) {
                mkdir($idUploadDir, 0755, true);
            }
            
            $idExt = strtolower(pathinfo($idFile['name'], PATHINFO_EXTENSION));
            $validIdFilename = $idFile['name'];
            $idUniqueFilename = 'admin_id_' . time() . '_' . uniqid() . '.' . $idExt;
            
            if (!move_uploaded_file($idFile['tmp_name'], $idUploadDir . $idUniqueFilename)) {
                $_SESSION['error'] = 'Failed to upload Valid ID. Please try again.';
                $this->redirect('home#signup');
            }
            
            $validIdPath = 'assets/uploads/admin_ids/' . $idUniqueFilename;
        }
        
        // Generate username from email (use email prefix as username)
        $usernameFromEmail = explode('@', $email)[0];
        // Ensure username is unique
        $username = $usernameFromEmail;
        $counter = 1;
        while ($this->userModel->usernameExists($username)) {
            $username = $usernameFromEmail . $counter;
            $counter++;
        }
        
        // Register user as admin with pending status (waiting for super admin approval)
        $userData = [
            'username' => $username, // Auto-generated from email
            'email' => $email,
            'password' => $password,
            'fullname' => $fullName,
            'phone' => $phone,
            'role' => 'admin',
            'status' => 'pending' // Set to pending - waiting for super admin to review and approve
        ];
        
        $userId = $this->userModel->register($userData);
        
        if ($userId) {
            // Get database connection
            $db = Database::getInstance()->getConnection();
            
            try {
                // Create record in admin_registrations table immediately so super admin can see it
                // This makes the account visible in super admin dashboard as "pending_verification"
                $user = $this->userModel->findById($userId);
                
                if (!$user) {
                    throw new Exception("User not found after registration");
                }
                
                // Check if record already exists (prevent duplicates)
                $checkStmt = $db->prepare("SELECT id FROM admin_registrations WHERE email = ? AND registration_status IN ('pending_verification', 'pending')");
                $checkStmt->execute([$user['email']]);
                $existing = $checkStmt->fetch();
                
                if (!$existing) {
                    // Geocode address to get coordinates (optional - can be done later during approval)
                    $latitude = null;
                    $longitude = null;
                    
                    // Try to geocode the address (using a simple approach - can be enhanced with Google Geocoding API)
                    // For now, we'll leave it null and let super admin set it during approval
                    
                    // Insert new registration record with status 'pending_verification'
                    // Super admin will send verification code via email
                    // Insert admin registration with 'pending' status
                    // Status will be 'pending' until super admin accepts it
                    $stmt = $db->prepare("
                        INSERT INTO admin_registrations 
                        (email, username, password, fullname, phone, employee_id, business_name, business_address, business_city, business_province, business_latitude, business_longitude, business_permit_path, business_permit_filename, valid_id_path, valid_id_filename, registration_status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                    ");
                    
                    try {
                        $stmt->execute([
                            $user['email'],
                            $user['username'],
                            $user['password'], // Already hashed
                            $user['fullname'],
                            $user['phone'] ?? '',
                            $employeeId,
                            $businessName,
                            $businessAddress,
                            $businessCity,
                            $businessProvince,
                            $latitude,
                            $longitude,
                            $businessPermitPath,
                            $businessPermitFilename,
                            $validIdPath,
                            $validIdFilename
                        ]);
                    } catch (PDOException $insertError) {
                        // If business fields don't exist, try without them (backward compatibility)
                        if (strpos($insertError->getMessage(), 'business_name') !== false || 
                            strpos($insertError->getMessage(), 'business_address') !== false) {
                            error_log("Warning: Business fields not found in admin_registrations table. Please run migration: database/add_business_fields_to_admin_registrations.sql");
                            
                            // Try insert without business fields
                            $stmt = $db->prepare("
                                INSERT INTO admin_registrations 
                                (email, username, password, fullname, phone, registration_status, created_at) 
                                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
                            ");
                            $stmt->execute([
                                $user['email'],
                                $user['username'],
                                $user['password'],
                                $user['fullname'],
                                $user['phone'] ?? ''
                            ]);
                            
                            $_SESSION['error'] = 'Business information could not be saved. Please contact administrator to run database migration.';
                        } else {
                            throw $insertError; // Re-throw if it's a different error
                        }
                    }
                    
                    // Verify the record was created
                    $verifyStmt = $db->prepare("SELECT id FROM admin_registrations WHERE email = ?");
                    $verifyStmt->execute([$user['email']]);
                    $created = $verifyStmt->fetch();
                    
                    if (!$created) {
                        throw new Exception("Failed to create admin_registrations record - record not found after insert");
                    }
                    
                    // Get the registration ID
                    $registrationId = $created['id'];
                    
                    // Registration is now pending super admin approval
                    // Super admin will review documents and approve/reject
                    // When approved, super admin will send verification code via email
                    error_log("INFO: Admin registration submitted - Email: {$email}, Registration ID: {$registrationId}. Status: pending (waiting for super admin approval).");
                    
                    // Clear form data since registration was successful
                    unset($_SESSION['registration_form_data']);
                    unset($_SESSION['registration_field_errors']);
                    
                    $_SESSION['success'] = "✅ Registration submitted successfully! Your account is now pending review by the super admin. The super admin will verify your documents and information. You will receive an email notification once your account is approved or rejected. Your email: <strong>{$email}</strong>";
                    $_SESSION['registration_email'] = $email;
                    
                    // Redirect to login page with message
                    $this->redirect('auth/login?tab=admin');
                } else {
                    // Registration already exists - check if verification is needed
                    // If already registered, redirect to verification page instead of login
                    $checkRegStmt = $db->prepare("SELECT id, registration_status, verification_code_verified_at FROM admin_registrations WHERE email = ? ORDER BY created_at DESC LIMIT 1");
                    $checkRegStmt->execute([$user['email']]);
                    $existingReg = $checkRegStmt->fetch();
                    
                    if ($existingReg && $existingReg['registration_status'] === 'pending_verification' && !$existingReg['verification_code_verified_at']) {
                        // Registration exists but not verified - redirect to verification page
                        $_SESSION['error'] = 'You have already registered. Please verify your email code to complete registration.';
                        error_log("INFO: Registration exists but not verified - redirecting to verification page: auth/verifyCode?email=" . urlencode($user['email']));
                        $this->redirect('auth/verifyCode?email=' . urlencode($user['email']));
                        return; // Ensure we don't continue after redirect
                    } else {
                        // Registration exists and verified or already processed - redirect to login
                        $_SESSION['error'] = 'You have already registered. Please login with your credentials.';
                        error_log("INFO: Registration exists and verified - redirecting to login: auth/login?tab=admin");
                        $this->redirect('auth/login?tab=admin');
                        return; // Ensure we don't continue after redirect
                    }
                }
                
            } catch (PDOException $e) {
                // Log the detailed error for debugging
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                error_log("PDO Error creating admin_registrations record: Code $errorCode - $errorMessage");
                error_log("SQL State: " . $e->getCode());
                
                // If admin_registrations insert fails, delete the user account
                try {
                    $this->userModel->delete($userId);
                } catch (Exception $deleteError) {
                    error_log("Error deleting user account: " . $deleteError->getMessage());
                }
                
                // Check if it's an ENUM value error
                if (strpos($errorMessage, 'pending_verification') !== false || strpos($errorMessage, 'ENUM') !== false) {
                    $_SESSION['error'] = 'Registration failed: Database schema needs to be updated. Please run the migration script: database/add_verification_code_to_admin_registrations.sql';
            } else {
                    $_SESSION['error'] = 'Registration failed: Database error - ' . $errorMessage;
                }
                $this->redirect('auth/registerAdmin');
            } catch (Exception $e) {
                // Log the error for debugging
                error_log("Error creating admin_registrations record: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                // If admin_registrations insert fails, delete the user account
                try {
                $this->userModel->delete($userId);
                } catch (Exception $deleteError) {
                    error_log("Error deleting user account: " . $deleteError->getMessage());
                }
                $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
                $this->redirect('auth/registerAdmin');
            }
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $this->redirect('auth/registerAdmin');
        }
    }
    
    /**
     * Show admin key verification page
     */
    public function verifyAdminKey() {
        // Check if there's a pending admin verification
        if (!isset($_SESSION['pending_admin_verification'])) {
            $_SESSION['error'] = 'No pending admin registration found. Please register first.';
            $this->redirect('auth/registerAdmin');
        }
        
        $data = [
            'title' => 'Admin Key Verification - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
            'email' => $_SESSION['pending_admin_email'] ?? ''
        ];
        
        // Clear flash messages
        unset($_SESSION['error']);
        unset($_SESSION['success']);
        
        $this->view('auth/verify_admin_key', $data);
    }
    
    /**
     * Process admin key verification
     */
    public function processVerifyAdminKey() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/verifyAdminKey');
        }
        
        // Check if there's a pending admin verification
        if (!isset($_SESSION['pending_admin_verification'])) {
            $_SESSION['error'] = 'No pending admin registration found. Please register first.';
            $this->redirect('auth/registerAdmin');
        }
        
        $adminKey = trim($_POST['admin_key'] ?? '');
        $userId = $_SESSION['pending_admin_verification'];
        
        if (empty($adminKey)) {
            $_SESSION['error'] = 'Admin verification key is required';
            $this->redirect('auth/verifyAdminKey');
        }
        
        // Verify the admin key
        if ($adminKey !== ADMIN_REGISTRATION_KEY) {
            $_SESSION['error'] = 'Invalid admin verification key. Please try again.';
            // Log failed verification attempt
            error_log("SECURITY ALERT: Failed admin key verification attempt for user ID: $userId");
            $this->redirect('auth/verifyAdminKey');
        }
        
        // Key is valid - update user status to pending (waiting for super admin approval)
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found. Please register again.';
            unset($_SESSION['pending_admin_verification']);
            unset($_SESSION['pending_admin_email']);
            $this->redirect('auth/registerAdmin');
        }
        
        // Key is valid - update user status to pending (waiting for super admin approval)
        // The admin_registrations record was already created during registration
        // So super admin can already see it in the dashboard
        try {
            // Update status to pending (super admin approval still needed)
            $this->userModel->updateUser($userId, ['status' => 'pending']);
            
            // Update the admin_registrations record to mark key as verified
            // (The record already exists from registration, so we just update it if needed)
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                UPDATE admin_registrations 
                SET updated_at = NOW() 
                WHERE email = ? AND registration_status = 'pending'
            ");
            $stmt->execute([$user['email']]);
            
            // Clear pending verification session
            unset($_SESSION['pending_admin_verification']);
            unset($_SESSION['pending_admin_email']);
            
            $_SESSION['success'] = "Admin key verified successfully! Your account is now pending approval by the super admin. You will be notified once your account is activated.";
            $this->redirect('auth/login?tab=admin');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to complete verification: ' . $e->getMessage();
            $this->redirect('auth/verifyAdminKey');
        }
    }
    
    /**
     * Show verification code page (for email verification)
     */
    public function verifyCode() {
        $email = $_GET['email'] ?? '';
        $role = $_GET['role'] ?? 'admin'; // Default to admin, but can be 'customer'
        
        if (empty($email)) {
            $_SESSION['error'] = 'Email address is required for verification.';
            $redirectTab = $role === 'customer' ? 'customer' : 'admin';
            $this->redirect('auth/login?tab=' . $redirectTab);
        }
        
        // Fetch verification code from database
        $verificationCode = null;
        $codeSentAt = null;
        $hasCode = false;
        
        try {
            $db = Database::getInstance()->getConnection();
            
            if ($role === 'customer') {
                // For customers, check users table
                $stmt = $db->prepare("
                    SELECT 
                        verification_code, 
                        verification_code_sent_at,
                        status,
                        role
                    FROM users
                    WHERE email = ? 
                    AND role = 'customer'
                    AND verification_code IS NOT NULL
                    AND verification_code != ''
                    ORDER BY verification_code_sent_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && !empty($user['verification_code'])) {
                    // Don't pass the actual code to the view for security - code is only in email
                    $codeSentAt = $user['verification_code_sent_at'];
                    $hasCode = true;
                    error_log("INFO: Verification code exists for customer {$email}, Status: {$user['status']} (code not displayed on page for security)");
                }
            } else {
                // For admins, check admin_registrations table
                // First, try to get code from admin_registrations table
                $stmt = $db->prepare("
                    SELECT 
                        ar.verification_code, 
                        ar.verification_code_sent_at,
                        ar.registration_status,
                        ar.approved_at
                    FROM admin_registrations ar
                    WHERE ar.email = ? 
                    AND ar.verification_code IS NOT NULL
                    AND ar.verification_code != ''
                    ORDER BY 
                        CASE 
                            WHEN ar.registration_status = 'pending_verification' THEN 1
                            WHEN ar.registration_status = 'pending' THEN 2
                            WHEN ar.registration_status = 'approved' THEN 3
                            ELSE 4
                        END,
                        ar.verification_code_sent_at DESC, 
                        ar.id DESC
                    LIMIT 1
                ");
                $stmt->execute([$email]);
                $registration = $stmt->fetch();
                
                if ($registration && !empty($registration['verification_code'])) {
                    // Don't pass the actual code to the view for security - code is only in email
                    $codeSentAt = $registration['verification_code_sent_at'];
                    $hasCode = true;
                    error_log("INFO: Verification code exists for admin {$email}, Status: {$registration['registration_status']} (code not displayed on page for security)");
                } else {
                    // Also check if there's a code in admin_verification_codes table linked to this email
                    try {
                        $stmt2 = $db->prepare("
                            SELECT 
                                avc.verification_code,
                                avc.assigned_at as verification_code_sent_at
                            FROM admin_verification_codes avc
                            INNER JOIN admin_registrations ar ON avc.admin_registration_id = ar.id
                            WHERE ar.email = ?
                            AND avc.status IN ('used', 'reserved')
                            AND avc.verification_code IS NOT NULL
                            ORDER BY avc.assigned_at DESC
                            LIMIT 1
                        ");
                        $stmt2->execute([$email]);
                        $codeRecord = $stmt2->fetch();
                        
                        if ($codeRecord && !empty($codeRecord['verification_code'])) {
                            // Don't pass the actual code to the view for security - code is only in email
                            $codeSentAt = $codeRecord['verification_code_sent_at'];
                            $hasCode = true;
                            error_log("INFO: Found verification code from admin_verification_codes table for {$email} (code not displayed on page for security)");
                        }
                    } catch (Exception $e2) {
                        error_log("Warning: Could not check admin_verification_codes table: " . $e2->getMessage());
                    }
                }
            }
            
            if (!$hasCode) {
                error_log("INFO: No verification code found for {$email} (role: {$role})");
            }
        } catch (Exception $e) {
            error_log("Error fetching verification code: " . $e->getMessage());
        }
        
        $data = [
            'title' => 'Verify Your Code - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
            'email' => $email,
            'role' => $role, // Pass role to view
            // Don't pass verification_code to view for security - code is only in email
            'code_sent_at' => $codeSentAt,
            'has_code' => $hasCode
        ];
        
        // Clear flash messages
        unset($_SESSION['error']);
        unset($_SESSION['success']);
        
        $this->view('auth/verify_code', $data);
    }
    
    /**
     * Resend verification code
     */
    public function resendVerificationCode() {
        $email = $_GET['email'] ?? $_POST['email'] ?? '';
        $role = $_GET['role'] ?? $_POST['role'] ?? 'admin'; // Get role from GET or POST
        
        if (empty($email)) {
            $_SESSION['error'] = 'Email address is required to resend verification code.';
            $redirectTab = $role === 'customer' ? 'customer' : 'admin';
            $this->redirect('auth/login?tab=' . $redirectTab);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            if ($role === 'customer') {
                // Handle customer resend
                // Get user record - check if they need verification (status is pending_verification OR verification_code_verified_at is NULL)
                $stmt = $db->prepare("
                    SELECT * FROM users
                    WHERE email = ?
                    AND role = 'customer'
                    AND (
                        status = 'pending_verification' 
                        OR verification_code_verified_at IS NULL
                        OR status = ''
                        OR status IS NULL
                    )
                    ORDER BY id DESC
                    LIMIT 1
                ");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    // Check if user exists but is already verified
                    $checkStmt = $db->prepare("
                        SELECT * FROM users
                        WHERE email = ?
                        AND role = 'customer'
                        LIMIT 1
                    ");
                    $checkStmt->execute([$email]);
                    $existingUser = $checkStmt->fetch();
                    
                    if ($existingUser) {
                        if ($existingUser['verification_code_verified_at']) {
                            $_SESSION['error'] = 'This account has already been verified. Please login.';
                            $this->redirect('auth/login?tab=customer');
                        } else if ($existingUser['status'] === 'active') {
                            $_SESSION['error'] = 'This account is already active. Please login.';
                            $this->redirect('auth/login?tab=customer');
                        } else {
                            // User exists but status might be different - allow resend anyway
                            $user = $existingUser;
                        }
                    } else {
                        $_SESSION['error'] = 'No customer account found for this email address. Please register first.';
                        $this->redirect('auth/registerCustomer');
                        return;
                    }
                }
                
                // If we still don't have a user, show error
                if (!$user) {
                    $_SESSION['error'] = 'No pending verification found for this email address.';
                    $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                    return;
                }
                
                // Check rate limiting (5 minutes between resends)
                $lastResend = $user['verification_code_sent_at'] ?? null;
                if ($lastResend) {
                    $lastResendTime = strtotime($lastResend);
                    $timeSinceLastResend = (time() - $lastResendTime) / 60; // minutes
                    
                    if ($timeSinceLastResend < 5) {
                        $waitTime = ceil(5 - $timeSinceLastResend);
                        $_SESSION['error'] = "Please wait {$waitTime} minute(s) before requesting another verification code.";
                        $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                        return;
                    }
                }
                
                // Generate new 4-digit verification code
                $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                
                // Update user with new verification code and set status to pending_verification
                $updateStmt = $db->prepare("
                    UPDATE users 
                    SET verification_code = ?,
                        verification_code_sent_at = NOW(),
                        verification_attempts = 0,
                        status = 'pending_verification'
                    WHERE id = ?
                ");
                $updateStmt->execute([$verificationCode, $user['id']]);
                
                // Get customer name
                $fullName = $user['fullname'] ?? 'Customer';
                
            } else {
                // Handle admin resend (existing code)
                // Get registration record
                $stmt = $db->prepare("
                    SELECT ar.*, u.fullname 
                    FROM admin_registrations ar
                    LEFT JOIN users u ON u.email = ar.email AND u.role = 'admin'
                    WHERE ar.email = ?
                    AND ar.registration_status = 'pending_verification'
                    ORDER BY ar.id DESC
                    LIMIT 1
                ");
                $stmt->execute([$email]);
                $registration = $stmt->fetch();
                
                if (!$registration) {
                    $_SESSION['error'] = 'No pending verification found for this email address.';
                    $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=admin');
                    return;
                }
                
                // Check rate limiting (5 minutes between resends)
                $lastResend = $registration['verification_code_sent_at'] ?? null;
                if ($lastResend) {
                    $lastResendTime = strtotime($lastResend);
                    $timeSinceLastResend = (time() - $lastResendTime) / 60; // minutes
                    
                    if ($timeSinceLastResend < 5) {
                        $waitTime = ceil(5 - $timeSinceLastResend);
                        $_SESSION['error'] = "Please wait {$waitTime} minute(s) before requesting another verification code.";
                        $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=admin');
                        return;
                    }
                }
                
                // Generate new 4-digit verification code
                $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                
                // Update registration with new verification code
                $updateStmt = $db->prepare("
                    UPDATE admin_registrations 
                    SET verification_code = ?,
                        verification_code_sent_at = NOW(),
                        verification_attempts = 0,
                        registration_status = 'pending_verification'
                    WHERE id = ?
                ");
                $updateStmt->execute([$verificationCode, $registration['id']]);
                
                // Get admin name
                $fullName = $registration['fullname'] ?? 'Admin';
            }
            
            // Send verification code via email using PHPMailer
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            error_log("INFO: Resending verification code to {$email} (role: {$role})");
            $emailSent = $notificationService->sendAdminVerificationCode(
                $email,
                $fullName,
                $verificationCode
            );
            
            if ($emailSent) {
                $_SESSION['success'] = "✅ A new verification code has been sent to your email address (<strong>{$email}</strong>). Please check your inbox and enter the code to verify your account.";
                error_log("INFO: Verification code resent successfully to {$email} (role: {$role})");
            } else {
                $_SESSION['error'] = "❌ Failed to send verification code. Please check your email configuration or contact the administrator.";
                error_log("WARNING: Failed to resend verification code to {$email} (role: {$role})");
            }
            
            $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=' . $role);
            
        } catch (Exception $e) {
            error_log("ERROR: Failed to resend verification code: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to resend verification code. Please try again later.';
            $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=' . $role);
        }
    }
    
    /**
     * Process verification code
     */
    public function processVerifyCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }
        
        $email = trim($_POST['email'] ?? '');
        $verificationCode = trim($_POST['verification_code'] ?? '');
        $role = $_POST['role'] ?? $_GET['role'] ?? 'admin'; // Get role from POST or GET
        
        if (empty($email) || empty($verificationCode)) {
            $_SESSION['error'] = 'Email and verification code are required';
            $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=' . $role);
        }
        
        // Normalize verification code (remove spaces, ensure 4 digits)
        $verificationCode = preg_replace('/\s+/', '', $verificationCode); // Remove all spaces
        $verificationCode = str_pad($verificationCode, 4, '0', STR_PAD_LEFT); // Ensure 4 digits
        
        try {
            $db = Database::getInstance()->getConnection();
            $isCustomer = false;
            $registration = null;
            $user = null;
            
            if ($role === 'customer') {
                // Handle customer verification
                // Get user with verification code (case-insensitive comparison)
                // Don't require status = 'pending_verification' - check if verification_code_verified_at is NULL instead
                $stmt = $db->prepare("
                    SELECT * FROM users 
                    WHERE email = ? 
                    AND role = 'customer'
                    AND LOWER(TRIM(verification_code)) = LOWER(?)
                    AND (status = 'pending_verification' OR status = '' OR status IS NULL)
                    AND verification_code_verified_at IS NULL
                ");
                $stmt->execute([$email, $verificationCode]);
                $user = $stmt->fetch();
                
                // If not found, try without case sensitivity and trimming
                if (!$user) {
                    $stmt2 = $db->prepare("
                        SELECT * FROM users 
                        WHERE email = ? 
                        AND role = 'customer'
                        AND verification_code = ?
                        AND (status = 'pending_verification' OR status = '' OR status IS NULL)
                        AND verification_code_verified_at IS NULL
                    ");
                    $stmt2->execute([$email, $verificationCode]);
                    $user = $stmt2->fetch();
                }
                
                // If still not found, try with trimmed code from database
                if (!$user) {
                    $stmt3 = $db->prepare("
                        SELECT * FROM users 
                        WHERE email = ? 
                        AND role = 'customer'
                        AND TRIM(verification_code) = ?
                        AND (status = 'pending_verification' OR status = '' OR status IS NULL)
                        AND verification_code_verified_at IS NULL
                    ");
                    $stmt3->execute([$email, $verificationCode]);
                    $user = $stmt3->fetch();
                }
                
                // If still not found, try without status check at all (just check if not verified)
                if (!$user) {
                    $stmt4 = $db->prepare("
                        SELECT * FROM users 
                        WHERE email = ? 
                        AND role = 'customer'
                        AND TRIM(verification_code) = ?
                        AND verification_code_verified_at IS NULL
                    ");
                    $stmt4->execute([$email, $verificationCode]);
                    $user = $stmt4->fetch();
                }
                
                if (!$user) {
                    // Get current user to check code
                    $checkStmt = $db->prepare("
                        SELECT verification_code, verification_attempts, verification_code_sent_at 
                        FROM users 
                        WHERE email = ? 
                        AND role = 'customer'
                        AND status = 'pending_verification'
                    ");
                    $checkStmt->execute([$email]);
                    $currentUser = $checkStmt->fetch();
                    
                    // Increment verification attempts
                    $updateStmt = $db->prepare("
                        UPDATE users 
                        SET verification_attempts = COALESCE(verification_attempts, 0) + 1 
                        WHERE email = ? AND role = 'customer'
                    ");
                    $updateStmt->execute([$email]);
                    
                    // Log the attempt for debugging
                    if ($currentUser) {
                        error_log("CUSTOMER VERIFICATION FAILED: Email: {$email}, Entered code: {$verificationCode}, Stored code: " . ($currentUser['verification_code'] ?? 'NULL') . ", Attempts: " . ($currentUser['verification_attempts'] ?? 0));
                    } else {
                        error_log("CUSTOMER VERIFICATION FAILED: No user found for email: {$email}");
                    }
                    
                    $_SESSION['error'] = 'Invalid verification code. Please check your email and try again. Make sure you enter the exact 4-digit code from your email.';
                    $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                    return;
                }
                
                $isCustomer = true;
                // Use user as registration for consistency
                $registration = $user;
                
            } else {
                // Handle admin verification (existing code)
                // Get registration with verification code (case-insensitive comparison)
                $stmt = $db->prepare("
                    SELECT * FROM admin_registrations 
                    WHERE email = ? 
                    AND LOWER(TRIM(verification_code)) = LOWER(?)
                    AND registration_status = 'pending_verification'
                ");
                $stmt->execute([$email, $verificationCode]);
                $registration = $stmt->fetch();
                
                // If not found, try without case sensitivity and trimming
                if (!$registration) {
                    $stmt2 = $db->prepare("
                        SELECT * FROM admin_registrations 
                        WHERE email = ? 
                        AND verification_code = ?
                        AND registration_status = 'pending_verification'
                    ");
                    $stmt2->execute([$email, $verificationCode]);
                    $registration = $stmt2->fetch();
                }
                
                // If still not found, try with trimmed code from database
                if (!$registration) {
                    $stmt3 = $db->prepare("
                        SELECT * FROM admin_registrations 
                        WHERE email = ? 
                        AND TRIM(verification_code) = ?
                        AND registration_status = 'pending_verification'
                    ");
                    $stmt3->execute([$email, $verificationCode]);
                    $registration = $stmt3->fetch();
                }
                
                if (!$registration) {
                    // Get current registration to check code
                    $checkStmt = $db->prepare("
                        SELECT verification_code, verification_attempts, verification_code_sent_at 
                        FROM admin_registrations 
                        WHERE email = ? 
                        AND registration_status = 'pending_verification'
                    ");
                    $checkStmt->execute([$email]);
                    $currentReg = $checkStmt->fetch();
                    
                    // Increment verification attempts
                    $updateStmt = $db->prepare("
                        UPDATE admin_registrations 
                        SET verification_attempts = COALESCE(verification_attempts, 0) + 1 
                        WHERE email = ?
                    ");
                    $updateStmt->execute([$email]);
                    
                    // Log the attempt for debugging
                    if ($currentReg) {
                        error_log("ADMIN VERIFICATION FAILED: Email: {$email}, Entered code: {$verificationCode}, Stored code: " . ($currentReg['verification_code'] ?? 'NULL') . ", Attempts: " . ($currentReg['verification_attempts'] ?? 0));
                    } else {
                        error_log("ADMIN VERIFICATION FAILED: No registration found for email: {$email}");
                    }
                    
                    $_SESSION['error'] = 'Invalid verification code. Please check your email and try again. Make sure you enter the exact 4-digit code from your email.';
                    $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=admin');
                    return;
                }
                
                $isCustomer = false;
            }
            
            // Check if code has expired (24 hours)
            $codeSentAt = $registration['verification_code_sent_at'] ?? null;
            if ($codeSentAt) {
                $codeSentAtTime = strtotime($codeSentAt);
                $now = time();
                $hoursSinceSent = ($now - $codeSentAtTime) / 3600;
                
                if ($hoursSinceSent > 24) {
                    $_SESSION['error'] = 'Verification code has expired. Please use the resend button to get a new code.';
                    $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=' . $role);
                    return;
                }
            }
            
            // Check verification attempts (max 5 attempts)
            $verificationAttempts = $registration['verification_attempts'] ?? 0;
            if ($verificationAttempts >= 5) {
                $_SESSION['error'] = 'Too many verification attempts. Please use the resend button to get a new code.';
                $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=' . $role);
                return;
            }
            
            // After code verification, activate account immediately
            $db->beginTransaction();
            
            try {
                if ($isCustomer) {
                    // Handle customer verification
                    // Update user to mark code as verified and change status to active
                    $updateStmt = $db->prepare("
                        UPDATE users 
                        SET verification_code_verified_at = NOW(),
                            verification_attempts = 0,
                            status = 'active'
                        WHERE id = ?
                    ");
                    $updateStmt->execute([$user['id']]);
                    
                    $db->commit();
                    
                    // Automatically log in the customer after successful verification
                    $userStmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
                    $userStmt->execute([$email]);
                    $verifiedUser = $userStmt->fetch();
                    
                    if ($verifiedUser && $verifiedUser['status'] === 'active') {
                        // Set session variables for automatic login
                        $_SESSION['user_id'] = $verifiedUser['id'];
                        $_SESSION['email'] = $verifiedUser['email'];
                        $_SESSION['username'] = $verifiedUser['username'] ?? $verifiedUser['email'];
                        $_SESSION['role'] = $verifiedUser['role'];
                        $_SESSION['name'] = $verifiedUser['fullname'] ?? $verifiedUser['full_name'] ?? 'User';
                        $_SESSION['last_activity'] = time();
                        
                        // Update last login
                        try {
                            $this->userModel->updateLastLogin($verifiedUser['id']);
                        } catch (Exception $e) {
                            error_log("Warning: Could not update last login: " . $e->getMessage());
                        }
                        
                        // Log successful login
                        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                        
                        try {
                            $logStmt = $db->prepare("
                                INSERT INTO login_logs 
                                (user_id, user_type, email, fullname, ip_address, user_agent, login_status, login_message, created_at) 
                                VALUES (?, 'customer', ?, ?, ?, ?, 'success', 'Automatic login after verification', NOW())
                            ");
                            $logStmt->execute([
                                $verifiedUser['id'],
                                $verifiedUser['email'],
                                $verifiedUser['fullname'] ?? $verifiedUser['full_name'] ?? 'User',
                                $ipAddress,
                                $userAgent
                            ]);
                        } catch (Exception $e) {
                            error_log("Warning: Could not log automatic login: " . $e->getMessage());
                        }
                        
                        $_SESSION['success'] = 'Verification code verified successfully! Your account has been activated. You have been automatically logged in.';
                        error_log("SUCCESS: Customer {$verifiedUser['email']} automatically logged in after verification");
                        
                        // Redirect to customer dashboard
                        $this->redirectToDashboard();
                    } else {
                        // User not found or status not active - redirect to login with success message
                        $_SESSION['success'] = 'Verification code verified successfully! Your account has been activated. You can now login.';
                        error_log("WARNING: Could not auto-login customer {$email} - user not found or status not active");
                        $this->redirect('auth/login?tab=customer');
                    }
                    
                } else {
                    // Handle admin verification (existing code)
                    // Update registration to mark code as verified and change status to approved
                    $updateStmt = $db->prepare("
                        UPDATE admin_registrations 
                        SET verification_code_verified_at = NOW(),
                            verification_attempts = 0,
                            registration_status = 'approved',
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $updateStmt->execute([$registration['id']]);
                    
                    // Update user status to active immediately after verification
                    $userStmt = $db->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
                    $userStmt->execute([$email]);
                    $adminUser = $userStmt->fetch();
                    
                    if ($adminUser) {
                        $userUpdateStmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                        $userUpdateStmt->execute([$adminUser['id']]);
                    }
                    
                    // Create/update admin account in control_panel_admins
                    $checkAdminStmt = $db->prepare("SELECT id FROM control_panel_admins WHERE email = ?");
                    $checkAdminStmt->execute([$email]);
                    $existingAdmin = $checkAdminStmt->fetch();
                    
                    if (!$existingAdmin) {
                        // Get password from users table
                        $userPwdStmt = $db->prepare("SELECT password FROM users WHERE email = ? AND role = 'admin'");
                        $userPwdStmt->execute([$email]);
                        $userData = $userPwdStmt->fetch();
                        
                        $insertAdminStmt = $db->prepare("
                            INSERT INTO control_panel_admins 
                            (email, password, fullname, role, status, created_at) 
                            VALUES (?, ?, ?, 'admin', 'active', NOW())
                        ");
                        $insertAdminStmt->execute([
                            $registration['email'],
                            $userData['password'] ?? $registration['password'],
                            $registration['fullname']
                        ]);
                    } else {
                        $updateAdminStmt = $db->prepare("UPDATE control_panel_admins SET status = 'active' WHERE id = ?");
                        $updateAdminStmt->execute([$existingAdmin['id']]);
                    }
                    
                    // Create store location entry if admin has business information
                    if (!empty($registration['business_name']) && !empty($registration['business_address'])) {
                        try {
                            // Get coordinates - use existing if available, otherwise geocode the address
                            $latitude = $registration['business_latitude'] ?? null;
                            $longitude = $registration['business_longitude'] ?? null;
                            
                            // If coordinates are not set, geocode the address
                            if (empty($latitude) || empty($longitude)) {
                                require_once ROOT . DS . 'core' . DS . 'GeocodingService.php';
                                
                                $coordinates = GeocodingService::geocodeAddressWithRetry(
                                    $registration['business_address'],
                                    $registration['business_city'] ?? 'Bohol',
                                    $registration['business_province'] ?? 'Bohol'
                                );
                                
                                if ($coordinates !== null) {
                                    $latitude = $coordinates['lat'];
                                    $longitude = $coordinates['lng'];
                                    
                                    // Update admin_registrations with geocoded coordinates
                                    try {
                                        $updateCoordsStmt = $db->prepare("
                                            UPDATE admin_registrations 
                                            SET business_latitude = ?, business_longitude = ?, updated_at = NOW()
                                            WHERE id = ?
                                        ");
                                        $updateCoordsStmt->execute([
                                            $latitude,
                                            $longitude,
                                            $registration['id']
                                        ]);
                                        error_log("INFO: Geocoded coordinates for admin: {$registration['business_name']} - Lat: {$latitude}, Lng: {$longitude}");
                                    } catch (Exception $coordError) {
                                        error_log("WARNING: Could not update coordinates in admin_registrations: " . $coordError->getMessage());
                                    }
                                } else {
                                    // Geocoding failed - use default Bohol coordinates as fallback
                                    $defaultCoords = GeocodingService::getDefaultBoholCoordinates();
                                    $latitude = $defaultCoords['lat'];
                                    $longitude = $defaultCoords['lng'];
                                    error_log("WARNING: Geocoding failed for {$registration['business_name']}, using default Bohol coordinates");
                                }
                            }
                            
                            // Verify coordinates are valid before creating store location
                            if (!empty($latitude) && !empty($longitude) && 
                                $latitude >= 9.0 && $latitude <= 10.5 && 
                                $longitude >= 123.0 && $longitude <= 125.0) {
                                
                                // Check if store location already exists for this admin
                                $checkStoreStmt = $db->prepare("
                                    SELECT id FROM store_locations 
                                    WHERE email = ? OR (store_name = ? AND address = ?)
                                ");
                                $checkStoreStmt->execute([
                                    $registration['email'],
                                    $registration['business_name'],
                                    $registration['business_address']
                                ]);
                                $existingStore = $checkStoreStmt->fetch();
                                
                                if (!$existingStore) {
                                    // Create store location entry
                                    $insertStoreStmt = $db->prepare("
                                        INSERT INTO store_locations 
                                        (store_name, address, city, province, latitude, longitude, 
                                         phone, email, status, created_at, updated_at) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                                    ");
                                    $insertStoreStmt->execute([
                                        $registration['business_name'],
                                        $registration['business_address'],
                                        $registration['business_city'] ?? 'Bohol',
                                        $registration['business_province'] ?? 'Bohol',
                                        $latitude,
                                        $longitude,
                                        $registration['phone'] ?? '',
                                        $registration['email']
                                    ]);
                                    error_log("INFO: Store location created for verified admin: {$registration['business_name']} ({$registration['email']}) at Lat: {$latitude}, Lng: {$longitude}");
                                } else {
                                    // Update existing store location
                                    $updateStoreStmt = $db->prepare("
                                        UPDATE store_locations 
                                        SET store_name = ?, address = ?, city = ?, province = ?, 
                                            latitude = ?, longitude = ?, phone = ?, status = 'active', 
                                            updated_at = NOW()
                                        WHERE id = ?
                                    ");
                                    $updateStoreStmt->execute([
                                        $registration['business_name'],
                                        $registration['business_address'],
                                        $registration['business_city'] ?? 'Bohol',
                                        $registration['business_province'] ?? 'Bohol',
                                        $latitude,
                                        $longitude,
                                        $registration['phone'] ?? '',
                                        $existingStore['id']
                                    ]);
                                    error_log("INFO: Store location updated for verified admin: {$registration['business_name']} ({$registration['email']}) at Lat: {$latitude}, Lng: {$longitude}");
                                }
                            } else {
                                error_log("WARNING: Invalid coordinates for admin {$registration['email']}. Lat: {$latitude}, Lng: {$longitude}. Store location not created.");
                            }
                        } catch (Exception $storeError) {
                            // Log error but don't fail verification if store creation fails
                            error_log("WARNING: Could not create store location for admin {$registration['email']}: " . $storeError->getMessage());
                        }
                    } else {
                        error_log("INFO: Admin {$registration['email']} verified but no business name or address available");
                    }
                    
                    $db->commit();
                    
                    // Automatically log in the admin after successful verification
                    $userStmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
                    $userStmt->execute([$email]);
                    $verifiedUser = $userStmt->fetch();
                    
                    if ($verifiedUser && $verifiedUser['status'] === 'active') {
                        // Set session variables for automatic login
                        $_SESSION['user_id'] = $verifiedUser['id'];
                        $_SESSION['email'] = $verifiedUser['email'];
                        $_SESSION['username'] = $verifiedUser['username'] ?? $verifiedUser['email'];
                        $_SESSION['role'] = $verifiedUser['role'];
                        $_SESSION['name'] = $verifiedUser['fullname'] ?? $verifiedUser['full_name'] ?? 'User';
                        $_SESSION['last_activity'] = time();
                        
                        // Update last login
                        try {
                            $this->userModel->updateLastLogin($verifiedUser['id']);
                        } catch (Exception $e) {
                            error_log("Warning: Could not update last login: " . $e->getMessage());
                        }
                        
                        // Log successful login
                        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                        
                        try {
                            $logStmt = $db->prepare("
                                INSERT INTO login_logs 
                                (user_id, user_type, email, fullname, ip_address, user_agent, login_status, login_message, created_at) 
                                VALUES (?, 'admin', ?, ?, ?, ?, 'success', 'Automatic login after verification', NOW())
                            ");
                            $logStmt->execute([
                                $verifiedUser['id'],
                                $verifiedUser['email'],
                                $verifiedUser['fullname'] ?? $verifiedUser['full_name'] ?? 'User',
                                $ipAddress,
                                $userAgent
                            ]);
                        } catch (Exception $e) {
                            error_log("Warning: Could not log automatic login: " . $e->getMessage());
                        }
                        
                        $_SESSION['success'] = 'Verification code verified successfully! Your account has been activated. You have been automatically logged in.';
                        error_log("SUCCESS: Admin {$verifiedUser['email']} automatically logged in after verification");
                        
                        // Redirect to dashboard
                        $this->redirectToDashboard();
                    } else {
                        // User not found or status not active - redirect to login with success message
                        $_SESSION['success'] = 'Verification code verified successfully! Your account has been activated. You can now login.';
                        error_log("WARNING: Could not auto-login admin {$email} - user not found or status not active");
                        $this->redirect('auth/login?tab=admin');
                    }
                }
                
            } catch (Exception $e) {
                $db->rollBack();
                error_log("Error activating account after verification: " . $e->getMessage());
                $_SESSION['error'] = 'Failed to activate account: ' . $e->getMessage();
                $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=' . $role);
            }
            
        } catch (Exception $e) {
            error_log("Error verifying code: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to verify code: ' . $e->getMessage();
            $this->redirect('auth/verifyCode?email=' . urlencode($email));
        }
    }
    
    /**
     * Process customer registration
     */
    public function processRegisterCustomer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/registerCustomer');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = 'customer'; // Always customer for this method
        
        // Validation
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required';
        }
        
        // Phone validation
        if (!empty($phone)) {
            if (!preg_match('/^[0-9]{11}$/', $phone)) {
                $errors[] = 'Phone number must be exactly 11 digits';
            }
        }
        
        // Customer ID Image validation
        $customerIdPath = null;
        $customerIdFilename = null;
        
        if (!isset($_FILES['customer_id_image']) || $_FILES['customer_id_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'A valid Customer ID image is required for verification';
        } else {
            $idFile = $_FILES['customer_id_image'];
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            
            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $idFile['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedMimes)) {
                $errors[] = 'Customer ID must be an image file (JPG, PNG, WEBP, or GIF)';
            }
            
            if ($idFile['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Customer ID image size must not exceed 5MB';
            }
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Email already exists';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('home#signup');
        }
        
        // Handle Customer ID image upload
        if (isset($_FILES['customer_id_image']) && $_FILES['customer_id_image']['error'] === UPLOAD_ERR_OK) {
            $idFile = $_FILES['customer_id_image'];
            $uploadDir = ROOT . DS . 'assets' . DS . 'uploads' . DS . 'customer_ids' . DS;
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($idFile['name'], PATHINFO_EXTENSION));
            $customerIdFilename = $idFile['name'];
            $uniqueFilename = 'customer_id_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $uniqueFilename;
            
            if (!move_uploaded_file($idFile['tmp_name'], $uploadPath)) {
                $_SESSION['error'] = 'Failed to upload Customer ID. Please try again.';
                $this->redirect('auth/registerCustomer');
            }
            
            $customerIdPath = 'assets/uploads/customer_ids/' . $uniqueFilename;
        }
        
        // Generate username from email (use email prefix as username)
        $usernameFromEmail = explode('@', $email)[0];
        // Ensure username is unique
        $username = $usernameFromEmail;
        $counter = 1;
        while ($this->userModel->usernameExists($username)) {
            $username = $usernameFromEmail . $counter;
            $counter++;
        }
        
        // Register user as customer with pending verification status
        $data = [
            'username' => $username, // Auto-generated from email
            'email' => $email,
            'password' => $password,
            'fullname' => $fullName,
            'phone' => $phone,
            'role' => 'customer',
            'status' => 'pending_verification', // Set to pending_verification - waiting for email verification
            'customer_id_path' => $customerIdPath,
            'customer_id_filename' => $customerIdFilename
        ];
        
        $userId = $this->userModel->register($data);
        
        if ($userId) {
            // Get database connection
            $db = Database::getInstance()->getConnection();
            
            try {
                // Automatically generate and send verification code via email
                // Generate 4-digit verification code (ensure it's exactly 4 digits)
                $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                
                // Log the generated code for debugging
                error_log("INFO: Generated verification code for customer {$email}: {$verificationCode}");
                
                // Check if verification_code column exists in users table
                $checkColumnStmt = $db->query("
                    SELECT COUNT(*) as col_count 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'verification_code'
                ");
                $columnExists = $checkColumnStmt->fetch()['col_count'] > 0;
                
                if ($columnExists) {
                    // Update user with verification code (store as string, trimmed)
                    $updateCodeStmt = $db->prepare("
                        UPDATE users 
                        SET verification_code = ?, 
                            verification_code_sent_at = NOW(),
                            verification_attempts = 0
                        WHERE id = ?
                    ");
                    $updateCodeStmt->execute([$verificationCode, $userId]);
                    
                    // Verify the code was stored correctly
                    $verifyStmt = $db->prepare("SELECT verification_code FROM users WHERE id = ?");
                    $verifyStmt->execute([$userId]);
                    $storedCode = $verifyStmt->fetch()['verification_code'] ?? null;
                    error_log("INFO: Verification code stored in database for customer {$email}: " . ($storedCode ?? 'NULL'));
                    
                    // Send verification code via email using PHPMailer (AUTOMATIC)
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    error_log("INFO: Attempting to send verification code email to customer {$email}");
                    $emailSent = $notificationService->sendAdminVerificationCode(
                        $email,
                        $fullName,
                        $verificationCode
                    );
                    
                    error_log("INFO: Email sending result for customer {$email}: " . ($emailSent ? 'SUCCESS' : 'FAILED'));
                    
                    if ($emailSent) {
                        $_SESSION['success'] = "✅ Registration submitted successfully! A verification code has been automatically sent to your email address (<strong>{$email}</strong>). <strong>You must verify this code before you can log in.</strong> Please check your inbox and enter the verification code on the next page.";
                        $_SESSION['registration_email'] = $email;
                        error_log("INFO: Customer registration submitted - Email: {$email}, User ID: {$userId}. Verification code {$verificationCode} automatically sent via email.");
                        
                        // CRITICAL: Redirect to verification page - customer MUST verify before login
                        // DO NOT redirect to login page - verification is required first
                        error_log("INFO: Redirecting customer to verification page: auth/verifyCode?email=" . urlencode($email) . "&role=customer");
                        $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                        return; // Ensure we don't continue after redirect
                    } else {
                        // Email sending failed, but code is stored
                        $_SESSION['success'] = "✅ Registration submitted successfully! A verification code has been generated. However, email sending failed. <strong>You must verify your code before you can log in.</strong> Please contact support or visit the verification page. Your email: <strong>{$email}</strong>";
                        $_SESSION['registration_email'] = $email;
                        error_log("WARNING: Customer registration submitted - Email: {$email}, User ID: {$userId}. Verification code {$verificationCode} generated but email sending failed.");
                        
                        // Redirect to verification page
                        error_log("INFO: Redirecting customer to verification page (email failed): auth/verifyCode?email=" . urlencode($email) . "&role=customer");
                        $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                        return; // Ensure we don't continue after redirect
                    }
                } else {
                    // Columns don't exist - still redirect to verification page
                    // Even if columns don't exist, customer should go to verification page
                    $_SESSION['success'] = "✅ Registration submitted successfully! However, the verification system needs to be set up. Please contact support. Your email: <strong>{$email}</strong>";
                    $_SESSION['registration_email'] = $email;
                    error_log("INFO: Customer registration submitted - Email: {$email}, User ID: {$userId}. Verification code columns not available - redirecting to verification page.");
                    
                    // Redirect to verification page (not login page)
                    error_log("INFO: Redirecting customer to verification page (columns missing): auth/verifyCode?email=" . urlencode($email) . "&role=customer");
                    $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                    return; // Ensure we don't continue after redirect
                }
            } catch (Exception $emailError) {
                // If email sending fails, still show success but mention they need to wait
                error_log("ERROR: Failed to send verification code email to customer: " . $emailError->getMessage());
                $_SESSION['success'] = "✅ Registration submitted successfully! However, there was an issue sending the verification code. Please contact support or visit the verification page. Your email: <strong>{$email}</strong>";
                $_SESSION['registration_email'] = $email;
                
                // Redirect to verification page
                error_log("INFO: Redirecting customer to verification page (exception): auth/verifyCode?email=" . urlencode($email) . "&role=customer");
                $this->redirect('auth/verifyCode?email=' . urlencode($email) . '&role=customer');
                return; // Ensure we don't continue after redirect
            }
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $this->redirect('auth/registerCustomer');
        }
    }
    
    /**
     * Process registration (legacy method - redirects to customer registration)
     */
    public function processRegister() {
        $this->redirect('auth/registerCustomer');
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        $this->redirect('home#login');
    }
    
    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard() {
        if ($this->hasRole(ROLE_CONTROL_PANEL_ADMIN)) {
            // Control Panel Admins go to control panel
            $_SESSION['control_panel_admin'] = [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['username'],
                'fullname' => $_SESSION['name']
            ];
            $this->redirect('control-panel/dashboard');
        } elseif ($this->hasRole(ROLE_ADMIN)) {
            $this->redirect('admin/dashboard');
        } else {
            $this->redirect('customer/dashboard');
        }
    }
}

