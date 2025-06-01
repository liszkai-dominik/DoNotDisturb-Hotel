<?php
include 'admin_layout.php';
$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

// Státusz módosítása
if (isset($_POST['change_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_bookings.php");
    exit;
}

// Foglalás törlése (GET paraméterrel)
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM bookings WHERE id=$del_id");
    header("Location: admin_bookings.php");
    exit;
}

// Foglalások lekérdezése
$result = $conn->query("
    SELECT b.id, u.name AS user_name, u.email, r.name AS room_name, b.start_date, b.end_date, b.status
    FROM bookings b
    JOIN users u ON b.user_email = u.email
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.start_date DESC
");
?>
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; }
    .container { background: #fff; padding: 30px; margin: 40px auto; width: 90%; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border-bottom: 1px solid #ddd; }
    th { background: #f0f0f0; }
    img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    .actions a { margin-right: 10px; }
</style>
<div class="container">
    <h2>Foglalások kezelése</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Felhasználó</th>
                <th>Email</th>
                <th>Szoba</th>
                <th>Érkezés</th>
                <th>Távozás</th>
                <th>Státusz</th>
                <th>Művelet</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['room_name']) ?></td>
                <td><?= htmlspecialchars($row['start_date']) ?></td>
                <td><?= htmlspecialchars($row['end_date']) ?></td>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                        <select name="status" class="form-select form-select-sm d-inline w-auto">
                            <?php
                            $statuses = ['aktív', 'lemondva', 'lezárva'];
                            foreach ($statuses as $status) {
                                $selected = ($row['status'] == $status) ? 'selected' : '';
                                echo "<option value=\"$status\" $selected>$status</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="change_status" class="btn btn-sm btn-outline-primary">Mentés</button>
                    </form>
                </td>
                <td>
                    <a href="admin_bookings.php?delete=<?= $row['id'] ?>" onclick="return confirm('Biztosan törlöd ezt a foglalást?')" class="btn btn-sm btn-outline-danger">Törlés</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</main>
</div>
</div>
</body>
</html>