<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';

if(!isset($_SESSION['role'])){ header("Location: ../auth/login.php"); exit; }

$kembali = ($_SESSION['role'] == 'admin') ? "dashboard.php" : "../siswa/dashboard.php";

if(!isset($_GET['id'])){ header("Location: $kembali"); exit; }

$id = intval($_GET['id']);
$jurusan = mysqli_query($conn, "SELECT * FROM jurusan WHERE id = $id");
$dataJurusan = mysqli_fetch_assoc($jurusan);
if(!$dataJurusan){ die("<div class='container mt-5 alert alert-danger'>Jurusan tidak ditemukan.</div>"); }

$tempat = mysqli_query($conn, "SELECT * FROM tempat_pkl WHERE jurusan_id = $id ORDER BY nama_tempat ASC");
$isAdmin = $_SESSION['role'] == 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Jurusan - PKL Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .navbar-top { background: linear-gradient(135deg, #1d4ed8, #7c3aed); }
        .header-box { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; border-radius: 20px; padding: 3rem 2rem; box-shadow: 0 10px 25px rgba(30,58,138,0.2); margin-bottom: 2.5rem; }
        .card-pkl { border: none; border-radius: 16px; transition: all 0.3s ease; overflow: hidden; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.06); }
        .card-pkl:hover { transform: translateY(-8px); box-shadow: 0 20px 30px rgba(0,0,0,0.1); }
        .img-wrapper { width: 100%; height: 180px; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center; }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .card-pkl:hover .img-wrapper img { transform: scale(1.08); }
        .badge-kuota { background: #dcfce7; color: #166534; font-weight: 600; border-radius: 8px; padding: 6px 12px; font-size: 0.8rem; }
        .footer-custom { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); color: #fff; padding: 60px 0 20px; margin-top: 40px; }
        .footer-custom h5 { font-weight: 600; margin-bottom: 20px; }
        .footer-custom p, .footer-custom li { color: #dcdcdc; font-size: 14px; line-height: 1.7; }
        .footer-contact { list-style: none; padding-left: 0; }
        .footer-contact li { margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .map-container iframe { width: 100%; height: 180px; border-radius: 12px; border: none; }
        .footer-line { border-top: 1px solid rgba(255,255,255,0.1); margin: 40px 0 20px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $kembali ?>">
            <span class="me-2"></span>PKL CENTER
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-white d-none d-md-block small">Halo, <b><?= htmlspecialchars($_SESSION['nama']) ?></b></span>
            <?php if($isAdmin): ?>
                <a href="dashboard.php" class="btn btn-light btn-sm text-primary fw-bold">
                    <i class="bi bi-grid-fill me-1"></i>Admin Panel
                </a>
            <?php endif; ?>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <a href="<?= $kembali ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 mb-4">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>

    <div class="header-box text-center">
        <?php if(!empty($dataJurusan['logo'])): ?>
            <div class="bg-white d-inline-block p-3 rounded-circle mb-3 shadow-sm">
                <img src="../<?= $dataJurusan['logo'] ?>" style="height:70px;width:70px;object-fit:contain;">
            </div>
        <?php endif; ?>
        <h2 class="fw-bold"><?= htmlspecialchars($dataJurusan['nama_jurusan']) ?></h2>
        <p class="opacity-80 mx-auto mb-0" style="max-width:700px;"><?= htmlspecialchars($dataJurusan['deskripsi'] ?? '') ?></p>
        <?php if($isAdmin): ?>
            <div class="mt-4">
                <a href="jurusan_tambah.php?id=<?= $dataJurusan['id'] ?>" class="btn btn-warning px-4 fw-bold rounded-pill">
                    <i class="bi bi-pencil-square me-2"></i>Edit Jurusan
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary p-2 rounded-3 me-3 text-white"><i class="bi bi-building fs-5"></i></div>
            <h5 class="fw-bold mb-0">Mitra Industri Terdaftar</h5>
        </div>
        <?php if($isAdmin): ?>
            <a href="tempat_pkl.php" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="bi bi-plus-lg me-1"></i>Tambah Mitra
            </a>
        <?php endif; ?>
    </div>

    <?php if(mysqli_num_rows($tempat) > 0): ?>
        <div class="row g-4">
            <?php while($t = mysqli_fetch_assoc($tempat)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-pkl h-100">
                        <div class="img-wrapper">
                            <?php if(!empty($t['foto'])): ?>
                                <img src="../<?= $t['foto'] ?>" alt="<?= htmlspecialchars($t['nama_tempat']) ?>">
                            <?php else: ?>
                                <i class="bi bi-building text-secondary" style="font-size:3rem;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($t['nama_tempat']) ?></h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($t['alamat']) ?>
                            </p>
                            <?php if(!empty($t['kontak'])): ?>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-telephone-fill text-success me-1"></i><?= htmlspecialchars($t['kontak']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if(!empty($t['deskripsi'])): ?>
                                <p class="text-muted small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    <?= htmlspecialchars($t['deskripsi']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="badge-kuota">
                                    <i class="bi bi-people-fill me-1"></i><?= $t['kuota'] ?> Slot
                                </span>
                                <?php if($isAdmin): ?>
                                    <div class="btn-group">
                                        <a href="tempat_pkl.php?action=edit&id=<?= $t['id'] ?>" class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="tempat_pkl.php?hapus=<?= $t['id'] ?>" class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Hapus tempat ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <a href="daftar_pkl.php?id=<?= $t['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bi bi-building-exclamation display-1 text-muted"></i>
            <p class="mt-3 text-secondary">Belum ada tempat PKL untuk jurusan ini.</p>
            <?php if($isAdmin): ?>
                <a href="tempat_pkl.php" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Tempat PKL
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="footer-custom">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5><i class="bi bi-mortarboard-fill me-2"></i>PKL CENTER</h5>
                <p>Website resmi informasi PKL SMKN 1 Cimahi.</p>
            </div>
            <div class="col-md-4">
                <h5>Kontak</h5>
                <ul class="footer-contact">
                    <li><span>📍</span> Jl. Mahar Martanegara No.48, Utama, Cimahi</li>
                    <li><span>✉️</span> info@smkn1cimahi.sch.id</li>
                    <li><span>📞</span> (022) 6629683</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Lokasi Sekolah</h5>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.03378563539!2d107.55404287587508!3d-6.886542767389148!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e43890f8450d%3A0x6b4474324f469911!2sSMKN%201%20Cimahi!5e0!3m2!1sid!2sid!4v1700000000000" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        <hr class="footer-line">
        <p class="text-center mb-0 opacity-75"><small>© 2026 SMKN 1 Cimahi | PKL Center - All Rights Reserved</small></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
