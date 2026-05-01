<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Navbar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand ps-3 pe-5" href="index.php">Kost Ibu Siti</a>

    <!-- Hamburger Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="kamar.php">Kamar</a></li>
        <li class="nav-item"><a class="nav-link" href="fasilitas.php">Fasilitas</a></li>
        <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
        <li class="nav-item"><a class="nav-link" href="kontrak.php">Kontrak</a></li>
        <li class="nav-item"><a class="nav-link" href="pembayaran.php">Pembayaran</a></li>
        <li class="nav-item"><a class="nav-link" href="keluhan.php">Keluhan</a></li>
        <li class="nav-item"><a class="nav-link" href="pengunjung.php">Pengunjung</a></li>
        <li class="nav-item"><a class="nav-link" href="booking.php">Booking</a></li>
      </ul>

      <!-- Login Status -->
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item">
            <span class="nav-link">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Akun
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="ganti_password.php">Ganti Password</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link text-success" href="login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
