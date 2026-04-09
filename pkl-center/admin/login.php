<?php
/**
 * Login Admin - PKL Center
 * File: admin/login.php
 * Hanya untuk role = 'admin'
 */
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';

redirectIfLoggedIn();

$sweetAlert = "";
$notif = "";

if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $notif = "logout";
}

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND role = 'admin'");

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            // Catat activity
            $uid = $user['id'];
            mysqli_query($conn, "INSERT INTO user_activity (user_id) VALUES ($uid) ON DUPLICATE KEY UPDATE last_activity = NOW()");

            $_SESSION['sweet_success'] = "Login berhasil, selamat datang Admin " . htmlspecialchars($user['nama']) . "!";
            header("Location: dashboard.php");
            exit;
        } else {
            $sweetAlert = "error|Password yang Anda masukkan salah.";
        }
    } else {
        $cek = mysqli_query($conn, "SELECT id, role FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $sweetAlert = "error|Akses ditolak. Akun ini bukan akun admin.";
        } else {
            $sweetAlert = "error|Email admin tidak ditemukan.";
        }
    }
}

$saIcon = ""; $saTitle = ""; $saMsg = "";
if ($sweetAlert) {
    [$saIcon, $saMsg] = explode("|", $sweetAlert, 2);
    $saTitle = "Login Gagal";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin – PKL Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:linear-gradient(135deg,#1e293b 0%,#334155 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .login-container { max-width:420px; width:100%; }
        .card-login { background:rgba(255,255,255,.97); border:none; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,.4); overflow:hidden; }
        .login-header { background:linear-gradient(135deg,#1e293b,#334155); padding:36px 40px 24px; text-align:center; color:#fff; }
        .brand-icon { width:64px; height:64px; background:rgba(255,255,255,.15); border:2px solid rgba(255,255,255,.3); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:1.9rem; color:#fff; }
        .form-section { padding:28px 40px 36px; }
        .form-label { font-size:.8rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.5px; }
        .input-group { border-radius:12px; overflow:hidden; border:1.5px solid #e2e8f0; transition:all .3s; }
        .input-group:focus-within { border-color:#334155; box-shadow:0 0 0 4px rgba(51,65,85,.12); }
        .input-group-text { background:#f8fafc; border:none; color:#94a3b8; }
        .form-control { border:none; padding:12px 15px; background:#f8fafc; font-size:.95rem; }
        .form-control:focus { background:#fff; box-shadow:none; }
        .btn-login { background:linear-gradient(135deg,#1e293b,#334155); border:none; border-radius:12px; padding:13px; font-weight:700; color:#fff; width:100%; margin-top:8px; transition:all .3s; }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 10px 20px rgba(30,41,59,.4); }
        .siswa-link { background:#f8fafc; border-top:1px solid #f1f5f9; padding:14px 40px; text-align:center; font-size:.82rem; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="card-login">
        <div class="login-header">
            <div class="brand-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h4 class="fw-bold mb-1">Admin Panel</h4>
            <p class="opacity-75 small mb-0">PKL Center – SMKN 1 Cimahi</p>
        </div>
        <div class="form-section">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email Admin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="admin@pkl.com" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-login">
                    MASUK ADMIN <i class="bi bi-shield-check ms-1"></i>
                </button>
            </form>
        </div>
        <div class="siswa-link">
            <i class="bi bi-mortarboard me-1 text-muted"></i>
            Siswa? <a href="../auth/login.php" class="text-primary fw-semibold text-decoration-none">Login Siswa</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if ($saMsg): ?>
Swal.fire({ icon:'<?= $saIcon ?>', title:'<?= $saTitle ?>', text:'<?= addslashes($saMsg) ?>', timer:3500, showConfirmButton:false });
<?php endif; ?>
<?php if ($notif === 'logout'): ?>
Swal.fire({ icon:'success', title:'Logout Berhasil', text:'Sampai jumpa, Admin!', timer:2500, showConfirmButton:false });
<?php endif; ?>
</script>
</body>
</html>
