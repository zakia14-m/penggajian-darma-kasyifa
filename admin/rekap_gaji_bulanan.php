
<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI LOGIN ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================= FILTER BULAN DAN TAHUN ================= */
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$bulan = (string) $bulan;
$tahun = (string) $tahun;

if (!preg_match('/^(0[1-9]|1[0-2])$/', $bulan)) {
    $bulan = date('m');
}

if (!preg_match('/^\d{4}$/', $tahun) ||
    (int)$tahun < (int)date('Y') - 5 ||
    (int)$tahun > (int)date('Y')) {
    $tahun = date('Y');
}

/* ================= AMBIL DATA REKAP ================= */
$stmt = mysqli_prepare($conn, "
    SELECT
        kp.id_karyawan,
        kp.nama_karyawan,
        SUM(k.jumlah * t.tarif) AS total_gaji
    FROM kinerja_harian k
    JOIN karyawan kp ON k.id_karyawan = kp.id_karyawan
    JOIN tarif_pekerjaan t ON k.id_tarif = t.id_tarif
    WHERE MONTH(k.tanggal) = ?
      AND YEAR(k.tanggal) = ?
    GROUP BY kp.id_karyawan, kp.nama_karyawan
    ORDER BY kp.nama_karyawan
");

mysqli_stmt_bind_param($stmt, "ii", $bulan, $tahun);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);

$total_pengeluaran = 0;
$total_karyawan = mysqli_num_rows($query);

$dataRekap = [];

