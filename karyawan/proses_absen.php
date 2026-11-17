<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';

/* ================= PROTEKSI ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: ../login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];
$aksi   = $_POST['aksi'] ?? '';
$lat    = $_POST['lat'] ?? '';
$lng    = $_POST['lng'] ?? '';
$alasan = trim($_POST['alasan'] ?? '');

/* ================= VALIDASI LOKASI ================= */
if (empty($lat) || empty($lng)) {
    echo "<script>alert('Lokasi tidak valid');history.back()</script>";
    exit;
}

/* ================= WAKTU ================= */
$tanggal = date('Y-m-d');
$jam     = date('H:i:s');

$jamMasukLimit  = strtotime("$tanggal 08:00:00");
$jamPulangResmi = strtotime("$tanggal 14:00:00");
$jamSekarang    = strtotime("$tanggal $jam");

/* ================= CEK DATA ABSENSI ================= */
$qCek = mysqli_query($conn, "
    SELECT * FROM absensi 
    WHERE id_karyawan='$id_karyawan'
    AND tanggal='$tanggal'
");
$dataAbsen = mysqli_fetch_assoc($qCek);

/* ====================================================
   ABSEN MASUK
==================================================== */
if ($aksi === 'masuk') {

    if ($dataAbsen) {
        echo "<script>alert('Anda sudah absen masuk hari ini');history.back()</script>";
        exit;
    }

    $statusMasuk = ($jamSekarang > $jamMasukLimit) ? 'telat' : 'hadir';

    mysqli_query($conn, "
        INSERT INTO absensi
        (id_karyawan, tanggal, jam_masuk, status_masuk, latitude, longitude)
        VALUES
        ('$id_karyawan', '$tanggal', '$jam', '$statusMasuk', '$lat', '$lng')
    ");
}

/* ====================================================
   ABSEN PULANG
==================================================== */
elseif ($aksi === 'pulang') {

    if (!$dataAbsen) {
        echo "<script>alert('Anda belum absen masuk');history.back()</script>";
        exit;
    }

    if (!empty($dataAbsen['jam_pulang'])) {
        echo "<script>alert('Anda sudah absen pulang');history.back()</script>";
        exit;
    }

    /* ===== LOGIKA STATUS PULANG ===== */
    if ($jamSekarang < $jamPulangResmi) {

        if ($alasan === '') {
            echo "<script>alert('Pulang sebelum jam 14 wajib isi alasan');history.back()</script>";
            exit;
        }

        $statusPulang = 'pulang_cepat';

    } elseif ($jamSekarang > $jamPulangResmi) {

        $statusPulang = 'lembur';

    } else {

        // tepat jam 14:00:00
        $statusPulang = 'normal';
    }

    mysqli_query($conn, "
        UPDATE absensi SET
            jam_pulang   = '$jam',
            status_pulang= '$statusPulang',
            alasan       = ".($alasan !== '' ? "'$alasan'" : "NULL")."
        WHERE id_karyawan='$id_karyawan'
        AND tanggal='$tanggal'
    ");
}

/* ================= REDIRECT ================= */
header("Location: absen.php?absen=sukses");
exit;
