
<?php
include 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_karyawan = trim($_POST['id_karyawan'] ?? '');
    $nama        = trim($_POST['nama'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $passwordRaw = $_POST['password'] ?? '';

    $role = 'karyawan';

    if ($id_karyawan === '' || $nama === '' || $username === '' || $passwordRaw === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif (strlen($passwordRaw) < 8) {
        $error = "Password minimal harus 8 karakter!";
    } else {

        // Cek ID karyawan
        $cekID = mysqli_prepare($conn, "
            SELECT id_karyawan FROM users WHERE id_karyawan = ?
        ");

        mysqli_stmt_bind_param($cekID, "s", $id_karyawan);
        mysqli_stmt_execute($cekID);
        mysqli_stmt_store_result($cekID);

        if (mysqli_stmt_num_rows($cekID) > 0) {
            $error = "ID Karyawan sudah digunakan!";
        } else {

            // Cek username
            $cekUsername = mysqli_prepare($conn, "
                SELECT username FROM users WHERE username = ?
            ");

            mysqli_stmt_bind_param($cekUsername, "s", $username);
            mysqli_stmt_execute($cekUsername);
            mysqli_stmt_store_result($cekUsername);

            if (mysqli_stmt_num_rows($cekUsername) > 0) {
                $error = "Username sudah digunakan!";
            } else {

                // Hash password
                $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

                // Simpan akun
                $insert = mysqli_prepare($conn, "
                    INSERT INTO users (id_karyawan, nama, username, password, role)
                    VALUES (?, ?, ?, ?, ?)
                ");

                mysqli_stmt_bind_param(
                    $insert,
                    "sssss",
                    $id_karyawan,
                    $nama,
                    $username,
                    $password,
                    $role
                );

                if (mysqli_stmt_execute($insert)) {
                    header("Location: login.php?success=register");
                    exit;
                } else {
                    $error = "Gagal melakukan registrasi. Silakan coba lagi.";
                }

                mysqli_stmt_close($insert);
            }

            mysqli_stmt_close($cekUsername);
        }

        mysqli_stmt_close($cekID);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Karyawan | Darma Kasyifa</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root {
    --navy: #101d3a;
    --blue: #2563eb;
    --text: #17233f;
    --muted: #64748b;
    --bg: #f5f8ff;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    min-height: 100vh;
    background:
        radial-gradient(circle at 5% 10%, #dbeafe 0, transparent 35%),
        linear-gradient(135deg, #f8fbff, #eef4ff);
    color: var(--text);
    display: flex;
    flex-direction: column;
}

/* NAVBAR */
header {
    height: 76px;
    padding: 0 7%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,.9);
    border-bottom: 1px solid rgba(148,163,184,.15);
    backdrop-filter: blur(18px);
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-icon {
    width: 43px;
    height: 43px;
    border-radius: 13px;
    display: grid;
    place-items: center;
    color: white;
    font-size: 20px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    box-shadow: 0 7px 18px rgba(37,99,235,.25);
}

.logo-text strong {
    display: block;
    font-size: 15px;
    font-weight: 800;
}

.logo-text small {
    color: var(--muted);
    font-size: 10px;
    letter-spacing: 1.2px;
}

.nav-link {
    color: #475569;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: .3s;
}

.nav-link:hover {
    color: var(--blue);
}

/* MAIN */
main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 45px 20px;
}

.register-card {
    width: 100%;
    max-width: 480px;
    padding: 38px;
    border-radius: 25px;
    background: rgba(255,255,255,.95);
    border: 1px solid rgba(255,255,255,.9);
    box-shadow: 0 25px 65px rgba(30,64,175,.12);
    animation: slideUp .7s ease;
    transition: .3s;
}

.register-card:hover {
    box-shadow: 0 30px 75px rgba(30,64,175,.17);
}

/* HEADING */
.register-heading {
    text-align: center;
    margin-bottom: 28px;
}

.register-icon {
    width: 58px;
    height: 58px;
    margin: 0 auto 17px;
    display: grid;
    place-items: center;
    color: white;
    font-size: 22px;
    border-radius: 18px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    box-shadow: 0 9px 22px rgba(37,99,235,.25);
}

.register-heading h1 {
    font-size: 24px;
    font-weight: 800;
    color: var(--navy);
}

.register-heading p {
    font-size: 12px;
    color: var(--muted);
    margin-top: 9px;
    line-height: 1.7;
}

/* ERROR */
.error {
    padding: 13px;
    margin-bottom: 20px;
    border-radius: 11px;
    color: #b91c1c;
    background: #fee2e2;
    border: 1px solid #fecaca;
    font-size: 12px;
    line-height: 1.6;
}

/* FORM */
.form-group {
    margin-bottom: 19px;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 9px;
}

.input-box {
    position: relative;
}

.input-icon {
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
    height: 52px;
    padding: 0 45px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    outline: none;
    background: #f8fafc;
    color: #334155;
    font-size: 13px;
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

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #94a3b8;
    font-size: 14px;
    transition: .2s;
}

.password-toggle:hover {
    color: var(--blue);
}

/* PASSWORD INFO */
.password-info {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 7px;
}

/* BUTTON */
.register-button {
    width: 100%;
    height: 53px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(110deg, #2563eb, #1d4ed8);
    color: white;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: .3s;
    box-shadow: 0 9px 20px rgba(37,99,235,.22);
}

.register-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 13px 25px rgba(37,99,235,.3);
}

.register-button:active {
    transform: scale(.98);
}

.register-button i {
    margin-left: 8px;
}

/* LOGIN LINK */
.login-link {
    text-align: center;
    margin-top: 23px;
    font-size: 12px;
    color: var(--muted);
}

.login-link a {
    color: var(--blue);
    font-weight: 700;
    text-decoration: none;
    margin-left: 3px;
}

.login-link a:hover {
    text-decoration: underline;
}

/* FOOTER */
footer {
    padding: 25px 7%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    background: #101d3a;
    color: #cbd5e1;
    font-size: 11px;
}

footer strong {
    color: white;
}

/* ANIMATION */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* RESPONSIVE */
@media(max-width: 600px) {
    header {
        padding: 0 5%;
        height: 68px;
    }

    .logo-text strong {
        font-size: 12px;
    }

    .logo-text small {
        font-size: 8px;
    }

    .logo-icon {
        width: 37px;
        height: 37px;
    }

    .register-card {
        padding: 28px 22px;
        border-radius: 19px;
    }

    .register-heading h1 {
        font-size: 21px;
    }

    footer {
        flex-direction: column;
        text-align: center;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
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

    <a href="login.php" class="nav-link">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
    </a>
</header>

<!-- REGISTER FORM -->
<main>

    <div class="register-card">

        <div class="register-heading">
            <div class="register-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>

            <h1>Buat Akun Karyawan</h1>
            <p>
                Silakan lengkapi data berikut untuk membuat
                akun dan mengakses sistem Darma Kasyifa.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>ID Karyawan</label>

                <div class="input-box">
                    <i class="fa-solid fa-id-card input-icon"></i>

                    <input
                        type="text"
                        name="id_karyawan"
                        placeholder="Masukkan ID Karyawan"
                        value="<?= htmlspecialchars($_POST['id_karyawan'] ?? ''); ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>

                <div class="input-box">
                    <i class="fa-solid fa-user input-icon"></i>

                    <input
                        type="text"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Username</label>

                <div class="input-box">
                    <i class="fa-solid fa-at input-icon"></i>

                    <input
                        type="text"
                        name="username"
                        placeholder="Buat username"
                        value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                        autocomplete="username"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>

                <div class="input-box">
                    <i class="fa-solid fa-lock input-icon"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Buat password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                    <i class="fa-solid fa-eye password-toggle"
                       id="toggleIcon"
                       onclick="togglePassword()"></i>
                </div>

                <p class="password-info">
                    <i class="fa-solid fa-circle-info"></i>
                    Password minimal 8 karakter.
                </p>
            </div>

            <button type="submit" class="register-button">
                Daftar Sekarang
                <i class="fa-solid fa-arrow-right"></i>
            </button>

        </form>

        <div class="login-link">
            Sudah memiliki akun?
            <a href="login.php">Masuk di sini</a>
        </div>

    </div>

</main>

<!-- FOOTER -->
<footer>
    <div>
        <strong>DARMA KASYIFA</strong> | Sistem Absensi & Penggajian
    </div>

    <div>
        &copy; <?= date('Y'); ?> Darma Kasyifa. All Rights Reserved.
    </div>
</footer>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        password.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>
