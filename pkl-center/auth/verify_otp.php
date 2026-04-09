<?php
/**
 * Verifikasi OTP - PKL Center
 * File: auth/verify_otp.php
 */
session_start();
require_once '../config/database.php';

// Harus ada pending_verify_id
if (!isset($_SESSION['pending_verify_id'])) {
    header("Location: register.php");
    exit;
}

$user_id  = (int)$_SESSION['pending_verify_id'];
$sim_otp  = $_SESSION['pending_verify_otp'] ?? null; // OTP simulasi
$error    = "";
$success  = "";
$resend   = isset($_GET['resend']);

// Ambil data user
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
if (!$row) {
    session_destroy();
    header("Location: register.php");
    exit;
}

// Sudah verified?
if ($row['is_verified']) {
    unset($_SESSION['pending_verify_id'], $_SESSION['pending_verify_otp']);
    header("Location: login.php?verified=1");
    exit;
}

// Proses submit OTP
if (isset($_POST['verify_otp'])) {
    $input_otp = trim($_POST['otp']);
    $now       = date('Y-m-d H:i:s');

    if ($row['otp_code'] === $input_otp && $row['otp_expired'] >= $now) {
        mysqli_query($conn, "UPDATE users SET is_verified=1, otp_code=NULL, otp_expired=NULL WHERE id=$user_id");
        unset($_SESSION['pending_verify_id'], $_SESSION['pending_verify_otp']);
        header("Location: login.php?verified=1");
        exit;
    } elseif ($row['otp_expired'] < $now) {
        $error = "OTP sudah kadaluarsa. Klik 'Kirim Ulang OTP'.";
    } else {
        $error = "Kode OTP salah. Periksa kembali.";
    }
}

// Kirim ulang OTP (re-generate)
if (isset($_GET['resend_otp'])) {
    $new_otp     = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $new_expired = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    mysqli_query($conn, "UPDATE users SET otp_code='$new_otp', otp_expired='$new_expired' WHERE id=$user_id");
    $_SESSION['pending_verify_otp'] = $new_otp;
    $sim_otp = $new_otp;
    $success = "OTP baru telah dikirim!";
    // Refresh row
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP – PKL Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:linear-gradient(135deg,#0d6efd,#0284c7); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .otp-card { background:#fff; border-radius:24px; padding:44px 40px; max-width:440px; width:100%; box-shadow:0 25px 50px rgba(0,0,0,.2); }
        .otp-icon { width:70px; height:70px; background:linear-gradient(135deg,#0d6efd,#0284c7); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; color:#fff; margin:0 auto 20px; }
        .sim-box { background:#fff8e1; border:2px dashed #f59e0b; border-radius:14px; padding:18px; text-align:center; margin-bottom:24px; }
        .sim-otp { font-size:2.8rem; font-weight:800; letter-spacing:10px; color:#1e293b; }
        .otp-inputs { display:flex; gap:10px; justify-content:center; margin-bottom:20px; }
        .otp-inputs input { width:50px; height:58px; text-align:center; font-size:1.4rem; font-weight:700; border:2px solid #e2e8f0; border-radius:12px; background:#f8fafc; transition:.3s; }
        .otp-inputs input:focus { border-color:#0d6efd; box-shadow:0 0 0 4px rgba(13,110,253,.1); outline:none; }
        .btn-verify { background:linear-gradient(135deg,#0d6efd,#0284c7); border:none; border-radius:12px; padding:13px; font-weight:700; color:#fff; width:100%; transition:.3s; }
        .btn-verify:hover { transform:translateY(-2px); box-shadow:0 10px 20px rgba(13,110,253,.35); }
    </style>
</head>
<body>
<div class="otp-card">
    <div class="otp-icon"><i class="bi bi-shield-check"></i></div>
    <h4 class="fw-bold text-center mb-1">Verifikasi OTP</h4>
    <p class="text-muted text-center small mb-4">
        Halo <strong><?= htmlspecialchars($row['nama']) ?></strong>, masukkan kode OTP di bawah ini untuk verifikasi akun Anda.
    </p>

    <?php if ($sim_otp): ?>
    <div class="sim-box">
        <small class="text-warning fw-bold d-block mb-1"><i class="bi bi-envelope-open me-1"></i> SIMULASI EMAIL OTP</small>
        <div class="sim-otp"><?= $sim_otp ?></div>
        <small class="text-muted">Berlaku 10 menit</small>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success border-0 rounded-3 small py-2 mb-3">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="otpForm">
        <div class="otp-inputs" id="otpInputs">
            <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
        </div>
        <input type="hidden" name="otp" id="otpHidden">
        <button type="submit" name="verify_otp" class="btn btn-verify mb-3">
            VERIFIKASI AKUN <i class="bi bi-check2-circle ms-1"></i>
        </button>
    </form>

    <div class="text-center">
        <a href="?resend_otp=1" class="small text-primary text-decoration-none">
            <i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang OTP
        </a>
        <span class="text-muted mx-2">|</span>
        <a href="login.php" class="small text-secondary text-decoration-none">Kembali ke Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-focus & auto-advance OTP inputs
const digits = document.querySelectorAll('.otp-digit');
digits.forEach((inp, idx) => {
    inp.addEventListener('input', () => {
        inp.value = inp.value.replace(/\D/g,'');
        if (inp.value && idx < digits.length - 1) digits[idx+1].focus();
        updateHidden();
    });
    inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !inp.value && idx > 0) digits[idx-1].focus();
    });
});
function updateHidden() {
    document.getElementById('otpHidden').value = [...digits].map(d => d.value).join('');
}
document.getElementById('otpForm').addEventListener('submit', e => {
    updateHidden();
    const otp = document.getElementById('otpHidden').value;
    if (otp.length < 6) { e.preventDefault(); Swal.fire({icon:'warning',title:'OTP Belum Lengkap',text:'Masukkan 6 digit kode OTP.'}); }
});

<?php if ($resend): ?>
Swal.fire({ icon:'info', title:'Akun Belum Terverifikasi', text:'Masukkan OTP untuk mengaktifkan akun Anda.', timer:3000, showConfirmButton:false });
<?php endif; ?>
</script>
</body>
</html>
