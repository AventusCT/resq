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
    <link rel="stylesheet" href="./css/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <div class="container">
        <h1>Welkom, <?= htmlspecialchars($username) ?>!</h1>
        <p>Kies een optie:</p>
    </div>
</body>
</html>
