<?php
require "../koneksi.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $query = mysqli_query($con, "DELETE FROM kamar WHERE id_kamar = '$id'");

    if ($query) {
        header("Location: kamar.php?msg=hapus_berhasil");
        exit();
    } else {
        header("Location: kamar.php?msg=hapus_gagal");
        exit();
    }
} else {
    header("Location: kamar.php");
    exit();
}
