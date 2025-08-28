<?php
session_start();
include_once 'auth.php';
include_once '_header.php';
?>
<h2>Dashboard</h2>
<p>Welcome <b><?php echo htmlspecialchars($_SESSION['user']); ?></b>!</p>
<p>Use the menu above to access the web page.</p>
<?php include_once '_footer.php';
?>