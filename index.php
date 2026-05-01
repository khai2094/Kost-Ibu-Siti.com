<?php
session_start();

// Koneksi database
$con = new mysqli("localhost", "root", "", "database_kos");
if ($con->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$msg = '';

if (isset($_POST['submit_keluhan'])) {
    $id_penghuni = trim($_POST['id_penghuni']);
    $tanggal = trim($_POST['tanggal']);
    $deskripsi = trim($_POST['deskripsi']);


    if (empty($id_penghuni) || empty($tanggal) || empty($deskripsi)) {
        $_SESSION['msg'] = '<div class="alert alert-danger">Semua field harus diisi.</div>';
    } else {
        // Escape data input untuk query
        $id_penghuni_esc = $con->real_escape_string($id_penghuni);
        $tanggal_esc = $con->real_escape_string($tanggal);
        $deskripsi_esc = $con->real_escape_string($deskripsi);

       // Prepare insert statement
        $stmt = $con->prepare("INSERT INTO keluhan (id_penghuni, tanggal, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $id_penghuni_esc, $tanggal_esc, $deskripsi_esc);

        if ($stmt->execute()) {
            $_SESSION['msg'] = '<div class="alert alert-success">Keluhan berhasil dikirim. Terima kasih!</div>';
        } else {
            $_SESSION['msg'] = '<div class="alert alert-danger">Gagal menyimpan keluhan.</div>';
        }
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "#");
    exit;
}

if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kost Ibu Siti</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to bottom, #ffffff, #f9f9f9);
      color: #333;
      font-family: 'Poppins', sans-serif;
    }
    .hero {
      background: url('uploads/ChatGPT Image Jun 8, 2025, 02_56_20 PM.png') center/cover no-repeat;
      color: #fff;
      text-align: center;
      padding: 140px 20px 100px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.7);
      position: relative;
      height: 100vh; /* Full viewport height */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: 4rem;
      margin-bottom: 20px;
      animation: fadeInDown 1s ease-out;
    }
    .hero p {
      font-size: 1.5rem;
      margin-bottom: 30px;
      animation: fadeInDown 1.2s ease-out;
    }
    .btn-jelajahi-custom {
      display: inline-block;
      background-color: #1565c0;
      color: white;
      padding: 14px 36px;
      font-size: 1.2rem;
      font-weight: 600;
      border-radius: 50px;
      transition: all 0.3s ease;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }
    .btn-jelajahi-custom .arrow {
      display: inline-block;
      margin-left: 10px;
      transition: transform 0.3s ease;
      font-size: 1.4rem;
    }
    .btn-jelajahi-custom:hover {
      background-color: #0d47a1;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .btn-jelajahi-custom:hover .arrow {
      transform: translateX(8px);
    }
    .section-title h2 {
      color: #00796b;
      font-size: 2.4rem;
      margin-bottom: 40px;
      text-align: center;
      font-family: 'Playfair Display', serif;
    }
    .facilities .card {
      background-color: #e8f5e9;
      border: none;
      padding: 25px;
      border-radius: 15px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .facilities .card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .facilities .bi {
      font-size: 2.5rem;
      color: #43a047;
      margin-bottom: 10px;
    }
    .kamar-section .row {
      justify-content: center;
    }
    .kamar-section img {
      width: 100%;
      max-width: 500px;
      height: 400px;
      object-fit: cover;
      border-radius: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      margin: 0 auto;
      display: block;
      transition: transform 0.2s ease;
    }
    .kamar-section img:hover {
      transform: scale(1.05);
    }
    .category-wrapper {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 60px;
    }
    h1 {
        margin-top: 40px;
        color: #0d6efd;
        font-weight: 700;
        text-align: center;
    }
    .kamar-label {
      text-align: center;
      margin-top: 15px;
      font-weight: 600;
      font-size: 1.2rem;
    }
    .contact-section {
      background-color: #fffde7;
      padding: 60px 20px;
      margin-top: 60px;
    }
    .contact-info {
      display: flex;
      justify-content: center;
      gap: 60px;
      align-items: center;
      flex-wrap: wrap;
    }
    .contact-info div {
      text-align: center;
    }
    .contact-info .bi {
      font-size: 2.2rem;
      color: #f57f17;
      margin-bottom: 5px;
    }
    .contact-line {
      width: 50px;
      height: 3px;
      background-color: #333;
      margin: 10px auto 30px;
    }
    iframe {
      width: 100%;
      height: 400px;
      border-radius: 15px;
      border: none;
    }
    footer {
      background-color: #333;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
    }
    /* Animasi fadeInDown */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-40px); }
      to { opacity: 1; transform: translateY(0); }
    }
     /* Tombol keluhan kecil fixed */
