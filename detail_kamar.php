<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? $con->real_escape_string($_GET['id']) : '';
if (!$id) {
    die("ID kamar tidak ditemukan.");
}

$sql = "SELECT * FROM kamar WHERE id_kamar = '$id'";
$result = $con->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Kamar tidak ditemukan.");
}

$kamar = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Detail Kamar - Kost Ibu Siti</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 40px;
            max-width: 900px;
        }
        .detail-box {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .img-preview {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 12px;
            background-color: #f8f9fa;
            transition: transform 0.3s ease;
        }
        .img-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
        }
        h3.text-primary {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.5;
        }
        .btn-book, .btn-back {
            min-width: 150px;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-book {
            background-color: #0d6efd;
            color: white;
        }
        .btn-book:hover {
            background-color: #0b5ed7;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
                gap: 10px;
            }
        }
        /* Optional: Tooltip style for booking button */
        .btn-book[title] {
            position: relative;
        }
        .btn-book[title]:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #0d6efd;
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
            z-index: 10;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body>
<?php require 'navbar.php'; ?>

<div class="container">
    <div class="detail-box">
        <img src="<?= !empty($kamar['foto']) ? 'uploads/' . htmlspecialchars($kamar['foto']) : 'https://via.placeholder.com/800x400?text=No+Image' ?>" 
             alt="Foto Kamar <?= htmlspecialchars($kamar['no_kamar']) ?>" class="img-preview" />
        <h3 class="text-primary">Kamar <?= htmlspecialchars($kamar['no_kamar']) ?></h3>
        <p><strong>Tipe Kamar:</strong> <?= htmlspecialchars(ucwords($kamar['tipe'])) ?></p>
        <p><strong>Harga:</strong> Rp<?= number_format($kamar['harga'], 0, ',', '.') ?> / bulan</p>
        <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($kamar['detail'])) ?></p>

        <div class="btn-group">
            <a href="kamar.php" class="btn btn-back">← Kembali</a>
            <a href="form_booking.php?id=<?= urlencode($kamar['id_kamar']) ?>" class="btn btn-book" title="Pesan kamar ini sekarang!">Booking Sekarang</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
