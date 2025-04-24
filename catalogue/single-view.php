<?php
$title = "Character Details - PGR Catalogue";
require_once('includes/header.php');
require_once('db_connection.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "
    SELECT c.*, GROUP_CONCAT(t.tag SEPARATOR ', ') AS tags
    FROM ttun_catalogue c
    LEFT JOIN catalogue_tags ct ON c.id = ct.catalogue_id
    LEFT JOIN tags t ON ct.tag_id = t.id
    WHERE c.id = :id
    GROUP BY c.id
";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $char = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<div class='alert alert-danger text-center my-4'>No character found with that ID.</div>";
        require_once('includes/footer.php');
        exit;
    }
} else {
    echo "<div class='alert alert-danger text-center my-4'>Invalid character ID.</div>";
    require_once('includes/footer.php');
    exit;
}
?>

<main class="container my-5 d-flex justify-content-center">
    <div class="card shadow d-flex flex-md-row flex-column align-items-center p-4 gap-4">
        <div class="col-md-4 text-center">
            <img src="includes/uploads/full/<?= htmlspecialchars($char['image_filename']) ?>"
                alt="<?= htmlspecialchars($char['title']) ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-8">
            <h2><?= htmlspecialchars($char['name']) ?></h2>
            <h4 class="text-muted mb-4"><?= htmlspecialchars($char['title']) ?></h4>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Element:</strong> <?= htmlspecialchars($char['element']) ?></p>
                    <p><strong>Rank:</strong> <?= htmlspecialchars($char['rank']) ?></p>
                    <p><strong>Age:</strong> <?= htmlspecialchars($char['age']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Class:</strong> <?= htmlspecialchars($char['class']) ?></p>
                    <p><strong>Faction:</strong> <?= htmlspecialchars($char['faction']) ?></p>
                </div>
            </div>
            <?php if (!empty($char['tags'])): ?>
                <div class="mt-3">
                    <strong>Tags:</strong>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <?php foreach (explode(',', $char['tags']) as $tag): ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="index.php" class="btn btn-secondary mt-3">← Back to Catalogue</a>
        </div>
    </div>
</main>

<?php require_once('includes/footer.php'); ?>