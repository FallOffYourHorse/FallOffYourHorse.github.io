<?php
$title = "Search Results";
include('includes/header.php');
include('includes/functions.php');

// Collect filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$element = $_GET['element'] ?? '';
$rank = $_GET['rank'] ?? '';
$class = $_GET['class'] ?? '';
$faction = $_GET['faction'] ?? '';

// Build SQL dynamically
$sql = "SELECT * FROM ttun_catalogue WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR title LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if (!empty($element)) {
    $sql .= " AND element = :element";
    $params[':element'] = $element;
}
if (!empty($rank)) {
    $sql .= " AND `rank` = :rank";
    $params[':rank'] = $rank;
}
if (!empty($class)) {
    $sql .= " AND `class` = :class";
    $params[':class'] = $class;
}
if (!empty($faction)) {
    $sql .= " AND faction = :faction";
    $params[':faction'] = $faction;
}

// Prepare and execute
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<main class="container my-5">
    <h3>Search Results</h3>

    <?php if (count($results) > 0): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($results as $row): ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="includes/uploads/thumbs/<?php echo htmlspecialchars($row['image_filename']); ?>"
                             class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?>
                                <small class="text-muted">(<?php echo htmlspecialchars($row['title']); ?>)</small>
                            </h5>
                            <p class="card-text">
                                <strong>Element:</strong> <?php echo $row['element']; ?><br>
                                <strong>Rank:</strong> <?php echo $row['rank']; ?><br>
                                <strong>Age:</strong> <?php echo $row['age']; ?><br>
                                <strong>Class:</strong> <?php echo $row['class']; ?><br>
                                <strong>Faction:</strong> <?php echo $row['faction']; ?>
                            </p>
                            <a href="single-view.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4">No results found.</div>
    <?php endif; ?>
</main>

<?php include('includes/footer.php'); ?>
