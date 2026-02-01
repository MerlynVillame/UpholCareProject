<?php
/**
 * Base Controller Class
 */

class Controller {
    
    /**
     * Load a view file
     */
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = ROOT . DS . 'views' . DS . str_replace('.', DS, $view) . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $view);
        }
    }
    
    /**
     * Load a model
     */
    protected function model($model) {
        $modelFile = ROOT . DS . 'models' . DS . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model not found: " . $model);
        }
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect($path) {
        header("Location: " . BASE_URL . $path);
        exit();
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Check if user has specific role
     */
    protected function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Require login
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            // For AJAX requests, return JSON error instead of redirecting
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Authentication required. Please login.']);
                exit;
            }
            
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Require specific role
     */
    protected function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            // For AJAX requests, return JSON error instead of redirecting
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied. Insufficient privileges.']);
                exit;
            }
            
            $this->redirect('error/unauthorized');
        }
    }
    
    /**
     * Get current user data
     */
    protected function currentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'name' => $_SESSION['name'] ?? $_SESSION['username']
            ];
        }
        return null;
    }
    
    /**
     * Require Admin role (security check)
     */
    protected function requireAdmin() {
        $this->requireLogin();
        
        // SECURITY: Verify user is actually an admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== ROLE_ADMIN) {
            error_log("SECURITY ALERT: Unauthorized admin access attempt by user: " . ($_SESSION['username'] ?? 'unknown'));
            
            // For AJAX requests, return JSON error instead of redirecting
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
                exit;
            }
            
            $_SESSION['error'] = 'Access denied. Admin privileges required.';
            $this->redirect('auth/login');
        }
    }
    
    /**
     * SECURITY: Verify role from database on protected pages
     */
    protected function verifyRoleIntegrity() {
        if ($this->isLoggedIn() && isset($_SESSION['user_id'])) {
            // Verify user role from database matches session
            $userModel = $this->model('User');
            $user = $userModel->findById($_SESSION['user_id']);
            
            if ($user && isset($user['role'])) {
                // If role mismatch, logout user
                if ($user['role'] !== $_SESSION['role']) {
                    error_log("SECURITY ALERT: Role mismatch for user {$user['username']}. Session: {$_SESSION['role']}, DB: {$user['role']}");
                    $this->logout();
                }
            }
        }
    }
    
    /**
     * Logout (accessible from any controller)
     */
    protected function logout() {
        if (isset($_SESSION['username'])) {
            error_log("User {$_SESSION['username']} logged out");
        }
        session_destroy();
        $this->redirect('home#login');
    }
}