while ($d = mysqli_fetch_assoc($query)) {
    $dataRekap[] = $d;
    $total_pengeluaran += $d['total_gaji'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rekap Gaji Bulanan | Sistem Absensi & Gaji</title>

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

/* NAVBAR */
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

/* CONTAINER */
.container {
    max-width: 1250px;
    margin: auto;
    padding: 40px 5%;
}

/* HEADER */
.page-header {
    margin-bottom: 28px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #14213d;
    margin-bottom: 7px;
}

.page-header p {
    color: #64748b;
    font-size: 13px;
}

/* SUMMARY */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.summary-card {
    background: white;
    padding: 22px;
    border-radius: 15px;
    border: 1px solid #e8edf5;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
    display: flex;
    align-items: center;
    gap: 16px;
}

.summary-icon {
    width: 52px;
    height: 52px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}

.blue {
    background: #dbeafe;
    color: #2563eb;
}

.green {
    background: #dcfce7;
    color: #16a34a;
}

.summary-info p {
    font-size: 11px;
    color: #94a3b8;
    margin-bottom: 5px;
}

.summary-info h3 {
    font-size: 18px;
    color: #1e293b;
}

/* CARD */
.card {
    background: white;
    border-radius: 17px;
    padding: 28px;
    border: 1px solid #e8edf5;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
}

.card-header {
    margin-bottom: 22px;
}

.card-header h2 {
    font-size: 18px;
    color: #1e293b;
}

.card-header p {
    color: #94a3b8;
    font-size: 12px;
    margin-top: 5px;
}

/* FILTER */
.filter-box {
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 22px;
}

.filter-title {
    font-size: 12px;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 13px;
}

.filter-form {
    display: flex;
    align-items: end;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-group label {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
}

.filter-group select {
    min-width: 140px;
    padding: 10px 12px;
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    background: white;
    color: #334155;
    font-size: 12px;
    outline: none;
}

.filter-group select:focus {
    border-color: #2563eb;
}

/* BUTTON */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: .3s;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

/* TABLE */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 10px;
}

table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
}

th {
    background: #14213d;
    color: white;
    padding: 15px 14px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

td {
    padding: 15px 14px;
    border-bottom: 1px solid #edf1f7;
    font-size: 12px;
    color: #475569;
}

tbody tr:hover {
    background: #f8faff;
}

td strong {
    color: #1e293b;
}

tfoot td {
    background: #eff6ff;
    color: #1e40af;
    font-weight: 700;
    border-bottom: none;
}

/* EMPTY */
.empty-data {
    text-align: center;
    padding: 40px 15px;
    color: #94a3b8;
}

.empty-data i {
    font-size: 38px;
    margin-bottom: 12px;
    color: #cbd5e1;
}

.empty-data p {
    font-size: 13px;
}

/* FOOTER */
.footer {
    text-align: center;
    padding: 22px;
    color: #94a3b8;
    font-size: 11px;
}

/* RESPONSIVE */
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

    .page-header h1 {
        font-size: 23px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .card {
        padding: 18px 14px;
    }

    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group select {
        width: 100%;
    }

    .filter-form .btn {
        width: 100%;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="logo">
        <i class="fa-solid fa-building-shield"></i>
        <span>Sistem Absensi & Gaji</span>
    </div>

    <ul>
        <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="input_kinerja.php"><i class="fa-solid fa-clipboard-list"></i> Input Kinerja</a></li>
        <li><a href="karyawan.php"><i class="fa-solid fa-users"></i> Data Karyawan</a></li>
        <li><a href="rekap_gaji_mingguan.php"><i class="fa-solid fa-calendar-week"></i> Rekap Mingguan</a></li>
        <li><a href="rekap_gaji_bulanan.php" class="active"><i class="fa-solid fa-calendar-days"></i> Rekap Bulanan</a></li>
        <li><a href="tarif.php"><i class="fa-solid fa-money-bill-wave"></i> Tarif</a></li>
        <li>
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari akun?')">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<!-- CONTENT -->
<main class="container">

    <div class="page-header">
        <h1>Rekap Gaji Bulanan</h1>
        <p>Pantau total pengeluaran gaji karyawan berdasarkan periode bulanan.</p>
    </div>

    <!-- SUMMARY -->
    <div class="summary-grid">

        <div class="summary-card">
            <div class="summary-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="summary-info">
                <p>Total Karyawan</p>
                <h3><?= number_format($total_karyawan, 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon green">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="summary-info">
                <p>Total Pengeluaran Gaji</p>
                <h3>Rp<?= number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
            </div>
        </div>

    </div>

    <!-- REKAP CARD -->
    <section class="card">

        <div class="card-header">
            <h2><i class="fa-solid fa-chart-column" style="color:#2563eb;"></i> Rincian Penggajian</h2>
            <p>Daftar total gaji setiap karyawan berdasarkan bulan dan tahun.</p>
        </div>

        <!-- FILTER -->
        <div class="filter-box">

            <div class="filter-title">
                <i class="fa-solid fa-filter"></i> Filter Periode
            </div>

            <form method="GET" class="filter-form">

                <div class="filter-group">
                    <label for="bulan">Bulan</label>
                    <select name="bulan" id="bulan">
                        <?php for ($b = 1; $b <= 12; $b++) { ?>
                            <option value="<?= sprintf('%02d', $b); ?>"
                                <?= $bulan == sprintf('%02d', $b) ? 'selected' : ''; ?>>
                                <?= date('F', mktime(0, 0, 0, $b, 1)); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="tahun">Tahun</label>
                    <select name="tahun" id="tahun">
                        <?php for ($t = (int)date('Y'); $t >= (int)date('Y') - 5; $t--) { ?>
                            <option value="<?= $t; ?>"
                                <?= $tahun == $t ? 'selected' : ''; ?>>
                                <?= $t; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
                </button>

            </form>
        </div>

        <!-- TABLE -->
        <div class="table-wrapper">

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Total Gaji</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($dataRekap) > 0) { ?>

                    <?php $no = 1; ?>

                    <?php foreach ($dataRekap as $d) { ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td>
                            <strong><?= htmlspecialchars($d['nama_karyawan']); ?></strong>
                        </td>

                        <td>
                            Rp<?= number_format($d['total_gaji'], 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="3">
                            <div class="empty-data">
                                <i class="fa-solid fa-folder-open"></i>
                                <p>Belum ada data penggajian pada periode ini.</p>
                            </div>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;">
                            TOTAL PENGELUARAN
                        </td>
                        <td>
                            Rp<?= number_format($total_pengeluaran, 0, ',', '.'); ?>
                        </td>
                    </tr>
                </tfoot>

            </table>

        </div>

    </section>

</main>

<footer class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

</body>
</html>
