<?php
/**
 * Dashboard Siswa - PKL Center
 * File: siswa/dashboard.php
 */
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireSiswa();

// Update activity
$uid = (int)$_SESSION['user_id'];
mysqli_query($conn, "INSERT INTO user_activity (user_id) VALUES ($uid) ON DUPLICATE KEY UPDATE last_activity = NOW()");

$jurusan = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
$total_jurusan = mysqli_num_rows($jurusan);
$total_tempat  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as t FROM tempat_pkl"))['t'];
$total_siswa   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as t FROM users WHERE role='siswa' AND is_verified=1"))['t'];

// Sweet alert dari session
$sa_msg = "";
if (isset($_SESSION['sweet_success'])) {
    $sa_msg = $_SESSION['sweet_success'];
    unset($_SESSION['sweet_success']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa | PKL Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#f0f4f8; }
        .navbar { background:linear-gradient(135deg,#0d6efd,#0284c7) !important; }
        .hero-section { background:linear-gradient(135deg,rgba(13,110,253,.88),rgba(2,132,199,.88)), url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop') center/cover; height:260px; display:flex; align-items:center; justify-content:center; color:#fff; text-align:center; border-radius:0 0 40px 40px; }
        .glass-card { background:#fff; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,.06); border:1px solid #e8edf2; }
        .stat-card { border-radius:16px; padding:20px; color:#fff; }
        .stat-blue { background:linear-gradient(135deg,#0d6efd,#0284c7); }
        .stat-teal { background:linear-gradient(135deg,#0891b2,#0e7490); }
        .stat-purple { background:linear-gradient(135deg,#7c3aed,#6d28d9); }
        .stat-green { background:linear-gradient(135deg,#059669,#047857); }
        .stat-badge { display:inline-block; width:14px; height:14px; background:#22c55e; border-radius:50%; animation:pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.2)} }
        .card-jurusan { border:none; border-radius:20px; transition:all .3s; overflow:hidden; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.06); }
        .card-jurusan:hover { transform:translateY(-8px); box-shadow:0 15px 35px rgba(0,0,0,.12); }
        .icon-box { width:68px; height:68px; background:#e7f1ff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; transition:.3s; }
        .card-jurusan:hover .icon-box { background:#0d6efd; }
        .card-jurusan:hover .icon-box img { filter:brightness(0) invert(1); }
        /* TAB */
        .nav-tabs { border:none; background:#f1f5f9; border-radius:14px; padding:4px; }
        .nav-tabs .nav-link { border:none; border-radius:10px; color:#64748b; font-weight:600; padding:10px 22px; }
        .nav-tabs .nav-link.active { background:#0d6efd; color:#fff; box-shadow:0 4px 12px rgba(13,110,253,.3); }
        /* CHAT */
        .chat-bubble { background:#f1f5f9; border-radius:0 14px 14px 14px; padding:12px 16px; max-width:85%; }
        .chat-bubble.admin { background:#e7f1ff; border-radius:14px 0 14px 14px; margin-left:auto; }
        .chat-timestamp { font-size:.75rem; color:#94a3b8; }
        .chat-box { max-height:380px; overflow-y:auto; padding:8px 0; }
        .footer-custom { background:linear-gradient(135deg,#0f2027,#203a43,#2c5364); color:#fff; padding:50px 0 16px; margin-top:40px; }
        .footer-custom p, .footer-custom li { color:#dcdcdc; font-size:14px; }
        .footer-contact { list-style:none; padding-left:0; }
        .footer-contact li { margin-bottom:8px; display:flex; align-items:center; gap:8px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-mortarboard-fill me-2"></i>PKL CENTER</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <span class="nav-link text-white small">
                        <span class="stat-badge me-1"></span>
                        <span id="activeCount">–</span> aktif
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-white">Halo, <strong><?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]) ?></strong>!</span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger btn-sm rounded-pill px-3" href="../auth/logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-section">
    <div>
        <h2 class="fw-bold mb-2">Wujudkan Karir Impianmu 🎯</h2>
        <p class="opacity-80 mb-0">Temukan tempat PKL terbaik sesuai jurusanmu</p>
    </div>
</div>

<div class="container py-4">

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4" style="margin-top:-30px;">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue text-center">
                <h3 class="fw-bold mb-0" id="statSiswa"><?= $total_siswa ?></h3>
                <small>Total Siswa</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-teal text-center">
                <h3 class="fw-bold mb-0" id="statJurusan"><?= $total_jurusan ?></h3>
                <small>Jurusan</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-purple text-center">
                <h3 class="fw-bold mb-0" id="statTempat"><?= $total_tempat ?></h3>
                <small>Mitra PKL</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green text-center">
                <h3 class="fw-bold mb-0"><span class="stat-badge"></span> <span id="statActive">–</span></h3>
                <small>Online Sekarang</small>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs mb-4" id="mainTab">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tabJurusan">
                <i class="bi bi-grid-fill me-1"></i> Jurusan & PKL
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabChat" id="tabChatLink">
                <i class="bi bi-chat-dots me-1"></i> Pesan Admin
                <span class="badge bg-danger ms-1" id="badgePesan" style="display:none"></span>
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- TAB 1: JURUSAN -->
        <div class="tab-pane fade show active" id="tabJurusan">
            <div class="row g-4 mb-5">
                <?php
                mysqli_data_seek($jurusan, 0);
                while ($j = mysqli_fetch_assoc($jurusan)):
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 card-jurusan" style="cursor:pointer"
                         onclick="showJurusanDetail(<?= $j['id'] ?>, '<?= addslashes($j['nama_jurusan']) ?>', '<?= addslashes($j['deskripsi'] ?? '') ?>', '<?= addslashes($j['syarat_pkl'] ?? '') ?>')">
                        <div class="card-body p-4 text-center">
                            <div class="icon-box">
                                <?php if (!empty($j['logo'])): ?>
                                    <img src="../<?= htmlspecialchars($j['logo']) ?>" style="width:44px;height:44px;object-fit:contain;">
                                <?php else: ?>
                                    <i class="bi bi-mortarboard-fill text-primary" style="font-size:1.8rem;"></i>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold mb-2"><?= htmlspecialchars($j['nama_jurusan']) ?></h6>
                            <p class="text-muted small mb-3" style="min-height:40px;"><?= htmlspecialchars(substr($j['deskripsi'] ?? 'Lihat daftar mitra industri.', 0, 80)) ?>…</p>
                            <span class="btn btn-outline-primary btn-sm rounded-pill fw-semibold w-100">
                                Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- TAB 2: CHAT -->
        <div class="tab-pane fade" id="tabChat">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-chat-left-dots text-primary me-2"></i>Pesan ke Admin</h5>
                <p class="text-muted small mb-4">Kirim pertanyaan, laporan, atau informasi tempat PKL baru kepada admin.</p>

                <div class="chat-box mb-3" id="chatBox">
                    <div class="text-center text-muted small py-4"><i class="bi bi-chat-dots display-6 d-block mb-2"></i>Memuat pesan...</div>
                </div>

                <div class="border-top pt-3">
                    <div class="input-group">
                        <input type="text" id="pesanInput" class="form-control rounded-start-3" placeholder="Ketik pesan Anda…" maxlength="500">
                        <button class="btn btn-primary px-4" onclick="kirimPesan()">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                    <small class="text-muted">Tekan Enter atau klik kirim</small>
                </div>
            </div>
        </div>

    </div><!-- end tab-content -->
</div>

<!-- MODAL DETAIL JURUSAN -->
<div class="modal fade" id="modalJurusan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalJurusanTitle">–</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="jurusanTab">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#jtDeskripsi">Deskripsi</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#jtSyarat">Syarat PKL</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#jtTempat">Mitra PKL</a></li>
                </ul>
                <div class="tab-content px-1">
                    <div class="tab-pane fade show active" id="jtDeskripsi">
                        <p id="modalDeskripsi" class="text-secondary"></p>
                    </div>
                    <div class="tab-pane fade" id="jtSyarat">
                        <div class="alert alert-info border-0 rounded-3">
                            <i class="bi bi-clipboard-check me-2"></i><strong>Syarat PKL:</strong>
                        </div>
                        <p id="modalSyarat" class="text-secondary"></p>
                    </div>
                    <div class="tab-pane fade" id="jtTempat">
                        <div id="modalTempatList"><div class="text-center py-3"><div class="spinner-border text-primary"></div></div></div>
                    </div>
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
                <p>Website resmi informasi PKL SMKN 1 Cimahi.</p>
            </div>
            <div class="col-md-4">
                <h5>Kontak</h5>
                <ul class="footer-contact">
                    <li><span>📍</span> Jl. Mahar Martanegara No.48, Cimahi</li>
                    <li><span>✉️</span> info@smkn1cimahi.sch.id</li>
                    <li><span>📞</span> (022) 6629683</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Lokasi Sekolah</h5>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.03378563539!2d107.55404287587508!3d-6.886542767389148!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e43890f8450d%3A0x6b4474324f469911!2sSMKN%201%20Cimahi!5e0!3m2!1sid!2sid!4v1700000000000" style="width:100%;height:160px;border-radius:12px;border:none;" loading="lazy"></iframe>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.1);margin:30px 0 14px;">
        <p class="text-center mb-0 opacity-75"><small>© 2026 SMKN 1 Cimahi | PKL Center</small></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── SweetAlert selamat datang ──
<?php if ($sa_msg): ?>
Swal.fire({ icon:'success', title:'Login Berhasil 🎉', text:'<?= addslashes($sa_msg) ?>', timer:3000, showConfirmButton:false });
<?php endif; ?>

// ── Realtime activity update ──
function pingActivity() {
    fetch('../api/activity.php', { method:'POST' });
}
function fetchStats() {
    fetch('../api/activity.php')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'ok') return;
            document.getElementById('activeCount').textContent = d.active_users;
            document.getElementById('statSiswa').textContent   = d.total_siswa;
            document.getElementById('statJurusan').textContent = d.total_jurusan;
            document.getElementById('statTempat').textContent  = d.total_tempat;
            document.getElementById('statActive').textContent  = d.active_users;
            // Badge pesan balasan belum dibaca (pakai total_pesan kosong sbg proxy)
        });
}
pingActivity();
fetchStats();
setInterval(fetchStats, 10000);
setInterval(pingActivity, 60000);

// ── Modal detail jurusan ──
function showJurusanDetail(id, nama, deskripsi, syarat) {
    document.getElementById('modalJurusanTitle').textContent = nama;
    document.getElementById('modalDeskripsi').textContent    = deskripsi || 'Deskripsi belum tersedia.';
    document.getElementById('modalSyarat').textContent       = syarat   || 'Syarat PKL belum tersedia.';
    // Reset to deskripsi tab
    document.querySelector('#jurusanTab .nav-link.active')?.classList.remove('active','show');
    document.querySelector('#jtDeskripsi').classList.remove('show','active');
    document.querySelector('#jurusanTab .nav-link:first-child').classList.add('active');
    document.querySelector('#jtDeskripsi').classList.add('show','active');
    // Load tempat PKL
    const tempatDiv = document.getElementById('modalTempatList');
    tempatDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
    fetch(`../admin/get_tempat_jurusan.php?jurusan_id=${id}`)
        .then(r => r.json())
        .then(d => {
            if (!d.length) { tempatDiv.innerHTML = '<p class="text-muted text-center py-3">Belum ada mitra PKL untuk jurusan ini.</p>'; return; }
            tempatDiv.innerHTML = d.map(t => `
                <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded-3" style="background:#f8fafc;">
                    <img src="../${t.foto||'assets/img/no-image.png'}" style="width:56px;height:56px;object-fit:cover;border-radius:10px;">
                    <div>
                        <h6 class="fw-bold mb-0">${t.nama_tempat}</h6>
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>${t.alamat}</small><br>
                        <small class="text-success"><i class="bi bi-people me-1"></i>Kuota: ${t.kuota} siswa</small>
                        ${t.kontak ? `<br><small class="text-info"><i class="bi bi-telephone me-1"></i>${t.kontak}</small>` : ''}
                    </div>
                </div>`).join('');
        });
    new bootstrap.Modal(document.getElementById('modalJurusan')).show();
}

// ── Chat ──
function loadPesan() {
    fetch('../api/pesan.php?action=list_siswa')
        .then(r => r.json())
        .then(d => {
            const box = document.getElementById('chatBox');
            if (!d.data || !d.data.length) {
                box.innerHTML = '<div class="text-center text-muted small py-4"><i class="bi bi-chat-dots display-6 d-block mb-2"></i>Belum ada pesan. Kirim pesan pertama kamu!</div>';
                return;
            }
            box.innerHTML = d.data.reverse().map(p => `
                <div class="mb-3">
                    <div class="chat-bubble">
                        <p class="mb-1 small">${escHtml(p.isi_pesan)}</p>
                        <span class="chat-timestamp">${p.created_at}</span>
                    </div>
                    ${p.balasan_admin ? `
                    <div class="mt-2 text-end">
                        <div class="chat-bubble admin d-inline-block">
                            <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-shield-check me-1"></i>Admin</small>
                            <p class="mb-1 small">${escHtml(p.balasan_admin)}</p>
                            <span class="chat-timestamp">${p.replied_at||''}</span>
                        </div>
                    </div>` : '<div class="text-end"><small class="text-muted"><i class="bi bi-clock me-1"></i>Menunggu balasan admin</small></div>'}
                </div>`).join('');
            box.scrollTop = box.scrollHeight;
        });
}

function kirimPesan() {
    const inp = document.getElementById('pesanInput');
    const isi = inp.value.trim();
    if (!isi) return;
    const fd = new FormData();
    fd.append('action', 'kirim'); fd.append('isi', isi);
    fetch('../api/pesan.php', { method:'POST', body:fd })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'ok') { inp.value = ''; loadPesan(); }
            else Swal.fire({icon:'error', title:'Gagal', text:d.msg});
        });
}

document.getElementById('pesanInput')?.addEventListener('keydown', e => { if (e.key === 'Enter') kirimPesan(); });
document.getElementById('tabChatLink')?.addEventListener('shown.bs.tab', loadPesan);

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>
