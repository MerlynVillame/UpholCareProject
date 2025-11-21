<?php
/**
 * Login Logger Helper
 * Centralized login activity logging
 */

class LoginLogger {
    private static $db = null;
    
    /**
     * Get database connection
     */
    private static function getDB() {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }
    
    /**
     * Log a login attempt
     * 
     * @param int|null $userId - User ID (null if login failed before user was found)
     * @param string $userType - 'customer', 'admin', or 'control_panel'
     * @param string $email - Email used for login
     * @param string|null $fullname - Full name of user
     * @param string $status - 'success' or 'failed'
     * @param string|null $failureReason - Reason for failure (if failed)
     */
    public static function log($userId, $userType, $email, $fullname, $status, $failureReason = null) {
        try {
            $db = self::getDB();
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            $stmt = $db->prepare("
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
            
            return true;
        } catch (Exception $e) {
            // Silently fail - don't break the login process if logging fails
            error_log("LoginLogger Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log successful login
     */
    public static function logSuccess($userId, $userType, $email, $fullname) {
        return self::log($userId, $userType, $email, $fullname, 'success', null);
    }
    
    /**
     * Log failed login
     */
    public static function logFailure($userId, $userType, $email, $fullname, $reason) {
        return self::log($userId, $userType, $email, $fullname, 'failed', $reason);
    }
}

