
<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';

/* ================= PROTEKSI LOGIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];
$tanggal = date('Y-m-d');

$q = mysqli_query($conn, "
    SELECT * FROM absensi
    WHERE id_karyawan='$id_karyawan'
    AND tanggal='$tanggal'
");

$absen = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Absensi Karyawan | Sistem Absensi & Gaji</title>

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
    max-width: 1250px;
    margin: auto;
    padding: 40px 5%;
}

.header-content {
    margin-bottom: 28px;
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
    line-height: 1.8;
}

/* ================= ABSENSI CARD ================= */
.absen-card {
    max-width: 750px;
    margin: auto;
    background: white;
    border-radius: 20px;
    padding: 35px;
    border: 1px solid #e8edf5;
    box-shadow: 0 8px 25px rgba(15, 23, 42, .05);
}

/* ================= CARD HEADER ================= */
.card-header {
    text-align: center;
    margin-bottom: 25px;
}

.card-icon {
    width: 75px;
    height: 75px;
    background: #dbeafe;
    color: #2563eb;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin: 0 auto 18px;
}

.card-header h2 {
    font-size: 22px;
    color: #14213d;
    margin-bottom: 7px;
}

.card-header p {
    color: #64748b;
    font-size: 12px;
}

/* ================= DATE INFORMATION ================= */
.date-box {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    margin-bottom: 25px;
}

.date-box i {
    color: #2563eb;
    margin-right: 7px;
}

.date-box span {
    color: #475569;
    font-size: 13px;
    font-weight: 500;
}

/* ================= ATTENDANCE DETAILS ================= */
.attendance-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.info-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
}

.info-box .info-icon {
    color: #2563eb;
    font-size: 20px;
    margin-bottom: 10px;
}

.info-box h4 {
    color: #64748b;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 5px;
}

.info-box p {
    color: #14213d;
    font-size: 17px;
    font-weight: 700;
}

/* ================= TEXTAREA ================= */
.form-group {
    margin-top: 20px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 9px;
}

textarea {
    width: 100%;
    min-height: 100px;
    resize: vertical;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 13px 15px;
    font-size: 13px;
    outline: none;
    transition: .3s;
}

textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
}

textarea::placeholder {
    color: #94a3b8;
}

/* ================= BUTTON ================= */
.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: .3s;
    margin-top: 15px;
}

.btn-green {
    background: #16a34a;
}

.btn-green:hover {
    background: #15803d;
    transform: translateY(-2px);
    box-shadow: 0 7px 15px rgba(22, 163, 74, .15);
}

.btn-blue {
    background: #2563eb;
}

.btn-blue:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 7px 15px rgba(37, 99, 235, .15);
}

.btn-disabled {
    background: #94a3b8;
    cursor: not-allowed;
}

/* ================= STATUS ================= */
.status-wrap {
    text-align: center;
    margin-top: 20px;
}

.status-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 10px;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 17px;
    border-radius: 30px;
    color: white;
    font-size: 12px;
    font-weight: 600;
}

.orange {
    background: #f59e0b;
}

.blueb {
    background: #2563eb;
}

.greenb {
    background: #16a34a;
}

/* ================= SUCCESS MESSAGE ================= */
.completed-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 17px;
    text-align: center;
    margin-top: 20px;
}

.completed-box i {
    color: #16a34a;
    font-size: 25px;
    margin-bottom: 8px;
}

.completed-box h3 {
    color: #166534;
    font-size: 15px;
    margin-bottom: 5px;
}

.completed-box p {
    color: #64748b;
    font-size: 12px;
    line-height: 1.7;
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

    .absen-card {
        padding: 23px 18px;
    }

    .card-header h2 {
        font-size: 19px;
    }

    .attendance-info {
        grid-template-columns: 1fr;
    }

    .info-box p {
        font-size: 16px;
    }
}
</style>

<script>
const BATAS_KERJA_MS = 7 * 60 * 60 * 1000;

function tanggalLokal() {
    const sekarang = new Date();
    const tahun = sekarang.getFullYear();
    const bulan = String(sekarang.getMonth() + 1).padStart(2, '0');
    const tanggal = String(sekarang.getDate()).padStart(2, '0');
    return `${tahun}-${bulan}-${tanggal}`;
}

