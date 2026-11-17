
<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI LOGIN KARYAWAN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];
$hari_ini = date('Y-m-d');

/* =====================
   GAJI HARI INI
===================== */
$qHari = mysqli_query($conn, "
    SELECT IFNULL(SUM(k.jumlah * t.tarif),0) AS gaji_hari_ini
    FROM kinerja_harian k
    JOIN tarif_pekerjaan t ON k.id_tarif = t.id_tarif
    WHERE k.id_karyawan = '$id_karyawan'
    AND k.tanggal = '$hari_ini'
");

$gaji_hari_ini = mysqli_fetch_assoc($qHari)['gaji_hari_ini'];

/* =====================
   GAJI MINGGU INI
===================== */
$awal_minggu = date('Y-m-d', strtotime('monday this week'));
$akhir_minggu = date('Y-m-d', strtotime('sunday this week'));

$qMinggu = mysqli_query($conn, "
    SELECT IFNULL(SUM(k.jumlah * t.tarif),0) AS gaji_mingguan
    FROM kinerja_harian k
    JOIN tarif_pekerjaan t ON k.id_tarif = t.id_tarif
    WHERE k.id_karyawan = '$id_karyawan'
    AND k.tanggal BETWEEN '$awal_minggu' AND '$akhir_minggu'
");

$gaji_mingguan = mysqli_fetch_assoc($qMinggu)['gaji_mingguan'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Karyawan | Sistem Absensi & Gaji</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f4f7fc;
    color: #1e293b;
}

/* ================= NAVBAR ================= */
.navbar {
    width: 100%;
    min-height: 75px;
    background: #101d35;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 5%;
    color: white;
    gap: 20px;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 17px;
    font-weight: 700;
    white-space: nowrap;
}

.logo i {
    color: #38bdf8;
    font-size: 25px;
}

.navbar ul {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.navbar ul li a {
    text-decoration: none;
    color: #cbd5e1;
    font-size: 12px;
    font-weight: 500;
    padding: 10px 12px;
    border-radius: 8px;
    transition: .3s;
    display: block;
}

.navbar ul li a:hover,
.navbar ul li a.active {
    background: #2563eb;
    color: white;
}

.navbar ul li:last-child a {
    background: #dc2626;
    color: white;
}

.navbar ul li:last-child a:hover {
    background: #b91c1c;
}

/* ================= CONTENT ================= */
.container {
    max-width: 1250px;
    margin: auto;
    padding: 40px 5%;
}

.header-content {
    margin-bottom: 28px;
}

.header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: #14213d;
    margin-bottom: 7px;
}

.header-content p {
    color: #64748b;
    font-size: 13px;
}

/* ================= WELCOME CARD ================= */
.welcome-card {
    background: linear-gradient(120deg, #14213d, #2563eb);
    border-radius: 20px;
    padding: 35px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
    overflow: hidden;
    position: relative;
    margin-bottom: 30px;
    box-shadow: 0 10px 25px rgba(37, 99, 235, .15);
}

.welcome-text {
    position: relative;
    z-index: 1;
}

.welcome-text .label {
    display: inline-block;
    background: rgba(255,255,255,.15);
    padding: 7px 13px;
    border-radius: 30px;
    font-size: 11px;
    margin-bottom: 15px;
}

.welcome-text h2 {
    font-size: 25px;
    margin-bottom: 10px;
}

.welcome-text p {
    color: #dbeafe;
    font-size: 13px;
    line-height: 1.8;
    max-width: 600px;
}

.welcome-image {
    width: 130px;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.12);
    border-radius: 25px;
    font-size: 65px;
    color: white;
    position: relative;
    z-index: 1;
}

.welcome-decoration {
    position: absolute;
    width: 220px;
    height: 220px;
    border: 35px solid rgba(255,255,255,.07);
    border-radius: 50%;
    right: 100px;
    top: -90px;
}

/* ================= SECTION ================= */
.section-title {
    margin-bottom: 18px;
}

.section-title h3 {
    font-size: 18px;
    color: #1e293b;
}

.section-title p {
    color: #94a3b8;
    font-size: 12px;
    margin-top: 4px;
}

/* ================= GAJI CARD ================= */
.gaji-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
    margin-bottom: 35px;
}

.gaji-card {
    background: white;
    border-radius: 17px;
    padding: 25px;
    border: 1px solid #e8edf5;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
    transition: .3s;
}

.gaji-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(37, 99, 235, .10);
    border-color: #bfdbfe;
}

.gaji-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
    margin-bottom: 18px;
}

.icon-blue {
    background: #dbeafe;
    color: #2563eb;
}

.icon-purple {
    background: #ede9fe;
    color: #7c3aed;
}

.gaji-card h3 {
    font-size: 15px;
    margin-bottom: 8px;
    color: #64748b;
    font-weight: 500;
}

.nominal {
    font-size: 25px;
    font-weight: 700;
    color: #14213d;
}

.gaji-card small {
    display: block;
    color: #94a3b8;
    font-size: 11px;
    margin-top: 8px;
}

/* ================= MENU GRID ================= */
.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
}

.menu-card {
    background: white;
    border-radius: 17px;
    padding: 25px;
    border: 1px solid #e8edf5;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
    transition: .3s;
}

.menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(37, 99, 235, .10);
    border-color: #bfdbfe;
}

.menu-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
    margin-bottom: 18px;
}

.menu-card h3 {
    font-size: 16px;
    margin-bottom: 8px;
    color: #1e293b;
}

