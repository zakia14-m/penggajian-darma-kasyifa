<?php
session_start();
include '../koneksi.php';

// proteksi admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? '';

if (!$id) {
    die("ID tidak valid");
}

// ambil data tarif
$qTarif = mysqli_query($conn, "
    SELECT * FROM tarif_pekerjaan 
    WHERE id_tarif='$id'
");

$data = mysqli_fetch_assoc($qTarif);
if (!$data) {
    die("Data tarif tidak ditemukan");
}

// proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jenis_pekerjaan = $_POST['jenis_pekerjaan'];
    $tarif           = $_POST['tarif'];
    $satuan          = $_POST['satuan'];
    $keterangan      = $_POST['keterangan'];

    mysqli_query($conn, "
        UPDATE tarif_pekerjaan SET
            jenis_pekerjaan = '$jenis_pekerjaan',
            tarif           = '$tarif',
            satuan          = '$satuan',
            keterangan      = '$keterangan'
        WHERE id_tarif = '$id'
    ");

    // ✅ ARAH KE tarif.php
    header("Location: tarif.php?status=update");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Tarif</title>

<style>
* {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    box-sizing: border-box;
}
body {
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    padding: 40px;
}
.container {
    max-width: 700px;
    margin: auto;
}
.card {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 15px 40px rgba(0,0,0,.25);
}
h2 {
    text-align: center;
    margin-top: 0;
}
label {
    font-weight: 600;
    margin-top: 15px;
    display: block;
}
input, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.btn {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    font-weight: bold;
    border: none;
    cursor: pointer;
    margin-top: 20px;
}
.btn-primary {
    background: #3498db;
    color: #fff;
}
.btn-secondary {
    background: #7f8c8d;
    color: #fff;
    text-decoration: none;
    text-align: center;
}
</style>
</head>

<body>

<div class="container">
<div class="card">

<h2>Edit Tarif Pekerjaan</h2>

<form method="POST">

<label>Jenis Pekerjaan</label>
<input type="text" name="jenis_pekerjaan" value="<?= $data['jenis_pekerjaan']; ?>" required>

<label>Tarif</label>
<input type="number" name="tarif" value="<?= $data['tarif']; ?>" required>

<label>Satuan</label>
<input type="text" name="satuan" value="<?= $data['satuan']; ?>" required>

<label>Keterangan</label>
<textarea name="keterangan"><?= $data['keterangan']; ?></textarea>

<button type="submit" class="btn btn-primary">
    💾 Update Tarif
</button>

</form>

</div>
</div>

</body>
</html>
