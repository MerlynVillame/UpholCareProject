<?php
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(ROOT . DS . 'database' . DS . 'migrate_logistics_v2.sql');
    
    // Split SQL into individual queries if needed, though exec() handles some cases
    // For safety with multiple statements, split by semicolon
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $db->exec($query);
        }
    }
    
    echo "Migration successful\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
