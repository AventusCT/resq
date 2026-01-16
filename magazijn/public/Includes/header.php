<?php
/**
 * Header Include for ResQ
 * Includes navigation and session management
 */
if (!isset($_SESSION)) {
    session_start();
}

// Check session timeout
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../classes/User.php';
    if (!User::checkSessionTimeout()) {
        header("Location: login.php?timeout=1");
        exit();
    }
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResQ - Inventory Management</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/png" href="assets/icon.png">
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/icon.png" alt="ResQ" class="navbar-logo">
            <span>ResQ</span>
        </a>
        <button class="navbar-toggler" type="button" id="navbarToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="navbar-menu" id="navbarMenu">
            <ul class="navbar-nav">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inventarisbeheer.php">Inventaris</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reservation.php">Reserveren</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warehouse.php">Magazijn</a>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Beheer</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <span class="nav-user">Welkom, <?= htmlspecialchars($username) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Uitloggen</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Inloggen</a>
                    </li>
                    <?php if (file_exists(__DIR__ . '/../registratie.php')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="registratie.php">Registreren</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="main-content">
