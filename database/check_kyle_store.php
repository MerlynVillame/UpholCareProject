<?php
/**
 * Check Kyle Store Display
 * This script checks if "kyle store" appears in the store ratings query
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

$db = Database::getInstance()->getConnection();

echo "<h2>Check Kyle Store in Store Ratings</h2>";

// 1. Find the store
echo "<h3>1. Finding 'kyle store'...</h3>";
$stmt = $db->prepare("SELECT * FROM store_locations WHERE store_name LIKE ? AND status = 'active'");
$stmt->execute(['%kyle%']);
$stores = $stmt->fetchAll();

if (empty($stores)) {
    echo "❌ No store found with 'kyle' in the name<br>";
    echo "<p>Let's check all stores:</p>";
    $stmt = $db->query("SELECT id, store_name, address, rating, status FROM store_locations WHERE status = 'active' LIMIT 10");
    $allStores = $stmt->fetchAll();
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Store Name</th><th>Address</th><th>Rating</th><th>Status</th></tr>";
    foreach ($allStores as $store) {
        echo "<tr>";
        echo "<td>" . $store['id'] . "</td>";
        echo "<td>" . htmlspecialchars($store['store_name']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($store['address'], 0, 50)) . "</td>";
        echo "<td>" . ($store['rating'] ?? 'NULL') . "</td>";
        echo "<td>" . $store['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    foreach ($stores as $store) {
        echo "✅ Found store: <strong>" . htmlspecialchars($store['store_name']) . "</strong><br>";
        echo "ID: " . $store['id'] . "<br>";
        echo "Address: " . htmlspecialchars($store['address']) . "<br>";
        echo "Rating (store_locations): " . ($store['rating'] ?? 'NULL') . "<br>";
        echo "Status: " . $store['status'] . "<br>";
        echo "Latitude: " . ($store['latitude'] ?? 'NULL') . "<br>";
        echo "Longitude: " . ($store['longitude'] ?? 'NULL') . "<br><br>";
        
        $storeId = $store['id'];
        
        // 2. Check ratings for this store
        echo "<h3>2. Checking ratings for this store...</h3>";
        $stmt = $db->prepare("SELECT COUNT(*) as cnt, AVG(rating) as avg_rating FROM store_ratings WHERE store_id = ? AND status = 'active'");
        $stmt->execute([$storeId]);
        $ratingData = $stmt->fetch();
        
        echo "Total ratings: " . $ratingData['cnt'] . "<br>";
        echo "Average rating: " . ($ratingData['avg_rating'] ?? 'NULL') . "<br><br>";
        
        // 3. Show individual ratings
        if ($ratingData['cnt'] > 0) {
            $stmt = $db->prepare("SELECT * FROM store_ratings WHERE store_id = ? AND status = 'active' ORDER BY created_at DESC");
            $stmt->execute([$storeId]);
            $ratings = $stmt->fetchAll();
            
            echo "<h4>Individual Ratings:</h4>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>User ID</th><th>Rating</th><th>Review</th><th>Created At</th></tr>";
            foreach ($ratings as $rating) {
                echo "<tr>";
                echo "<td>" . $rating['id'] . "</td>";
                echo "<td>" . $rating['user_id'] . "</td>";
                echo "<td>" . $rating['rating'] . "/5.0</td>";
                echo "<td>" . htmlspecialchars(substr($rating['review_text'] ?? '', 0, 50)) . "</td>";
                echo "<td>" . $rating['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table><br>";
        }
        
        // 4. Test the exact query used in ControlPanelController
        echo "<h3>3. Testing ControlPanelController Query...</h3>";
        $sql = "
            SELECT 
                sl.id,
                sl.store_name,
                sl.address,
                COALESCE(AVG(sr.rating), sl.rating, 0) as rating,
                sl.status as store_status,
                COUNT(DISTINCT sr.id) as total_ratings,
                COALESCE(AVG(sr.rating), sl.rating, 0) as avg_rating,
                COALESCE(MIN(sr.rating), 0) as min_rating,
                COALESCE(MAX(sr.rating), 0) as max_rating,
                sl.rating as store_rating_field
            FROM store_locations sl
            LEFT JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
            WHERE sl.status = 'active'
                AND sl.id = ?
            GROUP BY sl.id, sl.store_name, sl.address, sl.rating, sl.status
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$storeId]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "✅ Store found in query result:<br>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            foreach ($result as $key => $value) {
                echo "<tr><td>" . $key . "</td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "❌ Store NOT found in query result<br>";
        }
    }
}

// 5. Test the full query (all stores)
echo "<h3>4. Testing Full Query (All Stores)...</h3>";
$sql = "
    SELECT 
        sl.id,
        sl.store_name,
        sl.address,
        COALESCE(AVG(sr.rating), sl.rating, 0) as rating,
        COUNT(DISTINCT sr.id) as total_ratings,
        COALESCE(AVG(sr.rating), sl.rating, 0) as avg_rating
    FROM store_locations sl
    LEFT JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
    WHERE sl.status = 'active'
    GROUP BY sl.id, sl.store_name, sl.address, sl.rating
    ORDER BY COALESCE(AVG(sr.rating), sl.rating, 0) ASC
    LIMIT 10
";
$stmt = $db->query($sql);
$allStores = $stmt->fetchAll();

echo "Found " . count($allStores) . " stores in query:<br>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Store Name</th><th>Rating</th><th>Total Ratings</th><th>Avg Rating</th></tr>";
foreach ($allStores as $store) {
    $highlight = (stripos($store['store_name'], 'kyle') !== false) ? 'style="background-color: yellow;"' : '';
    echo "<tr $highlight>";
    echo "<td>" . $store['id'] . "</td>";
    echo "<td>" . htmlspecialchars($store['store_name']) . "</td>";
    echo "<td>" . number_format($store['rating'], 2) . "</td>";
    echo "<td>" . $store['total_ratings'] . "</td>";
    echo "<td>" . number_format($store['avg_rating'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><a href='../control-panel/storeRatings'>Go to Store Ratings Page</a></p>";
?>

