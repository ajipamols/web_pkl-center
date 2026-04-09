<?php
/**
 * Helper untuk menghitung pengunjung website
 * File: config/visitor_counter.php
 */

function trackVisitor($conn) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $tanggal = date('Y-m-d');
    $waktu = date('H:i:s');
    
    $check = mysqli_query($conn, "SELECT id FROM pengunjung WHERE ip_address = '$ip' AND tanggal = '$tanggal'");
    
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO pengunjung (ip_address, tanggal, waktu) VALUES ('$ip', '$tanggal', '$waktu')");
    }
}

function getTotalVisitors($conn) {
    $result = mysqli_query($conn, "SELECT COUNT(DISTINCT ip_address) as total FROM pengunjung");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getTodayVisitors($conn) {
    $tanggal = date('Y-m-d');
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengunjung WHERE tanggal = '$tanggal'");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getThisMonthVisitors($conn) {
    $bulan = date('Y-m');
    $result = mysqli_query($conn, "SELECT COUNT(DISTINCT ip_address) as total FROM pengunjung WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getTotalTempat($conn) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM tempat_pkl");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getTotalJurusan($conn) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM jurusan");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
?>