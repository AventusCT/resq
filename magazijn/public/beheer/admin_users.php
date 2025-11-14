\<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';
include 'navbar.php';

$stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruikersbeheer</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Gebruikersbeheer</h1>
    <a href="admin_panel.php">⬅ Terug</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Gebruikersnaam</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $user['id'] ?>">✏️ Bewerken</a> |
                <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">🗑️ Verwijderen</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
