<?php
/**
 * API: User Activity Realtime
 * File: api/activity.php
 * Method: POST → update activity | GET → get stats
 */
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

$uid = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update last_activity
    mysqli_query($conn, "INSERT INTO user_activity (user_id) VALUES ($uid)
                         ON DUPLICATE KEY UPDATE last_activity = NOW()");
    echo json_encode(['status' => 'ok']);
} else {
    // GET: return stats
    $five_min_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));

    $active = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS total FROM user_activity WHERE last_activity >= '$five_min_ago'"))['total'];

    $total_siswa = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS t FROM users WHERE role='siswa' AND is_verified=1"))['t'];

    $total_jurusan = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS t FROM jurusan"))['t'];

    $total_tempat = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS t FROM tempat_pkl"))['t'];

    $total_pesan_baru = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS t FROM pesan WHERE status='belum'"))['t'];

    echo json_encode([
        'status'         => 'ok',
        'active_users'   => (int)$active,
        'total_siswa'    => (int)$total_siswa,
        'total_jurusan'  => (int)$total_jurusan,
        'total_tempat'   => (int)$total_tempat,
        'pesan_baru'     => (int)$total_pesan_baru,
    ]);
}
?>
