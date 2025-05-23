<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo $title; ?>
    </title>
    <!-- BS Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- BS Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body class="d-flex flex-column justify-content-between min-vh-100">
    <header class="text-center">
        <nav class="py-2 bg-dark border-bottom">
            <div class="container d-flex flex-wrap">
                <ul class="nav">
                    <li class="nav-item"><a href="tag.php" class="nav-link link-light link-body-emphasis px-2">Tags</a></li>
                    <li class="nav-item"><a href="search.php" class="nav-link link-light link-body-emphasis px-2">Advanced Search</a></li>
                </ul>
                <ul class="nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item"><a href="add.php" class="nav-link link-light link-body-emphasis px-2">Add a Character</a></li>
                        <li class="nav-item"><a href="logout.php" class="nav-link link-light link-body-emphasis px-2">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="nav-link link-light link-body-emphasis px-2">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <section class="py-3 mb-4 border-bottom">
            <div class="container d-flex flex-wrap justify-content-center">
                <a href="index.php"
                    class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto link-body-emphasis text-decoration-none">
                    <svg class="bi me-2" width="40" height="32">
                        <use xlink:href="#bootstrap"></use>
                    </svg>
                    <h1 class="fs-4 fw-light text-danger"><i class="bi bi-controller"></i> Punishing: Gray Raven Catalogue</h1>

                </a>

                <!-- If you choose to do the 'quick search' as your challenge, include the widget here. -->
                <!-- <form class="col-12 col-lg-auto mb-3 mb-lg-0" role="search"> -->
                <!-- This is an input type of search, so the user has to hit 'enter' or 'return' to submit the form. A more user-friendly thing to do would be to also offer a submit button beside it. -->
                <!-- <input type="search" class="form-control" aria-label="Search">
                    </form> -->
            </div>
        </section>
    </header>
