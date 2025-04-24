<?php
$title = "Browse by Tag";
require_once('includes/header.php');
require_once('includes/functions.php');
require_once('db_connection.php');

$tags = $pdo->query("SELECT * FROM tags ORDER BY id ASC")->fetchAll();
?>

<main class="container my-5">
    <h2 class="text-center mb-4">Browse by Tag</h2>

    <?php if (empty($tags)): ?>
        <div class="alert alert-warning text-center">No tags found.</div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php foreach ($tags as $tag): ?>
                <div class="col">
                    <a href="index.php?tag=<?= $tag['id'] ?>" class="btn btn-outline-primary w-100">
                        <?= htmlspecialchars($tag['tag']) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once('includes/footer.php'); ?>
