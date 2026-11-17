
<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI LOGIN ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data karyawan
$data = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY id_karyawan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Karyawan | Sistem Absensi & Gaji</title>

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

/* PAGE HEADER */
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
    margin-bottom: 25px;
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

.btn-add {
    background: #2563eb;
    color: white;
}

.btn-add:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
}

.btn-success {
    background: #dcfce7;
    color: #15803d;
}

.btn-success:hover {
    background: #bbf7d0;
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
}

.btn-danger:hover {
    background: #fecaca;
}

/* TABLE */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 10px;
}

table {
    width: 100%;
    min-width: 750px;
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

th:first-child {
    border-radius: 9px 0 0 0;
}

th:last-child {
    border-radius: 0 9px 0 0;
}

td {
    padding: 15px 14px;
    border-bottom: 1px solid #edf1f7;
    font-size: 12px;
    color: #475569;
}

tr {
    transition: .2s;
}

tbody tr:hover {
    background: #f8faff;
}

/* STATUS */
.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
}

.status-aktif {
    background: #dcfce7;
    color: #15803d;
}

.status-nonaktif {
    background: #fee2e2;
    color: #dc2626;
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

    .card {
        padding: 18px 14px;
    }

    .card-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .btn-add {
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
        <li>
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>

        <li><a href="input_kinerja.php"><i class="fa-solid fa-clipboard-list"></i> Input Kinerja</a></li>

        <li>
            <a href="karyawan.php" class="active">
                <i class="fa-solid fa-users"></i> Data Karyawan
            </a>
        </li>

        <li>
            <a href="rekap_gaji_mingguan.php">
                <i class="fa-solid fa-calendar-week"></i> Rekap Mingguan
            </a>
        </li>

        <li>
            <a href="rekap_gaji_bulanan.php">
                <i class="fa-solid fa-calendar-days"></i> Rekap Bulanan
            </a>
        </li>

        <li>
            <a href="tarif.php">
                <i class="fa-solid fa-money-bill-wave"></i> Tarif
            </a>
        </li>

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
        <h1>Data Karyawan</h1>
        <p>Kelola informasi dan status karyawan Darma Kasyifa.</p>
    </div>

    <div class="card">

        <div class="card-header">
            <div>
                <h2><i class="fa-solid fa-users" style="color:#2563eb;"></i> Daftar Karyawan</h2>
                <p>Informasi seluruh karyawan yang terdaftar dalam sistem.</p>
            </div>

            <a href="tambah_karyawan.php" class="btn btn-add">
                <i class="fa-solid fa-plus"></i> Tambah Karyawan
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Karyawan</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($k = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($k['id_karyawan']); ?></td>

                        <td>
                            <strong><?= htmlspecialchars($k['nama_karyawan']); ?></strong>
                        </td>

                        <td><?= htmlspecialchars($k['alamat']); ?></td>

                        <td>
                            <?php if ($k['status'] == 'aktif') { ?>
                                <span class="status status-aktif">
                                    <i class="fa-solid fa-circle-check"></i> Aktif
                                </span>
                            <?php } else { ?>
                                <span class="status status-nonaktif">
                                    <i class="fa-solid fa-circle-xmark"></i> Nonaktif
                                </span>
                            <?php } ?>
                        </td>

                        <td>
                            <?php if ($k['status'] == 'aktif') { ?>
                                <a href="karyawan_status.php?id=<?= urlencode($k['id_karyawan']); ?>&status=nonaktif"
                                   class="btn btn-danger"
                                   onclick="return confirm('Nonaktifkan karyawan ini?')">
                                    <i class="fa-solid fa-user-slash"></i> Nonaktifkan
                                </a>
                            <?php } else { ?>
                                <a href="karyawan_status.php?id=<?= urlencode($k['id_karyawan']); ?>&status=aktif"
                                   class="btn btn-success"
                                   onclick="return confirm('Aktifkan kembali karyawan ini?')">
                                    <i class="fa-solid fa-user-check"></i> Aktifkan
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (mysqli_num_rows($data) == 0) { ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px;">
                            <i class="fa-solid fa-users" style="font-size:30px; color:#cbd5e1;"></i>
                            <p style="margin-top:10px;">Belum ada data karyawan.</p>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</main>

<footer class="footer">
    &copy; <?php echo date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

</body>
</html>
