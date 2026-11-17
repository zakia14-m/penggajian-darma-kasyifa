<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// daftar karyawan
$qKaryawan = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY nama_karyawan");

// filter
$id_karyawan = $_GET['id_karyawan'] ?? '';
$tanggal     = $_GET['tanggal'] ?? date('Y-m-d');
$status      = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Kinerja Karyawan</title>

<style>
/* ===== GLOBAL ===== */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI', sans-serif}
body{background:#f4f6f9}

/* ===== NAVBAR ===== */
.navbar{
    width:100%;height:60px;background:#1e293b;
    display:flex;align-items:center;justify-content:space-between;
    padding:0 30px;color:white
}
.navbar ul{list-style:none;display:flex;gap:25px}
.navbar ul li a{color:#e5e7eb;text-decoration:none;font-size:14px}
.navbar ul li a:hover{color:#38bdf8}

/* ===== CONTENT ===== */
.container{padding:30px;max-width:1100px;margin:auto}
.card{
    background:#fff;border-radius:14px;
    padding:25px 20px;box-shadow:0 6px 15px rgba(0,0,0,.06)
}
.header{
    display:flex;justify-content:space-between;
    align-items:center;margin-bottom:20px
}
label{display:block;margin-top:15px;font-weight:600}
select,input{
    width:100%;padding:10px;margin-top:6px;
    border-radius:6px;border:1px solid #ccc
}
.btn{
    padding:8px 14px;border-radius:6px;
    font-weight:600;color:#fff;border:none;
    cursor:pointer;text-decoration:none
}
.btn-primary{background:#3b82f6}
.btn-success{background:#22c55e}
.btn:hover{opacity:.9}

/* ===== TABLE ===== */
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;border-bottom:1px solid #e5e7eb;font-size:14px}
th{background:#3b82f6;color:#fff}
tfoot td{font-weight:bold;background:#f8fafc}
</style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<div class="navbar">
    <div class="logo">Sistem Absensi & Gaji</div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="karyawan.php">Data Karyawan</a></li>
        <li><a href="rekap_gaji_mingguan.php">Rekap Mingguan</a></li>
        <li><a href="rekap_gaji_bulanan.php">Rekap Bulanan</a></li>
        <li><a href="tarif.php">Tarif</a></li>
        <li><a href="../logout.php" onclick="return confirm('Yakin logout?')">Logout</a></li>
    </ul>
</div>

<div class="container">
<div class="card">

<div class="header">
    <h2>Data Kinerja Karyawan</h2>
</div>

<form method="GET">

<label>Karyawan</label>
<input list="listKaryawan" id="namaKaryawan" placeholder="Ketik nama karyawan..." autocomplete="off">
<datalist id="listKaryawan">
<?php mysqli_data_seek($qKaryawan,0); while($k=mysqli_fetch_assoc($qKaryawan)){ ?>
    <option data-id="<?= $k['id_karyawan']; ?>" value="<?= $k['nama_karyawan']; ?>"></option>
<?php } ?>
</datalist>
<input type="hidden" name="id_karyawan" id="id_karyawan" value="<?= $id_karyawan ?>">

<label>Tanggal</label>
<input type="date" name="tanggal" value="<?= $tanggal ?>">

<button class="btn btn-primary" style="margin-top:15px">Tampilkan Data</button>
</form>

<?php
$where = "WHERE k.tanggal='$tanggal'";
if ($id_karyawan) {
    $where .= " AND k.id_karyawan='$id_karyawan'";
}

$query = mysqli_query($conn,"
    SELECT
        kp.nama_karyawan,
        t.jenis_pekerjaan,
        t.tarif,
        t.satuan,
        k.jumlah,
        (k.jumlah*t.tarif) AS total
    FROM kinerja_harian k
    JOIN karyawan kp ON k.id_karyawan=kp.id_karyawan
    JOIN tarif_pekerjaan t ON k.id_tarif=t.id_tarif
    $where
    ORDER BY kp.nama_karyawan,t.jenis_pekerjaan
");

$total_harian = 0;
?>

<table>
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Pekerjaan</th>
    <th>Tarif</th>
    <th>Jumlah</th>
    <th>Total</th>
</tr>

<?php $no=1; while($d=mysqli_fetch_assoc($query)){
    $total_harian += $d['total']; ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $d['nama_karyawan'] ?></td>
    <td><?= $d['jenis_pekerjaan'] ?></td>
    <td>Rp<?= number_format($d['tarif']) ?>/<?= $d['satuan'] ?></td>
    <td><?= $d['jumlah'] ?></td>
    <td>Rp<?= number_format($d['total']) ?></td>
</tr>
<?php } ?>

<tfoot>
<tr>
    <td colspan="5" align="right">TOTAL</td>
    <td>Rp<?= number_format($total_harian) ?></td>
</tr>
</tfoot>
</table>

</div>
</div>

<script>
const input = document.getElementById('namaKaryawan');
const hidden = document.getElementById('id_karyawan');
const options = document.querySelectorAll('#listKaryawan option');

input.addEventListener('input', function () {
    hidden.value = '';
    options.forEach(opt => {
        if (opt.value === this.value) {
            hidden.value = opt.dataset.id;
        }
    });
});
</script>

</body>
</html>
