<?php
session_start();
include_once 'auth.php';
include_once '_header.php';
?>
<h2>Ping Server</h2>
<form method="get">
    <input type="text" name="target" placeholder="IP or domain" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$|^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
    <button type="submit">Ping!</button>
</form>
<?php
if (isset($_GET['target'])) {
    // Validasi input hanya menerima IP atau domain
    $target = filter_input(INPUT_GET, 'target', FILTER_VALIDATE_REGEXP, [
        'options' => ['regexp' => '/^([0-9]{1,3}\.){3}[0-9]{1,3}$|^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
    ]);
    if (!$target) {
        echo "<p>Invalid IP or domain.</p>";
    } else {
        // Gunakan escapeshellarg untuk mencegah Command Injection
        $safeTarget = escapeshellarg($target);
        $output = shell_exec("ping -c 2 " . $safeTarget);
        echo "<h3>Ping Result for: " . htmlspecialchars($target) . "</h3>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
}
?>
<?php include_once '_footer.php';
?>