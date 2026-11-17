
<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI LOGIN ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Validasi format tanggal
$parsedDate = DateTime::createFromFormat('Y-m-d', $tanggal);

if (!$parsedDate || $parsedDate->format('Y-m-d') !== $tanggal) {
    $tanggal = date('Y-m-d');
}

// Hitung rentang minggu
$awal  = date('Y-m-d', strtotime('monday this week', strtotime($tanggal)));
$akhir = date('Y-m-d', strtotime('sunday this week', strtotime($tanggal)));

// Ambil data rekap gaji
$query = mysqli_query($conn, "
    SELECT 
        k.id_karyawan,
        kp.nama_karyawan,
        SUM(k.jumlah) AS total_jumlah,
        SUM(k.jumlah * t.tarif) AS total_gaji
    FROM kinerja_harian k
    JOIN karyawan kp ON k.id_karyawan = kp.id_karyawan
    JOIN tarif_pekerjaan t ON k.id_tarif = t.id_tarif
    WHERE k.tanggal BETWEEN '$awal' AND '$akhir'
    GROUP BY k.id_karyawan, kp.nama_karyawan
    ORDER BY kp.nama_karyawan
");

if (!$query) {
    die("Gagal mengambil data rekap: " . mysqli_error($conn));
}

$totalKaryawan = mysqli_num_rows($query);
$totalProduksi = 0;
$totalGaji = 0;

$dataRekap = [];

while ($d = mysqli_fetch_assoc($query)) {
    $dataRekap[] = $d;
    $totalProduksi += $d['total_jumlah'];
    $totalGaji += $d['total_gaji'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rekap Gaji Mingguan | Sistem Absensi & Gaji</title>

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
    grid-template-columns: repeat(3, 1fr);
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

.purple {
    background: #ede9fe;
    color: #7c3aed;
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

/* CARD HEADER */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
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

/* PERIODE */
.periode-box {
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    padding: 17px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.periode-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.periode-icon {
    width: 42px;
    height: 42px;
    background: #dbeafe;
    color: #2563eb;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.periode-info p {
    font-size: 11px;
    color: #64748b;
}

.periode-info h3 {
    font-size: 13px;
    color: #1e40af;
    margin-top: 4px;
}

/* FILTER */
.filter-form {
    display: flex;
    align-items: center;
    gap: 9px;
    flex-wrap: wrap;
}

.filter-form input {
    padding: 9px 12px;
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    color: #334155;
    font-size: 12px;
    outline: none;
}

.filter-form input:focus {
    border-color: #2563eb;
}

/* BUTTON */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 15px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: .3s;
    white-space: nowrap;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-slip {
    background: #dbeafe;
    color: #1d4ed8;
}

.btn-slip:hover {
    background: #bfdbfe;
}

/* TABLE */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 10px;
}

table {
    width: 100%;
    min-width: 700px;
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

/* EMPTY */
.empty-data {
    text-align: center;
    padding: 45px 15px;
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

@media (max-width: 700px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }

    .periode-box {
        align-items: flex-start;
        flex-direction: column;
    }

    .filter-form {
        width: 100%;
    }

    .filter-form input {
        flex: 1;
        min-width: 0;
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

    .card {
        padding: 18px 14px;
    }

    .card-header {
        align-items: flex-start;
        flex-direction: column;
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
        <li><a href="rekap_gaji_mingguan.php" class="active"><i class="fa-solid fa-calendar-week"></i> Rekap Mingguan</a></li>
        <li><a href="rekap_gaji_bulanan.php"><i class="fa-solid fa-calendar-days"></i> Rekap Bulanan</a></li>
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
        <h1>Rekap Gaji Mingguan</h1>
        <p>Pantau hasil produksi dan total penggajian karyawan setiap minggu.</p>
    </div>

    <!-- SUMMARY -->
    <div class="summary-grid">

        <div class="summary-card">
            <div class="summary-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="summary-info">
                <p>Total Karyawan</p>
                <h3><?= number_format($totalKaryawan, 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon purple">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="summary-info">
                <p>Total Produksi</p>
                <h3><?= number_format($totalProduksi, 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon green">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="summary-info">
                <p>Total Pengeluaran Gaji</p>
                <h3>Rp<?= number_format($totalGaji, 0, ',', '.'); ?></h3>
            </div>
        </div>

    </div>

    <!-- REKAP CARD -->
    <section class="card">

        <div class="card-header">
            <div>
                <h2><i class="fa-solid fa-chart-column" style="color:#2563eb;"></i> Daftar Rekap Gaji</h2>
                <p>Rincian produksi dan gaji berdasarkan karyawan.</p>
            </div>
        </div>

        <!-- PERIODE -->
        <div class="periode-box">

            <div class="periode-info">
                <div class="periode-icon">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>

                <div>
                    <p>Periode Rekap Mingguan</p>
                    <h3>
                        <?= date('d-m-Y', strtotime($awal)); ?>
                        s/d
                        <?= date('d-m-Y', strtotime($akhir)); ?>
                    </h3>
                </div>
            </div>

            <form method="GET" class="filter-form">
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>" required>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Tampilkan
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
                        <th>Total Produksi</th>
                        <th>Total Gaji</th>
                        <th>Aksi</th>
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
                            <?= number_format($d['total_jumlah'], 0, ',', '.'); ?>
                        </td>

                        <td>
                            <strong>
                                Rp<?= number_format($d['total_gaji'], 0, ',', '.'); ?>
                            </strong>
                        </td>

                        <td>
                            <a href="slip_gaji_mingguan.php?id_karyawan=<?= urlencode($d['id_karyawan']); ?>&tanggal=<?= urlencode($tanggal); ?>"
                               class="btn btn-slip">
                                <i class="fa-solid fa-file-invoice"></i> Lihat Slip
                            </a>
                        </td>
                    </tr>
                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="5">
                            <div class="empty-data">
                                <i class="fa-solid fa-folder-open"></i>
                                <p>Belum ada data produksi pada periode minggu ini.</p>
                            </div>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>
        </div>

    </section>

</main>

<footer class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

</body>
</html>
