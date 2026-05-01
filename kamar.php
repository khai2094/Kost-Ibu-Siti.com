<?php 
include 'koneksi.php';

// Tangkap parameter tipe kamar dari URL
$filter_tipe = '';
if (isset($_GET['tipe'])) {
    $tipe_get = strtolower(trim($_GET['tipe']));
    if (in_array($tipe_get, ['ac', 'non ac', 'nonac'])) {
        // samakan format non ac jadi nonac untuk internal
        $filter_tipe = $tipe_get === 'non ac' ? 'nonac' : $tipe_get;
    }
}

// Query kamar sesuai filter tipe jika ada
$sql = "SELECT id_kamar, no_kamar, tipe, foto, harga FROM kamar WHERE status='kosong'";

if ($filter_tipe !== '') {
    if ($filter_tipe === 'ac') {
        $sql .= " AND LOWER(REPLACE(tipe, ' ', '')) = 'ac'";
    } elseif ($filter_tipe === 'nonac') {
        $sql .= " AND LOWER(REPLACE(tipe, ' ', '')) = 'nonac'";
    }
}

$sql .= " ORDER BY tipe, id_kamar";

$result = $con->query($sql);

$kamars = ['ac' => [], 'nonac' => []];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tipe = strtolower(str_replace(' ', '', $row['tipe']));
        if ($tipe === 'ac' || $tipe === 'nonac') {
            $kamars[$tipe][] = $row;
        }
    }
} else {
    die("Query gagal: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pilih Kamar - Kost Ibu Siti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f4f7fc;
        }
        .content-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .sidebar {
            flex: 1 1 200px;
            max-width: 220px;
            border-right: 1px solid #ddd;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            height: fit-content;
            position: sticky;
            top: 80px;
        }
        .sidebar h5 {
            margin-bottom: 20px;
            color: #0d6efd;
        }
        .sidebar a {
            width: 100%;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            text-align: center;
        }
        .room-cards {
            flex: 3;
            padding: 0 10px;
        }
        .card-img-top {
            height: 160px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #0d6efd;
            padding-left: 10px;
            color: #0d6efd;
            font-weight: bold;
        }
        .card:hover {
            transform: translateY(-5px);
            transition: 0.3s;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }
            .sidebar {
                max-width: 100%;
                position: relative;
                top: unset;
                border-right: none;
                border-bottom: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
<?php require "navbar.php"; ?>

<div class="container py-4">
    <h1 class="text-center text-primary mb-4 fw-bold" style="font-size: 2.5rem;">Pilih kamar yang anda mau!</h1>

    <div class="content-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h5>Tipe Kamar</h5>
            <a href="kamar.php" class="btn <?= $filter_tipe === '' ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
            <a href="kamar.php?tipe=AC" class="btn <?= $filter_tipe === 'ac' ? 'btn-primary' : 'btn-outline-primary' ?>">AC</a>
            <a href="kamar.php?tipe=Non AC" class="btn <?= $filter_tipe === 'nonac' ? 'btn-primary' : 'btn-outline-primary' ?>">Non AC</a>
        </div>

        <!-- Kamar -->
        <div class="room-cards">
            <!-- Kamar AC -->
            <div class="room-section" data-type="ac" <?= $filter_tipe !== '' && $filter_tipe !== 'ac' ? 'style="display:none;"' : '' ?>>
                <h4 class="section-title">Kamar AC</h4>
                <?php if (count($kamars['ac']) > 0): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    <?php foreach ($kamars['ac'] as $kamar): 
                        $img = !empty($kamar['foto']) ? "uploads/" . htmlspecialchars($kamar['foto']) : "https://via.placeholder.com/300x150?text=No+Image"; ?>
                        <div class="col kamar-card">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="<?= $img ?>" class="card-img-top" alt="Kamar <?= htmlspecialchars($kamar['no_kamar']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= htmlspecialchars($kamar['no_kamar']) ?></h5>
                                    <p class="text-muted">Rp<?= number_format($kamar['harga'], 0, ',', '.') ?></p>
                                    <a href="detail_kamar.php?id=<?= urlencode($kamar['id_kamar']) ?>" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada kamar AC yang kosong.</p>
                <?php endif; ?>
            </div>

            <!-- Kamar Non AC -->
            <div class="room-section" data-type="nonac" <?= $filter_tipe !== '' && $filter_tipe !== 'nonac' ? 'style="display:none;"' : '' ?>>
                <h4 class="section-title">Kamar Non AC</h4>
                <?php if (count($kamars['nonac']) > 0): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    <?php foreach ($kamars['nonac'] as $kamar): 
                        $img = !empty($kamar['foto']) ? "uploads/" . htmlspecialchars($kamar['foto']) : "https://via.placeholder.com/300x150?text=No+Image"; ?>
                        <div class="col kamar-card">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="<?= $img ?>" class="card-img-top" alt="Kamar <?= htmlspecialchars($kamar['no_kamar']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= htmlspecialchars($kamar['no_kamar']) ?></h5>
                                    <p class="text-muted">Rp<?= number_format($kamar['harga'], 0, ',', '.') ?></p>
                                    <a href="detail_kamar.php?id=<?= urlencode($kamar['id_kamar']) ?>" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada kamar Non AC yang kosong.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
