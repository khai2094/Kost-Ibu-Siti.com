<?php
session_start();
require "../koneksi.php";

$msg = '';

// Tambah data
if (isset($_POST['simpan'])) {
    $id = mysqli_real_escape_string($con, $_POST['id_pengunjung']);
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $hubungan = mysqli_real_escape_string($con, $_POST['hubungan']);
    $tanggal = mysqli_real_escape_string($con, $_POST['tanggal_kunjungan']);
    $id_penghuni = mysqli_real_escape_string($con, $_POST['id_penghuni']);

    $cek = mysqli_query($con, "SELECT * FROM pengunjung WHERE id_pengunjung='$id'");
    if (mysqli_num_rows($cek)) {
        $msg = "<div class='alert alert-danger'>ID sudah digunakan.</div>";
    } else {
        $s = mysqli_query($con, "INSERT INTO pengunjung VALUES('$id', '$nama', '$hubungan', '$tanggal', '$id_penghuni')");
        $msg = $s ? "<div class='alert alert-success'>Data berhasil disimpan.</div>" : "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
    }
}

// Edit data
if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id_pengunjung']);
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $hubungan = mysqli_real_escape_string($con, $_POST['hubungan']);
    $tanggal = mysqli_real_escape_string($con, $_POST['tanggal_kunjungan']);
    $id_penghuni = mysqli_real_escape_string($con, $_POST['id_penghuni']);

    $q = mysqli_query($con, "UPDATE pengunjung SET nama='$nama', hubungan='$hubungan', tanggal_kunjungan='$tanggal', id_penghuni='$id_penghuni' WHERE id_pengunjung='$id'");
    $msg = $q ? "<div class='alert alert-success'>Data berhasil diperbarui.</div>" : "<div class='alert alert-danger'>Gagal memperbarui data.</div>";
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($con, $_GET['hapus']);
    mysqli_query($con, "DELETE FROM pengunjung WHERE id_pengunjung='$id'");
    header("Location: pengunjung.php");
    exit;
}

// Pencarian dan pagination
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$where = $search ? "WHERE nama LIKE '%$search%'" : '';
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$total_result = mysqli_query($con, "SELECT COUNT(*) as total FROM pengunjung $where");
$total = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total / $limit);
$data = mysqli_query($con, "SELECT * FROM pengunjung $where ORDER BY tanggal_kunjungan DESC LIMIT $offset, $limit");
$penghuni = mysqli_query($con, "SELECT * FROM penghuni ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Pengunjung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-3">
  <div class="container-fluid">
    <a class="navbar-brand ps-3 pe-5" href="index.php">Kost Ibu Siti</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="kamar.php">Kamar</a></li>
        <li class="nav-item"><a class="nav-link" href="fasilitas.php">Fasilitas</a></li>
        <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
        <li class="nav-item"><a class="nav-link" href="kontrak.php">Kontrak</a></li>
        <li class="nav-item"><a class="nav-link" href="pembayaran.php">Pembayaran</a></li>
        <li class="nav-item"><a class="nav-link" href="keluhan.php">Keluhan</a></li>
        <li class="nav-item"><a class="nav-link active" href="pengunjung.php">Pengunjung</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item">
            <span class="nav-link">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Akun</a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="ganti_password.php">Ganti Password</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-success" href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Breadcrumb -->
<div class="container mb-3">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="index.php" class="text-muted"><i class="bi bi-house"></i> Home</a>
      </li>
      <li class="breadcrumb-item active text-muted" aria-current="page">Pengunjung</li>
    </ol>
  </nav>
</div>

<div class="container">
  <h3>Data Pengunjung</h3>
  <?= $msg ?>

  <!-- Form Tambah -->
  <form method="post" class="row g-3 mb-4">
    <div class="col-md-2"><input type="text" name="id_pengunjung" class="form-control" placeholder="ID Pengunjung" required></div>
    <div class="col-md-3"><input type="text" name="nama" class="form-control" placeholder="Nama" required></div>
    <div class="col-md-3"><input type="text" name="hubungan" class="form-control" placeholder="Hubungan" required></div>
    <div class="col-md-2"><input type="date" name="tanggal_kunjungan" class="form-control" required></div>
    <div class="col-md-2">
      <select name="id_penghuni" class="form-select" required>
        <option value="">Pilih Penghuni</option>
        <?php mysqli_data_seek($penghuni, 0); while($p = mysqli_fetch_assoc($penghuni)): ?>
          <option value="<?= $p['id_penghuni'] ?>"><?= $p['nama'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-12 d-grid"><button type="submit" name="simpan" class="btn btn-success btn-sm">Simpan</button></div>
  </form>

  <!-- Search -->
  <form method="get" class="input-group mb-3">
    <input type="text" name="search" class="form-control" placeholder="Cari nama pengunjung" value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-primary">Cari</button>
    <a href="pengunjung.php" class="btn btn-secondary">Reset</a>
  </form>

  <!-- Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Hubungan</th>
          <th>Tanggal</th>
          <th>ID Penghuni</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($data) == 0): ?>
          <tr><td colspan="6" class="text-center">Data belum ada</td></tr>
        <?php else: ?>
          <?php while ($d = mysqli_fetch_assoc($data)): ?>
            <tr>
              <td><?= $d['id_pengunjung'] ?></td>
              <td><?= $d['nama'] ?></td>
              <td><?= $d['hubungan'] ?></td>
              <td><?= $d['tanggal_kunjungan'] ?></td>
              <td><?= $d['id_penghuni'] ?></td>
              <td>
                <button class="btn btn-sm btn-primary btn-edit" data-id='<?= json_encode($d) ?>'>Edit</button>
                <a href="?hapus=<?= $d['id_pengunjung'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav class="d-flex justify-content-center">
    <ul class="pagination">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
      </li>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Pengunjung</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_pengunjung" id="edit-id">
        <div class="mb-2"><label>Nama</label><input type="text" name="nama" id="edit-nama" class="form-control" required></div>
        <div class="mb-2"><label>Hubungan</label><input type="text" name="hubungan" id="edit-hubungan" class="form-control" required></div>
        <div class="mb-2"><label>Tanggal Kunjungan</label><input type="date" name="tanggal_kunjungan" id="edit-tanggal" class="form-control" required></div>
        <div class="mb-2">
          <label>Penghuni</label>
          <select name="id_penghuni" id="edit-penghuni" class="form-select" required>
            <option value="">Pilih Penghuni</option>
            <?php mysqli_data_seek($penghuni, 0); while($p = mysqli_fetch_assoc($penghuni)): ?>
              <option value="<?= $p['id_penghuni'] ?>"><?= $p['nama'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="update" class="btn btn-primary btn-sm">Simpan</button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', () => {
    const data = JSON.parse(btn.dataset.id);
    document.getElementById('edit-id').value = data.id_pengunjung;
    document.getElementById('edit-nama').value = data.nama;
    document.getElementById('edit-hubungan').value = data.hubungan;
    document.getElementById('edit-tanggal').value = data.tanggal_kunjungan;
    document.getElementById('edit-penghuni').value = data.id_penghuni;
    new bootstrap.Modal(document.getElementById('editModal')).show();
  });
});
</script>

</body>
</html>
