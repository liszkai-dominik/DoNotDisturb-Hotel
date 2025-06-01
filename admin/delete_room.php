<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
if (isset($_GET['id'])) {
    $conn = new mysqli('localhost', 'root', '', 'hotel');
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id=?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
header('Location: admin_rooms.php');
exit;
?>