<?php
// koneksi database
include 'koneksi.php';

// Fungsi generate ID booking otomatis, format BK00000001 dst
function generateBookingId($con) {
    $prefix = "BK";
    $result = $con->query("SELECT id_booking FROM booking ORDER BY id_booking DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastId = $row['id_booking']; // e.g. BK00000009
        $num = (int)substr($lastId, 2);
        $num++;
        return $prefix . str_pad($num, 8, '0', STR_PAD_LEFT);
    } else {
        return $prefix . "00000001";
    }
}

// Inisialisasi variabel
$success = "";
$error = "";
$id_booking = "";
$nama = "";
$no_hp = "";
$jenis_kelamin = "";
$id_kamar = "";
$tanggal_booking = "";
$tanggal_mulai = "";
$tanggal_selesai = "";

// Ambil data kamar kosong untuk dropdown
$kamarResult = $con->query("SELECT id_kamar, no_kamar, tipe FROM kamar WHERE status = 'kosong' ORDER BY no_kamar ASC");

// Proses form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_booking = generateBookingId($con);
    $nama = trim($_POST['nama'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $id_kamar = $_POST['id_kamar'] ?? '';
    $tanggal_booking = $_POST['tanggal_booking'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

    // Validasi
    $valid_jenis_kelamin = ['L', 'P'];
    if ($nama === "" || $no_hp === "" || $jenis_kelamin === "" || $id_kamar === "" || $tanggal_booking === "" || $tanggal_mulai === "" || $tanggal_selesai === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!in_array($jenis_kelamin, $valid_jenis_kelamin)) {
        $error = "Jenis kelamin tidak valid.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $no_hp)) {
        $error = "Nomor HP harus terdiri dari 10-15 digit angka tanpa spasi atau tanda.";
    } elseif ($tanggal_mulai > $tanggal_selesai) {
        $error = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai.";
    } else {
        // Insert ke database
        $stmt = $con->prepare("INSERT INTO booking (id_booking, id_kamar, nama_pemesan, no_hp, jenis_kelamin, tanggal_booking, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param(
                "ssssssss",
                $id_booking,
                $id_kamar,
                $nama,
                $no_hp,
                $jenis_kelamin,
                $tanggal_booking,
                $tanggal_mulai,
                $tanggal_selesai
            );
            if ($stmt->execute()) {
                $success = "Booking berhasil dikirim! ID Booking: <strong>$id_booking</strong>";
                // Reset input
                $id_booking = "";
                $nama = $no_hp = $jenis_kelamin = $id_kamar = $tanggal_booking = $tanggal_mulai = $tanggal_selesai = "";
            } else {
                $error = "Gagal menyimpan booking, coba lagi.";
            }
            $stmt->close();
        } else {
            $error = "Kesalahan server, coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Booking Kost Ibu Siti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: #f0f5ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 6px 20px rgb(0 0 0 / 0.15);
            background: #fff;
        }
        label {
            font-weight: 600;
            color: #2c3e50;
        }
        .btn-primary {
            background: #2b6cb0;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #1e429f;
        }
        .form-control, .form-select {
            background: #e6f0ff;
            border: 1px solid #cbd5e0;
            transition: 0.3s;
            color: #1a202c;
        }
        .form-control:focus, .form-select:focus {
            background: #d0e2ff;
            border-color: #2b6cb0;
            box-shadow: 0 0 5px #2b6cb0;
            color: #1a202c;
        }
        .alert {
            border-radius: 12px;
            font-weight: 600;
        }
        h1 {
            font-weight: 700;
            color: #2b6cb0;
            margin-bottom: 1rem;
            text-align: center;
        }
        .footer-text {
            margin-top: 2rem;
            font-size: 0.9rem;
            opacity: 0.7;
            text-align: center;
            color: #718096;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <h1><i class="bi bi-house-door-fill me-2"></i>Booking Kost Ibu Siti</h1>
            <p class="text-center mb-4">Isi form berikut untuk booking kamar kost yang tersedia.</p>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4">
                <form method="POST" action="" novalidate>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            class="form-control"
                            id="nama"
                            name="nama"
                            placeholder="Nama Anda"
                            required
                            value="<?= htmlspecialchars($nama) ?>"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP / WhatsApp</label>
                        <input
                            type="tel"
                            class="form-control"
                            id="no_hp"
                            name="no_hp"
                            placeholder="08xxxxxxxxxx"
                            pattern="[0-9]{10,15}"
                            required
                            value="<?= htmlspecialchars($no_hp) ?>"
                        />
                        <div class="form-text">Masukkan nomor aktif, tanpa spasi atau tanda.</div>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="" disabled <?= $jenis_kelamin === "" ? "selected" : "" ?>>Pilih jenis kelamin</option>
                            <option value="L" <?= $jenis_kelamin === "L" ? "selected" : "" ?>>Laki-laki</option>
                            <option value="P" <?= $jenis_kelamin === "P" ? "selected" : "" ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="id_kamar" class="form-label">Pilih Kamar</label>
                        <select class="form-select" id="id_kamar" name="id_kamar" required>
                            <option value="" disabled <?= $id_kamar == "" ? "selected" : "" ?>>Pilih kamar</option>
                            <?php if ($kamarResult): ?>
                                <?php while ($kamar = $kamarResult->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($kamar['id_kamar']) ?>" <?= $id_kamar == $kamar['id_kamar'] ? "selected" : "" ?>>
                                        Kamar No. <?= htmlspecialchars($kamar['no_kamar']) ?> - <?= ucfirst(htmlspecialchars($kamar['tipe'])) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_booking" class="form-label">Tanggal Booking</label>
                        <input
                            type="date"
                            class="form-control"
                            id="tanggal_booking"
                            name="tanggal_booking"
                            required
                            value="<?= htmlspecialchars($tanggal_booking) ?>"
                            max="<?= date('Y-m-d') ?>"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai Sewa</label>
                        <input
                            type="date"
                            class="form-control"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            required
                            value="<?= htmlspecialchars($tanggal_mulai) ?>"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai Sewa</label>
                        <input
                            type="date"
                            class="form-control"
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            required
                            value="<?= htmlspecialchars($tanggal_selesai) ?>"
                        />
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Booking Sekarang</button>
                </form>
            </div>

            <div class="footer-text">&copy; <?= date('Y') ?> Kost Ibu Siti. All rights reserved.</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
