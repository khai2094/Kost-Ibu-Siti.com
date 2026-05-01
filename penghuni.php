<?php
session_start();
require "../koneksi.php";

// Fungsi untuk escape input
function esc($val) {
    global $con;
    return mysqli_real_escape_string($con, trim($val));
}

$msg = '';

// Simpan dan update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penghuni = esc($_POST['id_penghuni'] ?? '');
    $nama = esc($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $no_hp = esc($_POST['no_hp'] ?? '');
    $pekerjaan = esc($_POST['pekerjaan'] ?? '');

    if ($id_penghuni === '' || $nama === '' || ($jenis_kelamin !== 'L' && $jenis_kelamin !== 'P') || $no_hp === '' || $pekerjaan === '') {
        $msg = '<div class="alert alert-danger">Semua field harus diisi dengan benar.</div>';
    } else {
        if (isset($_POST['simpan_penghuni'])) {
            $cek = mysqli_query($con, "SELECT 1 FROM penghuni WHERE id_penghuni='$id_penghuni'");
            if (mysqli_num_rows($cek) > 0) {
                $msg = '<div class="alert alert-danger">ID Penghuni sudah ada, gunakan ID lain.</div>';
            } else {
                $ins = mysqli_query($con, "INSERT INTO penghuni (id_penghuni, nama, jenis_kelamin, no_hp, pekerjaan) VALUES ('$id_penghuni', '$nama', '$jenis_kelamin', '$no_hp', '$pekerjaan')");
                $msg = $ins ? '<div class="alert alert-success">Data berhasil disimpan.</div>' : '<div class="alert alert-danger">Gagal menyimpan data.</div>';
            }
        } elseif (isset($_POST['update_penghuni'])) {
            $upd = mysqli_query($con, "UPDATE penghuni SET nama='$nama', jenis_kelamin='$jenis_kelamin', no_hp='$no_hp', pekerjaan='$pekerjaan' WHERE id_penghuni='$id_penghuni'");
            $msg = $upd ? '<div class="alert alert-success">Data berhasil diperbarui.</div>' : '<div class="alert alert-danger">Gagal memperbarui data.</div>';
        }
    }
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id_hapus = esc($_GET['hapus']);
    $del = mysqli_query($con, "DELETE FROM penghuni WHERE id_penghuni='$id_hapus'");
    $msg = $del ? '<div class="alert alert-success">Data berhasil dihapus.</div>' : '<div class="alert alert-danger">Gagal menghapus data.</div>';
}

// Pencarian
$cari = esc($_GET['cari'] ?? '');
$where = $cari !== '' ? "WHERE id_penghuni LIKE '%$cari%' OR nama LIKE '%$cari%' OR no_hp LIKE '%$cari%' OR pekerjaan LIKE '%$cari%'" : '';

// Pagination setup
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM penghuni $where");
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_page = ceil($total_data / $limit);

