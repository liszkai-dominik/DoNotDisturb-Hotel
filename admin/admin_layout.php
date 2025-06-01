<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin felület</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .sidebar {
            min-height: 100vh;
            background: #222;
            color: #fff;
            padding-top: 30px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 12px 24px;
            border-radius: 0 20px 20px 0;
            margin-bottom: 8px;
        }
        .sidebar a.active, .sidebar a:hover {
            background: black;
            color: #fff;
        }
        .admin-navbar {
            background: black;
            border-bottom: 1px solid #ddd;
        }
        .admin-navbar .navbar-brand {
            font-weight: bold;
        }
        .content-area {
            padding: 30px;
        }
    </style>
</head>
<body>
    <nav class="navbar admin-navbar px-4">
        <span class="navbar-brand text-white">Admin felület</span>
        <div class="ms-auto">
            <a href="admin_logout.php" class="btn btn-outline-danger btn-sm">Kijelentkezés</a>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-2 sidebar">
                <a href="admin_rooms.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_rooms.php'?'active':'' ?>">Szobák</a>
                <a href="admin_users.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_users.php'?'active':'' ?>">Felhasználók</a>
                <a href="admin_bookings.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_bookings.php'?'active':'' ?>">Foglalások</a>
                <a href="admin_reviews.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_reviews.php'?'active':'' ?>">Vélemények</a>
            </aside>
            <main class="col-md-10 content-area">