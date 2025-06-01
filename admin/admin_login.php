<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Egyszerű, fix admin felhasználó/jelszó
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = "Hibás felhasználónév vagy jelszó!";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin bejelentkezés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .login-box { background: #fff; padding: 30px; margin: 100px auto; width: 350px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .login-box h2 { margin-bottom: 20px; }
        .login-box input { width: 100%; padding: 8px; margin-bottom: 10px; }
        .login-box button { width: 100%; padding: 10px; border: none; border-radius: 4px; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box p-4">
        <h2 class="text-center">Admin bejelentkezés</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="post">
            <input type="text" class="form-control shadow-none" name="username" placeholder="Felhasználónév" required>
            <input type="password" class="form-control shadow-none" name="password" placeholder="Jelszó" required>
            <button type="submit" class="btn btn-dark shadow-none">Bejelentkezés</button>
        </form>
    </div>
</body>
</html>