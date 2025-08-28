<?php
session_start();
session_destroy();
setcookie('profile', '', time() - 3600, '/', '', false, true); // Hapus cookie
header("Location: login.php");
exit;
?>