#btnKeluhanWrapper {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  flex-direction:row-reverse;
  align-items: center;
  gap: 6px;
  z-index: 1100;
}

#btnKeluhan {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  padding: 0;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.keluhan-label {
  background-color: #f8d7da;
  color: #842029;
  padding: 6px 10px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  transition: opacity 0.3s ease;
  white-space: nowrap;
}
</style>
</head>
<body>
    <?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-<?php echo $_GET['pesan'] === 'sukses' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $_GET['pesan'] === 'sukses' ? 'Keluhanmu berhasil ditambahkan.' : 'Gagal mengirim keluhan. Coba lagi.'; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

  <?php require "navbar.php"; ?>

  <!-- Pesan notifikasi -->
  <div class="container mt-3">
    <?= $msg ?>
  </div>

  <div class="hero">
    <h1 class="text-white">Selamat Datang di Kost Ibu Siti</h1>
    <p>Kost nyaman, aman, bersih, dan strategis</p>
    <a href="kamar.php" class="btn-jelajahi-custom">
      Jelajahi Kamar <span class="arrow">&rarr;</span>
    </a>
  </div>

  <section class="facilities container py-5">
    <div class="section-title"><h2>Fasilitas Unggulan</h2></div>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="card">
          <i class="bi bi-wifi"></i>
          <h5>Wi‑Fi Kencang</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card">
          <i class="bi bi-shield-check"></i>
          <h5>Lingkungan Aman</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card">
          <i class="bi bi-droplet"></i>
          <h5>Kamar Mandi Dalam</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card">
          <i class="bi bi-car-front"></i>
          <h5>Tempat Parkir Luas</h5>
        </div>
      </div>
    </div>
  </section>

  <section class="kamar-section container py-5">
    <div class="section-title"><h2>Pilihan Kamar</h2></div>
    <div class="row g-4">
      <div class="col-md-6 text-center">
        <a href="kamar.php?tipe=AC"><img src="uploads/68452a9a6f7d8-ac.jpg" alt="Kamar AC" /></a>
        <p class="kamar-label">Kamar AC</p>
      </div>
      <div class="col-md-6 text-center">
        <a href="kamar.php?tipe=Non AC"><img src="uploads/68452b5b147a4-non ac1.jpg" alt="Kamar Non AC" /></a>
        <p class="kamar-label">Kamar Non‑AC</p>
      </div>
    </div>
  </section>

  <section class="about-section text-white py-5" style="background: linear-gradient(135deg,rgb(100, 161, 221), #0d47a1);">
    <div class="container">
      <div class="section-title text-center">
        <h2 class="text-white">Tentang Kami</h2>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <p class="lead text-center" style="line-height: 1.8; font-size: 1.1rem;">
            <strong>Kost Ibu Siti</strong> berlokasi di <strong>Jl. Semampir Tengah VI No.22, Medokan Semampir, Kec. Sukolilo, Surabaya, Jawa Timur.</strong>
            Kami menawarkan hunian yang <strong>nyaman</strong>, <strong>bersih</strong>, dan <strong>aman</strong> bagi mahasiswa, pekerja, maupun perantau.
            Dengan lokasi strategis, Kost Ibu Siti menjadi pilihan ideal bagi Anda yang mencari tempat tinggal praktis di tengah kota.
            Kami menyediakan fasilitas penunjang lengkap serta lingkungan yang tenang dan bersahabat, sehingga Anda bisa merasa seperti di rumah sendiri.
          </p>
        </div>
      </div>
    </div>
  </section>

  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.855469031862!2d112.74537021456362!3d-7.265361477612153!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fbdc4e7755bf%3A0x87a9adf430a4ff3e!2sJl.%20Semampir%20Tengah%20VI%20No.22%2C%20Medokan%20Semampir%2C%20Kec.%20Sukolilo%2C%20Kota%20SBY%2C%20Jawa%20Timur%2060118!5e0!3m2!1sid!2sid!4v1686233485089!5m2!1sid!2sid"
    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

  <section class="contact-section container mt-5">
    <div class="section-title"><h2>Informasi Kontak</h2></div>
    <div class="contact-info mb-4">
      <div>
        <i class="bi bi-geo-alt-fill"></i>
        <p>Jl. Semampir Tengah VI No.22<br />Medokan Semampir, Sukolilo, Surabaya</p>
      </div>
      <div>
        <i class="bi bi-telephone-fill"></i>
        <p>0822 3153 9999</p>
      </div>
      <div>
        <i class="bi bi-envelope-fill"></i>
        <p>kostibusiti@gmail.com</p>
      </div>
    </div>
    <div class="contact-line"></div>
  </section>

  <footer>
    <p>© 2024 Kost Ibu Siti. All rights reserved.</p>
  </footer>

