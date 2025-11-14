<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';
include 'navbar.php';

$stmt = $pdo->query("
    SELECT o.id, u.username, o.total_price, o.status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bestellingenbeheer</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Bestellingenbeheer</h1>
    <a href="admin_panel.php">⬅ Terug</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Klant</th>
            <th>Totaal (€)</th>
            <th>Status</th>
            <th>Datum</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['username']) ?></td>
            <td><?= htmlspecialchars($order['total_price']) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td><?= htmlspecialchars($order['created_at']) ?></td>
            <td>
                <a href="edit_order.php?id=<?= $order['id'] ?>">✏️ Status wijzigen</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
