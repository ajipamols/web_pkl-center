<?php
/**
 * Middleware Auth - PKL Center
 * File: middleware/auth.php
 */

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function isSiswa() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'siswa';
}

// Proteksi halaman admin → redirect ke admin/login.php
function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: " . getAdminLoginPath() . "?reason=login");
        exit;
    }
    if (!isAdmin()) {
        // Siswa mencoba akses admin
        header("Location: " . getSiswaPath() . "?reason=denied");
        exit;
    }
}

// Proteksi halaman siswa → redirect ke auth/login.php
function requireSiswa() {
    if (!isLoggedIn()) {
        header("Location: " . getSiswaLoginPath() . "?reason=login");
        exit;
    }
    if (!isSiswa()) {
        // Admin mencoba akses siswa
        header("Location: " . getAdminPath() . "?reason=denied");
        exit;
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAdmin()) { header("Location: " . getAdminPath()); }
        else { header("Location: " . getSiswaPath()); }
        exit;
    }
}

// Helper path — deteksi kedalaman direktori
function getBasePath() {
    $depth = substr_count(str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__), '/');
    // middleware/ = 1 level dari root project
    return '../';
}

function getAdminLoginPath() {
    return '../admin/login.php';
}
function getSiswaLoginPath() {
    return '../auth/login.php';
}
function getAdminPath() {
    return '../admin/dashboard.php';
}
function getSiswaPath() {
    return '../siswa/dashboard.php';
}
?>
