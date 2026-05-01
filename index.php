<?php
require "session.php";
require "../koneksi.php";

$queryKamar = mysqli_query($con, "SELECT * FROM kamar");
$jumlahKamar = mysqli_num_rows($queryKamar);

$queryFasilitas = mysqli_query($con, "SELECT * FROM fasilitas");
$jumlahFasilitas = mysqli_num_rows($queryFasilitas);

$queryPenghuni = mysqli_query($con, "SELECT * FROM penghuni");
$jumlahPenghuni = mysqli_num_rows($queryPenghuni);

$queryKontrak = mysqli_query($con, "SELECT * FROM kontrak");  // Perbaikan typo: "kontrak" bukan "kotrak"
$jumlahKontrak = mysqli_num_rows($queryKontrak);

$queryPembayaran = mysqli_query($con, "SELECT * FROM pembayaran");
$jumlahPembayaran = mysqli_num_rows($queryPembayaran);

$queryKeluhan = mysqli_query($con, "SELECT * FROM keluhan");
$jumlahKeluhan = mysqli_num_rows($queryKeluhan);

$queryPengunjung = mysqli_query($con, "SELECT * FROM pengunjung");
$jumlahPengunjung = mysqli_num_rows($queryPengunjung);

$queryBooking = mysqli_query($con, "SELECT * FROM booking");
$jumlahBooking = mysqli_num_rows($queryBooking);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />

    <!-- Bootstrap Icons CDN -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />
</head>
<body>
    <?php require "navbar.php"; ?>

    <div class="container mt-5">

    <p>
        <a href="#" class="text-muted text-decoration-none">
            <i class="bi bi-house-door-fill"></i> Home
        </a>
    </p>
        <!-- Tulisan Halo Admin -->
        <h2>Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

        <!-- Kotak Dashboard -->
        <div class="row g-4 mt-4">
            <!-- Kamar -->
            <div class="col-md-3">
                <div class="p-3 bg-success text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-door-closed-fill fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Kamar</h5>
                            <small><?php echo $jumlahKamar; ?> Kamar</small><br />
                            <a href="kamar.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fasilitas -->
            <div class="col-md-3">
                <div class="p-3 bg-primary text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-tools fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Fasilitas</h5>
                            <small><?php echo $jumlahFasilitas; ?> Fasilitas</small><br />
                            <a href="fasilitas.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penghuni -->
            <div class="col-md-3">
                <div class="p-3 bg-info text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Penghuni</h5>
                            <small><?php echo $jumlahPenghuni; ?> Orang</small><br />
                            <a href="penghuni.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontrak -->
            <div class="col-md-3">
                <div class="p-3 bg-secondary text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text-fill fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Kontrak</h5>
                            <small><?php echo $jumlahKontrak; ?> Kontrak</small><br />
                            <a href="kontrak.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembayaran -->
            <div class="col-md-3">
                <div class="p-3 bg-warning text-dark rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-cash-coin fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Pembayaran</h5>
                            <small><?php echo $jumlahPembayaran; ?> Transaksi</small><br />
                            <a href="pembayaran.php" class="text-dark text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keluhan -->
            <div class="col-md-3">
                <div class="p-3 bg-danger text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Keluhan</h5>
                            <small><?php echo $jumlahKeluhan; ?> Keluhan</small><br />
                            <a href="keluhan.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengunjung -->
            <div class="col-md-3">
                <div class="p-3 bg-dark text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-eye-fill fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Pengunjung</h5>
                            <small><?php echo $jumlahPengunjung; ?> Pengunjung</small><br />
                            <a href="pengunjung.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking -->
            <div class="col-md-3">
                <div class="p-3 bg-primary text-white rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-journal-bookmark-fill fs-1 me-3"></i>
                        <div>
                            <h5 class="mb-0">Booking</h5>
                            <small><?php echo $jumlahBooking; ?> Booking</small><br />
                            <a href="booking.php" class="text-white text-decoration-underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>