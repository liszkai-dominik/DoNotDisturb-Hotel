<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $pin = trim($_POST['pin']);
    $birthdate = $_POST['birthdate'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // Jelszó egyezés ellenőrzése
    if ($password !== $password2) {
        $_SESSION['reg_error'] = "A jelszavak nem egyeznek!";
        header("Location: index.php#registerModal");
        exit;
    }

    // Kép feltöltése
    $img_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $img_path = 'uploads/' . $img_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $img_path);
    }

    // E-mail egyediségének ellenőrzése
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $_SESSION['reg_error'] = "Ez az e-mail cím már foglalt!";
        header("Location: index.php#registerModal");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, pin, birthdate, password, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $phone, $address, $pin, $birthdate, $hash, $img_path);

    if ($stmt->execute()) {
        $_SESSION['user'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['reg_error'] = "Hiba történt a regisztráció során!";
        header("Location: index.php#registerModal");
        exit;
    }
}

$_SESSION['success_message'] = "Sikeres regisztráció! Üdv, $name!";
header("Location: index.php");
exit;
?>