<?php

session_start();
if (!isset($_SESSION['user'])) {
    $_SESSION['login_required'] = "Foglalás csak bejelentkezve lehetséges!";
    header("Location: index.php");
    exit;
}
$conn = new mysqli('localhost', 'root', '', 'hotel');
$room_id = intval($_GET['room_id']);
$stmt = $conn->prepare("SELECT name, available_from, available_to FROM rooms WHERE id=?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$stmt->bind_result($room_name, $from, $to);
$stmt->fetch();
$stmt->close();

// Lekérjük az összes foglalást időrendben
$bookings = [];
$book_stmt = $conn->prepare("SELECT start_date, end_date FROM bookings WHERE room_id=? ORDER BY start_date ASC");
$book_stmt->bind_param("i", $room_id);
$book_stmt->execute();
$book_stmt->bind_result($b_start, $b_end);
while ($book_stmt->fetch()) {
    $bookings[] = ['start' => $b_start, 'end' => $b_end];
}
$book_stmt->close();

// Szabad időszakok számítása
$free_periods = [];
$current_start = $from;

foreach ($bookings as $b) {
    if ($current_start < $b['start']) {
        $free_periods[] = ['from' => $current_start, 'to' => date('Y-m-d', strtotime($b['start'] . ' -1 day'))];
    }
    $current_start = date('Y-m-d', strtotime($b['end'] . ' +1 day'));
}
if ($current_start <= $to) {
    $free_periods[] = ['from' => $current_start, 'to' => $to];
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Foglalás</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Foglalás: <?= htmlspecialchars($room_name) ?></h2>
    <div class="alert alert-info">
        <b>Szabad időszakok:</b><br>
        <?php if (empty($free_periods)): ?>
            <span class="text-danger">Jelenleg nincs szabad időszak ehhez a szobához.</span>
        <?php else: ?>
            <?php foreach ($free_periods as $fp): ?>
                <?= htmlspecialchars($fp['from']) ?> &rarr; <?= htmlspecialchars($fp['to']) ?><br>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if (empty($free_periods)): ?>
        <div class="alert alert-danger">Ebben az időszakban már nincs szabad hely ehhez a szobához!</div>
    <?php else: ?>
    <form method="post" action="booking_handler.php">
        <input type="hidden" name="room_id" value="<?= $room_id ?>">
        <div class="row">
            <div class="col-lg-6">
                <label>Érkezés:</label>
                <input type="date" name="start_date" min="<?= $free_periods[0]['from'] ?>" max="<?= $free_periods[count($free_periods)-1]['to'] ?>" required class="form-control shadow-none">
            </div>
            <div class="col-lg-6">
                <label>Távozás:</label>
                <input type="date" name="end_date" min="<?= $free_periods[0]['from'] ?>" max="<?= $free_periods[count($free_periods)-1]['to'] ?>" required class="form-control shadow-none">
            </div>
        </div>
        <div class="small text-muted mt-2">
            * Csak a fenti szabad időszakokban foglalhatsz!
        </div>
        <button type="submit" class="btn btn-dark shadow-none mt-2">Foglalás</button>
        <a href="index.php" class="btn btn-secondary mt-2">Mégse</a>
    </form>
    <?php endif; ?>
</div>
</body>
</html>