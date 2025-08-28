<?php
session_start();

// Periksa apakah sesi pengguna ada dan valid
if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || !isset($_SESSION['last_activity'])) {
    // Hapus sesi dan cookie jika tidak valid
    session_unset();
    session_destroy();
    setcookie('profile', '', time() - 3600, '/', '', false, true);
    header("Location: login.php");
    exit;
}

// Periksa apakah sesi telah kadaluarsa (misalnya, 30 menit tidak aktif)
$timeout = 1800; // 30 menit dalam detik
if (time() - $_SESSION['last_activity'] > $timeout) {
    session_unset();
    session_destroy();
    setcookie('profile', '', time() - 3600, '/', '', false, true);
    header("Location: login.php?error=session_expired");
    exit;
}

// Perbarui waktu aktivitas terakhir
$_SESSION['last_activity'] = time();

// Validasi bahwa user ada di database
$stmt = $GLOBALS['PDO']->prepare("SELECT username FROM users WHERE username = ?");
$stmt->execute([$_SESSION['user']]);
if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
    session_unset();
    session_destroy();
    setcookie('profile', '', time() - 3600, '/', '', false, true);
    header("Location: login.php?error=invalid_user");
    exit;
}

// Pastikan file bukan login.php
if (basename($_SERVER['PHP_SELF']) === 'login.php' && isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}
?>