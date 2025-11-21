<?php
/**
 * Setup Control Panel Admin
 * Run this file once to create the default control panel admin with proper password hashing
 */

// Database connection
$host = 'localhost';
$dbname = 'db_upholcare';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Default credentials
    $email = 'control@uphocare.com';
    $plainPassword = 'Control@2025';
    $fullname = 'Control Panel Admin';
    
    // Hash the password properly
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    
    // Insert or update the control panel admin
    $stmt = $pdo->prepare("
        INSERT INTO control_panel_admins (email, password, fullname, status) 
        VALUES (?, ?, ?, 'active')
        ON DUPLICATE KEY UPDATE password = VALUES(password), fullname = VALUES(fullname)
    ");
    
    $stmt->execute([$email, $hashedPassword, $fullname]);
    
    echo "✅ Control Panel Admin created successfully!\n\n";
    echo "Login Credentials:\n";
    echo "==================\n";
    echo "Email: $email\n";
    echo "Password: $plainPassword\n";
    echo "==================\n\n";
    echo "Access URL: http://localhost/UphoCare/control-panel\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

