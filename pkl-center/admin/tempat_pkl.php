<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireAdmin();

$success = "";
$error = "";

// --- LOGIC PHP ---
$upload_dir = '../uploads/tempat_pkl/';
if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }

$jurusan_result = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
$action = $_GET['action'] ?? '';
$edit_data = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM tempat_pkl WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Tambah Logic
if (isset($_POST['tambah'])) {
    $nama_tempat = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));
    $kuota = (int)$_POST['kuota'];
    $jurusan_id = (int)$_POST['jurusan_id'];
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'uploads/tempat_pkl/' . time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], '../'.$foto);
    }
    $query = "INSERT INTO tempat_pkl (nama_tempat, alamat, deskripsi, kontak, kuota, jurusan_id, foto) VALUES ('$nama_tempat','$alamat','$deskripsi','$kontak',$kuota,$jurusan_id,'$foto')";
    if (mysqli_query($conn, $query)) $success = "Tempat PKL berhasil ditambahkan!";
    else $error = mysqli_error($conn);
}

// Edit Logic
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $nama_tempat = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));
    $kuota = (int)$_POST['kuota'];
    $jurusan_id = (int)$_POST['jurusan_id'];
    $foto_sql = '';
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'uploads/tempat_pkl/' . time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], '../'.$foto);
        $foto_sql = ", foto='$foto'";
    }
    $query = "UPDATE tempat_pkl SET nama_tempat='$nama_tempat', alamat='$alamat', deskripsi='$deskripsi', kontak='$kontak', kuota=$kuota, jurusan_id=$jurusan_id $foto_sql WHERE id=$id";
    if (mysqli_query($conn, $query)) $success = "Data berhasil diupdate!";
    else $error = mysqli_error($conn);
}

// Hapus Logic
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tempat_pkl WHERE id=$id");
    $success = "Data berhasil dihapus!";
}

