<?php
/**
 * API: Get tempat PKL berdasarkan jurusan
 * File: admin/get_tempat_jurusan.php
 */
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }

$jurusan_id = (int)($_GET['jurusan_id'] ?? 0);
$result = mysqli_query($conn, "SELECT * FROM tempat_pkl WHERE jurusan_id = $jurusan_id ORDER BY nama_tempat ASC");
$data = [];
while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
echo json_encode($data);
?>
