<?php
session_start();
require_once 'config/database.php';

/* ===============================
    LOGIKA TRACKING PENGUNJUNG
================================ */
$ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
$tanggal = date('Y-m-d');
$waktu = date('H:i:s');

// Cek apakah IP ini sudah tercatat hari ini
$cek = mysqli_query($conn, "SELECT id FROM pengunjung WHERE ip_address='$ip' AND tanggal='$tanggal'");

if (mysqli_num_rows($cek) == 0) {
    mysqli_query($conn, "INSERT INTO pengunjung (ip_address, tanggal, waktu) VALUES ('$ip', '$tanggal', '$waktu')");
}

/* ===============================
    FUNGSI STATISTIK
================================ */
function getTotalVisitors($conn) {
    $q = mysqli_query($conn, "SELECT COUNT(id) as total FROM pengunjung");
    $data = mysqli_fetch_assoc($q);
    return number_format($data['total']);
}

function getTodayVisitors($conn) {
    $tgl = date('Y-m-d');
    $q = mysqli_query($conn, "SELECT COUNT(id) as total FROM pengunjung WHERE tanggal='$tgl'");
    $data = mysqli_fetch_assoc($q);
    return number_format($data['total']);
}

/* ===============================
    AUTH REDIRECT
================================ */
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'siswa';
    header("Location: " . ($role === 'admin' ? 'admin/dashboard.php' : 'siswa/dashboard.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Center | SMKN 1 Cimahi</title>
    <link rel="icon" type="image/x-icon" href="pkl1.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { --primary-blue: #1e3a8a; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        
        /* Navbar */
        .navbar { background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(10px); }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(30, 58, 138, 0.85), rgba(30, 58, 138, 0.85)), 
                        url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1200');
            background-size: cover;
            background-position: center;
            padding: 140px 0 100px;
            color: white;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
        }

        /* Cards */
        .card-custom {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .card-custom:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }

        .stat-icon {
            width: 60px; height: 60px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 15px; font-size: 1.5rem; margin-bottom: 1rem;
        }

        .img-feature { border-radius: 25px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }

        /* Team Section Custom */
        .team-section { padding: 80px 0; background: #f8fafc; }
        .profile-card {
            position: relative; overflow: hidden; border-radius: 20px;
            border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: all 0.4s ease;
        }
        .profile-card:hover { transform: translateY(-15px); }
        .profile-img-wrapper { position: relative; height: 300px; overflow: hidden; }
        .profile-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .profile-card:hover img { transform: scale(1.1); }
        .profile-info { padding: 25px; text-align: center; background: white; }
        .profile-social {
            position: absolute; top: 20px; right: -50px;
            display: flex; flex-direction: column; gap: 10px; transition: all 0.4s ease;
        }
        .profile-card:hover .profile-social { right: 20px; }
        .social-btn {
            width: 40px; height: 40px; background: white; color: var(--primary-blue);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s;
        }
        .social-btn:hover { background: var(--primary-blue); color: white; }

        /* Styling Section Jurusan */
        .jurusan-card {
            background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 30px 20px; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-align: center; height: 100%; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }
        .jurusan-card:hover {
            transform: translateY(-10px); border-color: #3b82f6;
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.1);
        }
        .jurusan-icon-wrapper {
            width: 70px; height: 70px; background: #f0f7ff; border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; transition: 0.3s;
        }
        .jurusan-card:hover .jurusan-icon-wrapper { background: #3b82f6; }
        .jurusan-icon { font-size: 2rem; color: #3b82f6; }
        .jurusan-card:hover .jurusan-icon { color: #ffffff; }

        @media (max-width: 768px) {
            .jurusan-card { padding: 20px 10px; }
            .jurusan-icon-wrapper { width: 50px; height: 50px; border-radius: 12px; margin-bottom: 12px; }
            .jurusan-icon { font-size: 1.5rem; }
        }

        /* Footer */
        .footer-custom {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #ffffff; padding: 80px 0 30px;
        }
        .footer-contact { list-style: none; padding-left: 0; }
        .footer-contact li { margin-bottom: 12px; display: flex; align-items: center; gap: 12px; opacity: 0.9; }
        .map-container iframe { width: 100%; height: 200px; border-radius: 15px; border: none; }
        .footer-line { border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 40px 0 20px; }
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
            <ul class="navbar-nav ms-auto gap-2 align-items-center">
                <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="btn btn-outline-primary px-4 rounded-pill btn-sm" href="auth/login.php">Login</a></li>
                <li class="nav-item"><a class="btn btn-primary px-4 rounded-pill shadow-sm btn-sm" href="auth/register.php">Daftar</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-info text-dark mb-3 px-3 py-2 rounded-pill fw-bold">PORTAL RESMI PKL</span>
                <h1 class="display-3 fw-bold mb-3">Tingkatkan Skillmu di Dunia Kerja Nyata</h1>
                <p class="lead mb-5 opacity-75">Sistem integrasi Praktik Kerja Lapangan SMKN 1 Cimahi. Menghubungkan talenta muda dengan industri terbaik secara profesional.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="auth/register.php" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold text-primary shadow">
                        Mulai Sekarang <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container" style="margin-top: -60px;">
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-4 text-center">
                <div class="stat-icon mx-auto" style="background: #e0e7ff; color: #4338ca;"><i class="bi bi-people-fill"></i></div>
                <h3 class="fw-bold mb-0"><?= getTotalVisitors($conn); ?></h3>
                <p class="text-muted mb-0 small fw-bold">TOTAL PENGUNJUNG</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-4 text-center">
                <div class="stat-icon mx-auto" style="background: #dcfce7; color: #15803d;"><i class="bi bi-eye-fill"></i></div>
                <h3 class="fw-bold mb-0"><?= getTodayVisitors($conn); ?></h3>
                <p class="text-muted mb-0 small fw-bold">HARI INI</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-4 text-center">
                <div class="stat-icon mx-auto" style="background: #fef3c7; color: #b45309;"><i class="bi bi-building"></i></div>
                <h3 class="fw-bold mb-0">150+</h3>
                <p class="text-muted mb-0 small fw-bold">MITRA INDUSTRI</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-4 text-center">
                <div class="stat-icon mx-auto" style="background: #fae8ff; color: #a21caf;"><i class="bi bi-check-circle-fill"></i></div>
                <h3 class="fw-bold mb-0">1,200</h3>
                <p class="text-muted mb-0 small fw-bold">SISWA AKTIF</p>
            </div>
        </div>
    </div>

    <div class="row align-items-center py-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800" class="img-fluid img-feature" alt="Meeting">
        </div>
        <div class="col-lg-6 ps-lg-5">
            <h2 class="fw-bold mb-4">Kenapa Harus Melalui PKL Center?</h2>
            <div class="d-flex mb-4">
                <div class="me-3 fs-3 text-primary"><i class="bi bi-shield-check-fill"></i></div>
                <div>
                    <h5 class="fw-bold">Terverifikasi & Aman</h5>
                    <p class="text-muted">Semua perusahaan yang terdaftar telah melewati proses seleksi administratif oleh pihak Hubin sekolah.</p>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="me-3 fs-3 text-warning"><i class="bi bi-lightning-charge-fill"></i></div>
                <div>
                    <h5 class="fw-bold">Akses Informasi Terpadu</h5>
                    <p class="text-muted">Cari referensi tempat, lihat profil perusahaan, dan pantau ketersediaan kuota PKL secara akurat dalam satu pintu.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="py-5 bg-white shadow-sm" id="jurusan">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Kompetensi Keahlian</h2>
            <p class="text-muted">9 Jurusan Unggulan SMKN 1 Cimahi</p>
            <div class="mx-auto" style="width: 60px; height: 4px; background: #3b82f6; border-radius: 2px;"></div>
        </div>

        <div class="row g-4">
            <?php 
            $jurusans = [
                ['SIJA', 'bi-cloud-check', 'Sistem Informatika, Jaringan & Aplikasi'],
                ['RPL', 'bi-code-slash', 'Rekayasa Perangkat Lunak'],
                ['IOP', 'bi-minecart-loaded', 'Instrumentasi Otomatisasi Proses'],
                ['TEI', 'bi-cpu', 'Teknik Elektronika Industri'],
                ['TOI', 'bi-gear-wide-connected', 'Teknik Otomasi Industri'],
                ['MEKA', 'bi-robot', 'Teknik Mekatronika'],
                ['TEDK', 'bi-broadcast-pin', 'Teknik Elektronika Daya & Komunikasi'],
                ['TPTU', 'bi-snow', 'Teknik Pendingin & Tata Udara'],
                ['PFPT', 'bi-camera-reels', 'Produksi Film & Program Televisi'],
            ];
            foreach($jurusans as $index => $j): ?>
            <div class="col-6 col-md-4">
                <div class="jurusan-card">
                    <div class="jurusan-icon-wrapper">
                        <i class="bi <?= $j[1] ?> jurusan-icon"></i>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?= $j[0] ?></h5>
                    <p class="small text-muted mb-0 d-none d-md-block"><?= $j[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
                        <img src="romi.png" alt="Romeero Nayotama Ramadhan">
                        <div class="profile-social">
                            <a href="https://www.github.com/romeerow" class="social-btn"><i class="bi bi-github"></i></a>
                            <a href="https://www.instagram.com/romeerow/" class="social-btn"><i class="bi bi-instagram"></i></a>
                            <a href="https://www.linkedin.com/in/romeerow" class="social-btn"><i class="bi bi-linkedin"></i></a>
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
                        <img src="aji.jpeg" alt="Muhammad Aji Pradana">
                        <div class="profile-social">
                            <a href="https://www.github.com/romeerow" class="social-btn"><i class="bi bi-github"></i></a>
                            <a href="https://www.instagram.com/romeerow/" class="social-btn"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h4 class="fw-bold mb-1">Muhammad Aji Pradana</h4>
                        <p class="text-primary fw-semibold mb-0">UI/UX Designer</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card profile-card">
                    <div class="profile-img-wrapper">
                        <img src="gisel.jpeg" alt="Developer">
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
        <div class="row gy-5">
            <div class="col-md-4">
                <h4 class="fw-bold mb-4">PKL CENTER</h4>
                <p class="opacity-75">
                    Website resmi informasi Praktik Kerja Lapangan (PKL) SMKN 1 Cimahi. 
                    Membantu siswa mempersiapkan masa depan di dunia industri.
                </p>
            </div>
            <div class="col-md-4">
                <h4 class="fw-bold mb-4">Kontak Kami</h4>
                <ul class="footer-contact">
                    <li><i class="bi bi-geo-alt-fill text-info"></i> Jl. Mahar Martanegara No.48, Cimahi</li>
                    <li><i class="bi bi-envelope-fill text-info"></i> info@smkn1cimahi.sch.id</li>
                    <li><i class="bi bi-telephone-fill text-info"></i> (022) 6629683</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h4 class="fw-bold mb-4">Lokasi</h4>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.037146522964!2d107.55434137510798!3d-6.886105367383679!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e44047706d3d%3A0x633d7405f63d9a!2sSMK%20Negeri%201%20Cimahi!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
        <hr class="footer-line">
        <div class="text-center">
            <small class="opacity-50">© 2026 SMKN 1 Cimahi | PKL Center Team - KELOMPOK PKL CENTER ❤️</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>