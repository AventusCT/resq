<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <style>
        header {
            background-color: #1f2937;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* NAVBAR CENTREREN */
        .navbar-nav {
            margin: 0 auto;      /* Centrale uitlijning */
            text-align: center;  /* Extra nette centrering */
        }

        .navbar {
            background-color: #1f2937 !important;
        }

        .navbar .nav-link,
        .navbar .navbar-brand,
        .navbar-text {
            color: white !important;
        }

        .navbar .nav-link:hover {
            color: #fbbf24 !important;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ResQ dashboard</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- NAVBAR ITEMS GE-CENTREERD -->
            <ul class="navbar-nav mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <?php if (isset($_SESSION['user'])):
                    $user = is_string($_SESSION['user']) ? unserialize($_SESSION['user']) : $_SESSION['user'];
                ?>
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
