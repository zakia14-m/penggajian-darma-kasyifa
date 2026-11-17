<?php
session_start();
include '../koneksi.php';

// proteksi admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// ambil id tarif
$id = $_GET['id'] ?? '';

if (!$id) {
    die("ID tarif tidak valid");
}

// cek data tarif
$qCek = mysqli_query($conn, "
    SELECT id_tarif 
    FROM tarif_pekerjaan 
    WHERE id_tarif = '$id'
");

if (mysqli_num_rows($qCek) == 0) {
    die("Data tarif tidak ditemukan");
}

// hapus tarif
mysqli_query($conn, "
    DELETE FROM tarif_pekerjaan 
    WHERE id_tarif = '$id'
");

// kembali ke halaman tarif
header("Location: tarif.php?status=hapus");
exit;
