<?php
/**
 * Database Seeder for Yearly Test Data (2010-2025)
 * 
 * This script creates sample booking records for 15 years (2010-2025)
 * to test the yearly reports functionality with historical data.
 * 
 * Features:
 * - 15 years of historical sales data
 * - Realistic inflation simulation (4% annual increase)
 * - Business growth simulation (more bookings in recent years)
 * - ~600-900 total test bookings
 * 
 * Test records are marked with:
 * - notes = 'TEST_DATA_DO_NOT_DELETE' (for easy identification)
 * - item_description starts with '[TEST]'
 * - booking_number starts with 'TEST-'
 * 
 * To run: Navigate to http://localhost/UphoCare/database/seed_yearly_test_data.php
 * To remove: Navigate to http://localhost/UphoCare/database/remove_test_data.php
 */

require_once __DIR__ . '/../config/database.php';

// Establish database connection
$db = Database::getInstance()->getConnection();

// Test data configuration - 15 years of historical data
$yearsToSeed = [2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025];
$servicesData = [
    ['name' => 'Sofa Repair', 'base_price' => 1500],
    ['name' => 'Mattress Cover', 'base_price' => 800],
    ['name' => 'Chair Upholstery', 'base_price' => 1200],
    ['name' => 'Couch Restoration', 'base_price' => 2500],
    ['name' => 'Cushion Repair', 'base_price' => 600],
    ['name' => 'Dining Chair Set', 'base_price' => 3500],
    ['name' => 'Ottoman Repair', 'base_price' => 900],
    ['name' => 'Recliner Restoration', 'base_price' => 2800],
];

$monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Counter for records created
$recordsCreated = 0;
$errors = [];

