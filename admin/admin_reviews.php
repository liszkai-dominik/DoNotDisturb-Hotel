<?php
include 'admin_layout.php';
$conn = new mysqli('localhost', 'root', '', 'hotel');
if ($conn->connect_error) die("Adatbázis hiba!");

// Engedélyezés/elutasítás
if (isset($_POST['review_id'], $_POST['action'])) {
    $id = intval($_POST['review_id']);
    $approved = ($_POST['action'] === 'approve') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE reviews SET approved=? WHERE id=?");
    $stmt->bind_param("ii", $approved, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_reviews.php");
    exit;
}

$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
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
    <h2>Vélemények kezelése</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Név</th>
                <th>Szöveg</th>
                <th>Értékelés</th>
                <th>Állapot</th>
                <th>Művelet</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
                <td><?= htmlspecialchars($row['rating']) ?></td>
                <td>
                    <?php if ($row['approved']): ?>
                        <span class="badge bg-success">Engedélyezve</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Függőben</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                        <?php if (!$row['approved']): ?>
                            <button name="action" value="approve" class="btn btn-sm btn-success">Engedélyez</button>
                        <?php endif; ?>
                        <button name="action" value="reject" class="btn btn-sm btn-danger">Elutasít</button>
                    </form>
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