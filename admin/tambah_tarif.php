<?php
session_start();
include '../koneksi.php';

// proteksi admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ===============================
   PROSES SIMPAN
================================ */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jenis_pekerjaan = $_POST['jenis_pekerjaan'];
    $tarif           = $_POST['tarif'];
    $satuan          = $_POST['satuan'];
    $keterangan      = $_POST['keterangan'];

    if ($jenis_pekerjaan && $tarif) {
        mysqli_query($conn, "
            INSERT INTO tarif_pekerjaan
            (jenis_pekerjaan, tarif, satuan, keterangan)
            VALUES
            ('$jenis_pekerjaan', '$tarif', '$satuan', '$keterangan')
        ");

        header("Location: tarif.php?status=sukses");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Tarif Pekerjaan</title>

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
    margin-top: 0;
    text-align: center;
    color: #333;
}
label {
    font-weight: 600;
    display: block;
    margin-top: 15px;
    color: #333;
}
input, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
textarea {
    resize: vertical;
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
.btn-primary:hover {
    background: #2980b9;
}
.btn-secondary {
    background: #2ecc71;
    color: #fff;
    text-decoration: none;
    text-align: center;
}
.btn-secondary:hover {
    background: #27ae60;
}
.footer {
    text-align: center;
    margin-top: 25px;
    font-size: 12px;
    color: #ddd;
}
</style>
</head>

<body>

<div class="container">
<div class="card">

<h2>Tambah Tarif Pekerjaan</h2>

<form method="POST">

    <label>Jenis Pekerjaan</label>
    <input type="text" name="jenis_pekerjaan" placeholder="Contoh: Linting Rokok" required>

    <label>Tarif</label>
    <input type="number" name="tarif" placeholder="Contoh: 500" required>

    <label>Satuan</label>
    <input type="text" name="satuan" placeholder="Contoh: batang / pack / kg">

    <label>Keterangan</label>
    <textarea name="keterangan" rows="3" placeholder="Opsional"></textarea>

    <button type="submit" class="btn btn-primary">
        💾 Simpan Tarif
    </button>
</form>

</div>

<a href="tarif.php" class="btn btn-secondary">
    ← Kembali Data Tarif
</a>

<div class="footer">
    © <?= date('Y'); ?> Pabrik Rokok Darma Kasyifa
</div>
</div>

</body>
</html>