try {
    $db->beginTransaction();
    
    // Get a test customer (create one if doesn't exist)
    $testCustomerEmail = 'test_customer@uphocare.test';
    
    // Check if test customer exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$testCustomerEmail]);
    $testCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$testCustomer) {
        // Create test customer
        $stmt = $db->prepare("INSERT INTO users (name, email, password, phone, role, created_at) 
                              VALUES (?, ?, ?, ?, 'customer', NOW())");
        $stmt->execute([
            'Test Customer',
            $testCustomerEmail,
            password_hash('TestPass123!', PASSWORD_DEFAULT),
            '09123456789'
        ]);
        $customerId = $db->lastInsertId();
    } else {
        $customerId = $testCustomer['id'];
    }
    
    // Get the first service ID from services table
    $stmt = $db->query("SELECT id FROM services LIMIT 1");
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    $serviceId = $service['id'] ?? 1;
    
    // Create test bookings for each year
    foreach ($yearsToSeed as $yearIndex => $year) {
        echo "<h3>Creating test data for year {$year}...</h3>";
        
        // Calculate inflation factor (prices increase over time)
        // Base year 2010 = 1.0, each year adds ~3-5% inflation
        $inflationRate = 1 + (($yearIndex) * 0.04); // 4% average annual increase
        
        // Business growth - more bookings in recent years
        $growthFactor = 1 + ($yearIndex * 0.05); // 5% more bookings each year
        $baseBookingsPerMonth = 3;
        $maxBookingsPerMonth = (int)min(8, $baseBookingsPerMonth + round($growthFactor));
        
        // Create bookings for each month
        for ($month = 1; $month <= 12; $month++) {
            $bookingsPerMonth = rand($baseBookingsPerMonth, $maxBookingsPerMonth);
            
            for ($i = 0; $i < $bookingsPerMonth; $i++) {
                // Random day of the month
                $day = rand(1, 28);
                $completedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                
                // Random service
                $serviceData = $servicesData[array_rand($servicesData)];
                $basePrice = $serviceData['base_price'];
                
                // Apply inflation to base price
                $adjustedBasePrice = (int)($basePrice * $inflationRate);
                
                // Add some variation to prices
                $priceVariation = rand(-200, 500);
                $totalAmount = $adjustedBasePrice + $priceVariation;
                
                // Calculate additional fees (randomly)
                $laborFee = rand(100, 300);
                $pickupFee = rand(0, 150);
                $deliveryFee = rand(0, 150);
                $gasFee = rand(0, 100);
                $travelFee = rand(0, 200);
                $totalAdditionalFees = $laborFee + $pickupFee + $deliveryFee + $gasFee + $travelFee;
                $grandTotal = $totalAmount + $totalAdditionalFees;
                
                // Generate unique booking number
                $bookingNumber = sprintf('TEST-%04d-%02d-%03d', $year, $month, rand(100, 999));
                
                // Insert booking
                $sql = "INSERT INTO bookings (
                    customer_id,
                    service_id,
                    booking_number,
                    item_description,
                    item_type,
                    notes,
                    status,
                    payment_status,
                    total_amount,
                    labor_fee,
                    pickup_fee,
                    delivery_fee,
                    gas_fee,
                    travel_fee,
                    total_additional_fees,
                    grand_total,
                    pickup_date,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([
                    $customerId,
                    $serviceId,
                    $bookingNumber,
                    '[TEST] ' . $serviceData['name'] . ' - Sample booking for testing',
                    $serviceData['name'],
                    'TEST_DATA_DO_NOT_DELETE',
                    'completed',
                    'paid',
                    $totalAmount,
                    $laborFee,
                    $pickupFee,
                    $deliveryFee,
                    $gasFee,
                    $travelFee,
                    $totalAdditionalFees,
                    $grandTotal,
                    $completedDate,
                    $completedDate . ' 10:00:00',
                    $completedDate . ' 15:00:00'
                ]);
                
                if ($result) {
                    $recordsCreated++;
                    echo "✓ Created booking: {$bookingNumber} - {$monthNames[$month-1]} {$day}, {$year} - ₱" . number_format($grandTotal, 2) . "<br>";
                } else {
                    $errors[] = "Failed to create booking for {$monthNames[$month-1]} {$year}";
                }
            }
        }
        
        echo "<p style='color: green; font-weight: bold;'>✓ Completed year {$year}</p><hr>";
    }
    
    $db->commit();
    
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724; margin: 0;'>✓ Success!</h2>";
    echo "<p style='color: #155724; font-size: 16px; margin: 10px 0 0 0;'>";
    echo "Created <strong>{$recordsCreated} test bookings</strong> across <strong>15 years</strong> ({$yearsToSeed[0]} - {$yearsToSeed[count($yearsToSeed)-1]})<br>";
    echo "Test customer created: <strong>{$testCustomerEmail}</strong><br>";
    echo "All records are marked with 'TEST_DATA_DO_NOT_DELETE' for easy removal.<br>";
    echo "<br><strong>Features:</strong><br>";
    echo "• Realistic price inflation (4% annual increase)<br>";
    echo "• Business growth simulation (more bookings in recent years)<br>";
    echo "• 3-8 bookings per month depending on year<br>";
    echo "• Total historical data: 15 years";
    echo "</p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3 style='color: #856404; margin: 0;'>⚠️ Important:</h3>";
    echo "<p style='color: #856404; margin: 10px 0 0 0;'>";
    echo "To remove all test data, visit: <a href='remove_test_data.php' style='color: #d9534f; font-weight: bold;'>Remove Test Data</a>";
    echo "</p>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3 style='color: #0c5460; margin: 0;'>📊 Next Steps:</h3>";
    echo "<p style='color: #0c5460; margin: 10px 0 0 0;'>";
    echo "1. Go to <a href='../admin/reports' style='color: #007bff; font-weight: bold;'>Admin Reports</a><br>";
    echo "2. Use the year search to view data for different years<br>";
    echo "3. Test the line graph and monthly breakdown table<br>";
    echo "4. When done testing, use the remove script to clean up";
    echo "</p>";
    echo "</div>";
    
    if (!empty($errors)) {
        echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h3 style='color: #721c24;'>Errors encountered:</h3>";
        foreach ($errors as $error) {
            echo "<p style='color: #721c24; margin: 5px 0;'>• {$error}</p>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    $db->rollBack();
    
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2 style='color: #721c24; margin: 0;'>✗ Error!</h2>";
    echo "<p style='color: #721c24; font-size: 16px; margin: 10px 0 0 0;'>";
    echo "Failed to create test data: " . $e->getMessage();
    echo "</p>";
    echo "</div>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Seed Yearly Test Data - UphoCare</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-top: 0;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 5px;
        }
        a:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌱 Database Seeder - Yearly Test Data</h1>
        
        <?php if ($recordsCreated > 0): ?>
        <div style="text-align: center; margin: 30px 0;">
            <a href="../admin/reports">📊 View Reports Dashboard</a>
            <a href="remove_test_data.php" style="background: #dc3545;">🗑️ Remove Test Data</a>
            <a href="../admin/dashboard">🏠 Admin Dashboard</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

