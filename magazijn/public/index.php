<?php
session_start();

$gebruikersnaam = $_SESSION['gebruikersnaam'] ?? 'Gebruiker';

?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hoofdpagina</title>
    <link rel="stylesheet" href="./css/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
</head>
<body>
<div class="container">
    <h1>Welkom, <?= htmlspecialchars($gebruikersnaam) ?>!</h1>
    <p>Kies een optie:</p>

    
    </div>
   <div class="nav-links">
    <a href="inventarisbeheer.php"><i class="fas fa-shopping-basket"></i> Inventarisbeheer</a>
    <a href="magazijnbeheer.php"><i class="fas fa-list"></i> Magazijnbeheer</a>
    <a href="inloggen.php"><i class="fas fa-star"></i> Inloggen</a>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <a href="productbeheer.php"><i class="fas fa-cogs"></i> Productbeheer</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['gebruikersnaam'])): ?>

        <a href="uitloggen.php"><i class="fas fa-sign-out-alt"></i> Uitloggen</a>
    <?php else: ?>

        <a href="registratie.php"><i class="fas fa-user-plus"></i> Registreren</a>

    <?php endif; ?>
</div>

</body>
</html>
