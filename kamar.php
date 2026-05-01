<?php
require "session.php";
require "../koneksi.php";

// Pagination
$dataPerHalaman = 5;
$halamanAktif = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($halamanAktif - 1) * $dataPerHalaman;

// Pencarian
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$where = $search ? "WHERE id_kamar LIKE '%$search%' OR no_kamar LIKE '%$search%'" : '';

// Hitung total & halaman
$totalQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM kamar $where");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$totalHalaman = ceil($totalData / $dataPerHalaman);

// Ambil data
$query = mysqli_query($con, "SELECT * FROM kamar $where ORDER BY id_kamar LIMIT $offset, $dataPerHalaman");

// Notifikasi
$msg = $_GET['msg'] ?? '';
$alert = '';
switch ($msg) {
  case 'simpan_berhasil':
      $alert = '<div class="alert alert-success">Data berhasil disimpan</div>'; break;
  case 'simpan_gagal':
      $alert = '<div class="alert alert-danger">Gagal menyimpan data (duplikat atau input salah)</div>'; break;
  case 'hapus_berhasil':
      $alert = '<div class="alert alert-success">Data berhasil dihapus</div>'; break;
  case 'hapus_gagal':
      $alert = '<div class="alert alert-danger">Gagal menghapus data</div>'; break;
}

// Tambah data
if (isset($_POST['simpan'])) {
  $id       = htmlspecialchars($_POST['id_kamar']);
  $no       = htmlspecialchars($_POST['no_kamar']);
  $tipe     = htmlspecialchars($_POST['tipe']);
  $hargaSel = $_POST['harga_preset'];
  $harga    = $hargaSel === 'custom' ? htmlspecialchars($_POST['harga']) : $hargaSel;
  $detail   = htmlspecialchars($_POST['detail']);
  $status   = htmlspecialchars($_POST['status']);
  $foto     = '';

  if ($_FILES['foto']['name']) {
    $foto = uniqid() . '-' . $_FILES['foto']['name'];
    move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/$foto");
  }

  $cek = mysqli_query($con, "SELECT * FROM kamar WHERE id_kamar='$id' OR no_kamar='$no'");
  if (mysqli_num_rows($cek) > 0) {
    header("Location: kamar.php?msg=simpan_gagal"); exit;
  }

  $sql = "INSERT INTO kamar (id_kamar,no_kamar,tipe,harga,foto,detail,status)
          VALUES ('$id','$no','$tipe','$harga','$foto','$detail','$status')";
  $res = mysqli_query($con, $sql);
  header("Location: kamar.php?msg=" . ($res ? 'simpan_berhasil' : 'simpan_gagal'));
  exit;
}

