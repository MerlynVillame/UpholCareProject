<?php
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

require_once ROOT . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Columns in bookings table:\n";
    foreach ($columns as $column) {
        echo $column['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
