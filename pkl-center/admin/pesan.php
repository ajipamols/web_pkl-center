<?php
/**
 * Halaman Pesan Admin - PKL Center
 * File: admin/pesan.php
 */
session_start();
require_once '../config/database.php';
require_once '../middleware/auth.php';
requireAdmin();

$uid = (int)$_SESSION['user_id'];
mysqli_query($conn, "INSERT INTO user_activity (user_id) VALUES ($uid) ON DUPLICATE KEY UPDATE last_activity = NOW()");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Siswa | Admin PKL Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#f0f4f8; }
        .sidebar { background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%); min-height:100vh; }
        .sidebar .nav-link { color:rgba(255,255,255,.7); padding:12px 20px; border-radius:10px; margin:2px 8px; transition:.2s; font-weight:600; font-size:.9rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background:rgba(255,255,255,.12); color:#fff; }
        .topbar { background:#fff; box-shadow:0 1px 10px rgba(0,0,0,.06); padding:14px 24px; }
        .glass-card { background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.06); border:1px solid #e8edf2; }
        .msg-card { border-left:4px solid #e2e8f0; padding:16px; margin-bottom:12px; background:#f8fafc; border-radius:0 14px 14px 0; }
        .msg-card.unread { border-left-color:#0d6efd; background:#eff6ff; }
        .msg-card.replied { border-left-color:#059669; background:#f0fdf4; }
    </style>
</head>
<body>
<div class="container-fluid p-0">
<div class="row g-0">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Pesan dari Siswa</h5>
                <small class="text-muted">Balas dan kelola pesan siswa</small>
            </div>
        </div>
        <div class="p-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Inbox Pesan</h6>
                    <button class="btn btn-sm btn-outline-secondary rounded-3" onclick="loadPesan()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
                <div id="pesanList"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- MODAL BALAS -->
<div class="modal fade" id="modalBalas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Balas Pesan Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light rounded-3 p-3 mb-3">
                    <small class="text-muted fw-bold">Pesan dari:</small>
                    <p class="fw-bold mb-1" id="fromNama"></p>
                    <p class="mb-0" id="originalMsg"></p>
                    <small class="text-muted" id="msgTime"></small>
                </div>
                <label class="form-label fw-bold small">BALASAN ADMIN</label>
                <textarea id="balasanText" class="form-control rounded-3" rows="4" placeholder="Ketik balasan Anda…"></textarea>
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
function loadPesan() {
    fetch('../api/pesan.php?action=list_admin')
        .then(r => r.json())
        .then(d => {
            const box = document.getElementById('pesanList');
            if (!d.data || !d.data.length) {
                box.innerHTML = '<p class="text-muted text-center py-5"><i class="bi bi-inbox display-4 d-block mb-3"></i>Belum ada pesan masuk.</p>';
                return;
            }
            box.innerHTML = d.data.map(p => `
                <div class="msg-card ${p.balasan_admin ? 'replied' : (p.status==='belum' ? 'unread' : '')}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold">${escHtml(p.nama)}</span>
                                ${p.status==='belum' && !p.balasan_admin ? '<span class="badge bg-danger">Baru</span>' : ''}
                                ${p.balasan_admin ? '<span class="badge bg-success">Dibalas</span>' : ''}
                            </div>
                            <p class="mb-1">${escHtml(p.isi_pesan)}</p>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>${p.created_at}</small>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-3 ms-3" onclick="bukaBalas(${p.id},'${escJs(p.nama)}','${escJs(p.isi_pesan)}','${p.created_at}')">
                            <i class="bi bi-reply me-1"></i>${p.balasan_admin ? 'Ubah Balasan' : 'Balas'}
                        </button>
                    </div>
                    ${p.balasan_admin ? `
                    <div class="mt-3 p-3 rounded-3" style="background:#d1fae5;border-left:3px solid #059669;">
                        <small class="fw-bold text-success"><i class="bi bi-shield-check me-1"></i>Balasan Admin · ${p.replied_at||''}</small>
                        <p class="mb-0 mt-1">${escHtml(p.balasan_admin)}</p>
                    </div>` : ''}
                </div>`).join('');
        });
}

function bukaBalas(id, nama, msg, time) {
    document.getElementById('pesanId').value = id;
    document.getElementById('fromNama').textContent = nama;
    document.getElementById('originalMsg').textContent = msg;
    document.getElementById('msgTime').textContent = time;
    document.getElementById('balasanText').value = '';
    new bootstrap.Modal(document.getElementById('modalBalas')).show();
    // Tandai dibaca
    const fd = new FormData(); fd.append('action','baca'); fd.append('pesan_id',id);
    fetch('../api/pesan.php', {method:'POST',body:fd});
}

function kirimBalasan() {
    const id  = document.getElementById('pesanId').value;
    const bal = document.getElementById('balasanText').value.trim();
    if (!bal) { Swal.fire({icon:'warning',title:'Kosong',text:'Masukkan balasan terlebih dahulu.'}); return; }
    const fd = new FormData(); fd.append('action','balas'); fd.append('pesan_id',id); fd.append('balasan',bal);
    fetch('../api/pesan.php', {method:'POST',body:fd})
        .then(r => r.json())
        .then(() => {
            bootstrap.Modal.getInstance(document.getElementById('modalBalas')).hide();
            Swal.fire({icon:'success',title:'Terkirim!',text:'Balasan berhasil dikirim.',timer:2000,showConfirmButton:false});
            loadPesan();
        });
}

function escHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function escJs(s){ return String(s).replace(/'/g,"\\'").replace(/\n/g,' '); }

loadPesan();
setInterval(loadPesan, 15000);
</script>
</body>
</html>
