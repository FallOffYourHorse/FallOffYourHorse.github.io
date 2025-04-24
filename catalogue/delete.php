<?php
$title = "Delete Character";
require_once('includes/auth.php');
require_once('includes/header.php');
require_once('includes/functions.php');
require_once('db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger text-center my-4'>Invalid character ID.</div>";
    require_once('includes/footer.php');
    exit;
}

$id = (int) $_GET['id'];
$char = getCharacterById($pdo, $id);

if (!$char) {
    echo "<div class='alert alert-danger text-center my-4'>Character not found.</div>";
    require_once('includes/footer.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM ttun_catalogue WHERE id = ?");
        $stmt->execute([$id]);

        echo "<div class='alert alert-success text-center my-5'>Character deleted successfully.</div>";
        echo "<div class='text-center'><a href='index.php' class='btn btn-primary'>Return to Catalogue</a></div>";
        require_once('includes/footer.php');
        exit;
    } catch (Exception $e) {
        echo "<div class='alert alert-danger text-center my-4'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        require_once('includes/footer.php');
        exit;
    }
}
?>

<main class="container my-5">
    <div class="alert alert-warning text-center">
        <h4>Are you sure you want to delete:</h4>
        <h2><?= htmlspecialchars($char['name']) ?> <small class="text-muted">(<?= htmlspecialchars($char['title']) ?>)</small></h2>
        <p>This will permanently delete the character and their tags.</p>

        <form method="post" class="d-inline">
            <button class="btn btn-danger" type="submit">Yes, Delete</button>
        </form>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</main>

<?php require_once('includes/footer.php'); ?>