<!-- Tombol Keluhan dengan Label Estetik -->
<div id="btnKeluhanWrapper">
  <button type="button" id="btnKeluhan" class="btn btn-danger shadow" data-bs-toggle="modal" data-bs-target="#modalKeluhan" aria-label="Keluhan">
    <i class="bi bi-plus-lg fs-4"></i>
  </button>
  <div class="keluhan-label">Anda penghuni Kost Ibu Siti?, Ada keluhan?</div>
</div>

<!-- Modal Keluhan -->
<div class="modal fade" id="modalKeluhan" tabindex="-1" aria-labelledby="modalKeluhanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="proses_keluhan.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalKeluhanLabel">Tambah Keluhan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <!-- Select ID Penghuni -->
          <div class="mb-3">
            <label for="id_penghuni" class="form-label">Pilih Penghuni</label>
            <select class="form-select" id="id_penghuni" name="id_penghuni" required>
              <option value="">-- Pilih Penghuni --</option>
              <?php
              $sql = "SELECT id_penghuni, nama FROM penghuni ORDER BY nama";
              $result = $con->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . htmlspecialchars($row['id_penghuni']) . '">' . htmlspecialchars($row['nama']) . '</option>';
                }
              } else {
                echo '<option value="">Data penghuni kosong</option>';
              }
              ?>
            </select>
            <div class="invalid-feedback">Harap pilih penghuni.</div>
          </div>

            <div class="mb-3">
              <label for="tanggal" class="form-label">Tanggal Keluhan</label>
              <input type="date" class="form-control" id="tanggal" name="tanggal" required>
              <div class="invalid-feedback">Harap isi tanggal keluhan.</div>
            </div>

          <!-- Deskripsi Keluhan -->
            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi Keluhan</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
              <div class="invalid-feedback">Harap isi deskripsi keluhan.</div>
            </div>

        </div>
        <div class="modal-footer">
          <button type="submit" name="submit_keluhan" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Validasi Bootstrap 5 pada form modal keluhan
    (() => {
      'use strict';
      const form = document.querySelector('#modalKeluhan form');
      form.addEventListener('submit', (event) => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    })();
  </script>
</body>
</html>
