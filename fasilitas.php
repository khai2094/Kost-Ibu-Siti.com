<?php
require "session.php";
require "../koneksi.php";

$msg = '';

// Proses hapus fasilitas
if (isset($_GET['hapus'])) {
    $id_hapus = htmlspecialchars($_GET['hapus']);
    $hapus = mysqli_query($con, "DELETE FROM fasilitas WHERE id_fasilitas = '$id_hapus'");
    $msg = $hapus 
        ? '<div class="alert alert-success">Data fasilitas berhasil dihapus.</div>'
        : '<div class="alert alert-danger">Gagal menghapus data fasilitas.</div>';
}

// Proses simpan fasilitas
if (isset($_POST['simpan_fasilitas'])) {
    $id_fasilitas = htmlspecialchars($_POST['id_fasilitas']);
    $nama = htmlspecialchars($_POST['nama']);
    $kondisi = htmlspecialchars($_POST['kondisi']);
    $tersedia = htmlspecialchars($_POST['tersedia']);
    $id_kamar = htmlspecialchars($_POST['id_kamar']);

    $cekID = mysqli_query($con, "SELECT id_fasilitas FROM fasilitas WHERE id_fasilitas = '$id_fasilitas' LIMIT 1");
    if (mysqli_num_rows($cekID) > 0) {
        $msg = '<div class="alert alert-danger">ID Fasilitas sudah ada. Gagal menyimpan data.</div>';
    } else {
        $simpan = mysqli_query($con, "INSERT INTO fasilitas (id_fasilitas, nama, kondisi, tersedia, id_kamar) 
                                      VALUES ('$id_fasilitas', '$nama', '$kondisi', '$tersedia', '$id_kamar')");
        $msg = $simpan 
            ? '<div class="alert alert-success">Data fasilitas berhasil disimpan.</div>'
            : '<div class="alert alert-danger">Gagal menyimpan data fasilitas.</div>';
    }
}

// Proses update
if (isset($_POST['update_fasilitas'])) {
    $id_fasilitas = htmlspecialchars($_POST['id_fasilitas']);
    $nama = htmlspecialchars($_POST['nama']);
    $kondisi = htmlspecialchars($_POST['kondisi']);
    $tersedia = htmlspecialchars($_POST['tersedia']);
    $id_kamar = htmlspecialchars($_POST['id_kamar']);

    $update = mysqli_query($con, "UPDATE fasilitas SET 
        nama='$nama', kondisi='$kondisi', tersedia='$tersedia', id_kamar='$id_kamar' 
        WHERE id_fasilitas='$id_fasilitas'");

    $msg = $update 
        ? '<div class="alert alert-success">Data fasilitas berhasil diupdate.</div>'
        : '<div class="alert alert-danger">Gagal mengupdate data fasilitas.</div>';
}

$filter_kamar = isset($_GET['filter_kamar']) ? htmlspecialchars($_GET['filter_kamar']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Fasilitas</title>
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php require("navbar.php"); ?>

<div class="container mt-4">

  <!-- Breadcrumb tanpa kotak, teks muted -->
  <nav aria-label="breadcrumb" class="mb-3" style="background: transparent; padding-left: 0;">
    <ol class="breadcrumb" style="background: transparent; padding-left: 0; margin-bottom: 0;">
      <li class="breadcrumb-item">
        <a href="index.php" class="text-muted text-decoration-none">
          <i class="bi bi-house-door-fill"></i> Home
        </a>
      </li>
      <li class="breadcrumb-item active text-muted" aria-current="page">Fasilitas</li>
    </ol>
  </nav>

  <h4>Data Fasilitas</h4>

  <?= $msg ?>

  <!-- Form Tambah Fasilitas -->
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Tambah Fasilitas</h5>
      <form method="post" class="row g-3">
        <div class="col-md-2">
          <input type="text" name="id_fasilitas" class="form-control" placeholder="ID Fasilitas" required />
        </div>
        <div class="col-md-3">
          <input type="text" name="nama" class="form-control" placeholder="Nama Fasilitas" required />
        </div>
        <div class="col-md-2">
          <select name="kondisi" class="form-select" required>
            <option value="">Kondisi</option>
            <option value="Baik">Baik</option>
            <option value="Rusak">Rusak</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="tersedia" class="form-select" required>
            <option value="">Tersedia</option>
            <option value="Ya">Ya</option>
            <option value="Tidak">Tidak</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="id_kamar" class="form-select" required>
            <option value="">Pilih Kamar</option>
            <?php 
            $kamarList = mysqli_query($con, "SELECT id_kamar, no_kamar FROM kamar ORDER BY id_kamar");
            while ($k = mysqli_fetch_assoc($kamarList)) : ?>
              <option value="<?= $k['id_kamar'] ?>"><?= htmlspecialchars($k['id_kamar']) ?> - <?= htmlspecialchars($k['no_kamar']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-1 d-grid">
          <button class="btn btn-success" type="submit" name="simpan_fasilitas">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Filter kamar -->
  <form class="mb-3 row g-2 align-items-center" method="get">
    <div class="col-auto">
      <select name="filter_kamar" class="form-select">
        <option value="">-- Semua Kamar --</option>
        <?php 
        $kamarDropdown = mysqli_query($con, "SELECT id_kamar, no_kamar FROM kamar ORDER BY id_kamar");
        while ($r = mysqli_fetch_assoc($kamarDropdown)) : ?>
          <option value="<?= htmlspecialchars($r['id_kamar']) ?>" <?= $filter_kamar == $r['id_kamar'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($r['id_kamar']) ?> - <?= htmlspecialchars($r['no_kamar']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">Tampilkan</button>
    </div>
  </form>

  <!-- Accordion Fasilitas per kamar -->
  <div class="accordion" id="accordionFasilitas">
    <?php
    $whereKamar = $filter_kamar !== '' ? "WHERE id_kamar = '$filter_kamar'" : "";
    $kamarGroupQuery = mysqli_query($con, "SELECT id_kamar, no_kamar FROM kamar $whereKamar ORDER BY id_kamar");
    if (mysqli_num_rows($kamarGroupQuery) == 0) {
        echo '<div class="alert alert-info">Tidak ada kamar yang ditemukan.</div>';
    } else {
        $index = 0;
        while ($kamar = mysqli_fetch_assoc($kamarGroupQuery)) {
            $idKamar = $kamar['id_kamar'];
            $fasilitasQuery = mysqli_query($con, "SELECT * FROM fasilitas WHERE id_kamar = '$idKamar' ORDER BY id_fasilitas");
            $collapseId = "collapse$idKamar";
            $headingId = "heading$idKamar";
    ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="<?= $headingId ?>">
        <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= $collapseId ?>">
          Kamar <?= htmlspecialchars($kamar['no_kamar']) ?> (ID: <?= htmlspecialchars($kamar['id_kamar']) ?>)
        </button>
      </h2>
      <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordionFasilitas">
        <div class="accordion-body p-0">
          <?php if (mysqli_num_rows($fasilitasQuery) == 0): ?>
            <div class="p-3">Tidak ada fasilitas untuk kamar ini.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>ID Fasilitas</th>
                    <th>Nama</th>
                    <th>Kondisi</th>
                    <th>Tersedia</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($f = mysqli_fetch_assoc($fasilitasQuery)) : ?>
                  <tr>
                    <td><?= htmlspecialchars($f['id_fasilitas']) ?></td>
                    <td><?= htmlspecialchars($f['nama']) ?></td>
                    <td><?= htmlspecialchars($f['kondisi']) ?></td>
                    <td><?= htmlspecialchars($f['tersedia']) ?></td>
                    <td>
                      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= htmlspecialchars($f['id_fasilitas']) ?>">Edit</button>
                      <a href="?hapus=<?= urlencode($f['id_fasilitas']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus fasilitas ini?')">Delete</a>
                    </td>
                  </tr>

                  <!-- Modal Edit -->
                  <div class="modal fade" id="editModal<?= htmlspecialchars($f['id_fasilitas']) ?>" tabindex="-1" aria-labelledby="editModalLabel<?= htmlspecialchars($f['id_fasilitas']) ?>" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <form method="post">
                          <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?= htmlspecialchars($f['id_fasilitas']) ?>">Edit Fasilitas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <input type="hidden" name="id_fasilitas" value="<?= htmlspecialchars($f['id_fasilitas']) ?>" />
                            <div class="mb-3">
                              <label class="form-label">Nama Fasilitas</label>
                              <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($f['nama']) ?>" required />
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Kondisi</label>
                              <select name="kondisi" class="form-select" required>
                                <option value="Baik" <?= $f['kondisi'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                                <option value="Rusak" <?= $f['kondisi'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                              </select>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Tersedia</label>
                              <select name="tersedia" class="form-select" required>
                                <option value="Ya" <?= $f['tersedia'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
                                <option value="Tidak" <?= $f['tersedia'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                              </select>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Pilih Kamar</label>
                              <select name="id_kamar" class="form-select" required>
                                <?php 
                                $kamarList2 = mysqli_query($con, "SELECT id_kamar, no_kamar FROM kamar ORDER BY id_kamar");
                                while ($k2 = mysqli_fetch_assoc($kamarList2)) : ?>
                                  <option value="<?= htmlspecialchars($k2['id_kamar']) ?>" <?= $k2['id_kamar'] == $f['id_kamar'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k2['id_kamar']) ?> - <?= htmlspecialchars($k2['no_kamar']) ?>
                                  </option>
                                <?php endwhile; ?>
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="update_fasilitas" class="btn btn-primary">Simpan Perubahan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
        $index++;
        }
    }
    ?>
  </div>
</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body
