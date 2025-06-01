<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
$booking_id = intval($_POST['booking_id']);
$rating = intval($_POST['rating']);
$content = trim($_POST['content']);

// Lekérjük a foglaláshoz tartozó szoba ID-t és a felhasználó nevét
$stmt = $conn->prepare("SELECT b.room_id, u.name FROM bookings b JOIN users u ON b.user_email = u.email WHERE b.id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($room_id, $name);
$stmt->fetch();
$stmt->close();

// Ellenőrizzük, hogy már van-e vélemény ehhez a foglaláshoz
$stmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE booking_id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    $_SESSION['success_message'] = "Ehhez a foglaláshoz már írtál véleményt!";
    header("Location: profile_bookings.php");
    exit;
}

// Beszúrjuk a véleményt
$stmt = $conn->prepare("INSERT INTO reviews (name, content, rating, room_id, booking_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiii", $name, $content, $rating, $room_id, $booking_id);
$stmt->execute();
$stmt->close();

$_SESSION['success_message'] = "Véleményed mentve!";
header("Location: profile_bookings.php");
exit;
?>