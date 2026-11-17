<?php
session_start();
include '../koneksi.php';

// proteksi admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id     = $_GET['id'];
$status = $_GET['status'];

// validasi status
if ($status != 'aktif' && $status != 'nonaktif') {
    header("Location: karyawan.php");
    exit;
}

mysqli_query($conn, "
    UPDATE karyawan 
    SET status='$status' 
    WHERE id_karyawan='$id'
");

header("Location: karyawan.php");
exit;
