<?php
include 'koneksi.php'; // pastikan koneksi dan $conn benar

if (isset($_POST['submit_keluhan'])) {
    $id_penghuni = $_POST['id_penghuni'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    // Validasi sederhana
    if (empty($id_penghuni) || empty($tanggal) || empty($deskripsi)) {
        // Bisa redirect dengan pesan error atau tampilkan pesan
        echo "Semua field wajib diisi!";
        exit;
    }

    // Prepare statement untuk keamanan
    $stmt = $con->prepare("INSERT INTO keluhan (id_penghuni, tanggal_keluhan, deskripsi) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Prepare statement gagal: " . $conn->error;
        exit;
    }

    $stmt->bind_param("sss", $id_penghuni, $tanggal, $deskripsi);

if ($stmt->execute()) {
    header("Location: index.php?pesan=sukses");
    exit;
} else {
    header("Location: index.php?pesan=gagal");
    exit;
}
}
?>