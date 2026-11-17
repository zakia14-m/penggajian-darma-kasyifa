
<?php
$host = "localhost";
$user = "USERNAME_DATABASE";
$pass = "PASSWORD_DATABASE";
$db   = "NAMA_DATABASE";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal");
}

date_default_timezone_set('Asia/Jakarta');
?>
