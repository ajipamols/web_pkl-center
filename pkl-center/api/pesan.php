<?php
/**
 * API: Sistem Pesan/Chat
 * File: api/pesan.php
 */
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauthorized']); exit;
}

$uid  = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];
$act  = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($act) {

    // Siswa kirim pesan baru
    case 'kirim':
        if ($role !== 'siswa') { echo json_encode(['status'=>'forbidden']); exit; }
        $isi = mysqli_real_escape_string($conn, trim($_POST['isi'] ?? ''));
        if (empty($isi)) { echo json_encode(['status'=>'error','msg'=>'Pesan tidak boleh kosong']); exit; }
        mysqli_query($conn, "INSERT INTO pesan (user_id, isi_pesan) VALUES ($uid, '$isi')");
        echo json_encode(['status'=>'ok','msg'=>'Pesan terkirim']);
        break;

    // Siswa ambil semua pesannya
    case 'list_siswa':
        if ($role !== 'siswa') { echo json_encode(['status'=>'forbidden']); exit; }
        $res = mysqli_query($conn, "SELECT * FROM pesan WHERE user_id=$uid ORDER BY created_at DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        echo json_encode(['status'=>'ok','data'=>$data]);
        break;

    // Admin ambil semua pesan semua siswa
    case 'list_admin':
        if ($role !== 'admin') { echo json_encode(['status'=>'forbidden']); exit; }
        $res = mysqli_query($conn,
            "SELECT p.*, u.nama FROM pesan p
             JOIN users u ON p.user_id = u.id
             ORDER BY p.created_at DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        echo json_encode(['status'=>'ok','data'=>$data]);
        break;

    // Admin balas pesan
    case 'balas':
        if ($role !== 'admin') { echo json_encode(['status'=>'forbidden']); exit; }
        $pid    = (int)($_POST['pesan_id'] ?? 0);
        $balas  = mysqli_real_escape_string($conn, trim($_POST['balasan'] ?? ''));
        if (!$pid || empty($balas)) { echo json_encode(['status'=>'error','msg'=>'Data tidak lengkap']); exit; }
        mysqli_query($conn, "UPDATE pesan SET balasan_admin='$balas', status='dibaca', replied_at=NOW() WHERE id=$pid");
        echo json_encode(['status'=>'ok','msg'=>'Balasan terkirim']);
        break;

    // Admin tandai dibaca
    case 'baca':
        if ($role !== 'admin') { echo json_encode(['status'=>'forbidden']); exit; }
        $pid = (int)($_POST['pesan_id'] ?? 0);
        mysqli_query($conn, "UPDATE pesan SET status='dibaca' WHERE id=$pid");
        echo json_encode(['status'=>'ok']);
        break;

    // Siswa hapus pesannya sendiri
    case 'hapus':
        if ($role !== 'siswa') { echo json_encode(['status'=>'forbidden']); exit; }
        $pid = (int)($_POST['pesan_id'] ?? 0);
        mysqli_query($conn, "DELETE FROM pesan WHERE id=$pid AND user_id=$uid");
        echo json_encode(['status'=>'ok']);
        break;

    default:
        echo json_encode(['status'=>'error','msg'=>'Action tidak dikenal']);
}
?>
