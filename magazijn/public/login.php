<?php
session_start();
if (isset($_SESSION['gebruiker_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

$msg = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'nog niet ingelogd') {
        $msg = "Je moet eerst inloggen om deze pagina te bekijken.";
    } else {
        $msg = htmlspecialchars($_GET['msg']);
    }
}
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/inventarisbeheer.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

                <h3 class="mb-4 text-center">Inloggen</h3>

                <?php if ($msg): ?>
                    <div class="alert alert-warning"><?= $msg ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="verwerk_inlog.php" novalidate>
                    <div class="form-group">
                        <label for="email">E-mailadres</label>
                        <input type="email" id="email" name="email" class="form-control" required autofocus>
                    </div>

                    
                    <div class="form-group">
                        <label for="password">Wachtwoord</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
                </form>

                <p class="mt-3 text-center">
                    Nog geen account? <a href="registratie.php">Registreer hier</a>
                    <br>
                    terug naar hoofdmenu? <a href="index.php">Hoofdmenu</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
