<?php
require 'config/config.php';
require 'config/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT booking_type, COUNT(*) as count FROM bookings GROUP BY booking_type");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