$tempat_pkl = mysqli_query($conn, "SELECT tp.*, j.nama_jurusan FROM tempat_pkl tp LEFT JOIN jurusan j ON tp.jurusan_id=j.id ORDER BY tp.created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tempat PKL | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --sidebar-bg: #1e293b; --primary-gradient: linear-gradient(135deg, #4f46e5, #3b82f6); }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .sidebar { background: var(--sidebar-bg); min-height: 100vh; position: fixed; color: white; width: 16.666667%; z-index: 100; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; border-radius: 8px; margin: 4px 15px; }
        .sidebar .nav-link.active { background: #334155; color: white; }

        .main-content { margin-left: 16.666667%; padding: 0; width: 83.333333%; }
        .topbar { background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        .form-card { border: none; border-radius: 20px; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .btn-gradient { background: var(--primary-gradient); color: white; border: none; font-weight: 600; border-radius: 10px; }
        .btn-gradient:hover { color: white; opacity: 0.9; }

        .place-card { border: none; border-radius: 18px; overflow: hidden; transition: 0.3s; background: white; height: 100%; }
        .place-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .img-container { position: relative; height: 180px; overflow: hidden; }
        .img-badge { position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); padding: 5px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; color: #4f46e5; }
        
        .kuota-box { background: #f1f5f9; padding: 8px 15px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; color: #475569; }
        
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
                <li class="nav-item"><a class="nav-link active" href="tempat_pkl.php"><i class="bi bi-building-fill me-2"></i> Tempat PKL</a></li>
                <li class="nav-item"><a class="nav-link" href="jurusan.php"><i class="bi bi-mortarboard-fill me-2"></i> Jurusan</a></li>
                <li class="nav-item"><a class="nav-link" href="pengunjung.php"><i class="bi bi-eye-fill me-2"></i> Pengunjung</a></li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="topbar d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">🏢 Manajemen Industri</h5>
                <span class="badge bg-light text-dark p-2 px-3 border rounded-pill small">
                    <i class="bi bi-calendar-event me-2"></i><?= date('d F Y') ?>
                </span>
            </div>

            <div class="p-4">
                <?php if($success): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-4 alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card form-card p-4 sticky-top" style="top: 100px; z-index: 10;">
                            <h5 class="fw-bold mb-4"><?= $edit_data ? '📝 Edit Data Industri' : '➕ Tambah Industri' ?></h5>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Nama Industri</label>
                                    <input type="text" name="nama_tempat" class="form-control bg-light border-0 py-2" placeholder="Contoh: PT. Maju Jaya" required value="<?= $edit_data['nama_tempat'] ?? '' ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Jurusan Prioritas</label>
                                    <select name="jurusan_id" class="form-select bg-light border-0 py-2" required>
                                        <option value="">Pilih Jurusan</option>
                                        <?php 
                                        mysqli_data_seek($jurusan_result, 0);
                                        while($j = mysqli_fetch_assoc($jurusan_result)): 
                                        ?>
                                        <option value="<?= $j['id'] ?>" <?= (isset($edit_data) && $edit_data['jurusan_id']==$j['id'])?'selected':'' ?>>
                                            <?= $j['nama_jurusan'] ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Kuota Tersedia</label>
                                    <input type="number" name="kuota" class="form-control bg-light border-0 py-2" value="<?= $edit_data['kuota'] ?? 0 ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Alamat</label>
                                    <textarea name="alamat" class="form-control bg-light border-0" rows="2" placeholder="Jl. Raya Utama..."><?= $edit_data['alamat'] ?? '' ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Deskripsi Singkat</label>
                                    <textarea name="deskripsi" class="form-control bg-light border-0" rows="3" placeholder="Jelaskan bidang usaha perusahaan..."><?= $edit_data['deskripsi'] ?? '' ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Kontak (No. Telepon / Email)</label>
                                    <input type="text" name="kontak" class="form-control bg-light border-0 py-2" placeholder="Contoh: 08123456789 / hrd@perusahaan.com" value="<?= htmlspecialchars($edit_data['kontak'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Foto Industri</label>
                                    <input type="file" name="foto" class="form-control bg-light border-0">
                                </div>

                                <button type="submit" name="<?= $edit_data?'edit':'tambah' ?>" class="btn btn-gradient w-100 py-3 mt-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i><?= $edit_data ? 'Simpan Perubahan' : 'Daftarkan Tempat' ?>
                                </button>
                                <?php if($edit_data): ?>
                                    <a href="tempat_pkl.php" class="btn btn-light w-100 mt-2 border">Batal</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Mitra Industri Aktif</h5>
                            <span class="text-muted small">Ditemukan <b><?= mysqli_num_rows($tempat_pkl) ?></b> data</span>
                        </div>

                        <div class="row g-3">
                            <?php while($tp = mysqli_fetch_assoc($tempat_pkl)): ?>
                            <div class="col-md-6">
                                <div class="card place-card shadow-sm border-0">
                                    <div class="img-container">
                                        <?php if(!empty($tp['foto'])): ?>
                                            <img src="../<?= $tp['foto'] ?>" class="w-100 h-100 object-fit-cover">
                                        <?php else: ?>
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white opacity-25">
                                                <i class="bi bi-building fs-1"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="img-badge shadow-sm">
                                            <i class="bi bi-tag-fill me-1"></i> <?= $tp['nama_jurusan'] ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-dark mb-2 text-truncate"><?= $tp['nama_tempat'] ?></h6>
                                        <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3em;">
                                            <i class="bi bi-geo-alt text-danger me-1"></i> <?= $tp['alamat'] ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="kuota-box small">
                                                <i class="bi bi-people-fill text-primary"></i>
                                                <span><?= $tp['kuota'] ?> <small>Kuota</small></span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="?action=edit&id=<?= $tp['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="?hapus=<?= $tp['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Hapus mitra ini?')">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div> </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>