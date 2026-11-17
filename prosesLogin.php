<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if ($username == '' || $password == '' || $role == '') {
    header("Location: index.php?error=kosong");
    exit;
}

$password_md5 = md5($password);

/* =====================
   CEK USER + ROLE
===================== */
if ($role === 'karyawan') {

    $query = mysqli_query($conn, "
        SELECT users.*, karyawan.status
        FROM users
        JOIN karyawan ON users.id_karyawan = karyawan.id_karyawan
        WHERE users.username='$username'
        AND users.role='karyawan'
        LIMIT 1
    ");

} else {

    $query = mysqli_query($conn, "
        SELECT *
        FROM users
        WHERE username='$username'
        AND role='$role'
        LIMIT 1
    ");
}

if (mysqli_num_rows($query) !== 1) {
    header("Location: index.php?error=user");
    exit;
}

$data = mysqli_fetch_assoc($query);

/* =====================
   CEK STATUS KARYAWAN
===================== */
if ($role === 'karyawan' && $data['status'] !== 'aktif') {
    header("Location: index.php?error=nonaktif");
    exit;
}

/* =====================
   CEK PASSWORD
===================== */
if ($data['password'] !== $password_md5) {
    header("Location: index.php?error=password");
    exit;
}

/* =====================
   SET SESSION
===================== */
$_SESSION['login']    = true;
$_SESSION['id_user']  = $data['id_user'];
$_SESSION['username'] = $data['username'];
$_SESSION['role']     = $data['role'];

if ($role === 'karyawan') {
    $_SESSION['id_karyawan'] = $data['id_karyawan'];
}

/* =====================
   REDIRECT DASHBOARD
===================== */
if ($role === 'admin') {

    header("Location: admin/dashboard.php");

} elseif ($role === 'asistenAdmin') {

    header("Location: asistenAdmin/dashboard.php");

} elseif ($role === 'karyawan') {

    header("Location: karyawan/dashboard.php");

} else {

    session_destroy();
    header("Location: login.php?error=role");
}

exit;
