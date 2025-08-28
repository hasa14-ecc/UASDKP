<?php
session_start();
require_once __DIR__ . '/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        // Sanitasi input
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        // Prepared statement untuk mencegah SQL Injection
        $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['user'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            // Simpan profil dalam JSON, bukan serialize
            $profile = ['username' => $row['username'], 'isAdmin' => $row['role'] === 'admin'];
            setcookie('profile', json_encode($profile), time() + 3600, '/', '', false, true); // HttpOnly
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Login failed.";
        }
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<?php include_once '_header.php'; ?>
<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; ?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <label>Username <input name="username" required></label>
    <label>Password <input type="password" name="password" required></label>
    <button type="submit">Login</button>
</form>
<?php include_once '_footer.php';
?>