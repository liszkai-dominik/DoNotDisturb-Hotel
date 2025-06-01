<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
$stmt = $conn->prepare("SELECT name, email, phone, address, pin, birthdate, image FROM users WHERE email=?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $address, $pin, $birthdate, $image);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profilom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php require('inc/links.php'); ?>
    <link rel="stylesheet" href="css/common.css"/>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Adataim</h2>

    <div class="row">
        <div class="col-lg-2">
            <?php if ($image): ?>
                <img src="<?= htmlspecialchars($image) ?>" alt="Profilkép" style="width:100px; height:100px; object-fit:cover; border-radius:50%;" class="mb-3">
            <?php endif; ?>
        </div>
        <div class="col-lg-5">
            <p><b>Név:</b> <?= htmlspecialchars($name) ?></p>
            <p><b>E-mail:</b> <?= htmlspecialchars($email) ?></p>
            <p><b>Telefonszám:</b> <?= htmlspecialchars($phone) ?></p>
        </div>
        <div class="col-lg-5">
            <p><b>Lakcím:</b> <?= htmlspecialchars($address) ?></p>
            <p><b>Pin kód:</b> <?= htmlspecialchars($pin) ?></p>
            <p><b>Születési idő:</b> <?= htmlspecialchars($birthdate) ?></p>
        </div>
    </div>

    <a href="index.php" class="btn btn-dark shadow-none">Vissza a főoldalra</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>