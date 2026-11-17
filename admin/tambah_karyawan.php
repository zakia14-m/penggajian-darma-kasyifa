<?php
session_start();
include '../koneksi.php';

// proteksi halaman
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// proses simpan data
if (isset($_POST['simpan'])) {
    $nama   = htmlspecialchars($_POST['nama_karyawan']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $status = $_POST['status'];

    $query = mysqli_query($conn, "
        INSERT INTO karyawan (nama_karyawan, alamat, status)
        VALUES ('$nama', '$alamat', '$status')
    ");

    if ($query) {
        header("Location: karyawan.php");
        exit;
    } else {
        $error = "Gagal menyimpan data karyawan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Karyawan</title>
    <style>
        * {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            width: 420px;
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
        }
        h2 {
            margin-top: 0;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 70px;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3498db;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
        }
        .btn-save {
            background: #3498db;
            color: #fff;
        }
        .btn-save:hover {
            background: #2980b9;
        }
        .btn-back {
            background: #bdc3c7;
            color: #2c3e50;
        }
        .btn-back:hover {
            background: #95a5a6;
        }
        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Tambah Data Karyawan</h2>

    <?php if (isset($error)) { ?>
        <div class="error"><?= $error; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Nama Karyawan</label>
        <input type="text" name="nama_karyawan" required>

        <label>Alamat</label>
        <textarea name="alamat" required></textarea>

        <label>Status</label>
        <select name="status" required>
            <option value="">-- Pilih Status --</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
        </select>

        <div class="btn-group">
            <button type="submit" name="simpan" class="btn btn-save">Simpan</button>
            <a href="karyawan.php" class="btn btn-back">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
