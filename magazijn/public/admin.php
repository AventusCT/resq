
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>AdminPanel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Admin Panel</h1>
    <p>Welkom, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>

    <a href="admin_users.php"> Beheer gebruikers</a><br>
    <a href="admin_products.php"> Beheer producten</a><br>
    <a href="admin_orders.php"> Beheer bestellingen</a><br>
</div>
</body>
</html>
