<?php
// index.php (halaman notifikasi login)

$error = $_GET['error'] ?? '';

$title   = "Login Tidak Berhasil";
$message = "Terjadi kesalahan saat login.";
$color   = "#d63031";

switch ($error) {
    case 'password':
        $message = "Password yang Anda masukkan <b>salah</b>.";
        break;

    case 'user':
        $message = "Username atau Role <b>tidak terdaftar</b>.";
        break;

    case 'nonaktif':
        $message = "Akun Anda <b>dinonaktifkan</b>.<br>Silakan hubungi administrator.";
        break;

    case 'kosong':
        $message = "Semua field <b>wajib diisi</b>.";
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <meta http-equiv="refresh" content="3;url=login.php">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
        }
        .box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            max-width: 420px;
            animation: fadeIn 0.6s ease;
        }
        h2 {
            color: <?= $color ?>;
            margin-bottom: 10px;
        }
        p {
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }
        .info {
            margin-top: 15px;
            font-size: 13px;
            color: #666;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="box">
    <h2><?= $title ?></h2>
    <p><?= $message ?></p>
    <div class="info">
        Anda akan diarahkan kembali ke halaman login...
    </div>
</div>

</body>
</html>
