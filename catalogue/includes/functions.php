<?php
include("./db_connection.php");

// Pagination
function display_pagination($pdo, $limit, $currentPage)
{
    $sqlTotal = "SELECT COUNT(*) AS total FROM ttun_catalogue";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute();
    $totalRows = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    $totalPages = ceil($totalRows / $limit);

    echo "<div class='pagination mt-4'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        echo "<a href='index.php?page=$i' class='btn btn-outline-primary $activeClass'>$i</a> ";
    }
    echo "</div>";
}

// get form
function get_form_value($field, $default = '')
{
    return isset($_GET[$field]) ? htmlspecialchars($_GET[$field]) : $default;
}

// sanitize filename from title input
function sanitize_filename($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '', $text);
    return $text;
}

// Upload image and resize
function processImageUpload($title, $tmpPath, $uploadDirFull, $uploadDirThumb)
{
    if (!function_exists('imagecreatefromstring')) {
        return [null, "Image processing is not available. (GD library missing)"];
    }

    $filename = sanitize_filename($title) . ".webp";
    $fullPath = $uploadDirFull . $filename;
    $thumbPath = $uploadDirThumb . $filename;

    try {
        $img = imagecreatefromstring(file_get_contents($tmpPath));
        if (!$img) return [null, "Invalid or unsupported image format."];

        // Get original dimensions
        $orig_w = imagesx($img);
        $orig_h = imagesy($img);

        // ai generated to preserve transparency "Using GD makes the character images background black" on ChatGPT 4o
        imagesavealpha($img, true);

        $new_full_w = 720;
        $new_full_h = (int) ($new_full_w * $orig_h / $orig_w);
        $full_resized = imagecreatetruecolor($new_full_w, $new_full_h);
        imagealphablending($full_resized, false);
        imagesavealpha($full_resized, true);
        $transparent = imagecolorallocatealpha($full_resized, 0, 0, 0, 127);
        imagefill($full_resized, 0, 0, $transparent);
        imagecopyresampled($full_resized, $img, 0, 0, 0, 0, $new_full_w, $new_full_h, $orig_w, $orig_h);
        imagewebp($full_resized, $fullPath, 90);
        imagedestroy($full_resized);

        $thumb_w = 300;
        $thumb_h = (int) ($thumb_w * $orig_h / $orig_w);
        $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefill($thumb, 0, 0, $transparent);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumb_w, $thumb_h, $orig_w, $orig_h);
        imagewebp($thumb, $thumbPath, 90);
        imagedestroy($thumb);

        imagedestroy($img);
        return [$filename, null];
    } catch (Exception $e) {
        return [null, "Image processing failed: " . $e->getMessage()];
    }
}



// dropdowns
function getDistinctOptions($pdo, $column)
{
    $stmt = $pdo->prepare("SELECT DISTINCT `$column` FROM ttun_catalogue ORDER BY `$column` ASC");
    $stmt->execute();
    return array_column($stmt->fetchAll(), $column);
}


// add.php
function insertCharacter($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO ttun_catalogue (name, title, element, age, `rank`, `class`, faction, image_filename)
        VALUES (:name, :title, :element, :age, :rank, :class, :faction, :image_filename)
    ");
    $stmt->execute([
        ':name' => $data['name'],
        ':title' => $data['title'],
        ':element' => $data['element'],
        ':age' => $data['age'],
        ':rank' => $data['rank'],
        ':class' => $data['class'],
        ':faction' => $data['faction'],
        ':image_filename' => $data['image_filename']
    ]);
    return $pdo->lastInsertId();
}

function assignTagsToCharacter($pdo, $characterId, $tagIds) {
    if (!empty($tagIds)) {
        $stmt = $pdo->prepare("INSERT INTO catalogue_tags (catalogue_id, tag_id) VALUES (:cid, :tid)");
        foreach ($tagIds as $tagId) {
            $stmt->execute([
                ':cid' => $characterId,
                ':tid' => $tagId
            ]);
        }
    }
}


// edit.php
function getCharacterById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM ttun_catalogue WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCharacterTags($pdo, $id) {
    $stmt = $pdo->prepare("SELECT tag_id FROM catalogue_tags WHERE catalogue_id = ?");
    $stmt->execute([$id]);
    return array_column($stmt->fetchAll(), 'tag_id');
}

function updateCharacter($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE ttun_catalogue
        SET name = :name, title = :title, element = :element, age = :age,
            `rank` = :rank, `class` = :class, faction = :faction, image_filename = :image_filename
        WHERE id = :id
    ");
    $stmt->execute([
        ':name' => $data['name'],
        ':title' => $data['title'],
        ':element' => $data['element'],
        ':age' => $data['age'],
        ':rank' => $data['rank'],
        ':class' => $data['class'],
        ':faction' => $data['faction'],
        ':image_filename' => $data['image_filename'],
        ':id' => $id
    ]);
}

function updateCharacterTags($pdo, $id, $tagIds) {
    $pdo->prepare("DELETE FROM catalogue_tags WHERE catalogue_id = ?")->execute([$id]);
    if (!empty($tagIds)) {
        $stmt = $pdo->prepare("INSERT INTO catalogue_tags (catalogue_id, tag_id) VALUES (?, ?)");
        foreach ($tagIds as $tagId) {
            $stmt->execute([$id, $tagId]);
        }
    }
}


// search
function get_distinct_values($pdo, $column) {
    $allowed = ['element', 'rank', 'class', 'faction'];
    if (!in_array($column, $allowed)) return [];

    $stmt = $pdo->prepare("SELECT DISTINCT `$column` FROM ttun_catalogue ORDER BY `$column` ASC");
    $stmt->execute();
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $column);
}
