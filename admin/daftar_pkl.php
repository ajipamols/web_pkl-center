<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';

// Cek login
if(!isset($_SESSION['role'])){
    header("Location: ../auth/login.php");
    exit;
}

// Validasi ID
if(!isset($_GET['id'])){
    header("Location: ../siswa/dashboard.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data tempat PKL beserta jurusan
$query = "SELECT t.*, j.nama_jurusan FROM tempat_pkl t LEFT JOIN jurusan j ON t.jurusan_id = j.id WHERE t.id = $id";
$result = mysqli_query($conn, $query);
$tempat = mysqli_fetch_assoc($result);

if(!$tempat){
    die("<div class='container mt-5 alert alert-danger'>Tempat PKL tidak ditemukan.</div>");
}

$kembali = "detail_jurusan.php?id=" . $tempat['jurusan_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tempat PKL - PKL Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        .hero-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .hero-placeholder {
            width: 100%;
            height: 320px;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: #94a3b8;
        }

        .info-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child { border-bottom: none; }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .badge-kuota {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(13, 110, 253, 0.92) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../siswa/dashboard.php">
            <i class="bi bi-rocket-takeoff-fill me-2"></i> PKL CENTER
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-white d-none d-md-block small">Halo, <b><?= htmlspecialchars($_SESSION['nama']) ?></b></span>
            <a href="../auth/logout.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">

    <a href="<?= $kembali ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 mb-4">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>

    <div class="row g-4">
        <!-- Gambar -->
        <div class="col-lg-5">
            <?php if(!empty($tempat['foto'])): ?>
                <img src="../<?= htmlspecialchars($tempat['foto']) ?>" class="hero-img" alt="<?= htmlspecialchars($tempat['nama_tempat']) ?>">
            <?php else: ?>
                <div class="hero-placeholder">
                    <i class="bi bi-building"></i>
                </div>
            <?php endif; ?>

            <!-- Badge Kuota -->
            <div class="text-center mt-4">
                <span class="badge-kuota">
                    <i class="bi bi-people-fill me-2"></i><?= $tempat['kuota'] ?> Slot Tersedia
                </span>
            </div>
        </div>

        <!-- Info -->
        <div class="col-lg-7">
            <div class="card info-card p-4">
                <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($tempat['nama_tempat']) ?></h2>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-4 d-inline-block">
                    <i class="bi bi-mortarboard-fill me-1"></i><?= htmlspecialchars($tempat['nama_jurusan'] ?? '-') ?>
                </span>

                <div class="info-row">
                    <div class="info-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Alamat</div>
                        <div class="fw-semibold"><?= htmlspecialchars($tempat['alamat']) ?></div>
                    </div>
                </div>

                <?php if(!empty($tempat['kontak'])): ?>
                <div class="info-row">
                    <div class="info-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Kontak</div>
                        <div class="fw-semibold"><?= htmlspecialchars($tempat['kontak']) ?></div>
                    </div>
                </div>
                <?php else: ?>
                <div class="info-row">
                    <div class="info-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Kontak</div>
                        <div class="text-muted fst-italic">Belum tersedia</div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!empty($tempat['deskripsi'])): ?>
                <div class="info-row">
                    <div class="info-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Deskripsi</div>
                        <div><?= nl2br(htmlspecialchars($tempat['deskripsi'])) ?></div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Info box -->
            <div class="alert alert-info border-0 rounded-4 mt-3 d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill fs-4 text-info mt-1"></i>
                <div>
                    <strong>Informasi Pendaftaran</strong><br>
                    <small>Untuk mendaftar PKL di tempat ini, hubungi bagian <b>Hubungan Industri (Hubin)</b> di sekolah.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .footer-custom {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: #ffffff;
        padding: 60px 0 20px;
        margin-top: 40px;
    }
    .footer-custom h4 { font-weight: 600; margin-bottom: 20px; color: #fff; }
    .footer-custom p, .footer-custom li { color: #dcdcdc; font-size: 14px; line-height: 1.6; }
    .footer-contact { list-style: none; padding-left: 0; }
    .footer-contact li { margin-bottom: 12px; display: flex; align-items: center; gap: 10px; }
    .map-container iframe { width: 100%; height: 180px; border-radius: 12px; border: none; }
    .footer-line { border-top: 1px solid rgba(255,255,255,0.1); margin: 40px 0 20px; }
</style>

<footer class="footer-custom">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h4>PKL CENTER</h4>
                <p>Website resmi informasi Praktik Kerja Lapangan (PKL) SMKN 1 Cimahi. Memfasilitasi siswa dalam mencari dan mengelola data industri.</p>
            </div>
            <div class="col-md-4">
                <h4>Kontak</h4>
                <ul class="footer-contact">
                    <li><span>📍</span> Jl. Mahar Martanegara No.48, Utama, Cimahi</li>
                    <li><span>✉️</span> info@smkn1cimahi.sch.id</li>
                    <li><span>📞</span> (022) 6629683</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h4>Lokasi Sekolah</h4>
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