function hitungDurasiKerja() {
    const form = document.getElementById('form');
    const teks = document.getElementById('durasiKerjaText');
    if (!form || !teks || !form.dataset.jamMasuk) return;

    const waktuMasuk = new Date(tanggalLokal() + 'T' + form.dataset.jamMasuk);
    const selisih = Math.max(0, Date.now() - waktuMasuk.getTime());

    const jam = Math.floor(selisih / 3600000);
    const menit = Math.floor((selisih % 3600000) / 60000);
    const detik = Math.floor((selisih % 60000) / 1000);

    let keterangan;
    if (selisih < BATAS_KERJA_MS) {
        const sisa = BATAS_KERJA_MS - selisih;
        const sisaJam = Math.floor(sisa / 3600000);
        const sisaMenit = Math.floor((sisa % 3600000) / 60000);
        keterangan = `Belum mencapai 7 jam. Sisa ${sisaJam} jam ${sisaMenit} menit. Jika pulang sekarang, alasan wajib diisi dan status menjadi pulang lebih awal/izin.`;
    } else if (selisih === BATAS_KERJA_MS) {
        keterangan = 'Durasi kerja tepat 7 jam. Status pulang: tepat waktu.';
    } else {
        keterangan = 'Durasi kerja lebih dari 7 jam. Status pulang: lembur.';
    }

    teks.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> Durasi kerja saat ini: <strong>${jam} jam ${menit} menit ${detik} detik</strong><br>${keterangan}`;
}

function absen(aksi) {
    const form = document.getElementById('form');
    const alasan = document.getElementById('alasan');

    if (aksi === 'pulang' && form && form.dataset.jamMasuk) {
        const waktuMasuk = new Date(tanggalLokal() + 'T' + form.dataset.jamMasuk);
        const durasi = Date.now() - waktuMasuk.getTime();

        if (durasi < BATAS_KERJA_MS && (!alasan || alasan.value.trim() === '')) {
            alert('Durasi kerja belum mencapai 7 jam. Silakan isi alasan pulang lebih awal/izin terlebih dahulu.');
            if (alasan) alasan.focus();
            return;
        }
    }

    if (!navigator.geolocation) {
        alert('Browser tidak mendukung GPS');
        return;
    }

    const tombol = document.querySelector('.btn-green, .btn-blue');
    if (tombol) {
        tombol.disabled = true;
        tombol.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengambil lokasi...';
    }

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            document.getElementById('aksi').value = aksi;
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
            form.submit();
        },
        function() {
            alert('Aktifkan GPS dan izinkan akses lokasi terlebih dahulu.');
            if (tombol) {
                tombol.disabled = false;
                tombol.innerHTML = (aksi === 'masuk')
                    ? '<i class="fa-solid fa-right-to-bracket"></i> Absen Masuk'
                    : '<i class="fa-solid fa-right-from-bracket"></i> Absen Pulang';
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );
}

document.addEventListener('DOMContentLoaded', function() {
    hitungDurasiKerja();
    if (document.getElementById('durasiKerjaText')) {
        setInterval(hitungDurasiKerja, 1000);
    }
});
</script>

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
            <a href="absen.php" class="active">
                <i class="fa-solid fa-calendar-check"></i> Absen
            </a>
        </li>

        <li>
            <a href="gaji_harian.php">
                <i class="fa-solid fa-money-bill-wave"></i> Gaji Harian
            </a>
        </li>

        <li>
            <a href="gaji_mingguan.php">
                <i class="fa-solid fa-calendar-week"></i> Slip Gaji Mingguan
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
        <h1>Absensi Karyawan</h1>
        <p>Catat kehadiran masuk dan pulang melalui sistem absensi Darma Kasyifa.</p>
    </div>

    <div class="absen-card">

        <!-- HEADER -->
        <div class="card-header">

            <div class="card-icon">
                <i class="fa-solid fa-calendar-check"></i>
            </div>

            <h2>Absensi Hari Ini</h2>

            <p>Pastikan GPS perangkat Anda aktif sebelum melakukan absensi.</p>

        </div>

        <!-- TANGGAL -->
        <div class="date-box">
            <span>
                <i class="fa-solid fa-calendar-days"></i>
                <?= date('d F Y'); ?>
                &nbsp; | &nbsp;
                <i class="fa-solid fa-clock"></i>
                <?= date('H:i'); ?> WIB
            </span>
        </div>

        <form method="POST" action="proses_absen.php" id="form"
              data-jam-masuk="<?= htmlspecialchars($absen['jam_masuk'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <input type="hidden" name="aksi" id="aksi">
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">

            <?php if (!$absen) { ?>

                <!-- BELUM ABSEN MASUK -->
                <div class="completed-box" style="background:#eff6ff;border-color:#bfdbfe;">
                    <i class="fa-solid fa-circle-info" style="color:#2563eb;"></i>

                    <h3 style="color:#1d4ed8;">Anda Belum Melakukan Absensi</h3>

                    <p>
                        Silakan lakukan absensi masuk untuk mencatat
                        kehadiran Anda hari ini.
                    </p>
                </div>

                <button type="button" class="btn btn-green" onclick="absen('masuk')">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Absen Masuk
                </button>

            <?php } elseif (!$absen['jam_pulang']) { ?>

                <!-- SUDAH ABSEN MASUK -->
                <div class="attendance-info">

                    <div class="info-box">
                        <div class="info-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>

                        <h4>Jam Masuk</h4>

                        <p><?= $absen['jam_masuk']; ?> WIB</p>
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <h4>Status Kehadiran</h4>

                        <p style="color:#16a34a;font-size:14px;">
                            Sudah Absen
                        </p>
                    </div>

                </div>

                <div class="date-box" id="durasiKerjaBox" style="margin-top:20px;">
                    <span id="durasiKerjaText">
                        <i class="fa-solid fa-hourglass-half"></i>
                        Menghitung durasi kerja...
                    </span>
                </div>

                <div class="form-group">
                    <label for="alasan">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Alasan Pulang Sebelum 7 Jam (Wajib Jika Kurang)
                    </label>

                    <textarea
                        name="alasan"
                        id="alasan"
                        placeholder="Wajib diisi jika durasi kerja belum mencapai 7 jam..."
                    ></textarea>
                </div>

                <button type="button" class="btn btn-blue" onclick="absen('pulang')">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Absen Pulang
                </button>

            <?php } else { ?>

                <!-- ABSENSI SELESAI -->
                <div class="attendance-info">

                    <div class="info-box">
                        <div class="info-icon">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </div>

                        <h4>Jam Masuk</h4>

                        <p><?= $absen['jam_masuk']; ?> WIB</p>
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </div>

                        <h4>Jam Pulang</h4>

                        <p><?= $absen['jam_pulang']; ?> WIB</p>
                    </div>

                </div>

                <div class="status-wrap">

                    <div class="status-label">
                        Status Absensi Hari Ini
                    </div>

                    <?php
                    if ($absen['status_pulang'] == 'pulang_cepat') {
                        echo "<span class='badge orange'>
                                <i class='fa-solid fa-clock'></i> Pulang Cepat
                              </span>";
                    } elseif ($absen['status_pulang'] == 'lembur') {
                        echo "<span class='badge blueb'>
                                <i class='fa-solid fa-moon'></i> Lembur
                              </span>";
                    } else {
                        echo "<span class='badge greenb'>
                                <i class='fa-solid fa-check-circle'></i> Pulang Tepat Waktu
                              </span>";
                    }
                    ?>

                </div>

                <div class="completed-box">
                    <i class="fa-solid fa-circle-check"></i>

                    <h3>Absensi Selesai!</h3>

                    <p>
                        Anda sudah menyelesaikan absensi hari ini.
                        Terima kasih atas kedisiplinan Anda.
                    </p>
                </div>

                <button type="button" class="btn btn-disabled" disabled>
                    <i class="fa-solid fa-check"></i>
                    Sudah Melakukan Absensi
                </button>

            <?php } ?>

        </form>

    </div>

</main>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    &copy; <?= date('Y'); ?> Sistem Absensi & Gaji Darma Kasyifa. All Rights Reserved.
</footer>

</body>
</html>
