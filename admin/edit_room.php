<?php
include 'admin_layout.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

$id = intval($_GET['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = intval($_POST['price']);
    $features = trim($_POST['features']);
    $facilities = trim($_POST['facilities']);
    $guests = trim($_POST['guests']);
    $stars = intval($_POST['stars']);
    $available_from = $_POST['available_from'];
    $available_to = $_POST['available_to'];

    // Kép feltöltés (ha van új)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $img_path = 'uploads/' . $img_name;
        move_uploaded_file($_FILES['image']['tmp_name'], '../' . $img_path);
        $stmt = $conn->prepare("UPDATE rooms SET name=?, description=?, price=?, image=?, features=?, facilities=?, guests=?, stars=?, available_from=?, available_to=? WHERE id=?");
        $stmt->bind_param("ssissssissi", $name, $description, $price, $img_path, $features, $facilities, $guests, $stars, $available_from, $available_to, $id);
    } else {
        $stmt = $conn->prepare("UPDATE rooms SET name=?, description=?, price=?, features=?, facilities=?, guests=?, stars=?, available_from=?, available_to=? WHERE id=?");
        $stmt->bind_param("ssissssssi", $name, $description, $price, $features, $facilities, $guests, $stars, $available_from, $available_to, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: admin_rooms.php");
    exit;
}

// Szoba adatainak lekérdezése
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$stmt->close();
?>
<div class="container">
    <h2>Szoba módosítása</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="name" value="<?= htmlspecialchars($room['name']) ?>" class="form-control shadow-none" placeholder="Szoba neve" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="number" name="price" value="<?= htmlspecialchars($room['price']) ?>" class="form-control shadow-none" placeholder="Ár (Ft/éj)" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="features" value="<?= htmlspecialchars($room['features']) ?>" placeholder="Jellemzők (pl. 2 szoba,1 fürdőszoba,1 erkély,3 kanapé)" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="file" name="image" class="form-control shadow-none" accept="image/*">
                        <label for="image">Jelenlegi kép:</label>
                        <?php if ($room['image']): ?>
                            <br><img src="../<?= htmlspecialchars($room['image']) ?>" alt="Szobakép" style="width:80px;height:60px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12 ps-0 mb-3">
                        <textarea name="description" placeholder="Leírás" class="form-control shadow-none" required><?= htmlspecialchars($room['description']) ?></textarea>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="facilities" value="<?= htmlspecialchars($room['facilities']) ?>" placeholder="Felszereltség (pl. Wifi,Televízió,Klíma)" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="text" name="guests" value="<?= htmlspecialchars($room['guests']) ?>" placeholder="Vendégek (pl. 5 Felnőtt,4 Gyerek)" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <label class="form-label">Elérhetőség kezdete</label>
                        <input type="date" name="available_from" value="<?= htmlspecialchars($room['available_from']) ?>" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <label class="form-label">Elérhetőség vége</label>
                        <input type="date" name="available_to" value="<?= htmlspecialchars($room['available_to']) ?>" class="form-control shadow-none" required>
                    </div>
                    <div class="col-md-6 ps-0 mb-3">
                        <input type="number" name="stars" min="1" max="5" value="<?= htmlspecialchars($room['stars']) ?>" placeholder="Csillagok száma (1-5)" class="form-control shadow-none" required>
                    </div>
                </div>
            </div>
            <div class="my-1">
                <button type="submit" class="btn btn-dark shadow-none">Mentés</button>
                <a href="admin_rooms.php" class="btn btn-secondary">Mégse</a>
            </div>
        </form>
</div>
</main>
</div>
</div>
</body>
</html>