// Edit data
if (isset($_POST['edit'])) {
  $id     = htmlspecialchars($_POST['id']);
  $no     = htmlspecialchars($_POST['no_kamar']);
  $tipe   = htmlspecialchars($_POST['tipe']);
  $harga  = htmlspecialchars($_POST['harga']);
  $status = htmlspecialchars($_POST['status']);
  $detail = htmlspecialchars($_POST['detail']);
  $foto_lama = $_POST['foto_lama'];
  $foto_baru = $foto_lama;

  if ($_FILES['foto']['name']) {
    $foto_baru = uniqid() . '-' . $_FILES['foto']['name'];
    move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/$foto_baru");
  }

  $res = mysqli_query($con, "UPDATE kamar SET no_kamar='$no', tipe='$tipe', harga='$harga', status='$status', detail='$detail', foto='$foto_baru' WHERE id_kamar='$id'");
  header("Location: kamar.php?msg=" . ($res ? 'simpan_berhasil' : 'simpan_gagal'));
  exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
  $id  = $_GET['hapus'];
  $res = mysqli_query($con, "DELETE FROM kamar WHERE id_kamar='$id'");
  header("Location: kamar.php?msg=" . ($res ? 'hapus_berhasil' : 'hapus_gagal'));
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Kamar</title>
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .badge-kosong { background:#dc3545!important; }
    .badge-terisi { background:#198754!important; }
    a.text-muted { text-decoration:none!important; }
  </style>
</head>
<body>
<?php require "navbar.php"; ?>
<div class="container mt-4">
  <p><a href="../adminpanel" class="text-muted"><i class="bi bi-house-door-fill"></i> Home</a> / <span class="text-secondary">Kamar</span></p>
  <h3>Data Kamar</h3>
  <?= $alert ?>

  <button class="btn btn-primary mb-3" id="toggleForm">Tambah Data</button>
  <div class="card mb-4" id="formBox" style="display:none">
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6 mb-2"><label>ID Kamar</label><input type="text" name="id_kamar" class="form-control" required></div>
          <div class="col-md-6 mb-2"><label>No Kamar</label><input type="text" name="no_kamar" class="form-control" required></div>
          <div class="col-md-6 mb-2"><label>Tipe</label>
            <select name="tipe" class="form-select"><option value="AC">AC</option><option value="Non AC">Non AC</option></select>
          </div>
          <div class="col-md-6 mb-2"><label>Harga</label>
            <select name="harga_preset" id="hargaPreset" class="form-select" required>
              <option value="">Pilih Harga</option>
              <option value="1000000">Rp 1.000.000</option>
              <option value="1800000">Rp 1.800.000</option>
              <option value="custom">Custom</option>
            </select>
            <input type="number" name="harga" id="hargaCustom" class="form-control mt-2" style="display:none;" placeholder="Masukkan harga custom">
          </div>
          <div class="col-md-6 mb-2">
            <label>Foto</label>
            <input type="file" name="foto" class="form-control" onchange="previewTambah(this)">
            <img id="previewTambahImg" src="" class="mt-2" style="max-height:100px; display:none;">
          </div>
          <div class="col-md-6 mb-2"><label>Status</label>
            <select name="status" class="form-select" required>
              <option value="">Pilih Status</option>
              <option value="Kosong">Kosong</option>
              <option value="Terisi">Terisi</option>
            </select>
          </div>
          <div class="col-md-12 mb-2"><label>Detail</label><textarea name="detail" class="form-control"></textarea></div>
        </div>
        <button class="btn btn-success" name="simpan">Simpan</button>
      </form>
    </div>
  </div>

  <form method="get" class="row mb-3">
    <div class="col-md-4"><input type="text" name="search" value="<?= $search ?>" class="form-control" placeholder="Cari ID atau No Kamar"></div>
    <div class="col-auto">
      <button class="btn btn-outline-primary">Cari</button>
      <a href="kamar.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr><th>ID</th><th>No</th><th>Tipe</th><th>Harga</th><th>Foto</th><th>Status</th><th>Detail</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($query)): ?>
        <tr>
          <td><?= $r['id_kamar'] ?></td>
          <td><?= $r['no_kamar'] ?></td>
          <td><?= $r['tipe'] ?></td>
          <td>Rp <?= number_format($r['harga'],0,',','.') ?></td>
          <td><?= $r['foto'] ? '<img src="../uploads/'.$r['foto'].'" width="60">' : '-' ?></td>
          <td><span class="badge <?= strtolower($r['status'])=='kosong'?'badge-kosong':'badge-terisi' ?> text-white"><?= $r['status'] ?></span></td>
          <td><?= $r['detail'] ?></td>
          <td>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id_kamar'] ?>">Edit</button>
            <a href="?hapus=<?= $r['id_kamar'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
          </td>
        </tr>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal<?= $r['id_kamar'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="post" enctype="multipart/form-data">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Kamar</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?= $r['id_kamar'] ?>">
                  <div class="mb-2"><label>No Kamar</label><input type="text" name="no_kamar" value="<?= $r['no_kamar']?>" class="form-control"></div>
                  <div class="mb-2"><label>Tipe</label>
                    <select name="tipe" class="form-select">
                      <option <?= $r['tipe']=='AC'?'selected':'' ?>>AC</option>
                      <option <?= $r['tipe']=='Non AC'?'selected':'' ?>>Non AC</option>
                    </select>
                  </div>
                  <div class="mb-2"><label>Harga</label><input type="number" name="harga" value="<?= $r['harga']?>" class="form-control"></div>
                  <div class="mb-2"><label>Status</label>
                    <select name="status" class="form-select">
                      <option value="Kosong" <?= $r['status']=='Kosong'?'selected':'' ?>>Kosong</option>
                      <option value="Terisi" <?= $r['status']=='Terisi'?'selected':'' ?>>Terisi</option>
                    </select>
                  </div>
                  <div class="mb-2"><label>Detail</label><textarea name="detail" class="form-control"><?= $r['detail']?></textarea></div>

                  <div class="mb-2">
                    <label>Foto Baru (Opsional)</label>
                    <input type="file" name="foto" class="form-control" onchange="previewEdit(this, 'previewEditImg<?= $r['id_kamar'] ?>')">
                    <?php if ($r['foto']) : ?>
                      <small>Foto saat ini: <img src="../uploads/<?= $r['foto'] ?>" width="60"></small>
                    <?php endif; ?>
                    <img id="previewEditImg<?= $r['id_kamar'] ?>" src="" class="mt-2" style="max-height:100px; display:none;">
                    <input type="hidden" name="foto_lama" value="<?= $r['foto'] ?>">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav><ul class="pagination justify-content-center">
    <?php if ($halamanAktif>1): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $halamanAktif-1 ?>&search=<?= $search ?>">Previous</a></li>
    <?php endif; ?>
    <?php for($i=1;$i<=$totalHalaman;$i++): ?>
      <li class="page-item <?= $i==$halamanAktif?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a></li>
    <?php endfor; ?>
    <?php if ($halamanAktif<$totalHalaman): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $halamanAktif+1 ?>&search=<?= $search ?>">Next</a></li>
    <?php endif; ?>
  </ul></nav>

</div>
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('toggleForm').onclick = () => {
    const f = document.getElementById('formBox');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
  };
  document.getElementById('hargaPreset').onchange = function() {
    const c = document.getElementById('hargaCustom');
    c.style.display = this.value==='custom' ? 'block':'none';
    c.required = this.value==='custom';
  };

  function previewTambah(input) {
    const img = document.getElementById('previewTambahImg');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        img.src = e.target.result;
        img.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function previewEdit(input, imgId) {
    const img = document.getElementById(imgId);
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        img.src = e.target.result;
        img.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
</body>
</html>
