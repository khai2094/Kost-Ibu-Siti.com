<?php
session_start();
require "../koneksi.php";

$msg = '';

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Input pencarian
$search_penghuni = isset($_GET['cari_penghuni']) ? trim($_GET['cari_penghuni']) : '';
$search_kamar = isset($_GET['cari_kamar']) ? trim($_GET['cari_kamar']) : '';

// Proses simpan/update data kontrak
if (isset($_POST['simpan'])) {
    $id = mysqli_real_escape_string($con, $_POST['id_kontrak']);
    $id_penghuni = mysqli_real_escape_string($con, $_POST['id_penghuni']);
    $id_kamar = mysqli_real_escape_string($con, $_POST['id_kamar']);
    $mulai = mysqli_real_escape_string($con, $_POST['tanggal_mulai']);
    $selesai = mysqli_real_escape_string($con, $_POST['tanggal_selesai']);
    $mode = $_POST['form_mode'];

    // Validasi foreign key id_penghuni
    $cek_penghuni = mysqli_query($con, "SELECT 1 FROM penghuni WHERE id_penghuni='$id_penghuni'");
    if (mysqli_num_rows($cek_penghuni) == 0) {
        $msg = "<div class='alert alert-danger'>ID Penghuni tidak valid.</div>";
    }
    // Validasi foreign key id_kamar
    elseif (mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM kamar WHERE id_kamar='$id_kamar'")) == 0) {
        $msg = "<div class='alert alert-danger'>ID Kamar tidak valid.</div>";
    }
    else {
        if ($mode === 'tambah') {
            $cek = mysqli_query($con, "SELECT * FROM kontrak WHERE id_kontrak='$id'");
            if (mysqli_num_rows($cek)) {
                $msg = "<div class='alert alert-danger'>ID kontrak sudah ada.</div>";
            } else {
                $q = mysqli_query($con, "INSERT INTO kontrak (id_kontrak, id_penghuni, id_kamar, tanggal_mulai, tanggal_selesai) VALUES ('$id','$id_penghuni','$id_kamar','$mulai','$selesai')");
                if ($q) {
                    $msg = "<div class='alert alert-success'>Data berhasil disimpan.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Gagal menyimpan data: " . mysqli_error($con) . "</div>";
                }
            }
        } else {
            $q = mysqli_query($con, "UPDATE kontrak SET id_penghuni='$id_penghuni', id_kamar='$id_kamar', tanggal_mulai='$mulai', tanggal_selesai='$selesai' WHERE id_kontrak='$id'");
            if ($q) {
                $msg = "<div class='alert alert-success'>Data berhasil diperbarui.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Gagal update data: " . mysqli_error($con) . "</div>";
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($con, "DELETE FROM kontrak WHERE id_kontrak='$id'");
    header("Location: kontrak.php");
    exit;
}

// Query filter pencarian
$where = [];
if ($search_penghuni !== '') $where[] = "id_penghuni LIKE '%$search_penghuni%'";
if ($search_kamar !== '') $where[] = "id_kamar LIKE '%$search_kamar%'";

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Ambil data dengan pagination
$data = mysqli_query($con, "SELECT * FROM kontrak $where_sql ORDER BY id_kontrak LIMIT $limit OFFSET $offset");
$total_rows = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM kontrak $where_sql"))['total'];
$total_pages = ceil($total_rows / $limit);

// Ambil data dropdown
$penghuni = mysqli_query($con, "SELECT id_penghuni, nama FROM penghuni ORDER BY nama");
$kamar = mysqli_query($con, "SELECT id_kamar FROM kamar ORDER BY id_kamar");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Data Kontrak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container mt-4">
    <h3>Data Kontrak</h3>
    <?= $msg ?>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="post" autocomplete="off">
                <input type="hidden" name="form_mode" id="form_mode" value="tambah">
                <div class="row g-3">
                    <div class="col-md-2">
                        <input type="text" name="id_kontrak" id="id_kontrak" class="form-control" placeholder="ID Kontrak" required>
                    </div>
                    <div class="col-md-3">
                        <select name="id_penghuni" id="id_penghuni" class="form-select" required>
                            <option value="">Pilih Penghuni</option>
                            <?php mysqli_data_seek($penghuni, 0); while ($p = mysqli_fetch_assoc($penghuni)): ?>
                                <option value="<?= htmlspecialchars($p['id_penghuni']) ?>"><?= htmlspecialchars($p['id_penghuni']) ?> - <?= htmlspecialchars($p['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="id_kamar" id="id_kamar" class="form-select" required>
                            <option value="">Pilih Kamar</option>
                            <?php mysqli_data_seek($kamar, 0); while ($k = mysqli_fetch_assoc($kamar)): ?>
                                <option value="<?= htmlspecialchars($k['id_kamar']) ?>"><?= htmlspecialchars($k['id_kamar']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Pencarian -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="cari_penghuni" class="form-control" placeholder="Cari Penghuni" value="<?= htmlspecialchars($search_penghuni) ?>">
                </div>
                <div class="col-md-5">
                    <input type="text" name="cari_kamar" class="form-control" placeholder="Cari Kamar" value="<?= htmlspecialchars($search_kamar) ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="kontrak.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
            <tr>
                <th>ID Kontrak</th>
                <th>ID Penghuni</th>
                <th>ID Kamar</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
            <?php else: ?>
                <?php while ($d = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['id_kontrak']) ?></td>
                        <td><?= htmlspecialchars($d['id_penghuni']) ?></td>
                        <td><?= htmlspecialchars($d['id_kamar']) ?></td>
                        <td><?= htmlspecialchars($d['tanggal_mulai']) ?></td>
                        <td><?= htmlspecialchars($d['tanggal_selesai']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-edit"
                                    data-id="<?= htmlspecialchars($d['id_kontrak']) ?>"
                                    data-penghuni="<?= htmlspecialchars($d['id_penghuni']) ?>"
                                    data-kamar="<?= htmlspecialchars($d['id_kamar']) ?>"
                                    data-mulai="<?= htmlspecialchars($d['tanggal_mulai']) ?>"
                                    data-selesai="<?= htmlspecialchars($d['tanggal_selesai']) ?>">Edit</button>
                            <a href="?hapus=<?= urlencode($d['id_kontrak']) ?>" onclick="return confirm('Hapus kontrak ini?')" class="btn btn-sm btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="post" autocomplete="off">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditLabel">Edit Kontrak</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="form_mode" id="modal_form_mode" value="edit">
              <div class="row g-3">
                <div class="col-md-3">
                  <input type="text" name="id_kontrak" id="modal_id_kontrak" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                  <select name="id_penghuni" id="modal_id_penghuni" class="form-select" required>
                    <option value="">Pilih Penghuni</option>
                    <?php mysqli_data_seek($penghuni, 0); while ($p = mysqli_fetch_assoc($penghuni)): ?>
                      <option value="<?= htmlspecialchars($p['id_penghuni']) ?>"><?= htmlspecialchars($p['id_penghuni']) ?> - <?= htmlspecialchars($p['nama']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="col-md-2">
                  <select name="id_kamar" id="modal_id_kamar" class="form-select" required>
                    <option value="">Pilih Kamar</option>
                    <?php mysqli_data_seek($kamar, 0); while ($k = mysqli_fetch_assoc($kamar)): ?>
                      <option value="<?= htmlspecialchars($k['id_kamar']) ?>"><?= htmlspecialchars($k['id_kamar']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <input type="date" name="tanggal_mulai" id="modal_tanggal_mulai" class="form-control" required>
                </div>
                <div class="col-md-3">
                  <input type="date" name="tanggal_selesai" id="modal_tanggal_selesai" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="simpan" class="btn btn-success">Simpan Perubahan</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center">
            <?php for ($i=1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&cari_penghuni=<?= urlencode($search_penghuni) ?>&cari_kamar=<?= urlencode($search_kamar) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Edit button handler
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            document.getElementById('modal_id_kontrak').value = this.dataset.id;
            document.getElementById('modal_id_penghuni').value = this.dataset.penghuni;
            document.getElementById('modal_id_kamar').value = this.dataset.kamar;
            document.getElementById('modal_tanggal_mulai').value = this.dataset.mulai;
            document.getElementById('modal_tanggal_selesai').value = this.dataset.selesai;
            document.getElementById('modal_form_mode').value = 'edit';
            modal.show();
        });
    });
</script>
</body>
</html>
