<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireAdmin();

// Ambil data statistik ringkas
$total_hits = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pengunjung"));
$today_hits = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pengunjung WHERE tanggal = CURDATE()"));
$unique_ips = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT ip_address FROM pengunjung"));

$query = mysqli_query($conn, "SELECT * FROM pengunjung ORDER BY id DESC LIMIT 500"); // Limit 500 agar tidak berat
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Pengunjung | Admin PKL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --sidebar-bg: #1e293b; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .sidebar { background: var(--sidebar-bg); min-height: 100vh; position: fixed; color: white; width: 16.666667%; z-index: 100; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; border-radius: 8px; margin: 4px 15px; }
        .sidebar .nav-link.active { background: #334155; color: white; }

        .main-content { margin-left: 16.666667%; padding: 0; width: 83.333333%; }
        .topbar { background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        .stat-card { border: none; border-radius: 15px; background: white; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 4px solid #4f46e5; }
        .table-card { border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        
        .ip-badge { font-family: 'Monaco', 'Consolas', monospace; background: #eef2ff; color: #4338ca; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; }
        
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
                <li class="nav-item"><a class="nav-link" href="jurusan.php"><i class="bi bi-mortarboard-fill me-2"></i> Jurusan</a></li>
                <li class="nav-item"><a class="nav-link active" href="pengunjung.php"><i class="bi bi-eye-fill me-2"></i> Pengunjung</a></li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="topbar d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Traffic Analyst</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <small class="text-muted fw-bold">TOTAL KUNJUNGAN</small>
                            <h3 class="fw-bold mb-0"><?= number_format($total_hits) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="border-left-color: #10b981;">
                            <small class="text-muted fw-bold">HARI INI</small>
                            <h3 class="fw-bold mb-0"><?= number_format($today_hits) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="border-left-color: #f59e0b;">
                            <small class="text-muted fw-bold">UNIQUE VISITOR (IP)</small>
                            <h3 class="fw-bold mb-0"><?= number_format($unique_ips) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="card table-card">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-list-stars me-2 text-primary"></i>Log Aktivitas Terkini</h6>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4">NO</th>
                                    <th>ALAMAT IP</th>
                                    <th>TANGGAL AKSES</th>
                                    <th class="text-end pe-4">WAKTU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($query) > 0): ?>
                                    <?php $no = 1; while($row = mysqli_fetch_assoc($query)): ?>
                                        <tr>
                                            <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                            <td><span class="ip-badge"><?= $row['ip_address'] ?></span></td>
                                            <td class="fw-medium">
                                                <i class="bi bi-calendar3 me-2 text-muted"></i>
                                                <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-light text-dark fw-normal border">
                                                    <i class="bi bi-clock me-1 text-primary"></i> <?= $row['waktu'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Belum ada data traffic yang tercatat.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>