<?php
session_start();
include_once 'auth.php';
include_once '_header.php';

if (!isset($_COOKIE['profile'])) {
    die("Profile cookie not found. Please login again.");
}

// Gunakan JSON alih-alih unserialize
$profile = json_decode($_COOKIE['profile'], true);
if (!$profile || !isset($profile['username'], $profile['isAdmin'])) {
    die("Invalid profile data.");
}

// Validasi server-side terhadap role
$stmt = $GLOBALS['PDO']->prepare("SELECT role FROM users WHERE username = ?");
$stmt->execute([$profile['username']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$isAdmin = $row && $row['role'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $msg = "<p style='color:red'>Invalid CSRF token.</p>";
    } else {
        $target = filter_input(INPUT_POST, 'delete_user', FILTER_SANITIZE_STRING);
        if ($target && $target !== $profile['username']) {
            $stmt = $GLOBALS['PDO']->prepare("DELETE FROM users WHERE username = ?");
            $stmt->execute([$target]);
            $msg = "<p style='color:green'>User <b>" . htmlspecialchars($target) . "</b> successfully deleted!</p>";
        }
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<h2>Profile Page</h2>
<p>User: <?php echo htmlspecialchars($profile['username']); ?>, Role: <?php echo $isAdmin ? 'Admin' : 'User'; ?></p>

<?php if ($isAdmin): ?>
  <h3>Admin Panel</h3>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <label>Delete user:
      <select name="delete_user" required>
        <?php
        $stmt = $GLOBALS['PDO']->query("SELECT username FROM users");
        while ($u = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($u['username'] !== $profile['username']) {
                echo "<option value='" . htmlspecialchars($u['username']) . "'>" . htmlspecialchars($u['username']) . "</option>";
            }
        }
        ?>
      </select>
    </label>
    <button type="submit">Delete</button>
  </form>
  <?php if (!empty($msg)) echo $msg; ?>
<?php else: ?>
  <p style="color:red">You are a regular user. You do not have admin panel access.</p>
<?php endif; ?>

<?php include_once '_footer.php';
?>