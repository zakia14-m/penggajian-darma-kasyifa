
<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI LOGIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================= PROSES SIMPAN ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_karyawan = $_POST['id_karyawan'] ?? '';
    $tanggal     = $_POST['tanggal'] ?? '';
    $id_tarif    = $_POST['id_tarif'] ?? [];
    $jumlah      = $_POST['jumlah'] ?? [];

    if (empty($id_karyawan)) {
        echo "<script>alert('Karyawan belum dipilih!');history.back();</script>";
        exit;
    }

    if (empty($tanggal) || empty($id_tarif) || count($id_tarif) != count($jumlah)) {
        echo "<script>alert('Data input belum lengkap!');history.back();</script>";
        exit;
    }

    /* ===== 1. CEK ABSENSI ===== */
    $cekAbsen = mysqli_query($conn, "
        SELECT * FROM absensi
        WHERE id_karyawan='$id_karyawan'
        AND tanggal='$tanggal'
        AND jam_masuk IS NOT NULL
    ");

    if (!$cekAbsen || mysqli_num_rows($cekAbsen) == 0) {
        echo "<script>alert('Karyawan belum absen pada tanggal tersebut!');history.back();</script>";
        exit;
    }

    /* ===== 2. CEK KINERJA DOBEL ===== */
    $cekKinerja = mysqli_query($conn, "
        SELECT * FROM kinerja_harian
        WHERE id_karyawan='$id_karyawan'
        AND tanggal='$tanggal'
    ");

    if (!$cekKinerja) {
        echo "<script>alert('Gagal memeriksa data kinerja!');history.back();</script>";
        exit;
    }

    if (mysqli_num_rows($cekKinerja) > 0) {
        echo "<script>alert('Kinerja karyawan sudah diinput pada tanggal tersebut!');history.back();</script>";
        exit;
    }

    /* ===== 3. SIMPAN KINERJA ===== */
    $berhasil = true;
    $jumlahInput = 0;

    mysqli_begin_transaction($conn);

    for ($i = 0; $i < count($id_tarif); $i++) {

        $tarif = (int)$id_tarif[$i];
        $qty   = (int)$jumlah[$i];

        if ($qty <= 0) {
            continue;
        }

        $simpan = mysqli_query($conn, "
            INSERT INTO kinerja_harian
            (id_karyawan, id_tarif, tanggal, jumlah)
            VALUES
            ('$id_karyawan', '$tarif', '$tanggal', '$qty')
        ");

        if (!$simpan) {
            $berhasil = false;
            break;
        }

        $jumlahInput++;
    }

    if ($berhasil && $jumlahInput > 0) {
        mysqli_commit($conn);
        header("Location: data_kinerja.php?status=sukses");
        exit;
    } else {
        mysqli_rollback($conn);

        if ($jumlahInput == 0) {
            echo "<script>alert('Masukkan minimal satu jumlah produksi!');history.back();</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data kinerja!');history.back();</script>";
        }
        exit;
    }
}

/* ================= DATA KARYAWAN ================= */
$qKaryawan = mysqli_query($conn, "
    SELECT id_karyawan, nama_karyawan
    FROM karyawan
    WHERE status='aktif'
    ORDER BY nama_karyawan
");

/* ================= DATA TARIF ================= */
$qTarif = mysqli_query($conn, "
    SELECT * FROM tarif_pekerjaan
    ORDER BY jenis_pekerjaan
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Input Kinerja | Sistem Absensi & Gaji</title>

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

/* ================= NAVBAR ================= */
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

/* ================= CONTENT ================= */
.container {
    max-width: 1100px;
    margin: auto;
    padding: 40px 5%;
}

.header-content {
    margin-bottom: 25px;
}

.header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: #14213d;
    margin-bottom: 7px;
}

.header-content p {
    color: #64748b;
    font-size: 13px;
    line-height: 1.7;
}

/* ================= CARD ================= */
.form-card {
    background: white;
    border-radius: 18px;
    padding: 30px;
    border: 1px solid #e8edf5;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
}

/* ================= CARD HEADER ================= */
.card-heading {
    display: flex;
    align-items: center;
    gap: 15px;
    padding-bottom: 23px;
    margin-bottom: 25px;
    border-bottom: 1px solid #e8edf5;
}

.heading-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #dbeafe;
    color: #2563eb;
    font-size: 23px;
    flex-shrink: 0;
}

.card-heading h2 {
    font-size: 18px;
    color: #14213d;
    margin-bottom: 4px;
}

.card-heading p {
    color: #94a3b8;
    font-size: 11px;
}

/* ================= FORM ================= */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 9px;
}

.input-box {
    position: relative;
}

.input-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
    pointer-events: none;
}

.input-box input {
    width: 100%;
    height: 49px;
    padding: 0 15px 0 43px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    outline: none;
    background: #f8fafc;
    color: #334155;
    font-size: 12px;
    transition: .3s;
}

.input-box input:focus {
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59,130,246,.10);
}

.input-box input::placeholder {
    color: #94a3b8;
}

/* ================= SECTION TITLE ================= */
.section-title {
    margin-top: 10px;
    margin-bottom: 15px;
}

.section-title h3 {
    font-size: 16px;
    color: #1e293b;
    margin-bottom: 5px;
}

.section-title p {
    color: #94a3b8;
    font-size: 11px;
}

/* ================= TABLE ================= */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e8edf5;
    border-radius: 12px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 550px;
}

th {
    background: #eff6ff;
    color: #1e40af;
    padding: 15px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
}

td {
    padding: 13px 15px;
    border-bottom: 1px solid #e8edf5;
    color: #475569;
    font-size: 12px;
}

