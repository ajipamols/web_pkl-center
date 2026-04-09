<?php
/**
 * Dashboard Admin - PKL Center
 * File: admin/dashboard.php
 */
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireAdmin();

// Update activity
$uid = (int)$_SESSION['user_id'];
mysqli_query($conn, "INSERT INTO user_activity (user_id) VALUES ($uid) ON DUPLICATE KEY UPDATE last_activity = NOW()");

// Stats
$five_min_ago  = date('Y-m-d H:i:s', strtotime('-5 minutes'));
$total_siswa   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as t FROM users WHERE role='siswa' AND is_verified=1"))['t'];
$total_jurusan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as t FROM jurusan"))['t'];
$total_tempat  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as t FROM tempat_pkl"))['t'];
$active_users  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM user_activity WHERE last_activity >= '$five_min_ago'"))['t'];
$pesan_baru    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pesan WHERE status='belum'"))['t'];

$sa_msg = "";
if (isset($_SESSION['sweet_success'])) { $sa_msg = $_SESSION['sweet_success']; unset($_SESSION['sweet_success']); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | PKL Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#f0f4f8; }
        .sidebar { background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%); min-height:100vh; }
        .sidebar .nav-link { color:rgba(255,255,255,.7); padding:12px 20px; border-radius:10px; margin:2px 8px; transition:.2s; font-weight:600; font-size:.9rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background:rgba(255,255,255,.12); color:#fff; }
        .sidebar .nav-link .bi { font-size:1.1rem; }
        .topbar { background:#fff; box-shadow:0 1px 10px rgba(0,0,0,.06); padding:14px 24px; }
        .stat-card { border-radius:18px; color:#fff; padding:22px; transition:.3s; }
        .stat-card:hover { transform:translateY(-4px); }
        .sc-blue   { background:linear-gradient(135deg,#0d6efd,#0284c7); }
        .sc-purple { background:linear-gradient(135deg,#7c3aed,#6d28d9); }
        .sc-teal   { background:linear-gradient(135deg,#0891b2,#0e7490); }
        .sc-green  { background:linear-gradient(135deg,#059669,#047857); }
        .sc-red    { background:linear-gradient(135deg,#dc2626,#b91c1c); }
        .stat-badge { display:inline-block; width:12px; height:12px; background:#22c55e; border-radius:50%; animation:pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.2)} }
        .glass-card { background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.06); border:1px solid #e8edf2; }
        .chat-item { border-left:4px solid #e2e8f0; padding:12px 16px; margin-bottom:10px; border-radius:0 12px 12px 0; background:#f8fafc; }
        .chat-item.unread { border-left-color:#0d6efd; background:#eff6ff; }
        .badge-live { background:#22c55e; font-size:.7rem; animation:pulse 1.5s infinite; }
    </style>
</head>
<body>
<div class="container-fluid p-0">
<div class="row g-0">

    <!-- SIDEBAR -->
    <div class="col-md-3 col-lg-2 sidebar d-none d-md-flex flex-column">
        <div class="p-4 text-center border-bottom border-white border-opacity-10">
            <div style="font-size:2rem; color:#fff; margin-bottom:8px;"><i class="bi bi-shield-check"></i></div>
            <h6 class="fw-bold text-white mb-0">ADMIN PKL</h6>
            <small class="text-secondary">SMKN 1 Cimahi</small>
        </div>
        <ul class="nav flex-column mt-2 flex-grow-1">
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="siswa.php"><i class="bi bi-people-fill me-2"></i> Data Siswa</a></li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="switchToChat()">
                    <i class="bi bi-chat-dots-fill me-2"></i> Pesan Siswa
                    <?php if ($pesan_baru > 0): ?><span class="badge bg-danger ms-1" id="sidebarBadge"><?= $pesan_baru ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="tempat_pkl.php"><i class="bi bi-building-fill-check me-2"></i> Tempat PKL</a></li>
            <li class="nav-item"><a class="nav-link" href="jurusan.php"><i class="bi bi-mortarboard-fill me-2"></i> Jurusan</a></li>
            <li class="nav-item"><a class="nav-link" href="pengunjung.php"><i class="bi bi-eye-fill me-2"></i> Pengunjung</a></li>
            <li class="nav-item mt-auto">
                <hr class="mx-3" style="border-color:rgba(255,255,255,.1);">
                <a class="nav-link text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="col-md-9 col-lg-10">
        <!-- TOPBAR -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0">Dashboard</h5>
                <small class="text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge badge-live text-white px-3 py-2 rounded-pill">
                    <span class="stat-badge me-1"></span>
                    <span id="liveCount"><?= $active_users ?></span> Online
                </span>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill d-md-none">Logout</a>
            </div>
        </div>

        <div class="p-4">
            <!-- STAT CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card sc-blue">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><div class="opacity-75 small mb-1">Total Siswa</div><h3 class="fw-bold mb-0" id="stSiswa"><?= $total_siswa ?></h3></div>
                            <i class="bi bi-people-fill opacity-50" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card sc-purple">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><div class="opacity-75 small mb-1">Jurusan</div><h3 class="fw-bold mb-0" id="stJurusan"><?= $total_jurusan ?></h3></div>
                            <i class="bi bi-mortarboard-fill opacity-50" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card sc-teal">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><div class="opacity-75 small mb-1">Mitra PKL</div><h3 class="fw-bold mb-0" id="stTempat"><?= $total_tempat ?></h3></div>
                            <i class="bi bi-building-fill opacity-50" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card sc-green">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><div class="opacity-75 small mb-1">Online Sekarang</div><h3 class="fw-bold mb-0"><span id="stActive"><?= $active_users ?></span></h3></div>
                            <span class="stat-badge" style="width:20px;height:20px;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABS KONTEN -->
            <ul class="nav nav-tabs mb-3" id="adminTab" style="background:#f1f5f9;border-radius:14px;border:none;padding:4px;">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabStats" style="border:none;border-radius:10px;font-weight:600;"><i class="bi bi-bar-chart-fill me-1"></i>Ringkasan</a></li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabPesan" id="tabPesanLink" style="border:none;border-radius:10px;font-weight:600;" onclick="loadPesanAdmin()">
                        <i class="bi bi-chat-dots-fill me-1"></i>Pesan Siswa
                        <?php if ($pesan_baru > 0): ?>
                        <span class="badge bg-danger ms-1" id="tabBadge"><?= $pesan_baru ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Ringkasan -->
                <div class="tab-pane fade show active" id="tabStats">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="glass-card p-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat</h6>
                                <div class="d-grid gap-2">
                                    <a href="jurusan.php" class="btn btn-outline-primary rounded-3 text-start"><i class="bi bi-mortarboard me-2"></i>Kelola Jurusan</a>
                                    <a href="tempat_pkl.php" class="btn btn-outline-teal rounded-3 text-start" style="color:#0891b2;border-color:#0891b2;"><i class="bi bi-building me-2"></i>Kelola Tempat PKL</a>
                                    <a href="siswa.php" class="btn btn-outline-secondary rounded-3 text-start"><i class="bi bi-people me-2"></i>Kelola Data Siswa</a>
                                    <a href="pengunjung.php" class="btn btn-outline-dark rounded-3 text-start"><i class="bi bi-eye me-2"></i>Log Pengunjung</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card p-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-activity text-success me-2"></i>Monitoring Realtime</h6>
                                <div class="mb-3">
                                    <small class="text-muted">User Online (< 5 menit)</small>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <div class="progress flex-grow-1" style="height:10px;border-radius:10px;">
                                            <div class="progress-bar bg-success" id="pgOnline" style="width:<?= min(100, $active_users * 10) ?>%;transition:width .5s;"></div>
                                        </div>
                                        <span class="fw-bold text-success" id="pgCount"><?= $active_users ?></span>
                                    </div>
                                </div>
                                <div class="alert alert-warning border-0 rounded-3 py-2 small mb-0">
                                    <i class="bi bi-clock me-1"></i>Data diperbarui otomatis setiap 10 detik.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pesan Siswa -->
                <div class="tab-pane fade" id="tabPesan">
                    <div class="glass-card p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-inbox-fill text-primary me-2"></i>Pesan Masuk dari Siswa</h6>
                        <div id="pesanAdminList"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
                    </div>
                </div>
            </div>

        </div><!-- end p-4 -->
    </div><!-- end main content -->
</div>
</div>

<!-- MODAL BALAS PESAN -->
<div class="modal fade" id="modalBalas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Balas Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small" id="originalMsg"></p>
                <textarea id="balasanText" class="form-control rounded-3" rows="4" placeholder="Ketik balasan…"></textarea>
                <input type="hidden" id="pesanId">
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary rounded-3" onclick="kirimBalasan()"><i class="bi bi-send-fill me-1"></i>Kirim Balasan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if ($sa_msg): ?>
Swal.fire({ icon:'success', title:'Login Berhasil 🎉', text:'<?= addslashes($sa_msg) ?>', timer:3000, showConfirmButton:false });
<?php endif; ?>

// Realtime stats
function fetchStats() {
    fetch('../api/activity.php')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'ok') return;
            document.getElementById('liveCount').textContent = d.active_users;
            document.getElementById('stSiswa').textContent   = d.total_siswa;
            document.getElementById('stJurusan').textContent = d.total_jurusan;
            document.getElementById('stTempat').textContent  = d.total_tempat;
            document.getElementById('stActive').textContent  = d.active_users;
            document.getElementById('pgCount').textContent   = d.active_users;
            document.getElementById('pgOnline').style.width  = Math.min(100, d.active_users * 10) + '%';
            // Badge pesan
            if (d.pesan_baru > 0) {
                ['sidebarBadge','tabBadge'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) { el.textContent = d.pesan_baru; el.style.display=''; }
                });
            }
        });
}
fetchStats();
setInterval(fetchStats, 10000);
setInterval(() => fetch('../api/activity.php', {method:'POST'}), 60000);

function switchToChat() {
    document.querySelector('[href="#tabPesan"]').click();
    loadPesanAdmin();
}

function loadPesanAdmin() {
    fetch('../api/pesan.php?action=list_admin')
        .then(r => r.json())
        .then(d => {
            const box = document.getElementById('pesanAdminList');
            if (!d.data || !d.data.length) {
                box.innerHTML = '<p class="text-muted text-center py-4"><i class="bi bi-inbox display-5 d-block mb-2"></i>Belum ada pesan masuk.</p>';
                return;
            }
            box.innerHTML = d.data.map(p => `
                <div class="chat-item ${p.status==='belum'?'unread':''} mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="fw-bold text-dark">${escHtml(p.nama)}</span>
                            ${p.status==='belum' ? '<span class="badge bg-danger ms-2 small">Baru</span>' : ''}
                            <p class="mb-1 mt-1">${escHtml(p.isi_pesan)}</p>
                            <small class="text-muted">${p.created_at}</small>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-3 ms-2" onclick="bukaBalas(${p.id},'${escHtml(p.isi_pesan)}')">
                            <i class="bi bi-reply me-1"></i>${p.balasan_admin ? 'Ubah' : 'Balas'}
                        </button>
                    </div>
                    ${p.balasan_admin ? `<div class="mt-2 p-2 rounded-3" style="background:#e7f1ff;"><small class="text-primary fw-bold"><i class="bi bi-shield-check me-1"></i>Balasan Admin:</small><p class="mb-0 small mt-1">${escHtml(p.balasan_admin)}</p></div>` : ''}
                </div>`).join('');
        });
}

function bukaBalas(id, msg) {
    document.getElementById('pesanId').value = id;
    document.getElementById('originalMsg').textContent = 'Pesan: ' + msg;
    document.getElementById('balasanText').value = '';
    new bootstrap.Modal(document.getElementById('modalBalas')).show();
    // Tandai dibaca
    const fd = new FormData(); fd.append('action','baca'); fd.append('pesan_id',id);
    fetch('../api/pesan.php', {method:'POST',body:fd});
}

function kirimBalasan() {
    const id  = document.getElementById('pesanId').value;
    const bal = document.getElementById('balasanText').value.trim();
    if (!bal) { Swal.fire({icon:'warning',title:'Kosong',text:'Ketik balasan terlebih dahulu.'}); return; }
    const fd = new FormData(); fd.append('action','balas'); fd.append('pesan_id',id); fd.append('balasan',bal);
    fetch('../api/pesan.php', {method:'POST',body:fd})
        .then(r => r.json())
        .then(d => {
            bootstrap.Modal.getInstance(document.getElementById('modalBalas')).hide();
            Swal.fire({icon:'success',title:'Terkirim!',text:'Balasan berhasil dikirim.',timer:2000,showConfirmButton:false});
            loadPesanAdmin();
        });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>
