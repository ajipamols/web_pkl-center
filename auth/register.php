<?php
/**
 * Register Siswa + OTP Simulasi - PKL Center
 * File: auth/register.php
 */
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';

redirectIfLoggedIn();

$error = "";

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $konfirm  = $_POST['konfirmasi_password'];

    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $konfirm) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password terlalu pendek (Min. 6 karakter)!";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email ini sudah digunakan!";
        } else {
            // Generate OTP 6 digit
            $otp     = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expired = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $hashed  = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (nama, email, password, role, is_verified, otp_code, otp_expired)
                      VALUES ('$nama', '$email', '$hashed', 'siswa', 0, '$otp', '$expired')";

            if (mysqli_query($conn, $query)) {
                $new_id = mysqli_insert_id($conn);
                // Simpan ke session untuk halaman verifikasi
                $_SESSION['pending_verify_id']  = $new_id;
                $_SESSION['pending_verify_otp'] = $otp; // simulasi → tampil ke user
                header("Location: verify_otp.php");
                exit;
            } else {
                $error = "Sistem sibuk, silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun – PKL Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#f4f7fe; min-height:100vh; display:flex; align-items:center; }
        .register-container { background:#fff; border-radius:28px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,.08); }
        .register-side { background:linear-gradient(135deg,rgba(13,110,253,.92),rgba(2,132,199,.92)), url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=800') center/cover; display:flex; flex-direction:column; justify-content:center; padding:44px; color:#fff; }
        .form-section { padding:48px; }
        .form-control, .input-group .form-control { border-radius:0 12px 12px 0; padding:12px 15px; background:#f8fafc; border:1px solid #e2e8f0; }
        .form-control:focus { box-shadow:0 0 0 4px rgba(13,110,253,.1); border-color:#0d6efd; }
        .input-group-text { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px 0 0 12px; }
        .btn-register { background:linear-gradient(135deg,#0d6efd,#0284c7); border:none; border-radius:12px; padding:14px; font-weight:700; transition:.3s; }
        .btn-register:hover { transform:translateY(-2px); box-shadow:0 10px 20px rgba(13,110,253,.3); }
        @media(max-width:768px){ .register-side{display:none} .form-section{padding:30px} }
    </style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center">
<div class="col-lg-10 col-xl-9">
<div class="register-container row g-0">
    <div class="col-md-5 register-side">
        <h2 class="fw-bold mb-3">Mulai Perjalanan Karirmu.</h2>
        <p class="opacity-80 mb-4">Daftarkan dirimu untuk mengakses peluang PKL di perusahaan mitra terbaik kami.</p>
        <div class="d-flex align-items-center mb-3"><i class="bi bi-check-circle-fill me-2 text-info"></i><span>Akses Tempat PKL Favorit</span></div>
        <div class="d-flex align-items-center mb-3"><i class="bi bi-check-circle-fill me-2 text-info"></i><span>Informasi Syarat PKL per Jurusan</span></div>
        <div class="d-flex align-items-center"><i class="bi bi-check-circle-fill me-2 text-info"></i><span>Komunikasi Langsung dengan Admin</span></div>
    </div>
    <div class="col-md-7 form-section">
        <div class="mb-4">
            <h3 class="fw-bold text-dark">Buat Akun Siswa</h3>
            <p class="text-muted small">Lengkapi data di bawah untuk mendaftar.</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger border-0 rounded-3 small py-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">NAMA LENGKAP</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="nama" class="form-control" placeholder="Nama sesuai ijazah" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">EMAIL AKTIF</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">KONFIRMASI</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock text-muted"></i></span>
                        <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label small text-muted" for="terms">
                    Saya menyetujui syarat & ketentuan PKL SMKN 1 Cimahi.
                </label>
            </div>
            <button type="submit" name="register" class="btn btn-register w-100 text-white mb-4">
                DAFTAR SEKARANG <i class="bi bi-arrow-right-short ms-1"></i>
            </button>
        </form>
        <div class="text-center">
            <p class="small text-muted mb-1">Sudah punya akun? <a href="login.php" class="text-primary fw-bold text-decoration-none">Masuk di sini</a></p>
            <a href="../public/index.php" class="small text-secondary text-decoration-none"><i class="bi bi-house-door me-1"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
