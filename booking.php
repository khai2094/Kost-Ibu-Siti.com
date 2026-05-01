<?php 
require "session.php";
require "../koneksi.php";

$msg = '';

// Pagination config
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hapus booking
if (isset($_GET['hapus'])) {
    $id_hapus = htmlspecialchars($_GET['hapus']);
    $hapus = mysqli_query($con, "DELETE FROM booking WHERE id_booking = '$id_hapus'");
    $msg = $hapus
        ? '<div class="alert alert-success">Data booking berhasil dihapus.</div>'
        : '<div class="alert alert-danger">Gagal menghapus data booking.</div>';
}

// Tambah booking
if (isset($_POST['simpan_booking'])) {
    $id_booking = htmlspecialchars($_POST['id_booking']);
    $id_kamar = htmlspecialchars($_POST['id_kamar']);
    $nama_pemesan = htmlspecialchars($_POST['nama_pemesan']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $tanggal_booking = htmlspecialchars($_POST['tanggal_booking']);
    $tanggal_mulai = htmlspecialchars($_POST['tanggal_mulai']);
    $tanggal_selesai = htmlspecialchars($_POST['tanggal_selesai']);
    $status = htmlspecialchars($_POST['status']);
    $catatan = htmlspecialchars($_POST['catatan']);

    // Cek ID unik
    $cekID = mysqli_query($con, "SELECT id_booking FROM booking WHERE id_booking = '$id_booking' LIMIT 1");
    if (mysqli_num_rows($cekID) > 0) {
        $msg = '<div class="alert alert-danger">ID Booking sudah ada.</div>';
    } else {
        $simpan = mysqli_query($con, "INSERT INTO booking 
            (id_booking, id_kamar, nama_pemesan, no_hp, jenis_kelamin, tanggal_booking, tanggal_mulai, tanggal_selesai, status, catatan) 
            VALUES 
            ('$id_booking', '$id_kamar', '$nama_pemesan', '$no_hp', '$jenis_kelamin', '$tanggal_booking', '$tanggal_mulai', '$tanggal_selesai', '$status', '$catatan')");
        $msg = $simpan
            ? '<div class="alert alert-success">Data booking berhasil disimpan.</div>'
            : '<div class="alert alert-danger">Gagal menyimpan data booking.</div>';
    }
}

// Update booking
if (isset($_POST['update_booking'])) {
    $id_booking = htmlspecialchars($_POST['id_booking']);
    $id_kamar = htmlspecialchars($_POST['id_kamar']);
    $nama_pemesan = htmlspecialchars($_POST['nama_pemesan']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $tanggal_booking = htmlspecialchars($_POST['tanggal_booking']);
    $tanggal_mulai = htmlspecialchars($_POST['tanggal_mulai']);
    $tanggal_selesai = htmlspecialchars($_POST['tanggal_selesai']);
    $status = htmlspecialchars($_POST['status']);
    $catatan = htmlspecialchars($_POST['catatan']);

    $update = mysqli_query($con, "UPDATE booking SET
        id_kamar='$id_kamar',
        nama_pemesan='$nama_pemesan',
        no_hp='$no_hp',
        jenis_kelamin='$jenis_kelamin',
        tanggal_booking='$tanggal_booking',
        tanggal_mulai='$tanggal_mulai',
        tanggal_selesai='$tanggal_selesai',
        status='$status',
        catatan='$catatan'
        WHERE id_booking='$id_booking'");

    $msg = $update
        ? '<div class="alert alert-success">Data booking berhasil diupdate.</div>'
        : '<div class="alert alert-danger">Gagal mengupdate data booking.</div>';
}

// Filter pencarian (contoh filter status dan tanggal_booking)
$filter_status = isset($_GET['filter_status']) ? htmlspecialchars($_GET['filter_status']) : '';
$filter_tanggal = isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : '';

$where = [];
if ($filter_status !== '') {
    $where[] = "status = '$filter_status'";
}
if ($filter_tanggal !== '') {
    $where[] = "tanggal_booking = '$filter_tanggal'";
}
$whereSQL = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

// Hitung total data
$totalDataQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM booking $whereSQL");
$totalData = mysqli_fetch_assoc($totalDataQuery)['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data dengan pagination
$query = mysqli_query($con, "SELECT * FROM booking $whereSQL ORDER BY id_booking ASC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Data Booking</title>
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

<?php require("navbar.php"); ?>

<div class="container mt-4">

  <nav aria-label="breadcrumb" class="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none"><i class="bi bi-house-door-fill"></i> Home</a></li>
      <li class="breadcrumb-item active text-muted" aria-current="page">Booking</li>
    </ol>
  </nav>

  <h4>Data Booking</h4>
  <?= $msg ?>

  <!-- Form tambah booking -->
  <div class="card mb-4">
    <div class="card-body">
      <h5>Tambah Booking</h5>
      <form method="post" class="row g-3 align-items-center">
        <div class="col-md-2">
          <input type="text" name="id_booking" class="form-control" placeholder="ID Booking" required />
        </div>
        <div class="col-md-2">
          <select name="id_kamar" class="form-select" required>
            <option value="">Pilih Kamar</option>
            <?php
            $kamarList = mysqli_query($con, "SELECT id_kamar, no_kamar FROM kamar ORDER BY id_kamar ASC");
            while ($k = mysqli_fetch_assoc($kamarList)) : ?>
              <option value="<?= $k['id_kamar'] ?>"><?= htmlspecialchars($k['no_kamar']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-2">
          <input type="text" name="nama_pemesan" class="form-control" placeholder="Nama Pemesan" required />
        </div>
        <div class="col-md-2">
          <input type="text" name="no_hp" class="form-control" placeholder="No HP" required />
        </div>
        <div class="col-md-2">
          <select name="jenis_kelamin" class="form-select" required>
            <option value="">Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="tanggal_booking" class="form-control" required />
        </div>
        <div class="col-md-2">
          <input type="date" name="tanggal_mulai" class="form-control" required />
        </div>
        <div class="col-md-2">
          <input type="date" name="tanggal_selesai" class="form-control" required />
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select" required>
            <option value="">Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="col-md-4">
          <input type="text" name="catatan" class="form-control" placeholder="Catatan (optional)" />
        </div>
        <div class="col-md-2">
          <button type="submit" name="simpan_booking" class="btn btn-success w-100">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Filter pencarian -->
  <form method="get" class="row g-3 mb-3 align-items-center">
    <div class="col-md-3">
      <select name="filter_status" class="form-select">
        <option value="">-- Semua Status --</option>
        <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="confirmed" <?= $filter_status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
        <option value="cancelled" <?= $filter_status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="date" name="filter_tanggal" class="form-control" value="<?= htmlspecialchars($filter_tanggal) ?>" />
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary">Cari</button>
      <a href="booking.php" class="btn btn-secondary ms-2">Reset</a>
    </div>
  </form>

  <!-- Tabel list booking -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID Booking</th>
          <th>ID Kamar</th>
          <th>Nama Pemesan</th>
          <th>No HP</th>
          <th>Jenis Kelamin</th>
          <th>Tanggal Booking</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Selesai</th>
          <th>Status</th>
          <th>Catatan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($query) == 0) : ?>
          <tr><td colspan="11" class="text-center">Tidak ada data booking.</td></tr>
        <?php else: ?>
          <?php while ($b = mysqli_fetch_assoc($query)) : ?>
          <tr>
            <td><?= htmlspecialchars($b['id_booking']) ?></td>
            <td><?= htmlspecialchars($b['id_kamar']) ?></td>
            <td><?= htmlspecialchars($b['nama_pemesan']) ?></td>
            <td><?= htmlspecialchars($b['no_hp']) ?></td>
            <td><?= htmlspecialchars($b['jenis_kelamin']) ?></td>
            <td><?= htmlspecialchars($b['tanggal_booking']) ?></td>
            <td><?= htmlspecialchars($b['tanggal_mulai']) ?></td>
            <td><?= htmlspecialchars($b['tanggal_selesai']) ?></td>
            <td><?= ucfirst(htmlspecialchars($b['status'])) ?></td>
            <td><?= htmlspecialchars($b['catatan']) ?></td>
            <td>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $b['id_booking'] ?>">Edit</button>
              <a href="?hapus=<?= $b['id_booking'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus booking ini?')">Delete</a>
            </td>
          </tr>

          <!-- Modal Edit -->
          <div class="modal fade" id="editModal<?= $b['id_booking'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $b['id_booking'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <form method="post">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel<?= $b['id_booking'] ?>">Edit Booking: <?= htmlspecialchars($b['id_booking']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id_booking" value="<?= htmlspecialchars($b['id_booking']) ?>" />
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">ID Kamar</label>
                        <select name="id_kamar" class="form-select" required>
                          <?php
                          $kamarList = mysqli_query($con, "SELECT id_kamar, no_kamar FROM kamar ORDER BY id_kamar ASC");
                          while ($k = mysqli_fetch_assoc($kamarList)) : ?>
                            <option value="<?= $k['id_kamar'] ?>" <?= $k['id_kamar'] == $b['id_kamar'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($k['no_kamar']) ?>
                            </option>
                          <?php endwhile; ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Nama Pemesan</label>
                        <input type="text" name="nama_pemesan" class="form-control" value="<?= htmlspecialchars($b['nama_pemesan']) ?>" required />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($b['no_hp']) ?>" required />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                          <option value="L" <?= $b['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                          <option value="P" <?= $b['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Tanggal Booking</label>
                        <input type="date" name="tanggal_booking" class="form-control" value="<?= htmlspecialchars($b['tanggal_booking']) ?>" required />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($b['tanggal_mulai']) ?>" required />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($b['tanggal_selesai']) ?>" required />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                          <option value="pending" <?= $b['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                          <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                          <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" class="form-control" value="<?= htmlspecialchars($b['catatan']) ?>" />
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update_booking" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&filter_status=<?= $filter_status ?>&filter_tanggal=<?= $filter_tanggal ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
