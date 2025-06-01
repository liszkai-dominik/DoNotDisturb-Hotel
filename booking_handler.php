<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
$room_id = intval($_POST['room_id']);
$user_email = $_SESSION['user'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Ellenőrizd, hogy a dátumok helyesek és nincs ütköző foglalás!
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE room_id=? AND (start_date <= ? AND end_date >= ?)");
$stmt->bind_param("iss", $room_id, $end_date, $start_date);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    $_SESSION['booking_error'] = "A kiválasztott időszakra már van foglalás!";
    header("Location: booking.php?room_id=$room_id");
    exit;
}

$stmt = $conn->prepare("INSERT INTO bookings (user_email, room_id, start_date, end_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $user_email, $room_id, $start_date, $end_date);
$stmt->execute();
$stmt->close();

$_SESSION['success_message'] = "Sikeres foglalás!";
header("Location: profile_bookings.php");
exit;
?>