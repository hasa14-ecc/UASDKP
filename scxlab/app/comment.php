<?php
session_start();
include_once 'auth.php';
include_once '_header.php';
?>
<h2>Post comments</h2>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <input name="author" placeholder="Name..." required>
    <textarea name="content" placeholder="Comments..." required></textarea>
    <button type="submit">Post</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<p style='color:red'>Invalid CSRF token.</p>";
    } else {
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
        $stmt = $GLOBALS['PDO']->prepare("INSERT INTO comments(author, content, created_at) VALUES(?, ?, datetime('now'))");
        $stmt->execute([$author, $content]);
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<h3>Comment lists:</h3>
<?php
$stmt = $GLOBALS['PDO']->query("SELECT * FROM comments ORDER BY id DESC");
foreach ($stmt as $row) {
    echo "<p><b>" . htmlspecialchars($row['author']) . "</b>: " . htmlspecialchars($row['content']) . "</p>";
}
?>
<?php include_once '_footer.php';
?>