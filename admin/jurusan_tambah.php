<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireAdmin();

$success = "";
$error = "";
$edit_data = null;
$is_edit = false;

// Edit mode
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM jurusan WHERE id = $id");
    $edit_data = mysqli_fetch_assoc($res);
    if(!$edit_data) { header("Location: jurusan.php"); exit; }
    $is_edit = true;
}

// Simpan / Update
if(isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $sejarah = mysqli_real_escape_string($conn, trim($_POST['sejarah']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $logo_sql = "";

    if(!empty($_FILES['logo']['name'])) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if(in_array(strtolower($ext), $allowed)) {
            if(!is_dir('../uploads/jurusan/')) mkdir('../uploads/jurusan/', 0755, true);
            $logo_path = 'uploads/jurusan/' . time() . '.' . $ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], '../' . $logo_path);
            $logo_sql = ", logo='$logo_path'";
        }
    }

    if($is_edit) {
        $id = intval($_POST['id']);
        $q = "UPDATE jurusan SET nama_jurusan='$nama', sejarah='$sejarah', deskripsi='$deskripsi' $logo_sql WHERE id=$id";
        if(mysqli_query($conn, $q)) { $success = "Jurusan berhasil diperbarui!"; }
        else { $error = "Gagal memperbarui: " . mysqli_error($conn); }
    } else {
        $q = "INSERT INTO jurusan (nama_jurusan, sejarah, deskripsi) VALUES ('$nama','$sejarah','$deskripsi')";
        if(mysqli_query($conn, $q)) {
            $new_id = mysqli_insert_id($conn);
            if($logo_sql) mysqli_query($conn, "UPDATE jurusan SET logo='" . substr($logo_sql, 8, -1) . "' WHERE id=$new_id");
            header("Location: jurusan.php"); exit;
        } else { $error = "Gagal menyimpan: " . mysqli_error($conn); }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit' : 'Tambah' ?> Jurusan | Admin PKL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-bg: #1e293b; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .sidebar { background: var(--sidebar-bg); min-height: 100vh; position: fixed; color: white; width: 16.666%; z-index: 100; top: 0; left: 0; bottom: 0; overflow-y: auto; }
        .sidebar .nav-link { color: #94a3b8; padding: 11px 20px; border-radius: 8px; margin: 3px 12px; transition: 0.2s; font-size: 0.9rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #334155; color: white; }
        .main-content { margin-left: 16.666%; }
        .topbar { background: white; padding: 14px 28px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 99; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 10px 14px; }
        .form-control:focus, .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); background: white; }
        @media(max-width:768px){.sidebar{display:none!important}.main-content{margin-left:0}}
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0 sidebar d-none d-md-block">
            <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
                <h5 class="fw-bold mb-0 text-white"><i class="bi bi-shield-check me-2"></i>ADMIN PKL</h5>
                <small class="text-secondary">SMKN 1 Cimahi</small>
            </div>
            <ul class="nav flex-column mt-2">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid-1x2-fill me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="siswa.php"><i class="bi bi-people-fill me-2"></i>Data Siswa</a></li>
                <li class="nav-item"><a class="nav-link" href="tempat_pkl.php"><i class="bi bi-building-fill-check me-2"></i>Tempat PKL</a></li>
                <li class="nav-item"><a class="nav-link active" href="jurusan.php"><i class="bi bi-mortarboard-fill me-2"></i>Jurusan</a></li>
                <li class="nav-item"><a class="nav-link" href="pengunjung.php"><i class="bi bi-eye-fill me-2"></i>Pengunjung</a></li>
                <li class="nav-item mt-3"><hr class="text-secondary mx-3"><a class="nav-link" href="../public/index.php" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>Lihat Website</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
            </ul>
        </div>

        <div class="main-content col-md-9 col-lg-10">
            <div class="topbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="jurusan.php" class="btn btn-light btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <h5 class="mb-0 fw-bold"><?= $is_edit ? 'Edit' : 'Tambah' ?> Jurusan</h5>
                </div>
            </div>

            <div class="p-4">
                <?php if($success): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                        <a href="jurusan.php" class="alert-link ms-2">Kembali ke daftar jurusan →</a>
                    </div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card form-card p-4">
                            <form method="POST" enctype="multipart/form-data">
                                <?php if($is_edit): ?>
                                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                                <?php endif; ?>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Nama Jurusan *</label>
                                    <input type="text" name="nama" class="form-control" required
                                           placeholder="Contoh: Rekayasa Perangkat Lunak"
                                           value="<?= htmlspecialchars($edit_data['nama_jurusan'] ?? '') ?>">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Logo Jurusan</label>
                                    <?php if(!empty($edit_data['logo'])): ?>
                                        <div class="mb-2">
                                            <img src="../<?= $edit_data['logo'] ?>" style="height:60px;object-fit:contain;border-radius:8px;background:#f1f5f9;padding:6px;">
                                            <small class="text-muted ms-2">Logo saat ini</small>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF, WEBP. <?= $is_edit ? 'Kosongkan jika tidak ingin mengganti.' : '' ?></small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="3"
                                              placeholder="Deskripsi singkat tentang jurusan ini..."><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Sejarah Jurusan</label>
                                    <textarea name="sejarah" class="form-control" rows="4"
                                              placeholder="Latar belakang dan sejarah berdirinya jurusan..."><?= htmlspecialchars($edit_data['sejarah'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" name="simpan" class="btn btn-primary rounded-3 px-4 fw-semibold">
                                        <i class="bi bi-<?= $is_edit ? 'check-lg' : 'plus-lg' ?> me-2"></i>
                                        <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Jurusan' ?>
                                    </button>
                                    <a href="jurusan.php" class="btn btn-light rounded-3 px-4">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
