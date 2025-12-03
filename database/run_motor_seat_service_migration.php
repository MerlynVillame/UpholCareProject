<?php
/**
 * Migration: Add Motor Seat service
 * Run this script once to add the Motor Seat service to the database
 */

// Database configuration
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "Connected to database successfully.\n";
    
    // Check if Motor Seat service already exists
    $checkService = $pdo->prepare("SELECT id FROM services WHERE service_name = 'Motor Seat' AND service_type = 'Vehicle Upholstery'");
    $checkService->execute();
    if ($checkService->rowCount() > 0) {
        echo "Motor Seat service already exists in the database.\n";
        echo "Migration not needed.\n";
        exit(0);
    }
    
    // Get Vehicle Upholstery category ID if category_id column exists
    $checkCategoryColumn = $pdo->query("SHOW COLUMNS FROM services LIKE 'category_id'");
    $hasCategoryColumn = $checkCategoryColumn->rowCount() > 0;
    
    $categoryId = null;
    if ($hasCategoryColumn) {
        $categoryStmt = $pdo->prepare("SELECT id FROM service_categories WHERE category_name = 'Vehicle Upholstery' LIMIT 1");
        $categoryStmt->execute();
        $category = $categoryStmt->fetch();
        if ($category) {
            $categoryId = $category['id'];
        }
    }
    
    // Insert Motor Seat service
    if ($hasCategoryColumn && $categoryId) {
        $sql = "INSERT INTO services (service_name, service_type, description, price, status, category_id, created_at)
                VALUES ('Motor Seat', 'Vehicle Upholstery', 'Repair and restore motorcycle seats', 120.00, 'active', ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoryId]);
    } else {
        $sql = "INSERT INTO services (service_name, service_type, description, price, status, created_at)
                VALUES ('Motor Seat', 'Vehicle Upholstery', 'Repair and restore motorcycle seats', 120.00, 'active', NOW())";
        $pdo->exec($sql);
    }
    
    echo "Successfully added 'Motor Seat' service to the database.\n";
    echo "Service Type: Vehicle Upholstery\n";
    echo "Price: ₱120.00\n";
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

