<<<<<<< HEAD
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Portfolio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="inventarisbeheer.php"></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"></a>
                        </li>
=======
<nav class="navbar">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
        <a class="navbar-brand" href="account.php" style="margin-right: 16px;">Webwinkel L.D.H</a>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 24px;">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php $user = unserialize($_SESSION['user']); ?>
                    <a class="nav-link" href="inventarisbeheer.php" style="margin-right: 16px;">Inventarisbeheer</a>
                    <a class="nav-link" href="warehouse.php" style="margin-right: 16px;">Warenhuisplaatsing</a>
                    <a class="nav-link" href="reservation.php" style="margin-right: 16px;">Status Veranderen</a>
                    <a class="nav-link" href="index.php" style="margin-right: 16px;">Index</a>
                    <?php if ($user->getRole() === 'admin'): ?>
                        <a class="nav-link" href="admin.php" style="margin-right: 16px;">AdminPanel</a>
>>>>>>> parent of e1b08e1 (sex)
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php" style="margin-right: 16px;">Uitloggen</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if (isset($_SESSION['user'])): ?>
            <?php $user = unserialize($_SESSION['user']); ?>
            <span class="navbar-text text-light" style="margin-left: 16px;">
                Welkom, <?= htmlspecialchars($user->getName()); ?>
            </span>
        <?php endif; ?>
    </div>
</nav>