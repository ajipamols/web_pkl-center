<?php
session_start();
require_once '../config/database.php';
require_once '../config/visitor_counter.php';
trackVisitor($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi PKL SMKN 1 Cimahi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .navbar { background: rgba(255,255,255,0.95) !important; backdrop-filter: blur(10px); }
        .page-header {
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            padding: 80px 0 60px; color: white;
            border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        }
        .info-card { border: none; border-radius: 20px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .step-number { width: 48px; height: 48px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; flex-shrink: 0; }
        .icon-box { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .footer-custom { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); color: #fff; padding: 60px 0 20px; }
        .footer-custom h5 { font-weight: 600; margin-bottom: 20px; }
        .footer-custom p, .footer-custom li { color: #dcdcdc; font-size: 14px; line-height: 1.7; }
        .footer-contact { list-style: none; padding-left: 0; }
        .footer-contact li { margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .map-container iframe { width: 100%; height: 180px; border-radius: 12px; border: none; }
        .footer-line { border-top: 1px solid rgba(255,255,255,0.1); margin: 40px 0 20px; }
        .btn-pill { border-radius: 50px; padding: 10px 28px; font-weight: 600; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="bi bi-mortarboard-fill me-2"></i>PKL Center
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold active" href="info.php">Informasi PKL</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-pill" href="<?= $_SESSION['role']==='admin' ? '../admin/dashboard.php' : '../siswa/dashboard.php' ?>">
                            <i class="bi bi-grid-fill me-1"></i>Dashboard
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-outline-primary btn-pill" href="../auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-pill shadow-sm" href="../auth/register.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Styling Header yang Diperbarui */
    .page-header {
        position: relative;
        background: linear-gradient(135deg, #1d4ed8 0%, #7c3aed 50%, #db2777 100%);
        padding: 100px 0 80px;
        color: white;
        border-bottom-left-radius: 60px;
        border-bottom-right-radius: 60px;
        overflow: hidden;
        z-index: 1;
    }

    /* Efek Dekorasi Lingkaran di Background */
    .page-header::before {
        content: "";
        position: absolute;
        top: -50px;
        right: -50px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: -1;
    }

    .page-header::after {
        content: "";
        position: absolute;
        bottom: -20px;
        left: 10%;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        z-index: -1;
    }

    /* Badge Glassmorphism */
    .badge-custom {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff !important;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 8px 20px !important;
    }

    /* Animasi Teks */
    .header-title {
        font-weight: 800 !important;
        letter-spacing: -1px;
        text-shadow: 0 10px 20px rgba(0,0,0,0.1);
        animation: fadeInUp 0.8s ease-out;
    }

    .header-subtitle {
        max-width: 600px;
        margin: 0 auto;
        font-weight: 400;
        line-height: 1.6;
        animation: fadeInUp 1s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="page-header text-center shadow-lg">
    <div class="container">
        <span class="badge badge-custom rounded-pill mb-4">
            <i class="bi bi-book-half"></i> Panduan Lengkap
        </span>
        <h1 class="header-title display-4 mb-3">Informasi Praktik Kerja Lapangan</h1>
        <p class="header-subtitle opacity-90 lead">
            Eksplorasi peluang karirmu dan temukan pengalaman dunia kerja terbaik melalui program PKL unggulan SMKN 1 Cimahi.
        </p>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">

        <div class="col-lg-8">

            <div class="info-card p-4 mb-4">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="icon-box" style="background:#e0e7ff;color:#4338ca;"><i class="bi bi-info-circle-fill"></i></div>
                    <div>
                        <h4 class="fw-bold mb-0">Apa itu PKL?</h4>
                        <small class="text-muted">Praktik Kerja Lapangan</small>
                    </div>
                </div>
                <p class="text-muted mb-0">Praktik Kerja Lapangan (PKL) adalah kegiatan pendidikan, pelatihan, dan pembelajaran yang dilaksanakan di dunia usaha atau dunia industri. PKL merupakan bagian dari program kurikulum SMK yang wajib ditempuh oleh setiap siswa sebagai bentuk penerapan ilmu yang dipelajari di sekolah ke dalam lingkungan kerja nyata.</p>
            </div>

            <div class="info-card p-4 mb-4">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="icon-box" style="background:#dcfce7;color:#15803d;"><i class="bi bi-bullseye"></i></div>
                    <div>
                        <h4 class="fw-bold mb-0">Tujuan PKL</h4>
                        <small class="text-muted">Manfaat yang kamu dapatkan</small>
                    </div>
                </div>
                <div class="row g-3">
                    <?php $goals = [
                        ['icon'=>'bi-briefcase-fill','color'=>'#e0e7ff','c2'=>'#4338ca','text'=>'Memberikan pengalaman kerja nyata kepada siswa'],
                        ['icon'=>'bi-award-fill','color'=>'#fef3c7','c2'=>'#b45309','text'=>'Meningkatkan kompetensi sesuai bidang keahlian'],
                        ['icon'=>'bi-people-fill','color'=>'#fce7f3','c2'=>'#be185d','text'=>'Memahami budaya kerja di dunia industri'],
                        ['icon'=>'bi-graph-up-arrow','color'=>'#dcfce7','c2'=>'#15803d','text'=>'Mempersiapkan siswa memasuki dunia kerja profesional'],
                    ]; foreach($goals as $g): ?>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc;">
                            <div style="width:38px;height:38px;background:<?=$g['color']?>;color:<?=$g['c2']?>;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                                <i class="<?=$g['icon']?>"></i>
                            </div>
                            <small class="fw-semibold"><?=$g['text']?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="info-card p-4 mb-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="icon-box" style="background:#fae8ff;color:#a21caf;"><i class="bi bi-list-check"></i></div>
                    <div>
                        <h4 class="fw-bold mb-0">Syarat Mengikuti PKL</h4>
                        <small class="text-muted">Pastikan kamu memenuhi persyaratan ini</small>
                    </div>
                </div>
                <?php $syarats = ['Siswa aktif kelas XII','Telah menyelesaikan mata pelajaran yang dipersyaratkan','Mendapatkan izin dari orang tua/wali','Memiliki akun terdaftar di sistem PKL Center','Telah mendapatkan pembekalan PKL dari sekolah']; ?>
                <?php foreach($syarats as $i => $s): ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="step-number"><?= $i+1 ?></div>
                    <span class="fw-semibold"><?= $s ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="info-card p-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="icon-box" style="background:#fef3c7;color:#b45309;"><i class="bi bi-arrow-right-circle-fill"></i></div>
                    <div>
                        <h4 class="fw-bold mb-0">Alur Pendaftaran PKL</h4>
                        <small class="text-muted">Ikuti langkah-langkah berikut</small>
                    </div>
                </div>
                <?php $steps = [
                    ['Buat akun siswa di PKL Center', 'Daftar menggunakan email aktif'],
                    ['Login & pilih jurusan', 'Cari tempat PKL sesuai bidangmu'],
                    ['Lihat detail tempat PKL', 'Periksa kuota, alamat, dan kontak'],
                    ['Hubungi bagian Hubin', 'Koordinasi dengan Hubungan Industri sekolah'],
                    ['Mulai PKL', 'Laksanakan PKL sesuai jadwal yang ditetapkan'],
                ]; foreach($steps as $i => $s): ?>
                <div class="d-flex gap-3 mb-3 <?= $i < count($steps)-1 ? 'pb-3 border-bottom' : '' ?>">
                    <div class="step-number"><?= $i+1 ?></div>
                    <div>
                        <div class="fw-bold"><?= $s[0] ?></div>
                        <small class="text-muted"><?= $s[1] ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="info-card p-4 mb-4" style="background: linear-gradient(135deg, #1d4ed8, #7c3aed); color: white;">
                <h5 class="fw-bold mb-3"><i class="bi bi-rocket-takeoff-fill me-2"></i>Mulai Sekarang</h5>
                <p class="opacity-75 small mb-4">Daftarkan dirimu dan akses ratusan informasi tempat PKL mitra sekolah.</p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="../auth/register.php" class="btn btn-light text-primary fw-bold w-100 mb-2 rounded-3">
                        <i class="bi bi-person-plus-fill me-2"></i>Daftar Akun Siswa
                    </a>
                    <a href="../auth/login.php" class="btn btn-outline-light w-100 rounded-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sudah Punya Akun
                    </a>
                <?php else: ?>
                    <a href="<?= $_SESSION['role']==='admin' ? '../admin/dashboard.php' : '../siswa/dashboard.php' ?>" class="btn btn-light text-primary fw-bold w-100 rounded-3">
                        <i class="bi bi-grid-fill me-2"></i>Pergi ke Dashboard
                    </a>
                <?php endif; ?>
            </div>

            <div class="info-card p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-calendar3 me-2 text-primary"></i>Jadwal PKL</h6>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Kelas XII (3 tahun_)</span>
                    <span class="fw-semibold small">3-4 Bulan</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Kelas XII (4 tahun)</span>
                    <span class="fw-semibold small">6-8 Bulan</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted small">Hari Kerja</span>
                    <span class="fw-semibold small">Senin - Jumat / (sesuai aturan perusahaan ) </span>
                </div>
            </div>

            <div class="info-card p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-telephone-fill me-2 text-success"></i>Hubungi Hubin</h6>
                <p class="text-muted small mb-3">Untuk informasi lebih lanjut, hubungi bagian Hubungan Industri SMKN 1 Cimahi:</p>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <small>Jl. Mahar Martanegara No.48, Cimahi</small>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-envelope-fill text-primary"></i>
                    <small>info@smkn1cimahi.sch.id</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-telephone-fill text-success"></i>
                    <small>(022) 6629683</small>
                </div>
            </div>
        </div>

    </div>
</div>

<footer class="footer-custom">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5><i class="bi bi-mortarboard-fill me-2"></i>PKL CENTER</h5>
                <p>Website resmi informasi Praktik Kerja Lapangan (PKL) SMKN 1 Cimahi.</p>
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
