<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';

requireAdmin();

$success = "";
$error = "";

// Hapus siswa
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $delete = mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role='siswa'");
    if ($delete) {
        $success = "Data siswa berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data siswa!";
    }
}

// Ambil data siswa
$siswa = mysqli_query($conn, "SELECT * FROM users WHERE role='siswa' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa | Admin PKL Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --primary-color: #4f46e5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            color: white;
            position: fixed;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 15px;
            transition: 0.3s;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--sidebar-hover);
            color: white;
        }

        /* Content Styling */
        .main-content {
            margin-left: 16.666667%; /* Offset for col-md-2 */
            padding: 0;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Table Styling */
        .card-table {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead {
            background-color: #f8fafc;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            border-top: none;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #334155;
            font-size: 0.9rem;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #475569;
            margin-right: 10px;
        }

        .btn-delete {
            background-color: #fee2e2;
            color: #dc2626;
            border: none;
            transition: 0.3s;
        }

        .btn-delete:hover {
            background-color: #dc2626;
            color: white;
        }

        .badge-date {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .sidebar { position: relative; min-height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 p-0 sidebar d-none d-md-block">
            <div class="p-4 text-center">
                <h5 class="fw-bold mb-0 text-white"><i class="bi bi-shield-check me-2"></i>ADMIN PKL</h5>
                <hr class="text-secondary">
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="siswa.php">
                        <i class="bi bi-people-fill me-2"></i> Data Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tempat_pkl.php">
                        <i class="bi bi-building-fill-check me-2"></i> Tempat PKL
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="jurusan.php">
                        <i class="bi bi-mortarboard-fill me-2"></i> Jurusan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengunjung.php">
                        <i class="bi bi-eye-fill me-2"></i> Pengunjung
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <hr class="text-secondary mx-3">
                    <a class="nav-link" href="../public/index.php" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-2"></i> Lihat Website
                    </a>
                </li>
                    <a class="nav-link text-danger" href="../auth/logout.php">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            
            <div class="topbar d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">Manajemen Siswa</h5>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="p-4">

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show rounded-4">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card card-table shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Daftar Siswa Terdaftar</h6>
                        <span class="badge bg-primary rounded-pill"><?= mysqli_num_rows($siswa) ?> Total</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email Akun</th>
                                    <th>Tanggal Daftar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (mysqli_num_rows($siswa) > 0): ?>
                                <?php $no=1; while($row = mysqli_fetch_assoc($siswa)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle">
                                                    <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                                </div>
                                                <span class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($row['email']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge-date small">
                                                <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($row['created_at'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="?hapus=<?= $row['id'] ?>"
                                               class="btn btn-delete btn-sm rounded-3 p-2 px-3"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini secara permanen?')">
                                                <i class="bi bi-trash3-fill"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/blue/abstract-art-4.svg" style="width: 150px;" class="mb-3 d-block mx-auto">
                                        <p class="text-muted">Belum ada siswa yang mendaftar ke sistem.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-white border-start border-4 border-primary rounded-3 shadow-sm">
                    <div class="d-flex">
                        <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                        <div>
                            <p class="mb-0 small text-secondary">
                                <b>Catatan Sistem:</b> Data siswa ini dikelola secara otomatis melalui modul registrasi publik. 
                                Pastikan untuk memverifikasi keaslian akun sebelum melakukan penghapusan data.
                            </p>
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