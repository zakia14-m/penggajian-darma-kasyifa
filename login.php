
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Darma Kasyifa</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root{
    --navy:#101d3a;
    --blue:#2563eb;
    --light-blue:#60a5fa;
    --text:#17233f;
    --muted:#64748b;
    --bg:#f5f8ff;
    --white:#fff;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

html{scroll-behavior:smooth;}

body{
    background:var(--bg);
    color:var(--text);
    overflow-x:hidden;
}

/* NAVBAR */
header{
    position:sticky;
    top:0;
    z-index:1000;
    height:76px;
    padding:0 7%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    background:rgba(255,255,255,.88);
    backdrop-filter:blur(18px);
    border-bottom:1px solid rgba(148,163,184,.15);
}

.logo{
    display:flex;
    align-items:center;
    gap:12px;
}

.logo-icon{
    width:43px;
    height:43px;
    border-radius:13px;
    display:grid;
    place-items:center;
    color:white;
    font-size:20px;
    background:linear-gradient(135deg,#2563eb,#1e40af);
    box-shadow:0 7px 18px rgba(37,99,235,.25);
}

.logo-text strong{
    display:block;
    font-size:15px;
    font-weight:800;
}

.logo-text small{
    color:var(--muted);
    font-size:10px;
    letter-spacing:1.2px;
}

nav{
    display:flex;
    align-items:center;
    gap:30px;
}

nav a{
    color:#475569;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
    transition:.3s;
}

nav a:hover{color:var(--blue);}

.nav-login{
    padding:12px 22px;
    border-radius:10px;
    color:white!important;
    background:var(--blue);
    box-shadow:0 6px 15px rgba(37,99,235,.2);
}

.nav-login:hover{
    background:#1d4ed8;
    transform:translateY(-2px);
}

/* HERO */
.hero{
    position:relative;
    min-height:690px;
    padding:75px 7%;
    display:grid;
    grid-template-columns:1fr 1fr;
    align-items:center;
    gap:60px;
    overflow:hidden;
    background:
        radial-gradient(circle at 5% 10%,#dbeafe 0,transparent 35%),
        linear-gradient(135deg,#f8fbff,#eef4ff);
}

.hero::before{
    content:"";
    position:absolute;
    width:500px;
    height:500px;
    right:-180px;
    top:-180px;
    border-radius:50%;
    background:rgba(96,165,250,.10);
}

.hero-content{
    position:relative;
    z-index:1;
    animation:slideUp .8s ease;
}

.eyebrow{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:9px 15px;
    border-radius:30px;
    background:#dbeafe;
    color:#1d4ed8;
    font-size:11px;
    font-weight:700;
    letter-spacing:1px;
    margin-bottom:25px;
}

.eyebrow i{font-size:13px;}

.hero h1{
    font-size:clamp(34px,4vw,54px);
    line-height:1.18;
    font-weight:800;
    letter-spacing:-2px;
    color:var(--navy);
}

.hero h1 span{
    color:var(--blue);
}

.hero-content p{
    margin-top:22px;
    max-width:500px;
    font-size:14px;
    line-height:1.9;
    color:var(--muted);
}

.hero-points{
    display:flex;
    gap:22px;
    flex-wrap:wrap;
    margin-top:30px;
}

.hero-points span{
    font-size:12px;
    font-weight:600;
    color:#334155;
}

.hero-points i{
    color:#2563eb;
    margin-right:7px;
}

/* LOGIN CARD */
.login-area{
    position:relative;
    z-index:2;
    animation:slideUp 1s ease;
}

.login-card{
    max-width:450px;
    margin:auto;
    padding:36px;
    border-radius:25px;
    background:rgba(255,255,255,.94);
    border:1px solid rgba(255,255,255,.9);
    box-shadow:0 25px 65px rgba(30,64,175,.12);
    transition:.35s;
}

.login-card:hover{
    transform:translateY(-5px);
    box-shadow:0 30px 75px rgba(30,64,175,.17);
}

.login-heading{
    text-align:center;
    margin-bottom:30px;
}

.login-icon{
    width:58px;
    height:58px;
    margin:0 auto 17px;
    display:grid;
    place-items:center;
    color:white;
    font-size:22px;
    border-radius:18px;
    background:linear-gradient(135deg,#2563eb,#1e40af);
    box-shadow:0 9px 22px rgba(37,99,235,.25);
}

.login-heading h2{
    font-size:24px;
    font-weight:800;
    color:var(--navy);
}

.login-heading p{
    font-size:12px;
    color:var(--muted);
    margin-top:8px;
}

.form-group{margin-bottom:19px;}

.form-group label{
    display:block;
    font-size:12px;
    font-weight:700;
    color:#334155;
    margin-bottom:9px;
}

.input-box{
    position:relative;
}

.input-box i.input-icon{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#94a3b8;
    font-size:14px;
    pointer-events:none;
}

.input-box input,
.input-box select{
    width:100%;
    height:52px;
    padding:0 45px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    outline:none;
    background:#f8fafc;
    color:#334155;
    font-size:13px;
    transition:.3s;
}

.input-box input:focus,
.input-box select:focus{
    border-color:#3b82f6;
    background:white;
    box-shadow:0 0 0 4px rgba(59,130,246,.10);
}

.password-toggle{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#94a3b8;
    font-size:14px;
    transition:.2s;
}

.password-toggle:hover{color:var(--blue);}

.login-options{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin:4px 0 23px;
    font-size:11px;
    color:var(--muted);
}

.login-options label{
    display:flex;
    align-items:center;
    gap:7px;
    cursor:pointer;
}

.login-options input{
    accent-color:var(--blue);
}

.login-button{
    width:100%;
    height:53px;
    border:0;
    border-radius:12px;
    background:linear-gradient(110deg,#2563eb,#1d4ed8);
    color:white;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
    box-shadow:0 9px 20px rgba(37,99,235,.22);
}

.login-button:hover{
    transform:translateY(-3px);
    box-shadow:0 13px 25px rgba(37,99,235,.3);
}

.login-button:active{transform:scale(.98);}

.login-button i{margin-left:8px;}

.error{
    display:none;
    padding:12px;
    margin-bottom:20px;
    border-radius:10px;
    color:#b91c1c;
    background:#fee2e2;
    font-size:12px;
    text-align:center;
}

.register-link{
    display:block;
    text-align:center;
    margin-top:22px;
    font-size:12px;
    color:var(--muted);
}

.register-link a{
    color:var(--blue);
    font-weight:700;
    text-decoration:none;
}

/* INFO */
.info-strip{
    padding:25px 7%;
    background:white;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    border-bottom:1px solid #eef2f7;
}

.info-item{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:14px;
}

.info-item i{
    width:43px;
    height:43px;
    display:grid;
    place-items:center;
    border-radius:13px;
    background:#eff6ff;
    color:var(--blue);
    font-size:17px;
}

.info-item strong{
    display:block;
    font-size:12px;
}

.info-item small{
    display:block;
    margin-top:5px;
    font-size:10px;
    color:var(--muted);
}

/* ABOUT */
.about{
    padding:100px 7%;
    text-align:center;
}

.section-label{
    font-size:11px;
    font-weight:800;
    letter-spacing:2px;
    color:var(--blue);
}

.about h2{
    font-size:34px;
    margin-top:13px;
    color:var(--navy);
    font-weight:800;
}

.about-description{
    max-width:700px;
    margin:20px auto 45px;
    font-size:13px;
    line-height:1.9;
    color:var(--muted);
}

.gallery{
    display:grid;
    grid-template-columns:repeat(6,1fr);
    grid-auto-rows:170px;
    gap:15px;
    max-width:1000px;
    margin:auto;
}

.gallery-item{
    position:relative;
    overflow:hidden;
    border-radius:17px;
    background:#dbeafe;
}

.gallery-item:nth-child(1),
.gallery-item:nth-child(2),
.gallery-item:nth-child(3){grid-column:span 2;}

.gallery-item:nth-child(4),
.gallery-item:nth-child(5){grid-column:span 3;}

.gallery-item img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.6s;
}

.gallery-item:hover img{
    transform:scale(1.08);
}

.gallery-item::after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(transparent,rgba(15,23,42,.15));
    pointer-events:none;
}

/* FOOTER */
footer{
    padding:30px 7%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    background:#101d3a;
    color:#cbd5e1;
    font-size:11px;
}

footer strong{color:white;}

/* ANIMATION */
@keyframes slideUp{
    from{opacity:0;transform:translateY(25px);}
    to{opacity:1;transform:translateY(0);}
}

/* RESPONSIVE */
@media(max-width:900px){
    .hero{
        grid-template-columns:1fr;
        gap:45px;
        padding:60px 6%;
    }

    .hero-content{text-align:center;}
    .hero-content p{margin-left:auto;margin-right:auto;}
    .hero-points{justify-content:center;}
    .login-card{max-width:500px;}
}

@media(max-width:650px){
    header{padding:0 5%;height:68px;}
    nav{gap:13px;}
    nav a{font-size:11px;}
    .nav-login{padding:10px 13px;}
    .logo-text strong{font-size:12px;}
    .logo-text small{font-size:8px;}
    .logo-icon{width:37px;height:37px;}
    .hero{padding:55px 5%;}
    .hero h1{font-size:36px;letter-spacing:-1px;}
    .hero-content p{font-size:12px;}
    .login-card{padding:28px 22px;border-radius:19px;}
    .info-strip{grid-template-columns:1fr;gap:20px;}
    .info-item{justify-content:flex-start;}
    .about{padding:75px 5%;}
    .about h2{font-size:27px;}
    .gallery{grid-template-columns:repeat(2,1fr);grid-auto-rows:130px;}
    .gallery-item{grid-column:span 1!important;}
    footer{flex-direction:column;text-align:center;}
}
</style>
</head>

<body>

<header>
    <div class="logo">
        <div class="logo-icon">
            <i class="fa-solid fa-building-shield"></i>
        </div>
        <div class="logo-text">
            <strong>DARMA KASYIFA</strong>
            <small>EMPLOYEE MANAGEMENT SYSTEM</small>
        </div>
    </div>

    <nav>
        <a href="#login">Beranda</a>
        <a href="#about">Tentang Kami</a>
        <a href="#login" class="nav-login">Login <i class="fa-solid fa-arrow-right"></i></a>
    </nav>
</header>

<main>
<section class="hero">

    <div class="hero-content">
        <div class="eyebrow">
            <i class="fa-solid fa-shield-halved"></i>
            SISTEM MANAJEMEN KARYAWAN
        </div>

        <h1>
            Kelola Kehadiran dan Gaji dengan
            <span>Lebih Mudah.</span>
        </h1>

        <p>
            Selamat datang di sistem informasi Darma Kasyifa.
            Platform terintegrasi untuk mendukung pengelolaan
            kehadiran, produktivitas, dan penggajian karyawan
            secara lebih efektif dan transparan.
        </p>

        <div class="hero-points">
            <span><i class="fa-solid fa-circle-check"></i>Data Terintegrasi</span>
            <span><i class="fa-solid fa-circle-check"></i>Akses Praktis</span>
            <span><i class="fa-solid fa-circle-check"></i>Informasi Terstruktur</span>
        </div>
    </div>

    <div class="login-area" id="login">
        <div class="login-card">

            <div class="login-heading">
                <div class="login-icon">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <h2>Selamat Datang</h2>
                <p>Silakan masuk untuk mengakses sistem.</p>
            </div>

            <div class="error" id="errorMessage">
                <i class="fa-solid fa-circle-exclamation"></i>
                Username atau password salah.
            </div>

            <form method="POST" action="prosesLogin.php">

                <div class="form-group">
                    <label>Login Sebagai</label>
                    <div class="input-box">
                        <i class="fa-solid fa-user-tag input-icon"></i>
                        <select name="role" id="role" required onchange="cekRole()">
                            <option value="">Pilih jenis akun</option>
                            <option value="admin">Admin</option>
                            <option value="karyawan">Karyawan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" name="username"
                               placeholder="Masukkan username"
                               autocomplete="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password"
                               id="password"
                               placeholder="Masukkan password"
                               autocomplete="current-password" required>

                        <i class="fa-solid fa-eye password-toggle"
                           id="toggleIcon"
                           onclick="togglePassword()"></i>
                    </div>
                </div>

                <div class="login-options">
                    <label>
                        <input type="checkbox" id="showInfo">
                        Ingatkan saya
                    </label>
                    <span><i class="fa-solid fa-shield-halved"></i> Secure Login</span>
                </div>

                <button type="submit" class="login-button">
                    Masuk ke Sistem
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

            </form>

            <div class="register-link" id="registerLink">
                Belum memiliki akun?
                <a href="register.php">Daftar sekarang</a>
            </div>

        </div>
    </div>

</section>

<section class="info-strip">
    <div class="info-item">
        <i class="fa-solid fa-clock"></i>
        <div>
            <strong>Manajemen Kehadiran</strong>
            <small>Pengelolaan data absensi karyawan</small>
        </div>
    </div>

    <div class="info-item">
        <i class="fa-solid fa-wallet"></i>
        <div>
            <strong>Pengelolaan Penggajian</strong>
            <small>Informasi gaji yang terstruktur</small>
        </div>
    </div>

    <div class="info-item">
        <i class="fa-solid fa-chart-line"></i>
        <div>
            <strong>Produktivitas Kerja</strong>
            <small>Mendukung pengelolaan kinerja</small>
        </div>
    </div>
</section>

<section class="about" id="about">
    <span class="section-label">TENTANG KAMI</span>

    <h2>Mengenal Darma Kasyifa</h2>

    <p class="about-description">
        Darma Kasyifa merupakan perusahaan yang bergerak di bidang
        produksi dengan dukungan sumber daya manusia yang berperan
        penting dalam setiap proses operasional. Melalui sistem
        informasi ini, pengelolaan kehadiran dan penggajian diharapkan
        menjadi lebih terstruktur, efektif, dan transparan.
    </p>

    <div class="gallery">
        <div class="gallery-item">
            <img src="assets/img/DK1.jpeg" alt="Dokumentasi Darma Kasyifa">
        </div>
        <div class="gallery-item">
            <img src="assets/img/DK2.jpeg" alt="Dokumentasi Darma Kasyifa">
        </div>
        <div class="gallery-item">
            <img src="assets/img/DK3.jpeg" alt="Dokumentasi Darma Kasyifa">
        </div>
        <div class="gallery-item">
            <img src="assets/img/DK4.jpeg" alt="Dokumentasi Darma Kasyifa">
        </div>
        <div class="gallery-item">
            <img src="assets/img/DK5.jpeg" alt="Dokumentasi Darma Kasyifa">
        </div>
    </div>
</section>
</main>

<footer>
    <div><strong>DARMA KASYIFA</strong> | Sistem Absensi & Penggajian</div>
    <div>&copy; 2026 Darma Kasyifa. All Rights Reserved.</div>
</footer>

<script>
function togglePassword(){
    const password = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");

    if(password.type === "password"){
        password.type = "text";
        icon.classList.replace("fa-eye","fa-eye-slash");
    }else{
        password.type = "password";
        icon.classList.replace("fa-eye-slash","fa-eye");
    }
}

function cekRole(){
    const role = document.getElementById("role").value;
    const registerLink = document.getElementById("registerLink");

    registerLink.style.display =
        role === "karyawan" ? "block" : "none";
}
</script>

</body>
</html>
