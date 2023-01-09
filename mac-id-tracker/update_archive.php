<?php

session_start();

require_once("conn.php");

$id = $_POST['id'];
$card = $_POST['card'];
$card = "{$card}_id";

try {
    $query = "SELECT * FROM {$card} WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $status = $row['is_archive'];
    if ($status == 1) {
        $sql = "UPDATE {$card} SET is_archive = 0 WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($card == "blue_id") $card = "blue";
        if ($card == "yellow_id") $card = "yellow";
        if ($card == "white_id") $card = "white";
        try {
            $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
            $action = "Disable Archive";
            $event = "Card ID Number: {$row['id_number']} Type: {$card}";
            $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
        }

        echo $status = 0;
    } else {
        $sql = "UPDATE {$card} SET is_archive = 1 WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($card == "blue_id") $card = "blue";
        if ($card == "yellow_id") $card = "yellow";
        if ($card == "white_id") $card = "white";
        try {
            $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
            $action = "Enable Archive";
            $event = "Card ID Number: {$row['id_number']} Type: {$card}";
            $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
        }

        echo $status = 1;
    }
} catch (PDOException $e) {
    echo "query failed: " . $e->getMessage();
}
?>