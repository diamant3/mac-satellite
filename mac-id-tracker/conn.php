<?php

$servername = "localhost";
$dbname = "id20077436_mac_db";
$username = "id20077436_mac_admin";
$password = "a9hfmENmJZDzzE/+";

date_default_timezone_set('Asia/Manila');
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>