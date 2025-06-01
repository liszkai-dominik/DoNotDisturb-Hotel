<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        alert("<?= $_SESSION['success_message'] ?>");
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['login_error'])): ?>
<script>
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
</script>
<div class="alert alert-danger"><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['reg_error'])): ?>
<script>
    var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
</script>
<div class="alert alert-danger"><?= $_SESSION['reg_error']; unset($_SESSION['reg_error']); ?></div>
<?php endif; ?>

<!-- Navigáció -->

<nav class="navbar navbar-expand-lg bg-body-tertiary bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3" href="index.php">Do<span style="color: red;">Not</span>Disturb Hotel</a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        </ul>
        <div class="d-flex">
            <?php if (isset($_SESSION['user'])): 
                $conn = new mysqli('localhost', 'root', '', 'hotel');
                $stmt = $conn->prepare("SELECT name, image FROM users WHERE email=?");
                $stmt->bind_param("s", $_SESSION['user']);
                $stmt->execute();
                $stmt->bind_result($name, $image);
                $stmt->fetch();
                $stmt->close();
                $conn->close();
            ?>
                <div class="d-flex align-items-center">
                    <?php if ($image): ?>
                        <img src="<?= htmlspecialchars($image) ?>" alt="Profilkép" style="width:40px; height:40px; object-fit:cover; border-radius:50%; margin-right:10px;">
                    <?php endif; ?>
                    <span class="me-2 fw-bold"><?= htmlspecialchars($name) ?></span>
                    <a href="#" class="btn btn-outline-dark shadow-none me-2" data-bs-toggle="offcanvas" data-bs-target="#profileSidebar">Profilom</a>
                    <a href="logout.php" class="btn btn-outline-danger shadow-none">Kijelentkezés</a>
                </div>
            <?php else: ?>
                <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Bejelentkezés
                </button>
                <button type="button" class="btn btn-outline-dark shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                    Regisztráció
                </button>
            <?php endif; ?>
        </div>
        </div>
        </div>
        </div>
    </div>
</nav>

<!-- Bejelentkezés/Regisztráció -->

<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="login_handler.php">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-circle fs-3 me-2"></i>Bejelenkezés
                    </h5>
                    <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">E-mail cím</label>
                        <input type="email" name="email" class="form-control shadow-none" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jelszó</label>
                        <input type="password" name="password" class="form-control shadow-none" required>
                    </div>
                    <div class="d-flex align-items-center justify-content-left mb-2">
                        <button type="submit" class="btn btn-dark shadow-none">BEJELENTKEZÉS</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="register_handler.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-lines-fill fs-3 me-2"></i></i>Regisztráció
                    </h5>
                    <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span class="badge rounded-pill text-bg-light text-dark mb-3 text-wrap lh-base">
                        Figyelem, az adataid meg kell, hogy egyezzenek a hivatalos dokumentumodon lévőkkel (személyigazolvány, útlevél, vezetői engedély, stb.) ugyanis erre szükség lesz a helyszínen történő bejelentkezésnél!
                    </span>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Név</label>
                                <input type="text" name="name" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Telefonszám</label>
                                <input type="number" name="phone" class="form-control shadow-none">
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Kép</label>
                                <input type="file" name="image" class="form-control shadow-none">
                            </div>
                            <div class="col-md-12 ps-0 mb-3">
                                <label class="form-label">Lakcím</label>
                                <textarea name="address" class="form-control shadow-none" rows="1"></textarea>
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Pin kód</label>
                                <input type="number" name="pin" class="form-control shadow-none">
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Születési idő</label>
                                <input type="date" name="birthdate" class="form-control shadow-none">
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Jelszó</label>
                                <input type="password" name="password" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Jelszó megerősítése</label>
                                <input type="password" name="password2" class="form-control shadow-none" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-center my-1">
                        <button type="submit" class="btn btn-dark shadow-none">REGISZTRÁCIÓ</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Profil oldalsáv -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="profileSidebar" aria-labelledby="profileSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="profileSidebarLabel">Profilom</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Bezárás"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav flex-column mb-3">
      <a href="profile_data.php" class="btn btn-dark shadow-none py-2 mb-2 w-100">Adataim</a>
      <a href="profile_bookings.php" class="btn btn-dark shadow-none py-2 w-100">Foglalásaim</a>
    </ul>
    <a href="logout.php" class="btn btn-outline-danger w-100">Kijelentkezés</a>
  </div>
</div>