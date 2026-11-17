<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_karyawan = $_GET['id_karyawan'] ?? '';
$tanggal     = $_GET['tanggal'] ?? '';

$awal  = date('Y-m-d', strtotime('monday this week', strtotime($tanggal)));
$akhir = date('Y-m-d', strtotime('sunday this week', strtotime($tanggal)));

$qKaryawan = mysqli_query($conn, "
    SELECT nama_karyawan FROM karyawan WHERE id_karyawan='$id_karyawan'
");
$karyawan = mysqli_fetch_assoc($qKaryawan);

$query = mysqli_query($conn, "
    SELECT 
        t.jenis_pekerjaan,
        SUM(k.jumlah) AS jumlah,
        t.tarif,
        SUM(k.jumlah * t.tarif) AS total
    FROM kinerja_harian k
    JOIN tarif_pekerjaan t ON k.id_tarif = t.id_tarif
    WHERE k.id_karyawan='$id_karyawan'
    AND k.tanggal BETWEEN '$awal' AND '$akhir'
    GROUP BY k.id_tarif
");

$total_gaji = 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Slip Gaji Mingguan</title>
<style>
body { font-family:Arial; padding:40px }
h2,h3 { text-align:center }
table { width:100%; border-collapse:collapse; margin-top:20px }
th,td { border:1px solid #333; padding:10px }
th { background:#eee }
tfoot td { font-weight:bold }
@media print { .no-print { display:none } }
</style>
</head>
<body>

<h2>SLIP GAJI MINGGUAN</h2>
<h3><?= $karyawan['nama_karyawan']; ?></h3>

<p><b>Periode:</b>
<?= date('d-m-Y', strtotime($awal)); ?> s/d <?= date('d-m-Y', strtotime($akhir)); ?>
</p>

<table>
<tr>
    <th>No</th>
    <th>Pekerjaan</th>
    <th>Jumlah</th>
    <th>Tarif</th>
    <th>Total</th>
</tr>

<?php $no=1; while ($d=mysqli_fetch_assoc($query)) {
$total_gaji += $d['total'];
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $d['jenis_pekerjaan']; ?></td>
    <td><?= $d['jumlah']; ?></td>
    <td>Rp<?= number_format($d['tarif']); ?></td>
    <td>Rp<?= number_format($d['total']); ?></td>
</tr>
<?php } ?>

<tfoot>
<tr>
    <td colspan="4" align="right">TOTAL GAJI</td>
    <td>Rp<?= number_format($total_gaji); ?></td>
</tr>
</tfoot>
</table>

<br>
<button onclick="window.print()" class="no-print">🖨 Cetak</button>

</body>
</html>
