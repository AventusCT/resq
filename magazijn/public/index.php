<?php
session_start();
include 'includes/header.php';

// Username ophalen uit session
$username = $_SESSION['username'] ?? 'Gast';
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hoofdpagina</title>
    <link rel="stylesheet" href="css/inventarisbeheer.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <div class="container">
        <h1>Welkom, <?= htmlspecialchars($username) ?>!</h1>
        <p>Kies een optie:</p>

        <div class="NavCards">
            <a href="inventarisbeheer.php" class="NavCard">
                <i class="fas fa-boxes fa-3x"></i>
                <h3>Inventaris</h3>
            </a>

            <a href="warehouse.php" class="NavCard">
                <i class="fas fa-warehouse fa-3x"></i>
                <h3>Magazijn</h3>
            </a>

            <a href="reservation.php" class="NavCard">
                <i class="fas fa-clipboard-list fa-3x"></i>
                <h3>Bestellingen</h3>
            </a>
        </div>
</body>

</html>