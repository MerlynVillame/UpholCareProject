<?php
/**
 * Quick Seeder - 2024-2025 Example Data
 * 
 * Creates sample booking records for 2024 and 2025 only
 * Perfect for quick testing of the year search and monthly graphs
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

$yearsToSeed = [2024, 2025];
$servicesData = [
    ['name' => 'Sofa Repair', 'base_price' => 2000],
    ['name' => 'Mattress Cover', 'base_price' => 1200],
    ['name' => 'Chair Upholstery', 'base_price' => 1500],
    ['name' => 'Couch Restoration', 'base_price' => 3000],
    ['name' => 'Cushion Repair', 'base_price' => 800],
];

$monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

$recordsCreated = 0;

try {
    $db->beginTransaction();
    
    // Get or create test customer
    $testCustomerEmail = 'test_customer@uphocare.test';
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$testCustomerEmail]);
    $testCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$testCustomer) {
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
    
    // Get first service ID
    $stmt = $db->query("SELECT id FROM services LIMIT 1");
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    $serviceId = $service['id'] ?? 1;
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Creating Example Data (2024-2025)</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                max-width: 1000px; 
                margin: 50px auto; 
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
                border-bottom: 3px solid #4e73df; 
                padding-bottom: 10px;
            }
            .year-section { 
                margin: 20px 0; 
                padding: 15px; 
                background: #f8f9fc; 
                border-left: 4px solid #4e73df;
                border-radius: 5px;
            }
            .booking-item { 
                padding: 8px; 
                margin: 5px 0; 
                font-size: 14px;
                color: #2c3e50;
            }
            .success { 
                background: #d4edda; 
                border: 2px solid #28a745; 
                padding: 20px; 
                border-radius: 10px;
                margin: 20px 0;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                margin: 10px 5px;
                background: #4e73df;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
            }
            .btn:hover { background: #2e59d9; }
            .btn-danger { background: #dc3545; }
            .btn-danger:hover { background: #c82333; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>🚀 Creating Example Data (2024-2025)</h1>
            <p>Generating sample bookings for monthly graph testing...</p>";
    
    // Create bookings for each year
    foreach ($yearsToSeed as $year) {
        echo "<div class='year-section'>";
        echo "<h3 style='color: #4e73df; margin-top: 0;'>📅 Creating data for year {$year}...</h3>";
        
        // Create 5-7 bookings per month
        for ($month = 1; $month <= 12; $month++) {
            $bookingsPerMonth = rand(5, 7);
            
            for ($i = 0; $i < $bookingsPerMonth; $i++) {
                $day = rand(1, 28);
                $completedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                
                // Random service
                $serviceData = $servicesData[array_rand($servicesData)];
                $basePrice = $serviceData['base_price'];
                
                // Add variation
                $priceVariation = rand(-300, 800);
                $totalAmount = $basePrice + $priceVariation;
                
                // Calculate fees
                $laborFee = rand(200, 400);
                $pickupFee = rand(0, 200);
                $deliveryFee = rand(0, 200);
                $gasFee = rand(50, 150);
                $travelFee = rand(100, 300);
                $totalAdditionalFees = $laborFee + $pickupFee + $deliveryFee + $gasFee + $travelFee;
                $grandTotal = $totalAmount + $totalAdditionalFees;
                
                $bookingNumber = sprintf('TEST-%04d-%02d-%03d', $year, $month, rand(100, 999));
                
                // Insert booking
                $sql = "INSERT INTO bookings (
                    customer_id, service_id, booking_number, item_description, item_type,
                    notes, status, payment_status, total_amount, labor_fee, pickup_fee,
                    delivery_fee, gas_fee, travel_fee, total_additional_fees, grand_total,
                    pickup_date, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([
                    $customerId, $serviceId, $bookingNumber,
                    '[TEST] ' . $serviceData['name'] . ' - Example for monthly graph',
                    $serviceData['name'],
                    'TEST_DATA_DO_NOT_DELETE',
                    'completed', 'paid',
                    $totalAmount, $laborFee, $pickupFee, $deliveryFee, $gasFee, $travelFee,
                    $totalAdditionalFees, $grandTotal, $completedDate,
                    $completedDate . ' 09:00:00',
                    $completedDate . ' 16:00:00'
                ]);
                
                if ($result) {
                    $recordsCreated++;
                    if ($i === 0) { // Show first booking of each month
                        echo "<div class='booking-item'>✓ {$monthNames[$month-1]}: Created {$bookingsPerMonth} bookings (~₱" . number_format($grandTotal * $bookingsPerMonth, 0) . ")</div>";
                    }
                }
            }
        }
        
        echo "<p style='color: #28a745; font-weight: bold; margin-top: 10px;'>✓ Completed year {$year} - Created " . (12 * 6) . " bookings</p>";
        echo "</div>";
    }
    
    $db->commit();
    
    echo "<div class='success'>
            <h2 style='color: #155724; margin: 0 0 15px 0;'>✅ Success!</h2>
            <p style='color: #155724; font-size: 16px; margin: 0;'>
                Created <strong>{$recordsCreated} example bookings</strong> for years 2024-2025<br>
                All records marked with 'TEST_DATA_DO_NOT_DELETE' for easy removal
            </p>
          </div>";
    
    echo "<div style='background: #d1ecf1; border: 2px solid #17a2b8; padding: 20px; border-radius: 10px; margin: 20px 0;'>
            <h3 style='color: #0c5460; margin: 0 0 15px 0;'>📊 What You'll See:</h3>
            <ul style='color: #0c5460; line-height: 1.8;'>
                <li><strong>Year 2024:</strong> ~72 bookings, Total Revenue ~₱500,000-600,000</li>
                <li><strong>Year 2025:</strong> ~72 bookings, Total Revenue ~₱500,000-600,000</li>
                <li><strong>Monthly Graph:</strong> Shows 12 months with varied data points</li>
                <li><strong>Line Trends:</strong> Revenue, Profit, and Expenses visible</li>
            </ul>
          </div>";
    
    echo "<div style='text-align: center; margin-top: 30px;'>
            <a href='../admin/reports' class='btn'>📊 View Reports Dashboard</a>
            <a href='../admin/reports/2024' class='btn'>📅 View Year 2024</a>
            <a href='../admin/reports/2025' class='btn'>📅 View Year 2025</a>
            <a href='remove_test_data.php' class='btn btn-danger'>🗑️ Remove Test Data</a>
          </div>";
    
    echo "</div></body></html>";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; margin: 20px; border-radius: 10px;'>
            <h2 style='color: #721c24;'>✗ Error!</h2>
            <p style='color: #721c24;'>Failed to create data: " . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}
?>

