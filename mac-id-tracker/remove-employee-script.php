<?php 

require_once("conn.php");

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT firstname, lastname FROM accounts WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $fetch = $stmt->fetch();

        $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
        $action = "Delete";
        $event = "Account Full Name: {$fetch['firstname']} {$fetch['lastname']}";
        $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
    } catch (PDOException $e) {
        echo "query failed: " . $e->getMessage();
    }

    try {
        $sql = "DELETE FROM accounts WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo "<script>alert(\"Employee Deleted Successfully!\");</script>";
        header('location: remove-employee.php');
    } catch (PDOException $e) {
        echo "query failed: " . $e->getMessage();
    }
}

?>