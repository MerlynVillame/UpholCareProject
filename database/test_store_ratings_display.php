<?php
/**
 * Test Store Ratings Display
 * This script helps verify that stores with ratings are being displayed correctly
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

$db = Database::getInstance()->getConnection();

echo "<h2>Store Ratings Display Test</h2>";

// 1. Check if store_ratings table exists
echo "<h3>1. Checking store_ratings table...</h3>";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'store_ratings'");
    if ($stmt->fetch()) {
        echo "✅ store_ratings table exists<br>";
    } else {
        echo "❌ store_ratings table does NOT exist. Please run: <a href='setup_ratings_table_now.php'>setup_ratings_table_now.php</a><br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Count active stores
echo "<h3>2. Active Stores:</h3>";
$stmt = $db->query("SELECT COUNT(*) as cnt FROM store_locations WHERE status = 'active'");
$result = $stmt->fetch();
echo "Total active stores: " . $result['cnt'] . "<br>";

// 3. Count stores with coordinates
$stmt = $db->query("SELECT COUNT(*) as cnt FROM store_locations WHERE status = 'active' AND latitude IS NOT NULL AND longitude IS NOT NULL");
$result = $stmt->fetch();
echo "Active stores with coordinates: " . $result['cnt'] . "<br>";

// 4. Count ratings
echo "<h3>3. Store Ratings:</h3>";
$stmt = $db->query("SELECT COUNT(*) as cnt FROM store_ratings WHERE status = 'active'");
$result = $stmt->fetch();
echo "Total active ratings: " . $result['cnt'] . "<br>";

// 5. Show stores with ratings
echo "<h3>4. Stores with Ratings:</h3>";
$sql = "
    SELECT 
        sl.id,
        sl.store_name,
        sl.address,
        sl.rating as store_rating,
        COUNT(sr.id) as total_ratings,
        AVG(sr.rating) as avg_rating,
        sl.latitude,
        sl.longitude,
        sl.status
    FROM store_locations sl
    LEFT JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
    WHERE sl.status = 'active'
    GROUP BY sl.id, sl.store_name, sl.address, sl.rating, sl.latitude, sl.longitude, sl.status
    ORDER BY total_ratings DESC, sl.store_name ASC
";
$stmt = $db->query($sql);
$stores = $stmt->fetchAll();

if (empty($stores)) {
    echo "❌ No stores found!<br>";
} else {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Store Name</th><th>Store Rating</th><th>Total Ratings</th><th>Avg Rating</th><th>Has Coordinates</th><th>Status</th></tr>";
    foreach ($stores as $store) {
        $hasCoords = ($store['latitude'] && $store['longitude']) ? '✅ Yes' : '❌ No';
        echo "<tr>";
        echo "<td>" . $store['id'] . "</td>";
        echo "<td>" . htmlspecialchars($store['store_name']) . "</td>";
        echo "<td>" . ($store['store_rating'] ?? 'NULL') . "</td>";
        echo "<td>" . $store['total_ratings'] . "</td>";
        echo "<td>" . number_format($store['avg_rating'] ?? 0, 2) . "</td>";
        echo "<td>" . $hasCoords . "</td>";
        echo "<td>" . $store['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 6. Test the exact query used in ControlPanelController
echo "<h3>5. Testing ControlPanelController Query (with coordinates requirement):</h3>";
$sql = "
    SELECT 
        sl.id,
        sl.store_name,
        sl.address,
        COALESCE(sl.rating, 0) as rating,
        COUNT(DISTINCT sr.id) as total_ratings,
        COALESCE(AVG(sr.rating), 0) as avg_rating,
        COALESCE(MIN(sr.rating), 0) as min_rating,
        COALESCE(MAX(sr.rating), 0) as max_rating
    FROM store_locations sl
    LEFT JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
    WHERE sl.status = 'active'
        AND sl.latitude IS NOT NULL 
        AND sl.longitude IS NOT NULL
    GROUP BY sl.id, sl.store_name, sl.address, sl.rating
    ORDER BY 
        CASE WHEN COALESCE(sl.rating, 0) = 0 AND COUNT(DISTINCT sr.id) = 0 THEN 1 ELSE 0 END ASC,
        COALESCE(sl.rating, 0) ASC, 
        COUNT(DISTINCT sr.id) DESC, 
        sl.store_name ASC
";
$stmt = $db->query($sql);
$testStores = $stmt->fetchAll();

if (empty($testStores)) {
    echo "❌ No stores found with this query!<br>";
    echo "<p><strong>Possible issues:</strong></p>";
    echo "<ul>";
    echo "<li>Stores don't have latitude/longitude coordinates</li>";
    echo "<li>Stores are not active</li>";
    echo "<li>No ratings exist</li>";
    echo "</ul>";
} else {
    echo "✅ Found " . count($testStores) . " stores with this query<br>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin-top: 10px;'>";
    echo "<tr><th>Store Name</th><th>Rating</th><th>Total Ratings</th><th>Avg Rating</th><th>Min</th><th>Max</th></tr>";
    foreach ($testStores as $store) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($store['store_name']) . "</td>";
        echo "<td>" . number_format($store['rating'], 2) . "</td>";
        echo "<td>" . $store['total_ratings'] . "</td>";
        echo "<td>" . number_format($store['avg_rating'], 2) . "</td>";
        echo "<td>" . number_format($store['min_rating'], 1) . "</td>";
        echo "<td>" . number_format($store['max_rating'], 1) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 7. Show sample ratings
echo "<h3>6. Sample Ratings:</h3>";
$stmt = $db->query("SELECT sr.*, sl.store_name FROM store_ratings sr LEFT JOIN store_locations sl ON sr.store_id = sl.id WHERE sr.status = 'active' ORDER BY sr.created_at DESC LIMIT 10");
$ratings = $stmt->fetchAll();

if (empty($ratings)) {
    echo "❌ No ratings found in database<br>";
} else {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Store</th><th>Rating</th><th>Review</th><th>Date</th></tr>";
    foreach ($ratings as $rating) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($rating['store_name'] ?? 'Unknown') . "</td>";
        echo "<td>" . $rating['rating'] . "/5.0</td>";
        echo "<td>" . htmlspecialchars(substr($rating['review_text'] ?? '', 0, 50)) . "</td>";
        echo "<td>" . $rating['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p><a href='../control-panel/storeRatings'>Go to Store Ratings Page</a></p>";
?>

