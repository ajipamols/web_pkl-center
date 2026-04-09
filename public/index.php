<?php
session_start();

require_once '../config/database.php';
require_once '../config/visitor_counter.php';

trackVisitor($conn);

// Hanya siswa yang di-redirect otomatis, admin boleh lihat halaman publik
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'siswa';
    if ($role === 'siswa') {
        header("Location: ../siswa/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Center SMKN 1 Cimahi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-blue: #1d4ed8;
            --secondary-purple: #7c3aed;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        
        /* Navbar & Hero */
        .navbar { background: rgba(255,255,255,0.95) !important; backdrop-filter: blur(10px); }
        .hero {
            background: linear-gradient(rgba(30,58,138,0.85), rgba(30,58,138,0.85)),
                        url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1400');
            background-size: cover; background-position: center;
            padding: 130px 0 100px; color: white;
            border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        }

        /* Cards & Components */
        .stat-card { border: none; border-radius: 20px; transition: all 0.3s ease; background: white; }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .stat-icon { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 1.4rem; margin-bottom: 1rem; }
        
        .feature-card { border: none; border-radius: 24px; padding: 2rem; transition: all 0.3s ease; background: white; }
        .feature-card:hover { background: var(--primary-blue); color: white; }
        .feature-card:hover .feature-icon { background: rgba(255,255,255,0.2); color: white; }
        .feature-card:hover p { color: rgba(255,255,255,0.8) !important; }
        .feature-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1.2rem; transition: 0.3s; }
        
        .img-feature { border-radius: 24px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
        .btn-pill { border-radius: 50px; padding: 10px 28px; font-weight: 600; transition: all 0.3s; }
        .btn-pill:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .admin-bar { background: linear-gradient(90deg, var(--primary-blue), var(--secondary-purple)); color: white; padding: 8px 0; font-size: 14px; }

        /* Team Section Styles */
        .team-section { padding: 80px 0; background: #fff; }
        .profile-card {
            border: none;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
        }
        .profile-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .profile-img-wrapper {
            position: relative;
            padding-top: 20px;
        }
        .profile-card img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #f8fafc;
            transition: 0.3s;
        }
        .profile-social {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .social-btn {
            width: 35px;
            height: 35px;
            background: #f1f5f9;
            color: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: 0.3s;
        }
        .social-btn:hover {
            background: var(--primary-blue);
            color: white;
        }
        .profile-info { padding: 25px; }

        /* Footer */
        .footer-custom { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); color: #fff; padding: 60px 0 20px; }
        .footer-custom h5 { font-weight: 600; margin-bottom: 20px; }
        .footer-custom p, .footer-custom li { color: #dcdcdc; font-size: 14px; line-height: 1.7; }
        .footer-contact { list-style: none; padding-left: 0; }
        .footer-contact li { margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .map-container iframe { width: 100%; height: 180px; border-radius: 12px; border: none; }
        .footer-line { border-top: 1px solid rgba(255,255,255,0.1); margin: 40px 0 20px; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
<div class="admin-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-fill-check me-2"></i>Anda login sebagai <strong>Admin</strong> — sedang melihat halaman publik</span>
        <a href="../admin/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3">
            <i class="bi bi-speedometer2 me-1"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?php endif; ?>

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
                <li class="nav-item"><a class="nav-link fw-semibold" href="info.php">Informasi PKL</a></li>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-pill" href="../admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
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

<section class="hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge bg-info text-dark mb-3 px-3 py-2 rounded-pill fw-bold fs-6">
                    <i class="bi bi-patch-check-fill me-1"></i> Portal Resmi PKL SMKN 1 Cimahi
                </span>
                <h1 class="display-3 fw-bold mb-3">Tingkatkan Skillmu<br>di Dunia Kerja Nyata</h1>
                <p class="lead mb-5 opacity-75 fs-5">
                    Temukan mitra industri terbaik untuk Praktik Kerja Lapanganmu.<br>
                    Mudah, terverifikasi, dan terpercaya.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="../auth/register.php" class="btn btn-light btn-lg btn-pill fw-bold text-primary shadow">
                            <i class="bi bi-person-plus-fill me-2"></i>Daftar Sekarang
                        </a>
                    <?php endif; ?>
                    <a href="info.php" class="btn btn-outline-light btn-lg btn-pill">
                        <i class="bi bi-info-circle me-2"></i>Pelajari PKL
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container" style="margin-top: -50px; position: relative; z-index: 10;">
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="card stat-card p-4 text-center shadow">
                <div class="stat-icon mx-auto" style="background:#e0e7ff;color:#4338ca;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= getTotalVisitors($conn) ?></h3>
                <p class="text-muted mb-0 small fw-semibold">TOTAL PENGUNJUNG</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-4 text-center shadow">
                <div class="stat-icon mx-auto" style="background:#dcfce7;color:#15803d;">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= getTodayVisitors($conn) ?></h3>
                <p class="text-muted mb-0 small fw-semibold">PENGUNJUNG HARI INI</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-4 text-center shadow">
                <div class="stat-icon mx-auto" style="background:#fef3c7;color:#b45309;">
                    <i class="bi bi-building"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= getTotalTempat($conn) ?></h3>
                <p class="text-muted mb-0 small fw-semibold">MITRA INDUSTRI</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-4 text-center shadow">
                <div class="stat-icon mx-auto" style="background:#fae8ff;color:#a21caf;">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= getTotalJurusan($conn) ?></h3>
                <p class="text-muted mb-0 small fw-semibold">PROGRAM KEAHLIAN</p>
            </div>
        </div>
    </div>

    <div class="row align-items-center py-4 mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800"
                 class="img-fluid img-feature" alt="Meeting">
        </div>
        <div class="col-lg-6 ps-lg-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold rounded-pill px-3 py-2 mb-3">Kenapa Kami?</span>
            <h2 class="fw-bold mb-4">Kenapa Harus Melalui<br>PKL Center?</h2>
            <div class="d-flex mb-4">
                <div class="me-3 fs-3 text-primary flex-shrink-0"><i class="bi bi-shield-check-fill"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Terverifikasi & Aman</h5>
                    <p class="text-muted mb-0">Semua perusahaan telah melewati proses seleksi ketat oleh pihak sekolah.</p>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="me-3 fs-3 text-success flex-shrink-0"><i class="bi bi-lightning-charge-fill"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Proses Mudah & Cepat</h5>
                    <p class="text-muted mb-0">Daftar, pilih tempat, dan pantau informasi PKL dalam satu platform.</p>
                </div>
            </div>
            <div class="d-flex">
                <div class="me-3 fs-3 text-warning flex-shrink-0"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Tingkatkan Kompetensi</h5>
                    <p class="text-muted mb-0">PKL di industri terpilih untuk pengalaman kerja yang relevan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <h2 class="fw-bold">Tentang Program PKL</h2>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="feature-card h-100 shadow-sm text-center">
                <div class="feature-icon mx-auto" style="background:#e0e7ff;color:#4338ca;">
                    <i class="bi bi-book-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Apa itu PKL?</h5>
                <p class="text-muted">Implementasi nyata ilmu sekolah di Dunia Usaha & Dunia Industri untuk membentuk mental profesional.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card h-100 shadow-sm text-center">
                <div class="feature-icon mx-auto" style="background:#dcfce7;color:#15803d;">
                    <i class="bi bi-buildings-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Mitra Industri</h5>
                <p class="text-muted">Bekerjasama dengan perusahaan teknologi, manufaktur, dan kreatif di seluruh Indonesia.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card h-100 shadow-sm text-center">
                <div class="feature-icon mx-auto" style="background:#fef3c7;color:#b45309;">
                    <i class="bi bi-send-check-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Pendaftaran Mudah</h5>
                <p class="text-muted">Pilih tempat pkl lalu hubungi hubin sekolah.</p>
            </div>
        </div>
    </div>
</div>

<section class="team-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Our Creative Team</h2>
            <div class="mx-auto" style="width: 80px; height: 4px; background: var(--primary-blue); border-radius: 2px;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card profile-card">
                    <div class="profile-img-wrapper">
                        <img src="romi.png" alt="Romeero Nayotama">
                        <div class="profile-social">
                            <a href="#" class="social-btn"><i class="bi bi-github"></i></a>
                            <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h4 class="fw-bold mb-1">Romeero Nayotama</h4>
                        <p class="text-primary fw-semibold mb-0">Frontend Engineer</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card profile-card">
                    <div class="profile-img-wrapper">
                        <img src="aji.jpeg" alt="UI Designer">
                        <div class="profile-social">
                            <a href="#" class="social-btn"><i class="bi bi-github"></i></a>
                            <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h4 class="fw-bold mb-1">Muhammad AjiPradana</h4>
                        <p class="text-primary fw-semibold mb-0">UI/UX Designer</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card profile-card">
                    <div class="profile-img-wrapper">
                        <img src="gisel.jpeg" alt="Backend">
                        <div class="profile-social">
                            <a href="#" class="social-btn"><i class="bi bi-github"></i></a>
                            <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h4 class="fw-bold mb-1">Charoline Gisell</h4>
                        <p class="text-primary fw-semibold mb-0">Backend Engineer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-custom">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5><i class="bi bi-mortarboard-fill me-2"></i>PKL CENTER</h5>
                <p>Website resmi informasi Praktik Kerja Lapangan (PKL) SMKN 1 Cimahi. Memfasilitasi siswa dalam mencari dan mengelola data industri.</p>
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
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.037466540608!2d107.54636307475677!3d-6.886071493112882!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e4420bc24707%3A0x95971a80436d6288!2sSMK%20Negeri%201%20Cimahi!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
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