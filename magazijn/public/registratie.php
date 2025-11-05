<?php
include 'db_connect.php';
include 'verwerking/verwerking_registratie.php';
include 'navbar.php';
if (isset($_SESSION['user'])) {
    header("Location: account.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Maak een account</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="verwerk_registratie.php">
                    <div class="form-group">
                        <label>Gebruikersnaam</label>
                        <input type="text" name="gebruikersnaam" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Wachtwoord</label>
                        <input type="password" name="wachtwoord" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Bevestig wachtwoord</label>
                        <input type="password" name="bevestig_wachtwoord" class="form-control" required>
                    </div>
                    <button type="submit" class="formsubmit">Registreren</button>
                </form>
            </div>
        </div>
        <p class="text-center mt-3">
            Al een account? <a href="login.php">Log in</a>
        </p>
    </div>
</body>

</html>