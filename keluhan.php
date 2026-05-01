<?php
session_start();
require '../koneksi.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

function h($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$msg = '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$where = $search ? "WHERE k.id_keluhan LIKE '%" . mysqli_real_escape_string($con, $search) . "%' OR p.nama LIKE '%" . mysqli_real_escape_string($con, $search) . "%'" : '';

if (isset($_POST['simpan'])) {
  $mode = $_POST['form_mode'] ?? '';
  $id   = trim($_POST['id_keluhan'] ?? '');
  $hp   = trim($_POST['id_penghuni'] ?? '');
  $tgl  = trim($_POST['tanggal_keluhan'] ?? '');
  $desc = trim($_POST['deskripsi'] ?? '');
  $stat = trim($_POST['status'] ?? '');

  $id   = mysqli_real_escape_string($con, $id);
  $hp   = mysqli_real_escape_string($con, $hp);
  $tgl  = mysqli_real_escape_string($con, $tgl);
  $desc = mysqli_real_escape_string($con, $desc);
  $stat = mysqli_real_escape_string($con, $stat);

  if ($mode === 'tambah') {
    $cek = mysqli_query($con, "SELECT * FROM keluhan WHERE id_keluhan='$id'");
    if (mysqli_num_rows($cek)) {
      $msg = "<div class='alert alert-danger'>ID sudah ada.</div>";
    } else {
      $insert = mysqli_query($con, "INSERT INTO keluhan (id_keluhan, id_penghuni, tanggal_keluhan, deskripsi, status)
        VALUES ('$id', '$hp', '$tgl', '$desc', '$stat')");
      if ($insert) {
        header("Location: keluhan.php");
        exit();
      } else {
        $msg = "<div class='alert alert-danger'>Gagal menyimpan data: " . mysqli_error($con) . "</div>";
      }
    }
  } elseif ($mode === 'edit') {
    $update = mysqli_query($con, "UPDATE keluhan SET 
      id_penghuni='$hp', tanggal_keluhan='$tgl', deskripsi='$desc', status='$stat' 
      WHERE id_keluhan='$id'");
    if ($update) {
      header("Location: keluhan.php");
      exit();
    } else {
      $msg = "<div class='alert alert-danger'>Gagal mengubah data: " . mysqli_error($con) . "</div>";
    }
  }
}

if (isset($_GET['hapus'])) {
  $id_hapus = mysqli_real_escape_string($con, $_GET['hapus']);
  mysqli_query($con, "DELETE FROM keluhan WHERE id_keluhan='$id_hapus'");
  header("Location: keluhan.php");
  exit();
}

$total = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM keluhan k JOIN penghuni p ON k.id_penghuni=p.id_penghuni $where"))['t'];
$total_pages = ceil($total / $limit);
$data = mysqli_query($con, "SELECT k.*, p.nama FROM keluhan k JOIN penghuni p ON k.id_penghuni=p.id_penghuni $where ORDER BY k.tanggal_keluhan DESC LIMIT $offset,$limit");
$penghuni = mysqli_query($con, "SELECT * FROM penghuni ORDER BY nama");
?>
<!DOCTYPE html><html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Keluhan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">
      <i class="bi bi-house-door-fill"></i> Home</a></li>
    <li class="breadcrumb-item active text-muted" aria-current="page">Keluhan</li>
  </ol></nav>

  <h3>Data Keluhan</h3>
  <?= $msg ?>

  <!-- Form Input -->
  <form method="post" class="card p-3 mb-3" id="formKeluhan">
    <input type="hidden" name="form_mode" value="tambah" id="form_mode">
    <div class="row g-2">
      <div class="col-md-2"><input type="text" class="form-control" name="id_keluhan" id="id_keluhan" placeholder="ID Keluhan" required></div>
      <div class="col-md-3">
        <select class="form-select" name="id_penghuni" id="id_penghuni" required>
          <option value="">Pilih Penghuni</option>
          <?php mysqli_data_seek($penghuni, 0); while ($p = mysqli_fetch_assoc($penghuni)): ?>
            <option value="<?= h($p['id_penghuni']) ?>"><?= h($p['id_penghuni'].' - '.$p['nama']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-2"><input type="date" class="form-control" name="tanggal_keluhan" id="tanggal_keluhan" required></div>
      <div class="col-md-3"><input type="text" class="form-control" name="deskripsi" id="deskripsi" placeholder="Deskripsi" required></div>
      <div class="col-md-1">
        <select class="form-select" name="status" id="status">
          <option value="baru">Baru</option>
          <option value="proses">Proses</option>
          <option value="selesai">Selesai</option>
        </select>
      </div>
      <div class="col-md-1 d-grid"><button type="submit" name="simpan" class="btn btn-success">Simpan</button></div>
    </div>
  </form>

  <!-- Search -->
  <form method="get" class="row g-2 mb-3">
    <div class="col-md-10"><input type="text" class="form-control" name="search" placeholder="Cari ID atau Nama" value="<?= h($search) ?>"></div>
    <div class="col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Cari</button>
      <a href="keluhan.php" class="btn btn-secondary" onclick="resetForm(); return false;">Reset</a>
    </div>
  </form>

  <!-- Tabel -->
  <div class="table-responsive"><table class="table table-bordered table-hover">
    <thead class="table-light"><tr>
      <th>ID Keluhan</th><th>Penghuni</th><th>Tanggal</th><th>Deskripsi</th><th>Status</th><th>Aksi</th>
    </tr></thead><tbody>
    <?php if (mysqli_num_rows($data) === 0): ?>
      <tr><td colspan="6" class="text-center">Data belum ada</td></tr>
    <?php else: while ($r = mysqli_fetch_assoc($data)): ?>
      <tr>
        <td><?= h($r['id_keluhan']) ?></td>
        <td><?= h($r['nama']) ?></td>
        <td><?= h($r['tanggal_keluhan']) ?></td>
        <td><?= h($r['deskripsi']) ?></td>
        <td><span class="badge bg-<?= $r['status'] === 'selesai' ? 'success' : ($r['status'] === 'proses' ? 'warning' : 'secondary') ?>">
          <?= h($r['status']) ?></span></td>
        <td>
          <button class="btn btn-sm btn-primary btn-edit"
            data-bs-toggle="modal" data-bs-target="#editModal"
            data-id="<?= h($r['id_keluhan']) ?>"
            data-peng="<?= h($r['id_penghuni']) ?>"
            data-tgl="<?= h($r['tanggal_keluhan']) ?>"
            data-desc="<?= h($r['deskripsi']) ?>"
            data-stat="<?= h($r['status']) ?>">Edit</button>
          <a href="?hapus=<?= h($r['id_keluhan']) ?>" onclick="return confirm('Yakin?')" class="btn btn-sm btn-danger">Hapus</a>
        </td>
      </tr>
    <?php endwhile; endif; ?>
    </tbody>
  </table></div>

  <!-- Pagination -->
  <div class="d-flex justify-content-center mt-3">
    <ul class="pagination">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Previous</a>
      </li>
      <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a>
      </li>
    </ul>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="post">
      <div class="modal-header">
        <h5 class="modal-title">Edit Keluhan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-3 p-3">
        <input type="hidden" name="form_mode" value="edit">
        <div class="col-md-4">
          <label>ID Keluhan</label>
          <input type="text" class="form-control" name="id_keluhan" id="edit_id" readonly>
        </div>
        <div class="col-md-4">
          <label>Penghuni</label>
          <select class="form-select" name="id_penghuni" id="edit_penghuni" required>
            <option value="">Pilih</option>
            <?php mysqli_data_seek($penghuni, 0); while ($p = mysqli_fetch_assoc($penghuni)): ?>
              <option value="<?= h($p['id_penghuni']) ?>"><?= h($p['id_penghuni'].' - '.$p['nama']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label>Tanggal</label>
          <input type="date" class="form-control" name="tanggal_keluhan" id="edit_tanggal" required>
        </div>
        <div class="col-md-6">
          <label>Deskripsi</label>
          <input type="text" class="form-control" name="deskripsi" id="edit_deskripsi" required>
        </div>
        <div class="col-md-6">
          <label>Status</label>
          <select class="form-select" name="status" id="edit_status">
            <option value="baru">Baru</option>
            <option value="proses">Proses</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit_id').value = btn.dataset.id;
    document.getElementById('edit_penghuni').value = btn.dataset.peng;
    document.getElementById('edit_tanggal').value = btn.dataset.tgl;
    document.getElementById('edit_deskripsi').value = btn.dataset.desc;
    document.getElementById('edit_status').value = btn.dataset.stat;
  });
});

function resetForm() {
  document.getElementById('form_mode').value = 'tambah';
  document.getElementById('id_keluhan').readOnly = false;
  document.getElementById('id_keluhan').value = '';
  document.getElementById('id_penghuni').value = '';
  document.getElementById('tanggal_keluhan').value = '';
  document.getElementById('deskripsi').value = '';
  document.getElementById('status').value = 'baru';
}
</script>
</body>
</html>
