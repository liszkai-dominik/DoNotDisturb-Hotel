<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hash);
    if ($stmt->fetch() && password_verify($password, $hash)) {
        $_SESSION['user'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Hibás e-mail vagy jelszó!";
        header("Location: index.php#loginModal");
        exit;
    }
}

$_SESSION['success_message'] = "Sikeres regisztráció! Üdv, $name!";
header("Location: index.php");
exit;
?>