// Ambil data dengan limit dan offset
$data = mysqli_query($con, "SELECT * FROM penghuni $where ORDER BY id_penghuni ASC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Penghuni</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container mt-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3" style="background: transparent;">
    <ol class="breadcrumb" style="background: transparent; padding-left: 0;">
      <li class="breadcrumb-item">
        <a href="index.php" class="text-muted text-decoration-none">
          <i class="bi bi-house-door-fill"></i> Home
        </a>
      </li>
      <li class="breadcrumb-item active text-muted" aria-current="page">Penghuni</li>
    </ol>
  </nav>

  <h4>Data Penghuni</h4>

  <?= $msg ?>

  <!-- Form tambah penghuni -->
  <div class="card mb-3">
    <div class="card-body">
      <h5>Tambah Penghuni</h5>
      <form method="post" id="form-penghuni" autocomplete="off">
        <div class="row g-3">
          <div class="col-md-2">
            <input type="text" name="id_penghuni" id="id_penghuni" class="form-control" placeholder="ID Penghuni" required>
          </div>
          <div class="col-md-3">
            <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama" required>
          </div>
          <div class="col-md-2">
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
              <option value="">Jenis Kelamin</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="No HP" required pattern="^\d{10,15}$" title="Masukkan nomor HP 10-15 digit">
          </div>
          <div class="col-md-3">
            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" placeholder="Pekerjaan" required>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-success" name="simpan_penghuni" id="btn-simpan">Simpan</button>
          <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Form pencarian -->
  <form method="get" class="mb-3">
    <div class="input-group">
      <input type="text" class="form-control" placeholder="Cari ID, Nama, No HP, Pekerjaan" name="cari" value="<?= htmlspecialchars($cari) ?>">
      <button class="btn btn-outline-primary" type="submit">Cari</button>
      <a href="penghuni.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <!-- Tabel data -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID Penghuni</th>
          <th>Nama</th>
          <th>Jenis Kelamin</th>
          <th>No HP</th>
          <th>Pekerjaan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($data) == 0): ?>
          <tr><td colspan="6" class="text-center">Data tidak ditemukan.</td></tr>
        <?php else: ?>
          <?php while ($row = mysqli_fetch_assoc($data)): ?>
          <tr>
            <td><?= htmlspecialchars($row['id_penghuni']) ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
            <td><?= htmlspecialchars($row['no_hp']) ?></td>
            <td><?= htmlspecialchars($row['pekerjaan']) ?></td>
            <td>
              <button
                class="btn btn-sm btn-primary btn-edit"
                data-id="<?= htmlspecialchars($row['id_penghuni']) ?>"
                data-nama="<?= htmlspecialchars($row['nama']) ?>"
                data-jenis_kelamin="<?= $row['jenis_kelamin'] ?>"
                data-no_hp="<?= htmlspecialchars($row['no_hp']) ?>"
                data-pekerjaan="<?= htmlspecialchars($row['pekerjaan']) ?>"
                data-bs-toggle="modal"
                data-bs-target="#editPenghuniModal"
              >Edit</button>
              <a href="?hapus=<?= urlencode($row['id_penghuni']) ?>" onclick="return confirm('Yakin ingin hapus data?')" class="btn btn-sm btn-danger">Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page - 1 ?>&cari=<?= urlencode($cari) ?>">Prev</a>
        </li>
      <?php else: ?>
        <li class="page-item disabled"><span class="page-link">Prev</span></li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_page; $i++): ?>
        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&cari=<?= urlencode($cari) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $total_page): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page + 1 ?>&cari=<?= urlencode($cari) ?>">Next</a>
        </li>
      <?php else: ?>
        <li class="page-item disabled"><span class="page-link">Next</span></li>
      <?php endif; ?>
    </ul>
  </nav>

</div>

<!-- Modal Edit Penghuni -->
<div class="modal fade" id="editPenghuniModal" tabindex="-1" aria-labelledby="editPenghuniModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="form-edit-penghuni" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title" id="editPenghuniModalLabel">Edit Penghuni</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_penghuni" id="edit_id_penghuni" readonly>
          <div class="mb-3">
            <label for="edit_nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="edit_nama" name="nama" required>
          </div>
          <div class="mb-3">
            <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select class="form-select" id="edit_jenis_kelamin" name="jenis_kelamin" required>
              <option value="">Pilih Jenis Kelamin</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_no_hp" class="form-label">No HP</label>
            <input type="text" class="form-control" id="edit_no_hp" name="no_hp" required pattern="^\d{10,15}$" title="Masukkan nomor HP 10-15 digit">
          </div>
          <div class="mb-3">
            <label for="edit_pekerjaan" class="form-label">Pekerjaan</label>
            <input type="text" class="form-control" id="edit_pekerjaan" name="pekerjaan" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" name="update_penghuni">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Isi modal edit dengan data dari tombol edit
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('edit_id_penghuni').value = btn.dataset.id;
      document.getElementById('edit_nama').value = btn.dataset.nama;
      document.getElementById('edit_jenis_kelamin').value = btn.dataset.jenis_kelamin;
      document.getElementById('edit_no_hp').value = btn.dataset.no_hp;
      document.getElementById('edit_pekerjaan').value = btn.dataset.pekerjaan;
    });
  });
</script>

</body>
</html>
