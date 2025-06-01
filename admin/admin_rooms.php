<?php include 'admin_layout.php'; ?>

<?php
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = intval($_POST['price']);
    $features = trim($_POST['features']);
    $facilities = trim($_POST['facilities']);
    $guests = trim($_POST['guests']);
    $stars = intval($_POST['stars']);
    $img_path = null;
    $available_from = $_POST['available_from'];
    $available_to = $_POST['available_to'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $img_path = 'uploads/' . $img_name;
        move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img_path);
    } else {
        $img_path = null;
    }
    $stmt = $conn->prepare("INSERT INTO rooms (name, description, price, image, features, facilities, guests, stars, available_from, available_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssiss", $name, $description, $price, $img_path, $features, $facilities, $guests, $stars, $available_from, $available_to);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_rooms.php");
    exit;
}

$result = $conn->query("SELECT * FROM rooms ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szobák kezelése</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { background: #fff; padding: 30px; margin: 40px auto; width: 90%; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }
        .actions a { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Új szoba hozzáadása</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="name" class="form-control shadow-none" placeholder="Szoba neve" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="number" name="price" class="form-control shadow-none" placeholder="Ár (Ft/éj)" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="features" placeholder="Jellemzők (pl. 2 szoba,1 fürdőszoba,1 erkély,3 kanapé)" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="file" name="image" class="form-control shadow-none" accept="image/*">
                    </div>
                    <div class="col-md-12 ps-0 mb-3">
                        <textarea name="description" placeholder="Leírás" class="form-control shadow-none" required></textarea>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="facilities" placeholder="Felszereltség (pl. Wifi,Televízió,Klíma)" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="guests" placeholder="Vendégek (pl. 5 Felnőtt,4 Gyerek)" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <label class="form-label">Elérhetőség kezdete</label>
                        <input type="date" name="available_from" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <label class="form-label">Elérhetőség vége</label>
                        <input type="date" name="available_to" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="number" name="stars" min="1" max="5" placeholder="Csillagok száma (1-5)" class="form-control shadow-none" required>
                    </div>
                </div>
            </div>
            <div class="my-1">
                <button type="submit" class="btn btn-dark shadow-none">Hozzáadás</button>
            </div>
        </form>
        <h3 class="mt-4">Szobák listája</h3>
        <table>
            <tr>
                <th>Kép</th>
                <th>Név</th>
                <th>Leírás</th>
                <th>Ár (Ft/éj)</th>
                <th>Művelet</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Szobakép">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                <td><?= htmlspecialchars($row['price']) ?></td>
                <td class="actions">
                    <a href="edit_room.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Módosítás</a>
                    <a href="delete_room.php?id=<?= $row['id'] ?>" onclick="return confirm('Biztosan törlöd ezt a szobát?')" class="btn btn-sm btn-outline-danger">Törlés</a>
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