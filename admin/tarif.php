
<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI LOGIN ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================= AMBIL DATA TARIF ================= */
$data = mysqli_query($conn, "
    SELECT * FROM tarif_pekerjaan
    ORDER BY jenis_pekerjaan ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Tarif Pekerjaan | Sistem Absensi & Gaji</title>

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
    background: #101d35;
    min-height: 75px;
    padding: 15px 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    color: white;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
}

.logo i {
    color: #60a5fa;
    font-size: 22px;
}

.navbar ul {
    list-style: none;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.navbar ul li a {
    display: block;
    text-decoration: none;
    color: #cbd5e1;
    font-size: 12px;
    font-weight: 500;
    padding: 9px 12px;
    border-radius: 7px;
    transition: .2s;
}

.navbar ul li a:hover,
.navbar ul li a.active {
    background: #2563eb;
    color: white;
}

.navbar ul li a.logout {
    background: #dc2626;
    color: white;
}

/* ================= CONTENT ================= */
.container {
    max-width: 1250px;
    padding: 40px 5%;
    margin: auto;
}

/* ================= CARD ================= */
.card {
    background: white;
    border-radius: 17px;
    padding: 25px;
    border: 1px solid #e5eaf2;
    box-shadow: 0 5px 20px rgba(15, 23, 42, .04);
}

/* ================= HEADER ================= */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.header h2 {
    font-size: 21px;
    font-weight: 700;
    color: #14213d;
}

.header p {
    font-size: 13px;
    color: #64748b;
    margin-top: 5px;
}

/* ================= BUTTON ================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    color: white;
    border: none;
    transition: .2s;
    cursor: pointer;
    white-space: nowrap;
}

.btn:hover {
    opacity: .88;
    transform: translateY(-1px);
}

.btn-success {
    background: #16a34a;
}

.btn-warning {
    background: #f59e0b;
}

.btn-danger {
    background: #dc2626;
}

/* ================= TABLE ================= */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 750px;
    border-collapse: collapse;
}

th {
    background: #1e40af;
    color: white;
    padding: 14px 12px;
    font-size: 12px;
    font-weight: 600;
    text-align: left;
    white-space: nowrap;
}

td {
    padding: 13px 12px;
    border-bottom: 1px solid #e5eaf2;
    font-size: 12px;
    color: #475569;
    vertical-align: middle;
}

tr:hover td {
    background: #f8fafc;
}

td:first-child,
th:first-child {
    text-align: center;
    width: 55px;
}

.tarif {
    color: #15803d;
    font-weight: 700;
    white-space: nowrap;
}

.keterangan {
    max-width: 250px;
}

/* ================= EMPTY DATA ================= */
.empty-data {
    text-align: center;
    padding: 35px 15px;
    color: #94a3b8;
}

.empty-data i {
    font-size: 35px;
    margin-bottom: 12px;
}

.empty-data p {
    font-size: 13px;
}

/* ================= FOOTER ================= */
.footer {
    text-align: center;
    padding: 20px;
    color: #94a3b8;
    font-size: 11px;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 900px) {
    .navbar {
        justify-content: center;
    }

    .navbar ul {
        justify-content: center;
    }
}

@media (max-width: 600px) {
    .navbar {
        padding: 15px 4%;
    }

    .logo {
        font-size: 16px;
    }

    .navbar ul {
        gap: 5px;
    }

    .navbar ul li a {
        font-size: 11px;
        padding: 8px;
    }

    .container {
        padding: 25px 4%;
    }

    .card {
        padding: 18px 14px;
    }

    .header {
        align-items: flex-start;
    }

    .header h2 {
        font-size: 18px;
    }
}
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar">

    <div class="logo">
        <i class="fas fa-building-shield"></i>
        <span>Sistem Absensi & Gaji</span>
    </div>

    <ul>
        <li>
            <a href="dashboard.php">
                <i class="fas fa-house"></i> Dashboard
            </a>
        </li>

        <li><a href="input_kinerja.php"><i class="fa-solid fa-clipboard-list"></i> Input Kinerja</a></li>

        <li>
            <a href="karyawan.php">
                <i class="fas fa-users"></i> Data Karyawan
            </a>
        </li>

        <li>
            <a href="rekap_gaji_mingguan.php">
                <i class="fas fa-calendar-week"></i> Rekap Mingguan
            </a>
        </li>

        <li>
            <a href="rekap_gaji_bulanan.php">
                <i class="fas fa-calendar-days"></i> Rekap Bulanan
            </a>
        </li>

        <li>
            <a href="tarif.php" class="active">
                <i class="fas fa-money-bill-wave"></i> Tarif
            </a>
        </li>

        <li>
            <a href="../logout.php" class="logout"
               onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>

</nav>

<!-- ================= CONTENT ================= -->
<div class="container">

    <div class="card">

        <div class="header">
            <div>
                <h2>Data Tarif Pekerjaan</h2>
                <p>Kelola daftar tarif dan upah setiap jenis pekerjaan karyawan.</p>
            </div>

            <a href="tambah_tarif.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Tarif
            </a>
        </div>

        <div class="table-wrapper">

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Pekerjaan</th>
                        <th>Tarif</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($data && mysqli_num_rows($data) > 0) { ?>

                    <?php $no = 1; ?>

                    <?php while ($t = mysqli_fetch_assoc($data)) { ?>

                    <tr>
                        <td><?= $no++; ?></td>

                        <td>
                            <strong>
                                <?= htmlspecialchars($t['jenis_pekerjaan'] ?? ''); ?>
                            </strong>
                        </td>

                        <td class="tarif">
                            Rp<?= number_format((float)$t['tarif'], 0, ',', '.'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($t['satuan'] ?? ''); ?>
                        </td>

                        <td class="keterangan">
                            <?= htmlspecialchars($t['keterangan'] ?? ''); ?>
                        </td>

                        <td>
                            <a href="edit_tarif.php?id=<?= urlencode($t['id_tarif']); ?>"
                               class="btn btn-warning">
                                <i class="fas fa-pen"></i> Edit
                            </a>

                            <a href="hapus_tarif.php?id=<?= urlencode($t['id_tarif']); ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Yakin ingin menghapus tarif ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="6">
                            <div class="empty-data">
                                <i class="fas fa-folder-open"></i>
                                <p>Belum ada data tarif pekerjaan.</p>
                            </div>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>

        </div>

    </div>

</div>

<!-- ================= FOOTER ================= -->
<div class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</div>

</body>
</html>
