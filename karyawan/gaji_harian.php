<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];

$query = mysqli_query($conn, "
    SELECT
        k.tanggal,
        t.jenis_pekerjaan,
        k.jumlah,
        t.tarif,
        (k.jumlah * t.tarif) AS total
    FROM kinerja_harian k
    JOIN tarif_pekerjaan t ON k.id_tarif = t.id_tarif
    WHERE k.id_karyawan='$id_karyawan'
    ORDER BY k.tanggal DESC
");

$totalKeseluruhan = 0;
$dataGaji = [];

if ($query) {
    while ($d = mysqli_fetch_assoc($query)) {
        $dataGaji[] = $d;
        $totalKeseluruhan += (float) $d['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gaji Harian | Sistem Absensi & Gaji</title>

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
    line-height: 1.8;
}

/* ================= SUMMARY ================= */
.summary-card {
    background: linear-gradient(135deg, #14213d, #2563eb);
    color: white;
    border-radius: 18px;
    padding: 25px 28px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(37, 99, 235, .12);
}

.summary-card .summary-label {
    font-size: 12px;
    color: #dbeafe;
    margin-bottom: 8px;
}

.summary-card h2 {
    font-size: 27px;
    font-weight: 700;
}

.summary-card .summary-icon {
    float: right;
    font-size: 34px;
    color: rgba(255,255,255,.75);
    margin-top: -38px;
}

/* ================= TABLE CARD ================= */
.table-card {
    background: white;
    border-radius: 18px;
    padding: 28px;
    border: 1px solid #e8edf5;
    box-shadow: 0 8px 25px rgba(15, 23, 42, .05);
}

.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 22px;
}

.table-header h2 {
    color: #14213d;
    font-size: 18px;
    font-weight: 700;
}

.table-header p {
    color: #64748b;
    font-size: 12px;
    margin-top: 4px;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

thead th {
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 12px;
    font-weight: 600;
    padding: 15px 14px;
    text-align: left;
    white-space: nowrap;
}

tbody td {
    padding: 15px 14px;
    border-bottom: 1px solid #e8edf5;
    color: #475569;
    font-size: 12px;
}

tbody tr:hover {
    background: #f8fafc;
}

tbody tr:last-child td {
    border-bottom: none;
}

.total {
    color: #16a34a;
    font-weight: 700;
    white-space: nowrap;
}

.tarif {
    white-space: nowrap;
}

.empty-state {
    text-align: center;
    padding: 45px 15px;
    color: #64748b;
}

.empty-state i {
    font-size: 38px;
    color: #94a3b8;
    margin-bottom: 14px;
}

.empty-state h3 {
    color: #334155;
    font-size: 15px;
    margin-bottom: 6px;
}

.empty-state p {
    font-size: 12px;
    line-height: 1.7;
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

    .summary-card {
        padding: 22px;
    }

    .summary-card h2 {
        font-size: 23px;
    }

    .table-card {
        padding: 20px 15px;
    }

    .table-header h2 {
        font-size: 16px;
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
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="absen.php">
                <i class="fa-solid fa-calendar-check"></i> Absen
            </a>
        </li>
        <li>
            <a href="gaji_harian.php" class="active">
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
        <h1>Gaji Harian</h1>
        <p>Lihat rincian hasil pekerjaan dan total pendapatan harian Anda.</p>
    </div>

    <section class="summary-card">
        <div class="summary-label">Total Pendapatan dari Seluruh Data</div>
        <h2>Rp<?= number_format($totalKeseluruhan, 0, ',', '.'); ?></h2>
        <i class="fa-solid fa-wallet summary-icon"></i>
    </section>

    <section class="table-card">
        <div class="table-header">
            <div>
                <h2><i class="fa-solid fa-list-check" style="color:#2563eb;margin-right:7px;"></i> Detail Gaji Harian</h2>
                <p>Rincian pekerjaan berdasarkan data kinerja yang telah dicatat.</p>
            </div>
        </div>

        <?php if (count($dataGaji) > 0) { ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Pekerjaan</th>
                            <th>Jumlah</th>
                            <th>Tarif</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($dataGaji as $d) { ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= date('d M Y', strtotime($d['tanggal'])); ?></td>
                                <td><?= htmlspecialchars($d['jenis_pekerjaan']); ?></td>
                                <td><?= number_format((float)$d['jumlah'], 0, ',', '.'); ?></td>
                                <td class="tarif">Rp<?= number_format((float)$d['tarif'], 0, ',', '.'); ?></td>
                                <td class="total">Rp<?= number_format((float)$d['total'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="empty-state">
                <i class="fa-solid fa-folder-open"></i>
                <h3>Belum Ada Data Gaji</h3>
                <p>Data gaji harian akan muncul setelah kinerja pekerjaan Anda dicatat oleh admin.</p>
            </div>
        <?php } ?>
    </section>

</main>

<footer class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

</body>
</html>
