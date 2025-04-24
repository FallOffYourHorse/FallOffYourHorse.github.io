<?php
$title = "Advanced Search: PGR Character Catalogue";
include('includes/header.php');
include('includes/functions.php');

// Fetch distinct dropdown values
$elements = get_distinct_values($pdo, 'element');
$ranks = get_distinct_values($pdo, 'rank');
$classes = get_distinct_values($pdo, 'class');
$factions = get_distinct_values($pdo, 'faction');
?>

<main class="container">
    <section class="row justify-content-between my-5">
        <div class="col-md-12">
            <h3>Advanced Search</h3>

            <form method="GET" action="search-results.php">
                <div class="mb-3">
                    <label for="search" class="form-label">Search by Name or Title</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?php echo get_form_value('search'); ?>" placeholder="Lucia, Crimson Weave...">
                </div>

                <div class="mb-3">
                    <label for="element" class="form-label">Element</label>
                    <select class="form-select" id="element" name="element">
                        <option value="">Any</option>
                        <?php foreach ($elements as $e): ?>
                            <option value="<?php echo htmlspecialchars($e); ?>"><?php echo htmlspecialchars($e); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="rank" class="form-label">Rank</label>
                    <select class="form-select" id="rank" name="rank">
                        <option value="">Any</option>
                        <?php foreach ($ranks as $r): ?>
                            <option value="<?php echo htmlspecialchars($r); ?>"><?php echo htmlspecialchars($r); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="class" class="form-label">Class</label>
                    <select class="form-select" id="class" name="class">
                        <option value="">Any</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="faction" class="form-label">Faction</label>
                    <select class="form-select" id="faction" name="faction">
                        <option value="">Any</option>
                        <?php foreach ($factions as $f): ?>
                            <option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars($f); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </section>
</main>

<?php include('includes/footer.php'); ?>
