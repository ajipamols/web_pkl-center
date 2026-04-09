<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireAdmin();

$success = "";
$error = "";

// --- LOGIC CRUD ---
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
    $logo = null;
    
    if (!empty($_FILES['logo']['name'])) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo = 'uploads/jurusan/' . time() . '.' . $ext;
        if (!is_dir('../uploads/jurusan/')) mkdir('../uploads/jurusan/', 0755, true);
        move_uploaded_file($_FILES['logo']['tmp_name'], '../' . $logo);
    }

    $q = "INSERT INTO jurusan (nama_jurusan, logo) VALUES ('$nama', '$logo')";
    if (mysqli_query($conn, $q)) $success = "Jurusan baru berhasil ditambahkan!";
    else $error = "Gagal menambah data.";
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM jurusan WHERE id=$id");
    $success = "Jurusan berhasil dihapus!";
}

$jurusan = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jurusan | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --sidebar-bg: #1e293b; --primary-gradient: linear-gradient(135deg, #6366f1, #a855f7); }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .sidebar { background: var(--sidebar-bg); min-height: 100vh; position: fixed; color: white; width: 16.666667%; z-index: 100; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; border-radius: 8px; margin: 4px 15px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #334155; color: white; }

        .main-content { margin-left: 16.666667%; padding: 0; width: 83.333333%; }
        .topbar { background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        .jurusan-card { border: none; border-radius: 20px; transition: all 0.3s ease; background: white; border-bottom: 4px solid transparent; }
        .jurusan-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-bottom: 4px solid #6366f1; }
        
        .logo-wrapper { width: 80px; height: 80px; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 20px; overflow: hidden; }
        .logo-wrapper img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
        
        .btn-add { background: var(--primary-gradient); color: white; border: none; border-radius: 12px; padding: 10px 24px; font-weight: 600; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
        .btn-add:hover { color: white; opacity: 0.9; transform: scale(1.02); }

        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0 sidebar">
            <div class="p-4 text-center">
                <h5 class="fw-bold"><i class="bi bi-shield-check me-2"></i>ADMIN PKL</h5>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="siswa.php"><i class="bi bi-people-fill me-2"></i> Data Siswa</a></li>
                <li class="nav-item"><a class="nav-link" href="tempat_pkl.php"><i class="bi bi-building-fill me-2"></i> Tempat PKL</a></li>
                <li class="nav-item"><a class="nav-link active" href="jurusan.php"><i class="bi bi-mortarboard-fill me-2"></i> Jurusan</a></li>
                <li class="nav-item"><a class="nav-link" href="pengunjung.php"><i class="bi bi-eye-fill me-2"></i> Pengunjung</a></li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="topbar d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Daftar Jurusan</h5>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Jurusan
                </button>
            </div>

            <div class="p-4">
                <?php if($success): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm"><?= $success ?></div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php if(mysqli_num_rows($jurusan) > 0): ?>
                        <?php while($j = mysqli_fetch_assoc($jurusan)): ?>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="card jurusan-card h-100 p-4 text-center">
                                    <div class="logo-wrapper shadow-sm">
                                        <?php if(!empty($j['logo'])): ?>
                                            <img src="../<?= $j['logo'] ?>" alt="Logo">
                                        <?php else: ?>
                                            <i class="bi bi-mortarboard fs-1 text-primary"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($j['nama_jurusan']) ?></h6>
                                    <p class="small text-muted mb-4">SMK PKL Center</p>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="detail_jurusan.php?id=<?= $j['id'] ?>" class="btn btn-light rounded-pill btn-sm fw-bold">
                                            <i class="bi bi-search me-1"></i> Detail
                                        </a>
                                        <a href="?hapus=<?= $j['id'] ?>" class="text-danger small mt-2 text-decoration-none" onclick="return confirm('Hapus jurusan ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <img src="https://illustrations.popsy.co/slate/empty-state.svg" style="height: 200px;" class="mb-4">
                            <h5 class="text-muted">Belum ada data jurusan</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold">Tambah Jurusan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA JURUSAN</label>
                        <input type="text" name="nama_jurusan" class="form-control bg-light border-0 py-2" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">LOGO JURUSAN</label>
                        <input type="file" name="logo" class="form-control bg-light border-0 py-2">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary rounded-3 px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>