<?php
require __DIR__ . '/Includes/db.php';
require __DIR__ . '/classes/User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User($db);
    if ($user->login($_POST['email'], $_POST['password'])) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Ongeldige inloggegevens.";
    }
}

include __DIR__ . '/../Includes/header.php';
?>
<h2>Inloggen</h2>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo $_GET['success']; ?></div>
<?php endif; ?>
<form action="login.php" method="POST" class="form-container">
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Wachtwoord</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<?php include __DIR__ . '/../Includes/footer.php'; ?>