tbody tr:last-child td {
    border-bottom: none;
}

tbody tr:hover {
    background: #f8fafc;
}

.job-name {
    font-weight: 600;
    color: #334155;
}

.tarif {
    color: #64748b;
    white-space: nowrap;
}

.quantity-input {
    width: 110px;
    height: 39px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0 10px;
    outline: none;
    background: #f8fafc;
    font-size: 12px;
    color: #1e293b;
}

.quantity-input:focus {
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 3px rgba(59,130,246,.10);
}

/* ================= INFO ================= */
.info-box {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px;
    margin-top: 20px;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 11px;
    color: #1e40af;
}

.info-box i {
    margin-top: 3px;
    font-size: 15px;
}

.info-box p {
    font-size: 11px;
    line-height: 1.8;
}

/* ================= BUTTON ================= */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 12px 20px;
    border: none;
    border-radius: 9px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: .3s;
}

.btn-primary {
    background: #2563eb;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 7px 15px rgba(37,99,235,.20);
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
    transform: translateY(-2px);
}

/* ================= FOOTER ================= */
.footer {
    text-align: center;
    padding: 22px;
    color: #94a3b8;
    font-size: 11px;
}

/* ================= RESPONSIVE ================= */
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

    .header-content h1 {
        font-size: 23px;
    }

    .form-card {
        padding: 20px 15px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .card-heading h2 {
        font-size: 16px;
    }

    .button-group {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
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

        <li>
            <a href="input_kinerja.php" class="active">
                <i class="fa-solid fa-clipboard-list"></i> Input Kinerja
            </a>
        </li>

        <li>
            <a href="karyawan.php">
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

<!-- ================= CONTENT ================= -->
<main class="container">

    <div class="header-content">
        <h1>Input Kinerja Karyawan</h1>
        <p>
            Catat hasil produksi karyawan berdasarkan jenis pekerjaan
            dan tanggal pengerjaan.
        </p>
    </div>

    <section class="form-card">

        <!-- CARD HEADER -->
        <div class="card-heading">

            <div class="heading-icon">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>

            <div>
                <h2>Form Kinerja Harian</h2>
                <p>Lengkapi informasi karyawan dan hasil pekerjaan.</p>
            </div>

        </div>

        <form method="POST">

            <!-- IDENTITAS KINERJA -->
            <div class="form-row">

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-user"></i>
                        Karyawan
                    </label>

                    <div class="input-box">
                        <i class="fa-solid fa-users"></i>

                        <input
                            list="listKaryawan"
                            id="namaKaryawan"
                            placeholder="Ketik nama karyawan..."
                            autocomplete="off"
                            required
                        >
                    </div>

                    <datalist id="listKaryawan">
                        <?php while($k = mysqli_fetch_assoc($qKaryawan)) { ?>
                            <option
                                data-id="<?= htmlspecialchars($k['id_karyawan']); ?>"
                                value="<?= htmlspecialchars($k['nama_karyawan']); ?>">
                            </option>
                        <?php } ?>
                    </datalist>

                    <input type="hidden" name="id_karyawan" id="id_karyawan">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-calendar-days"></i>
                        Tanggal Kinerja
                    </label>

                    <div class="input-box">
                        <i class="fa-solid fa-calendar-check"></i>

                        <input
                            type="date"
                            name="tanggal"
                            value="<?= date('Y-m-d'); ?>"
                            required
                        >
                    </div>
                </div>

            </div>

            <!-- DAFTAR PEKERJAAN -->
            <div class="section-title">
                <h3>
                    <i class="fa-solid fa-list-check"></i>
                    Detail Hasil Pekerjaan
                </h3>

                <p>Masukkan jumlah produksi sesuai jenis pekerjaan.</p>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th width="40%">Jenis Pekerjaan</th>
                            <th width="30%">Tarif</th>
                            <th width="30%">Jumlah Produksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($t = mysqli_fetch_assoc($qTarif)) { ?>

                        <tr>

                            <td>
                                <span class="job-name">
                                    <?= htmlspecialchars($t['jenis_pekerjaan']); ?>
                                </span>

                                <input
                                    type="hidden"
                                    name="id_tarif[]"
                                    value="<?= htmlspecialchars($t['id_tarif']); ?>"
                                >
                            </td>

                            <td class="tarif">
                                Rp<?= number_format($t['tarif'],0,',','.'); ?>
                                / <?= htmlspecialchars($t['satuan']); ?>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="jumlah[]"
                                    class="quantity-input"
                                    min="0"
                                    value="0"
                                    placeholder="Jumlah"
                                >
                            </td>

                        </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

            <!-- INFORMASI -->
            <div class="info-box">
                <i class="fa-solid fa-circle-info"></i>

                <p>
                    Pastikan karyawan sudah melakukan absensi pada tanggal
                    yang dipilih. Sistem hanya mengizinkan satu kali input
                    kinerja per karyawan dalam satu hari.
                </p>
            </div>

            <!-- BUTTON -->
            <div class="button-group">

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Kinerja
                </button>

                <a href="data_kinerja.php" class="btn btn-secondary">
                    <i class="fa-solid fa-table-list"></i>
                    Lihat Data Kinerja
                </a>

            </div>

        </form>

    </section>

</main>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

<!-- ================= JAVASCRIPT ================= -->
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

document.querySelector('form').addEventListener('submit', function(e) {

    if (hidden.value === '') {
        e.preventDefault();
        alert('Silakan pilih nama karyawan dari daftar yang tersedia!');
        input.focus();
    }

});
</script>

</body>
</html>
