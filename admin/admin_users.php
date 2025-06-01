
<?php include 'admin_layout.php'; ?>
<?php 
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

$result = $conn->query("SELECT id, name, email, phone, address, pin, birthdate, image, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Felhasználók kezelése</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { background: #fff; padding: 30px; margin: 40px auto; width: 90%; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
        .actions a { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Felhasználók kezelése</h2>
        <table>
            <tr>
                <th>Kép</th>
                <th>Név</th>
                <th>E-mail</th>
                <th>Telefon</th>
                <th>Lakcím</th>
                <th>Pin</th>
                <th>Születési idő</th>
                <th>Regisztráció</th>
                <th>Művelet</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Profilkép">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['pin']) ?></td>
                <td><?= htmlspecialchars($row['birthdate']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td class="actions">
                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Biztosan törlöd ezt a felhasználót?')">Törlés</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    </main>
    </div>
</div>
</body>
</html>