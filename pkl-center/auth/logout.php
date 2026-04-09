<?php
/**
 * Logout - PKL Center
 * File: auth/logout.php
 */
session_start();
require_once '../config/database.php';

// Hapus activity record saat logout
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM user_activity WHERE user_id = $uid");
}

$role = $_SESSION['role'] ?? 'siswa';
session_unset();
session_destroy();

// Redirect ke halaman login sesuai role
if ($role === 'admin') {
    header("Location: ../admin/login.php?logout=1");
} else {
    header("Location: login.php?logout=1");
}
exit;
?>
