<?php
require "session.php";
require "../koneksi.php";

$msg = '';

// Tambah pembayaran
if (isset($_POST['simpan_pembayaran'])) {
    $id_pembayaran = htmlspecialchars($_POST['id_pembayaran']);
    $id_kontrak = htmlspecialchars($_POST['id_kontrak']);
    $tanggal_bayar = htmlspecialchars($_POST['tanggal_bayar']);
    $jumlah = htmlspecialchars($_POST['jumlah']);
    $metode_bayar = htmlspecialchars($_POST['metode_bayar']);

    // Cek apakah ID pembayaran sudah ada
    $cekID = mysqli_query($con, "SELECT id_pembayaran FROM pembayaran WHERE id_pembayaran = '$id_pembayaran' LIMIT 1");
    if (mysqli_num_rows($cekID) > 0) {
        $msg = '<div class="alert alert-danger">ID Pembayaran sudah ada.</div>';
    } else {
        // Simpan pembayaran
        $simpan = mysqli_query($con, "INSERT INTO pembayaran (id_pembayaran, id_kontrak, tanggal_bayar, jumlah, metode_bayar) 
            VALUES ('$id_pembayaran', '$id_kontrak', '$tanggal_bayar', '$jumlah', '$metode_bayar')");
        
        if ($simpan) {
            // Jika berhasil, update status kontrak jadi confirmed
            $updateStatus = mysqli_query($con, "UPDATE kontrak SET status='confirmed' WHERE id_kontrak = '$id_kontrak'");
            if ($updateStatus) {
                $msg = '<div class="alert alert-success">Data pembayaran berhasil disimpan dan status kontrak diupdate menjadi confirmed.</div>';
            } else {
                $msg = '<div class="alert alert-warning">Pembayaran berhasil disimpan, tapi gagal mengupdate status kontrak.</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger">Gagal menyimpan data pembayaran.</div>';
        }
    }
}
?>
