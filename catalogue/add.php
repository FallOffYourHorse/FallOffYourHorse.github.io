<?php
$title = "Add Character";
require_once('includes/auth.php');
require_once('includes/header.php');
require_once('includes/functions.php');
require_once('db_connection.php');

$tags = $pdo->query("SELECT * FROM tags ORDER BY id ASC")->fetchAll();
$elements = getDistinctOptions($pdo, 'element');
$ranks = getDistinctOptions($pdo, 'rank');
$classes = getDistinctOptions($pdo, 'class');
$factions = getDistinctOptions($pdo, 'faction');

$errors = [];
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $titleInput = trim($_POST['title']);
    $element = trim($_POST['element']);
    $age = (int) $_POST['age'];
    $rank = trim($_POST['rank']);
    $class = trim($_POST['class']);
    $faction = trim($_POST['faction']);
    $selectedTags = $_POST['tags'] ?? [];

    if ($name === "" || $titleInput === "" || $element === "" || $rank === "" || $class === "" || $faction === "") {
        $errors[] = "All fields are required.";
    }
    if (!is_numeric($age) || $age < 0) {
        $errors[] = "Age must be a valid number.";
    }

    $image_filename = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        [$image_filename, $imageError] = processImageUpload(
            $titleInput,
            $_FILES['image']['tmp_name'],
            "includes/uploads/full/",
            "includes/uploads/thumbs/"
        );
        if ($imageError) $errors[] = $imageError;
    } else {
        $errors[] = "Image upload required.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO ttun_catalogue (name, title, element, age, `rank`, `class`, faction, image_filename)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $titleInput, $element, $age, $rank, $class, $faction, $image_filename]);
            $newId = $pdo->lastInsertId();

            if (!empty($selectedTags)) {
                $tagStmt = $pdo->prepare("INSERT INTO catalogue_tags (catalogue_id, tag_id) VALUES (?, ?)");
                foreach ($selectedTags as $tagId) {
                    $tagStmt->execute([$newId, $tagId]);
                }
            }

            $pdo->commit();
            $success = "Character added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<main class="container my-5">
    <h2 class="mb-4">Add New Character</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>"; ?></ul>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name</label>
                <input name="name" class="form-control" value="<?= $_POST['name'] ?? '' ?>" required>
            </div>
            <div class="col-md-6"><label class="form-label">Title</label>
                <input name="title" class="form-control" value="<?= $_POST['title'] ?? '' ?>" required>
            </div>

            <div class="col-md-6"><label class="form-label">Element</label>
                <select name="element" class="form-select" required>
                    <option value="">Select Element</option>
                    <?php foreach ($elements as $element): ?>
                        <option value="<?= $element ?>" <?= ($element === ($_POST['element'] ?? '')) ? 'selected' : '' ?>><?= $element ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6"><label class="form-label">Age</label>
                <input name="age" type="number" class="form-control" value="<?= $_POST['age'] ?? '' ?>" required>
            </div>

            <div class="col-md-4"><label class="form-label">Rank</label>
                <select name="rank" class="form-select" required>
                    <option value="">Select Rank</option>
                    <?php foreach ($ranks as $rank): ?>
                        <option value="<?= $rank ?>" <?= ($rank === ($_POST['rank'] ?? '')) ? 'selected' : '' ?>><?= $rank ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4"><label class="form-label">Class</label>
                <select name="class" class="form-select" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class ?>" <?= ($class === ($_POST['class'] ?? '')) ? 'selected' : '' ?>><?= $class ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4"><label class="form-label">Faction</label>
                <select name="faction" class="form-select" required>
                    <option value="">Select Faction</option>
                    <?php foreach ($factions as $faction): ?>
                        <option value="<?= $faction ?>" <?= ($faction === ($_POST['faction'] ?? '')) ? 'selected' : '' ?>><?= $faction ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Upload Image</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="form-control" required>
            </div>

            <div class="col-12"><label class="form-label">Tags</label>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"
                                   id="tag<?= $tag['id'] ?>" <?= in_array($tag['id'], $_POST['tags'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tag<?= $tag['id'] ?>"><?= htmlspecialchars($tag['tag']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary" type="submit">Add Character</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</main>

<?php require_once('includes/footer.php'); ?>
