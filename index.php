<?php
session_start();
if (isset($_SESSION['login_required'])): ?>
    <div class="alert alert-warning text-center">
        <?= $_SESSION['login_required']; unset($_SESSION['login_required']); ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoNotDisturb Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <?php require('inc/links.php'); ?>
    <link rel="stylesheet" href="css/common.css"/>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>




<?php
$conn = new mysqli('localhost', 'root', '', 'hotel');

// Felszereltségek kigyűjtése minden szobából
$facilities_res = $conn->query("SELECT facilities FROM rooms");
$all_facilities = [];
while ($row = $facilities_res->fetch_assoc()) {
    foreach (explode(',', $row['facilities']) as $f) {
        $f = trim($f);
        if ($f && !in_array($f, $all_facilities)) $all_facilities[] = $f;
    }
}

// Szűrés feldolgozása
$where = [];
$params = [];
$types = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dátum szűrés
    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $where[] = "(available_from <= ? AND available_to >= ?)";
        $params[] = $_POST['start_date'];
        $params[] = $_POST['end_date'];
        $types .= 'ss';
    }
    // Felnőttek szűrés
    if (!empty($_POST['adults'])) {
        $where[] = "(CAST(SUBSTRING_INDEX(guests, ' Felnőtt', 1) AS UNSIGNED) >= ?)";
        $params[] = intval($_POST['adults']);
        $types .= 'i';
    }
    // Gyerekek szűrés
    if (isset($_POST['children'])) {
        // Ha van "Gyerek" a guests-ben, akkor ellenőrizzük, különben átmegy
        $where[] = "(IF(guests LIKE '%,%', CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(guests, ',', -1), ' Gyerek', 1) AS UNSIGNED) >= ?, 1))";
        $params[] = intval($_POST['children']);
        $types .= 'i';
    }
    // Felszereltség szűrés
    if (!empty($_POST['facilities'])) {
        foreach ($_POST['facilities'] as $f) {
            $where[] = "facilities LIKE ?";
            $params[] = "%$f%";
            $types .= 's';
        }
    }
}

$sql = "SELECT * FROM rooms";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$rooms = $stmt->get_result();
?>

