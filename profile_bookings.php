<?php if (isset($_SESSION['success_message'])): ?>
    <script>alert("<?= $_SESSION['success_message'] ?>");</script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
$stmt = $conn->prepare("
    SELECT b.id, r.name, b.start_date, b.end_date, b.status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_email=?
    ORDER BY b.start_date DESC
");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($id, $room_name, $start, $end, $status);
$bookings = [];
while ($stmt->fetch()) {
    // Ellenőrizzük, hogy van-e már vélemény ehhez a foglaláshoz
    $reviewed = false;
    $conn2 = new mysqli('localhost', 'root', '', 'hotel');
    $stmt2 = $conn2->prepare("SELECT COUNT(*) FROM reviews WHERE booking_id=?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->bind_result($review_count);
    $stmt2->fetch();
    $stmt2->close();
    $conn2->close();
    if ($review_count > 0) $reviewed = true;

    $bookings[] = [
        'id'=>$id,
        'room'=>$room_name,
        'start'=>$start,
        'end'=>$end,
        'status'=>$status,
        'reviewed'=>$reviewed
    ];
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Foglalásaim</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php require('inc/header.php'); ?>
    <div class="container mt-4">
        <h2>Foglalásaim</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Szoba</th>
                    <th>Érkezés</th>
                    <th>Távozás</th>
                    <th>Státusz</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['room']) ?></td>
                    <td><?= htmlspecialchars($b['start']) ?></td>
                    <td><?= htmlspecialchars($b['end']) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                    <td>
                        <?php if (strtotime($b['end']) < time()): ?>
                            <?php if ($b['reviewed']): ?>
                                <?php
                                // Lekérdezzük az adott foglaláshoz tartozó review approved státuszát
                                $conn3 = new mysqli('localhost', 'root', '', 'hotel');
                                $stmt3 = $conn3->prepare("SELECT approved FROM reviews WHERE booking_id=?");
                                $stmt3->bind_param("i", $b['id']);
                                $stmt3->execute();
                                $stmt3->bind_result($approved);
                                $stmt3->fetch();
                                $stmt3->close();
                                $conn3->close();
                                ?>
                                <?php if ($approved): ?>
                                    <span class="badge bg-success">Vélemény engedélyezve</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Vélemény függőben</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewModal" data-booking="<?= $b['id'] ?>">Vélemény írása</button>
                            <?php endif; ?>
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-dark shadow-none">Vissza a főoldalra</a>
    </div>

    <!-- Vélemény írása modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" action="save_review.php">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Vélemény írása</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="booking_id" id="bookingIdInput">
                <div class="mb-3">
                    <label class="form-label">Értékelés (1-5)</label>
                    <input type="number" name="rating" min="1" max="5" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vélemény</label>
                    <textarea name="content" class="form-control" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Mentés</button>
              </div>
            </div>
        </form>
      </div>
    </div>
    
    <script>
    // Booking ID átadása a modalnak
    var reviewModal = document.getElementById('reviewModal');
    reviewModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var bookingId = button.getAttribute('data-booking');
      document.getElementById('bookingIdInput').value = bookingId;
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>