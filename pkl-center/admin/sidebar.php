<?php
/**
 * Sidebar Admin - PKL Center
 * File: admin/sidebar.php
 */
// Hitung pesan baru
$pesan_baru_count = 0;
if (isset($conn)) {
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pesan WHERE status='belum'"));
    $pesan_baru_count = (int)$r['t'];
}
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-3 col-lg-2 p-0 sidebar d-none d-md-block">
    <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-shield-check me-2"></i>ADMIN PKL</h5>
        <small class="text-secondary">SMKN 1 Cimahi</small>
    </div>
    <ul class="nav flex-column mt-2">
        <li class="nav-item">
            <a class="nav-link <?= $current=='dashboard.php'?'active':'' ?>" href="dashboard.php">
                <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current=='siswa.php'?'active':'' ?>" href="siswa.php">
                <i class="bi bi-people-fill me-2"></i> Data Siswa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current=='pesan.php'?'active':'' ?>" href="pesan.php">
                <i class="bi bi-chat-dots-fill me-2"></i> Pesan Siswa
                <?php if ($pesan_baru_count > 0): ?>
                    <span class="badge bg-danger ms-1"><?= $pesan_baru_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($current,['tempat_pkl.php','daftar_pkl.php'])?'active':'' ?>" href="tempat_pkl.php">
                <i class="bi bi-building-fill-check me-2"></i> Tempat PKL
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($current,['jurusan.php','detail_jurusan.php','jurusan_tambah.php'])?'active':'' ?>" href="jurusan.php">
                <i class="bi bi-mortarboard-fill me-2"></i> Jurusan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current=='pengunjung.php'?'active':'' ?>" href="pengunjung.php">
                <i class="bi bi-eye-fill me-2"></i> Pengunjung
            </a>
        </li>
        <li class="nav-item mt-3">
            <hr class="text-secondary mx-3">
            <a class="nav-link" href="../public/index.php" target="_blank">
                <i class="bi bi-box-arrow-up-right me-2"></i> Lihat Website
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="../auth/logout.php">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
