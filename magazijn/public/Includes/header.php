<?php

?>
<<<<<<< Updated upstream
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ResQ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <?php if (isset($_SESSION['user'])):
                    $user = is_string($_SESSION['user']) ? unserialize($_SESSION['user']) : $_SESSION['user'];
                ?>
=======
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">ResQ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
>>>>>>> Stashed changes
                    <li class="nav-item">
                        <a class="nav-link" href="inventarisbeheer.php">Inventarisbeheer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warehouse.php">Warenhuisplaatsing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reservation.php">Status Veranderen</a>
                    </li>
                    <?php if (method_exists($user, 'getRole') && $user->getRole() === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">AdminPanel</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Uitloggen</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>

            <?php if (isset($user) && method_exists($user, 'getName')): ?>
                <span class="navbar-text">
                    Welkom, <?= htmlspecialchars($user->getName()); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</nav>