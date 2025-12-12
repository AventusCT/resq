<?php
/**
 * Login Page for ResQ
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';

$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Vul alle velden in.";
    } else {
        $user = new User($db);
        if ($user->login($email, $password)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Ongeldige inloggegevens.";
        }
    }
}

// Check for timeout message
if (isset($_GET['timeout'])) {
    $error = "Uw sessie is verlopen. Log opnieuw in.";
}

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="login-card">
        <div class="card-header">
            <h2>Inloggen</h2>
            <p>Log in op uw ResQ account</p>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
            </form>
        </div>
        <div class="card-footer">
            <p>Geen account? <a href="registratie.php">Registreer hier</a></p>
        </div>
    </div>
</div>
<?php include __DIR__ . '/Includes/footer.php'; ?>
