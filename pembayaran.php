<?php 
require "session.php";
require "../koneksi.php";

$msg = '';

// Pagination config
$limit = 10; // data per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hapus pembayaran
if (isset($_GET['hapus'])) {
    $id_hapus = htmlspecialchars($_GET['hapus']);
    $hapus = mysqli_query($con, "DELETE FROM pembayaran WHERE id_pembayaran = '$id_hapus'");
    $msg = $hapus
        ? '<div class="alert alert-success">Data pembayaran berhasil dihapus.</div>'
        : '<div class="alert alert-danger">Gagal menghapus data pembayaran.</div>';
}

// Tambah pembayaran
if (isset($_POST['simpan_pembayaran'])) {
    $id_pembayaran = htmlspecialchars($_POST['id_pembayaran']);
    $id_kontrak = htmlspecialchars($_POST['id_kontrak']);
    $tanggal_bayar = htmlspecialchars($_POST['tanggal_bayar']);
    $jumlah = htmlspecialchars($_POST['jumlah']);
    $metode_bayar = htmlspecialchars($_POST['metode_bayar']);

    $cekID = mysqli_query($con, "SELECT id_pembayaran FROM pembayaran WHERE id_pembayaran = '$id_pembayaran' LIMIT 1");
    if (mysqli_num_rows($cekID) > 0) {
        $msg = '<div class="alert alert-danger">ID Pembayaran sudah ada.</div>';
    } else {
        $simpan = mysqli_query($con, "INSERT INTO pembayaran (id_pembayaran, id_kontrak, tanggal_bayar, jumlah, metode_bayar) 
            VALUES ('$id_pembayaran', '$id_kontrak', '$tanggal_bayar', '$jumlah', '$metode_bayar')");
        $msg = $simpan
            ? '<div class="alert alert-success">Data pembayaran berhasil disimpan.</div>'
            : '<div class="alert alert-danger">Gagal menyimpan data pembayaran.</div>';
    }
}

// Update pembayaran
if (isset($_POST['update_pembayaran'])) {
    $id_pembayaran = htmlspecialchars($_POST['id_pembayaran']);
    $id_kontrak = htmlspecialchars($_POST['id_kontrak']);
    $tanggal_bayar = htmlspecialchars($_POST['tanggal_bayar']);
    $jumlah = htmlspecialchars($_POST['jumlah']);
    $metode_bayar = htmlspecialchars($_POST['metode_bayar']);

    $update = mysqli_query($con, "UPDATE pembayaran SET id_kontrak='$id_kontrak', tanggal_bayar='$tanggal_bayar', jumlah='$jumlah', metode_bayar='$metode_bayar' WHERE id_pembayaran='$id_pembayaran'");

    $msg = $update
        ? '<div class="alert alert-success">Data pembayaran berhasil diupdate.</div>'
        : '<div class="alert alert-danger">Gagal mengupdate data pembayaran.</div>';
}

// Filter pencarian
$filter_kontrak = isset($_GET['filter_kontrak']) ? htmlspecialchars($_GET['filter_kontrak']) : '';
$filter_metode = isset($_GET['filter_metode']) ? htmlspecialchars($_GET['filter_metode']) : '';
$filter_tanggal = isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : '';

// Bangun kondisi WHERE berdasarkan filter
$where = [];
if ($filter_kontrak !== '') {
    $where[] = "id_kontrak = '$filter_kontrak'";
}
if ($filter_metode !== '') {
    $where[] = "metode_bayar = '$filter_metode'";
}
if ($filter_tanggal !== '') {
    $where[] = "tanggal_bayar = '$filter_tanggal'";
}
$whereSQL = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

// Hitung total data untuk pagination
$totalDataQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM pembayaran $whereSQL");
$totalData = mysqli_fetch_assoc($totalDataQuery)['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data dengan limit offset dan sorting id_pembayaran ascending (byr001 paling atas)
$query = mysqli_query($con, "SELECT * FROM pembayaran $whereSQL ORDER BY id_pembayaran ASC LIMIT $limit OFFSET $offset");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Data Pembayaran</title>
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<style>
  nav.breadcrumb {
    padding-left: 0 !important;
    margin-bottom: 0 !important;
    background: transparent !important;
  }
  nav.breadcrumb ol.breadcrumb {
    padding-left: 0 !important;
    margin-bottom: 0 !important;
  }
</style>
</head>
<body>

<?php require("navbar.php"); ?>

<div class="container mt-4">


   <!-- Breadcrumb tepat di atas data pembayaran -->
  <nav aria-label="breadcrumb" class="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none"><i class="bi bi-house-door-fill"></i> Home</a></li>
      <li class="breadcrumb-item active text-muted" aria-current="page">Pembayaran</li>
    </ol>
  </nav>

  
  <h4>Data Pembayaran</h4>

  <?= $msg ?>

  <!-- Form tambah pembayaran -->
  <div class="card mb-4">
    <div class="card-body">
      <h5>Tambah Pembayaran</h5>
      <form method="post" class="row g-3 align-items-center">
        <div class="col-md-2">
          <input type="text" name="id_pembayaran" class="form-control" placeholder="ID Pembayaran" required />
        </div>
        <div class="col-md-3">
          <select name="id_kontrak" class="form-select" required>
            <option value="">Pilih Kontrak</option>
            <?php
            $kontrakList = mysqli_query($con, "SELECT id_kontrak FROM kontrak ORDER BY id_kontrak ASC");
            while ($k = mysqli_fetch_assoc($kontrakList)) : ?>
              <option value="<?= $k['id_kontrak'] ?>"><?= $k['id_kontrak'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="tanggal_bayar" class="form-control" required />
        </div>
        <div class="col-md-2">
          <input type="number" name="jumlah" step="0.01" min="0" class="form-control" placeholder="Jumlah" required />
        </div>
        <div class="col-md-2">
          <select name="metode_bayar" class="form-select" required>
            <option value="">Metode Bayar</option>
            <option value="tunai">Tunai</option>
            <option value="tf">Transfer</option>
            <option value="qris">QRIS</option>
          </select>
        </div>
        <div class="col-md-1">
          <button type="submit" name="simpan_pembayaran" class="btn btn-success w-100">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Form filter / search -->
  <form method="get" class="row g-3 mb-3 align-items-center">
    <div class="col-md-3">
      <select name="filter_kontrak" class="form-select">
        <option value="">-- Semua Kontrak --</option>
        <?php
        $kontrakDropdown = mysqli_query($con, "SELECT id_kontrak FROM kontrak ORDER BY id_kontrak ASC");
        while ($r = mysqli_fetch_assoc($kontrakDropdown)) : ?>
          <option value="<?= $r['id_kontrak'] ?>" <?= $filter_kontrak == $r['id_kontrak'] ? 'selected' : '' ?>>
            <?= $r['id_kontrak'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="filter_metode" class="form-select">
        <option value="">-- Semua Metode Bayar --</option>
        <option value="tunai" <?= $filter_metode == 'tunai' ? 'selected' : '' ?>>Tunai</option>
        <option value="transfer" <?= $filter_metode == 'transfer' ? 'selected' : '' ?>>Transfer</option>
        <option value="qris" <?= $filter_metode == 'qris' ? 'selected' : '' ?>>QRIS</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="date" name="filter_tanggal" class="form-control" value="<?= htmlspecialchars($filter_tanggal) ?>" />
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary">Cari</button>
      <a href="pembayaran.php" class="btn btn-secondary ms-2">Reset</a>
    </div>
  </form>

  <!-- Tabel list pembayaran -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID Pembayaran</th>
          <th>ID Kontrak</th>
          <th>Tanggal Bayar</th>
          <th>Jumlah</th>
          <th>Metode Bayar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($query) == 0) : ?>
          <tr><td colspan="6" class="text-center">Tidak ada data pembayaran.</td></tr>
        <?php else: ?>
          <?php while ($p = mysqli_fetch_assoc($query)) : ?>
          <tr>
            <td><?= htmlspecialchars($p['id_pembayaran']) ?></td>
            <td><?= htmlspecialchars($p['id_kontrak']) ?></td>
            <td><?= htmlspecialchars($p['tanggal_bayar']) ?></td>
            <td><?= number_format($p['jumlah'], 2, ',', '.') ?></td>
            <td><?= ucfirst(htmlspecialchars($p['metode_bayar'])) ?></td>
            <td>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id_pembayaran'] ?>">Edit</button>
              <a href="?hapus=<?= $p['id_pembayaran'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pembayaran ini?')">Delete</a>
            </td>
          </tr>

          <!-- Modal Edit -->
          <div class="modal fade" id="editModal<?= $p['id_pembayaran'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $p['id_pembayaran'] ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="post">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel<?= $p['id_pembayaran'] ?>">Edit Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id_pembayaran" value="<?= $p['id_pembayaran'] ?>" />
                    <div class="mb-3">
                      <label class="form-label">ID Kontrak</label>
                      <select name="id_kontrak" class="form-select" required>
                        <?php
                        $kontrakList2 = mysqli_query($con, "SELECT id_kontrak FROM kontrak ORDER BY id_kontrak ASC");
                        while ($k2 = mysqli_fetch_assoc($kontrakList2)) : ?>
                          <option value="<?= $k2['id_kontrak'] ?>" <?= $k2['id_kontrak'] == $p['id_kontrak'] ? 'selected' : '' ?>>
                            <?= $k2['id_kontrak'] ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Tanggal Bayar</label>
                      <input type="date" name="tanggal_bayar" class="form-control" value="<?= $p['tanggal_bayar'] ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Jumlah</label>
                      <input type="number" name="jumlah" step="0.01" min="0" class="form-control" value="<?= $p['jumlah'] ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Metode Bayar</label>
                      <select name="metode_bayar" class="form-select" required>
                        <option value="tunai" <?= $p['metode_bayar'] == 'tunai' ? 'selected' : '' ?>>Tunai</option>
                        <option value="transfer" <?= $p['metode_bayar'] == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                        <option value="qris" <?= $p['metode_bayar'] == 'qris' ? 'selected' : '' ?>>QRIS</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update_pembayaran" class="btn btn-primary">Update</button>
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
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page-1 ?>&filter_kontrak=<?= urlencode($filter_kontrak) ?>&filter_metode=<?= urlencode($filter_metode) ?>&filter_tanggal=<?= urlencode($filter_tanggal) ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&filter_kontrak=<?= urlencode($filter_kontrak) ?>&filter_metode=<?= urlencode($filter_metode) ?>&filter_tanggal=<?= urlencode($filter_tanggal) ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page+1 ?>&filter_kontrak=<?= urlencode($filter_kontrak) ?>&filter_metode=<?= urlencode($filter_metode) ?>&filter_tanggal=<?= urlencode($filter_tanggal) ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </nav>

</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