.menu-card p {
    font-size: 12px;
    line-height: 1.8;
    color: #64748b;
    margin-bottom: 20px;
}

/* ================= BUTTON ================= */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    background: #2563eb;
    color: white;
    padding: 10px 17px;
    border-radius: 9px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: .3s;
}

.btn:hover {
    background: #1d4ed8;
    transform: translateX(3px);
}

/* ================= FOOTER ================= */
.footer {
    text-align: center;
    padding: 22px;
    color: #94a3b8;
    font-size: 11px;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 900px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar ul {
        width: 100%;
    }
}

@media (max-width: 600px) {
    .navbar {
        padding: 18px 5%;
    }

    .navbar ul {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        width: 100%;
    }

    .navbar ul li a {
        text-align: center;
        font-size: 11px;
    }

    .container {
        padding-top: 28px;
    }

    .header-content h1 {
        font-size: 23px;
    }

    .welcome-card {
        padding: 25px;
    }

    .welcome-text h2 {
        font-size: 20px;
    }

    .welcome-image {
        width: 80px;
        height: 80px;
        font-size: 40px;
    }

    .gaji-grid,
    .card-grid {
        grid-template-columns: 1fr;
    }

    .nominal {
        font-size: 22px;
    }
}

@media (max-width: 380px) {
    .welcome-image {
        display: none;
    }
}
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar">

    <div class="logo">
        <i class="fa-solid fa-building-shield"></i>
        <span>Sistem Absensi & Gaji</span>
    </div>

    <ul>
        <li>
            <a href="dashboard.php" class="active">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="absen.php">
                <i class="fa-solid fa-calendar-check"></i> Absen
            </a>
        </li>

        <li>
            <a href="gaji_harian.php">
                <i class="fa-solid fa-money-bill-wave"></i> Gaji Harian
            </a>
        </li>

        <li>
            <a href="gaji_mingguan.php">
                <i class="fa-solid fa-calendar-week"></i> Slip Gaji Mingguan
            </a>
        </li>

        <li>
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari akun?')">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>

</nav>

<!-- ================= CONTENT ================= -->
<main class="container">

    <div class="header-content">
        <h1>Dashboard Karyawan</h1>
        <p>Selamat datang di halaman informasi absensi dan penggajian Darma Kasyifa.</p>
    </div>

    <!-- ================= WELCOME ================= -->
    <section class="welcome-card">

        <div class="welcome-decoration"></div>

        <div class="welcome-text">

            <span class="label">
                <i class="fa-solid fa-user"></i> Karyawan
            </span>

            <h2>Selamat Datang! 👋</h2>

            <p>
                Pantau penghasilan harian dan mingguan, lakukan absensi,
                serta periksa informasi gaji melalui satu halaman.
            </p>

        </div>

        <div class="welcome-image">
            <i class="fa-solid fa-user-tie"></i>
        </div>

    </section>

    <!-- ================= INFORMASI GAJI ================= -->
    <div class="section-title">
        <h3>Ringkasan Penghasilan</h3>
        <p>Informasi total penghasilan berdasarkan data kinerja yang tercatat.</p>
    </div>

    <div class="gaji-grid">

        <div class="gaji-card">

            <div class="gaji-icon icon-blue">
                <i class="fa-solid fa-wallet"></i>
            </div>

            <h3>Gaji Hari Ini</h3>

            <div class="nominal">
                Rp <?= number_format($gaji_hari_ini, 0, ',', '.'); ?>
            </div>

            <small>
                <i class="fa-solid fa-calendar-day"></i>
                Penghasilan tanggal <?= date('d-m-Y'); ?>
            </small>

        </div>

        <div class="gaji-card">

            <div class="gaji-icon icon-purple">
                <i class="fa-solid fa-chart-line"></i>
            </div>

            <h3>Gaji Minggu Ini</h3>

            <div class="nominal">
                Rp <?= number_format($gaji_mingguan, 0, ',', '.'); ?>
            </div>

            <small>
                <i class="fa-solid fa-calendar-week"></i>
                <?= date('d-m-Y', strtotime($awal_minggu)); ?>
                s/d
                <?= date('d-m-Y', strtotime($akhir_minggu)); ?>
            </small>

        </div>

    </div>

    <!-- ================= MENU ================= -->
    <div class="section-title">
        <h3>Menu Karyawan</h3>
        <p>Pilih menu untuk mengakses layanan sistem.</p>
    </div>

    <div class="card-grid">

        <div class="menu-card">

            <div class="menu-icon icon-blue">
                <i class="fa-solid fa-calendar-check"></i>
            </div>

            <h3>Absensi Karyawan</h3>

            <p>
                Lakukan absensi masuk dan pulang serta pantau
                status kehadiran melalui sistem.
            </p>

            <a href="absen.php" class="btn">
                Mulai Absen
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

        <div class="menu-card">

            <div class="menu-icon icon-purple">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>

            <h3>Gaji Harian</h3>

            <p>
                Lihat rincian penghasilan harian berdasarkan
                jenis pekerjaan dan jumlah produksi.
            </p>

            <a href="gaji_harian.php" class="btn">
                Lihat Gaji
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

        <div class="menu-card">

            <div class="menu-icon icon-blue">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>

            <h3>Slip Gaji Mingguan</h3>

            <p>
                Periksa rincian penghasilan dan total gaji
                selama satu periode mingguan.
            </p>

            <a href="gaji_mingguan.php" class="btn">
                Lihat Slip Gaji
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</main>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

</body>
</html>
