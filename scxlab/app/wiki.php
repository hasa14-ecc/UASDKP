<?php
session_start();
include_once 'auth.php';
include_once '_header.php';
?>
<h2>Wiki Search</h2>
<form method="get">
    <input type="text" name="q" placeholder="Search..." required>
    <button type="submit">Search</button>
</form>
<?php
if (isset($_GET['q'])) {
    $q = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
    // Prepared statement untuk mencegah SQL Injection
    $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM articles WHERE title LIKE ?");
    $stmt->execute(["%$q%"]);
    echo "<p>Search for: " . htmlspecialchars($q) . "</p>";
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        foreach ($results as $row) {
            echo "<li>" . htmlspecialchars($row['title']) . ": " . htmlspecialchars($row['body']) . "</li>";
        }
    } else {
        echo "<p>No results found.</p>";
    }
}
?>
<?php include_once '_footer.php';
?>