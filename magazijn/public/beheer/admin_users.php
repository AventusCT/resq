<?php
/**
 * Admin Users Management Page
 */
require_once __DIR__ . '/../Includes/db.php';
require_once __DIR__ . '/../classes/User.php';

User::requireAdmin();

$user = new User($db);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_role') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? 'employee';
        
        if ($user->updateUserRole($userId, $role)) {
            $success = "Rol succesvol bijgewerkt!";
        } else {
            $error = "Fout bij bijwerken van rol.";
        }
    } elseif ($action === 'delete') {
        $userId = (int)($_POST['user_id'] ?? 0);
        
        if ($user->deleteUser($userId)) {
            $success = "Gebruiker succesvol verwijderd!";
        } else {
            $error = "Fout bij verwijderen van gebruiker.";
        }
    }
}

$allUsers = $user->getAllUsers();

include __DIR__ . '/../Includes/header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Gebruikersbeheer</h1>
        <a href="../admin.php" class="btn btn-secondary">← Terug</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Alle Gebruikers</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gebruikersnaam</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Aangemaakt</th>
                            <th>Laatste Activiteit</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $usr): ?>
                            <tr>
                                <td><?= $usr['id'] ?></td>
                                <td><strong><?= htmlspecialchars($usr['username']) ?></strong></td>
                                <td><?= htmlspecialchars($usr['email']) ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="update_role">
                                        <input type="hidden" name="user_id" value="<?= $usr['id'] ?>">
                                        <select name="role" class="form-control form-control-sm" 
                                                onchange="this.form.submit()" 
                                                <?= $usr['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                            <option value="employee" <?= $usr['role'] === 'employee' ? 'selected' : '' ?>>Medewerker</option>
                                            <option value="admin" <?= $usr['role'] === 'admin' ? 'selected' : '' ?>>Beheerder</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?= date('d-m-Y', strtotime($usr['created_at'])) ?></td>
                                <td><?= $usr['last_activity'] ? date('d-m-Y H:i', strtotime($usr['last_activity'])) : 'N/A' ?></td>
                                <td>
                                    <?php if ($usr['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $usr['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Verwijderen</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Huidige gebruiker</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../Includes/footer.php'; ?>
