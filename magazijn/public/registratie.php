<?php
/**
 * Registration Page for ResQ
 * Admin-only registration (employees register through admin panel)
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';

// Check if registration is allowed (admin only or open registration)
$allowRegistration = true; // Set to false to make admin-only

if (!$allowRegistration) {
    User::requireAdmin();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = isset($_POST['role']) && User::isAdmin() ? $_POST['role'] : 'employee';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Vul alle velden in.";
    } elseif ($password !== $confirmPassword) {
        $error = "Wachtwoorden komen niet overeen.";
    } elseif (strlen($password) < 6) {
        $error = "Wachtwoord moet minimaal 6 tekens lang zijn.";
    } else {
        $user = new User($db);
        if ($user->register($username, $email, $password, $role)) {
            $success = "Account succesvol aangemaakt! U kunt nu inloggen.";
            // Clear form
            $_POST = [];
        } else {
            $error = "Registratie mislukt. E-mailadres of gebruikersnaam bestaat mogelijk al.";
        }
    }
}

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="register-card">
        <div class="card-header">
            <h2>Account Aanmaken</h2>
            <p>Maak een nieuw ResQ account aan</p>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="registratie.php" class="register-form">
                <div class="form-group">
                    <label for="username">Gebruikersnaam</label>
                    <input type="text" name="username" id="username" class="form-control" 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" id="password" class="form-control" 
                           minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Bevestig Wachtwoord</label>
                    <input type="password" name="confirm_password" id="confirm_password" 
                           class="form-control" minlength="6" required>
                </div>
                <?php if (User::isAdmin()): ?>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select name="role" id="role" class="form-control">
                            <option value="employee">Medewerker</option>
                            <option value="admin">Beheerder</option>
                        </select>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Registreren</button>
            </form>
        </div>
        <div class="card-footer">
            <p>Al een account? <a href="login.php">Log hier in</a></p>
        </div>
    </div>
</div>
<?php include __DIR__ . '/Includes/footer.php'; ?>
