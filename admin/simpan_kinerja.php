<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: input_kinerja.php");
    exit;
}

/* ================= DATA ================= */
$id_karyawan = $_POST['id_karyawan'] ?? '';
$tanggal     = $_POST['tanggal'] ?? '';
$id_tarif    = $_POST['id_tarif'] ?? [];
$jumlah      = $_POST['jumlah'] ?? [];

if (!$id_karyawan || !$tanggal) {
    die("Data tidak lengkap");
}

/* ================= 1. CEK ABSENSI ================= */
$qAbsen = mysqli_query($conn,"
    SELECT * FROM absensi
    WHERE id_karyawan='$id_karyawan'
    AND tanggal='$tanggal'
    AND jam_masuk IS NOT NULL
");

if (mysqli_num_rows($qAbsen) == 0) {
    echo "<script>
        alert('❌ Karyawan belum absen pada tanggal ini');
        history.back();
    </script>";
    exit;
}

/* ================= 2. CEK KINERJA DOBEL ================= */
$qCek = mysqli_query($conn,"
    SELECT * FROM kinerja_harian
    WHERE id_karyawan='$id_karyawan'
    AND tanggal='$tanggal'
");

if (mysqli_num_rows($qCek) > 0) {
    echo "<script>
        alert('⚠ Kinerja tanggal ini sudah diinput');
        history.back();
    </script>";
    exit;
}

/* ================= 3. TARIF OTOMATIS ABSEN ================= */
/*
   GANTI ID DI BAWAH SESUAI DATABASE KAMU
*/
$ID_ABSEN     = 1; // tarif absen 7.500
$ID_TRANSPORT = 2; // tarif transport 4.000

mysqli_query($conn,"
    INSERT INTO kinerja_harian
    (id_karyawan,id_tarif,tanggal,jumlah,subtotal)
    VALUES
    ('$id_karyawan','$ID_ABSEN','$tanggal',1,7500),
    ('$id_karyawan','$ID_TRANSPORT','$tanggal',1,4000)
");

/* ================= 4. KINERJA MANUAL ================= */
for ($i = 0; $i < count($id_tarif); $i++) {

    $qty = (int)$jumlah[$i];
    if ($qty <= 0) continue;

    $idTarif = $id_tarif[$i];

    $qTarif = mysqli_query($conn,"
        SELECT tarif FROM tarif_pekerjaan
        WHERE id_tarif='$idTarif'
    ");

    $t = mysqli_fetch_assoc($qTarif);
    if (!$t) continue;

    $subtotal = $qty * $t['tarif'];

    mysqli_query($conn,"
        INSERT INTO kinerja_harian
        (id_karyawan,id_tarif,tanggal,jumlah,subtotal)
        VALUES
        ('$id_karyawan','$idTarif','$tanggal','$qty','$subtotal')
    ");
}

header("Location: data_kinerja.php?status=sukses");
exit;
