<?php
/**
 * Login Siswa - PKL Center
 * File: auth/login.php
 * Hanya untuk role = 'siswa'
 */
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';

redirectIfLoggedIn();

$error = "";
$success = "";

if (isset($_GET['register']) && $_GET['register'] === 'success') {
    $success = "Pendaftaran berhasil! Silakan verifikasi OTP terlebih dahulu.";
}
if (isset($_GET['reason'])) {
    if ($_GET['reason'] === 'denied') $error = "Akses ditolak. Halaman ini hanya untuk siswa.";
    if ($_GET['reason'] === 'login') $error = "Silakan login terlebih dahulu.";
}
if (isset($_GET['verified']) && $_GET['verified'] === '1') {
    $success = "Akun berhasil diverifikasi! Silakan login.";
}

$sweetAlert = "";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND role = 'siswa'");

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                // Kirim ulang OTP & arahkan ke verifikasi
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expired = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                mysqli_query($conn, "UPDATE users SET otp_code='$otp', otp_expired='$expired' WHERE id={$user['id']}");
                $_SESSION['pending_verify_id'] = $user['id'];
                $_SESSION['pending_verify_otp'] = $otp; // simulasi
                header("Location: verify_otp.php?resend=1");
                exit;
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            // Catat activity
            $uid = $user['id'];
            mysqli_query($conn, "INSERT INTO user_activity (user_id) VALUES ($uid) ON DUPLICATE KEY UPDATE last_activity = NOW()");

            // SweetAlert login sukses — simpan di session, tampil di dashboard
            $_SESSION['sweet_success'] = "Login berhasil, selamat datang " . htmlspecialchars($user['nama']) . "!";
            header("Location: ../siswa/dashboard.php");
            exit;
        } else {
            $sweetAlert = "error|Email atau password salah!";
        }
    } else {
        // Cek apakah email ada tapi role admin
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $sweetAlert = "error|Akun ini bukan akun siswa.";
        } else {
            $sweetAlert = "error|Email tidak ditemukan dalam sistem.";
        }
    }
}

$saIcon = ""; $saTitle = ""; $saMsg = "";
if ($sweetAlert) {
    [$saIcon, $saMsg] = explode("|", $sweetAlert, 2);
    $saTitle = $saIcon === "error" ? "Login Gagal" : "Berhasil";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa – PKL Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:linear-gradient(135deg,#0d6efd 0%,#0284c7 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .login-container { max-width:440px; width:100%; }
        .card-login { background:rgba(255,255,255,0.97); border:none; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden; }
        .login-header { background:#fff; padding:36px 40px 16px; text-align:center; border-bottom:1px solid #f1f5f9; }
        .brand-icon { width:60px; height:60px; background:linear-gradient(135deg,#0d6efd,#0284c7); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:1.8rem; color:#fff; }
        .form-section { padding:24px 40px 36px; }
        .form-label { font-size:.8rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.5px; }
        .input-group { border-radius:12px; overflow:hidden; border:1.5px solid #e2e8f0; transition:all .3s; }
        .input-group:focus-within { border-color:#0d6efd; box-shadow:0 0 0 4px rgba(13,110,253,.1); }
        .input-group-text { background:#f8fafc; border:none; color:#94a3b8; }
        .form-control { border:none; padding:12px 15px; background:#f8fafc; font-size:.95rem; }
        .form-control:focus { background:#fff; box-shadow:none; }
        .btn-login { background:linear-gradient(135deg,#0d6efd,#0284c7); border:none; border-radius:12px; padding:13px; font-weight:700; color:#fff; width:100%; margin-top:8px; transition:all .3s; }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 10px 20px rgba(13,110,253,.35); }
        .alert { border:none; border-radius:12px; font-size:.9rem; }
        .admin-link { background:#f8fafc; border-top:1px solid #f1f5f9; padding:14px 40px; text-align:center; font-size:.82rem; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="card-login">
        <div class="login-header">
            <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <h4 class="fw-bold text-dark mb-1">Login Siswa</h4>
            <p class="text-muted small mb-0">PKL Center – SMKN 1 Cimahi</p>
        </div>
        <div class="form-section">
            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <?php if ($error && !$sweetAlert): ?>
                <div class="alert alert-danger d-flex align-items-center mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email siswa" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-login">
                    MASUK <i class="bi bi-arrow-right-short ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted mb-1">Belum punya akun? <a href="register.php" class="text-primary fw-bold text-decoration-none">Daftar Sekarang</a></p>
                <a href="../public/index.php" class="small text-secondary text-decoration-none"><i class="bi bi-house-door me-1"></i> Beranda</a>
            </div>
        </div>
        <div class="admin-link">
            <i class="bi bi-shield-lock me-1 text-muted"></i>
            Admin? <a href="../admin/login.php" class="text-primary fw-semibold text-decoration-none">Login Admin</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($saMsg): ?>
<script>
Swal.fire({
    icon: '<?= $saIcon ?>',
    title: '<?= $saTitle ?>',
    text: '<?= addslashes($saMsg) ?>',
    timer: 3000,
    showConfirmButton: false
});
</script>
<?php endif; ?>
</body>
</html>
