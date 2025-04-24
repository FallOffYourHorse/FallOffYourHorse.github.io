<?php
$title = "Punishing: Gray Raven Character Catalogue";
include('includes/header.php');
include('includes/functions.php');
require_once('db_connection.php');

$limit = 9;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$start = ($page - 1) * $limit;

$filterSQL = '';
$sql = "
SELECT c.*, GROUP_CONCAT(t.tag SEPARATOR ', ') AS tags
FROM ttun_catalogue c
LEFT JOIN catalogue_tags ct ON c.id = ct.catalogue_id
LEFT JOIN tags t ON ct.tag_id = t.id
" . ($filterSQL ? $filterSQL : "") . "
GROUP BY c.id
ORDER BY c.name ASC
LIMIT :start, :limit";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_sql = "SELECT COUNT(*) FROM ttun_catalogue";
$total_stmt = $pdo->query($total_sql);
$total_count = $total_stmt->fetchColumn();
$total_pages = ceil($total_count / $limit);
?>

<main class="container my-5">
    <header class="text-center mb-5">
        <h1 class="display-4">PGR Character Catalogue</h1>
        <p class="lead">Browse profiles of characters from Punishing: Gray Raven.</p>
    </header>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($characters as $char): ?>
            <div class="col">
                <div class="card h-100 shadow-sm text-center">
                    <img src="includes/uploads/thumbs/<?= htmlspecialchars($char['image_filename']) ?>"
                        class="card-img-top mx-auto" style="height: 300px; width: auto; object-fit: contain;"
                        alt="<?= htmlspecialchars($char['name']) ?> thumbnail">


                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($char['name']) ?>
                            <small class="text-muted d-block"><?= htmlspecialchars($char['title']) ?></small>
                        </h5>

                        <p class="mb-1"><strong>Element:</strong> <?= htmlspecialchars($char['element']) ?></p>
                        <p class="mb-1"><strong>Rank:</strong> <?= htmlspecialchars($char['rank']) ?></p>
                        <p class="mb-1"><strong>Age:</strong> <?= htmlspecialchars($char['age']) ?></p>
                        <p class="mb-1"><strong>Class:</strong> <?= htmlspecialchars($char['class']) ?></p>
                        <p class="mb-2"><strong>Faction:</strong> <?= htmlspecialchars($char['faction']) ?></p>

                        <?php if (!empty($char['tags'])): ?>
                            <div class="mb-2">
                                <?php foreach (explode(', ', $char['tags']) as $tag): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <a href="single-view.php?id=<?= $char['id'] ?>" class="btn btn-outline-primary btn-sm mt-2">
                            View Details
                        </a>
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="edit.php?id=<?= $char['id'] ?>" class="btn btn-outline-warning text-warning btn-sm mt-2">
                            Edit
                        </a>
                            <a href="delete.php?id=<?= $char['id'] ?>" class="btn btn-outline-danger text-danger btn-sm mt-2">
                            Delete
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</main>

<?php include('includes/footer.php'); ?>