<?php

session_start();

include_once("conn.php");

try {
    $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
    $action = "Logout";
    $event = "Account Full Name: {$_SESSION["firstname"]} {$_SESSION["lastname"]}";
    $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
    $conn = null;
    session_destroy();
    header("location: index.php");
} catch (PDOException $e) {
    echo "query failed: " . $e->getMessage();
}

?>