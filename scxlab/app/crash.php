<?php
session_start();
include_once 'auth.php';
include_once '_header.php';
?>
<h2>Crash Test</h2>
<?php
// Validasi input sebagai angka
$factor = filter_input(INPUT_GET, 'factor', FILTER_VALIDATE_FLOAT, ['options' => ['default' => 1]]);
if ($factor === 0) {
    echo "<p>Error: Division by zero is not allowed.</p>";
} else {
    $result = 100 / $factor;
    echo "<p>100 / " . htmlspecialchars($factor) . " = " . htmlspecialchars($result) . "</p>";
}
?>
<?php include_once '_footer.php';
?>