<div class="container">
    <div class="row">

        <!-- Hotel ismertető -->
        <div class="container my-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="fw-bold mb-3">Üdvözlünk a Do<span style="color: red;">Not</span>Disturb Hotelben!</h1>
                    <p class="lead">
                        Fedezd fel szállodánkat, ahol a kényelem, a nyugalom és a modern elegancia találkozik! 
                        Tágas, jól felszerelt szobáinkkal, családbarát szolgáltatásainkkal és prémium wellness részlegünkkel garantáljuk a tökéletes kikapcsolódást minden vendégünk számára.
                    </p>
                    <ul>
                        <li>Ingyenes Wi-Fi és parkolás</li>
                        <li>Wellness és fitnesz részleg</li>
                        <li>Gyerekbarát szolgáltatások</li>
                        <li>Panorámás étterem és bár</li>
                        <li>24 órás recepció</li>
                    </ul>
                    <p>
                        Foglalj nálunk, és tapasztald meg a valódi vendégszeretetet!
                    </p>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="kepek/bemutato/intro.jpg" alt="Hotel" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>

        <h2 class="mt-5 pt-4 mb-4 text-center fw-bold">SZOBÁINK</h2>

        <!-- Szűrő -->
        <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 px-lg-0">
            <form method="post">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                    <div class="container-fluid flex-lg-column align-items-stretch">
                        <h4 class="mt-2">SZŰRŐK</h4>
                        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">ELÉRHETŐSÉG ELLENŐRZÉSE</h5>
                                <label class="form-label">Érkezés</label>
                                <input type="date" class="form-control shadow-none mb-3" name="start_date" value="<?= isset($_POST['start_date']) ? $_POST['start_date'] : '' ?>">
                                <label class="form-label">Távozás</label>
                                <input type="date" class="form-control shadow-none" name="end_date" value="<?= isset($_POST['end_date']) ? $_POST['end_date'] : '' ?>">
                            </div>
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">FELSZERELTSÉG</h5>
                                <?php foreach ($all_facilities as $i => $f): ?>
                                    <div class="mb-2">
                                        <input type="checkbox" id="f<?= $i ?>" name="facilities[]" value="<?= htmlspecialchars($f) ?>" class="form-check-input shadow-none me-1"
                                            <?= (isset($_POST['facilities']) && in_array($f, $_POST['facilities'])) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="f<?= $i ?>"><?= htmlspecialchars($f) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">VENDÉGEK</h5>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <label class="form-label">Felnőttek</label>
                                        <input type="number" class="form-control shadow-none" name="adults" min="1" value="<?= isset($_POST['adults']) ? $_POST['adults'] : 1 ?>">
                                    </div>
                                    <div>
                                        <label class="form-label">Gyerekek</label>
                                        <input type="number" class="form-control shadow-none" name="children" min="0" value="<?= isset($_POST['children']) ? $_POST['children'] : 0 ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Szűrés</button>
                        </div>
                    </div>
                </nav>
            </form>
        </div>

        <!-- Szobák -->
        <div class="col-lg-9 col-md-12 px-4">
            <?php if ($rooms->num_rows == 0): ?>
                <div class="alert alert-warning text-center">Nincs találat a megadott feltételekre.</div>
            <?php endif; ?>
            <?php while($room = $rooms->fetch_assoc()): ?>
            <div class="card mb-4 border-0 shadow">
                <div class="row g-0 p-3 align-items-center">
                    <div class="col-md-5 mb-lg-0 mb-md-0 mb-3">
                        <?php if ($room['image']): ?>
                            <img src="<?= htmlspecialchars($room['image']) ?>" class="img-fluid rounded">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-5 px-lg-3 px-md-3 px-0">
                        <h5 class="mb-3"><?= htmlspecialchars($room['name']) ?></h5>
                        <div class="features mb-3">
                            <h6 class="mb-1">Jellemzők</h6>
                            <?php foreach (explode(',', $room['features']) as $feature): ?>
                                <span class="badge rounded-pill text-bg-light text-dark text-wrap"><?= htmlspecialchars(trim($feature)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="facilities mb-3">
                            <h6 class="mb-1">Felszereltség</h6>
                            <?php foreach (explode(',', $room['facilities']) as $facility): ?>
                                <span class="badge rounded-pill text-bg-light text-dark text-wrap"><?= htmlspecialchars(trim($facility)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="guests">
                            <h6 class="mb-1">Vendégek</h6>
                            <?php foreach (explode(',', $room['guests']) as $guest): ?>
                                <span class="badge rounded-pill text-bg-light text-dark text-wrap"><?= htmlspecialchars(trim($guest)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-2 mt-lg-0 mt-md-0 mt-4 text-center">
                        <h6 class="mb-4"><?= htmlspecialchars($room['price']) ?> Ft / éjszaka</h6>
                        <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-sm w-100 text-white custom-bg shadow-none">Foglalás</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Beszámolók -->

<h2 class="mt-5 pt-4 mb-4 text-center fw-bold">BESZÁMOLÓK</h2>

<?php
$reviews = $conn->query("SELECT name, content, rating FROM reviews WHERE approved=1 ORDER BY created_at DESC LIMIT 10");
?>

<div class="container mt-5">
    <div class="swiper swiper-testimonials">
        <div class="swiper-wrapper mb-5">
            <?php if ($reviews->num_rows == 0): ?>
                <div class="swiper-slide bg-white p-4 text-center">
                    <span class="text-muted">Még nincs elérhető vélemény</span>
                </div>
            <?php else: ?>
                <?php while($rev = $reviews->fetch_assoc()): ?>
                <div class="swiper-slide bg-white p-4">
                    <div class="prifile d-flex align-items-center mb-3">
                        <img src="kepek/bemutato/star.png" width="30px">
                        <h6 class="m-0 ms-2"><?= htmlspecialchars($rev['name']) ?></h6>
                    </div>
                    <p><?= nl2br(htmlspecialchars($rev['content'])) ?></p>
                    <div class="rating">
                        <?php
                        for ($i = 0; $i < intval($rev['rating']); $i++) {
                            echo '<i class="bi bi-star-fill text-warning"></i>';
                        }
                        for ($i = intval($rev['rating']); $i < 5; $i++) {
                            echo '<i class="bi bi-star text-warning"></i>';
                        }
                        ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<!-- Elérhetőségeink -->

<h2 class="mt-5 pt-4 mb-4 text-center fw-bold">ELÉRHETŐSÉGEINK</h2>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-8 p-4 mb-lg-0 mb-3 bg-white rounded">
            <iframe class="w-100 rounded" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51170.84957580083!2d-4.490466411589414!3d36.71828370291555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd7259c44fdb212d%3A0x6025dc92c9ca32cf!2zTcOhbGFnYSwgU3BhbnlvbG9yc3rDoWc!5e0!3m2!1shu!2shu!4v1747841964349!5m2!1shu!2shu" height="320" loading="lazy"></iframe>
        </div>
        <div class="col-lg-4 col-md-4">
            <div class="bg-white p-4 rounded mb-4">
                <h5>Hívj fel</h5>
                <a href="tel: +36 30 854 9874" class="d-inline-block mb-2 text-decoration-none text-dark"><i class="bi bi-telephone-fill"></i>+36 30 854 9874</a>
                <br>
                <a href="tel: +36 30 854 9874" class="d-inline-block  text-decoration-none text-dark"><i class="bi bi-telephone-fill"></i>+36 30 854 9874</a>
            </div>
            <div class="bg-white p-4 rounded mb-4">
                <h5>Kövess be</h5>
                <a href="#" class="d-inline-block mb-3">
                    <span class="badge bg-light text-dark fs-6 p-2">
                        <i class="bi bi-twitter-x me-1"></i>Twitter - X
                    </span>
                </a>
                <br>
                <a href="#" class="d-inline-block mb-3">
                    <span class="badge bg-light text-dark fs-6 p-2">
                        <i class="bi bi-facebook me-1"></i>Facebook
                    </span>
                </a>
                <br>
                <a href="#" class="d-inline-block mb-3">
                    <span class="badge bg-light text-dark fs-6 p-2">
                        <i class="bi bi-instagram me-1"></i>Instagram
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<h6 class="text-center bg-dark text-white p-3 m-0">Dizájn és Fejlesztés >>> Fatura Brigita / Liszkai Dominik</h6>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".swiper-testimonials", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        loop: true,
        slidesPerView: 3,
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false,
        },
        pagination: {
            el: ".swiper-pagination",
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
            },
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        }
    });
</script>
</body>
</html>