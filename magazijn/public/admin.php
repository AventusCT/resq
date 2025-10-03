<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';
include 'navbar.php';
?>
<html>

<head>
    <title>AdminPanel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
</head>

<body>
    <div class="container">
        <h1>Admin Panel</h1>

        <p>Welkom, admin!</p>
        <a href="admin_users.php">Beheer gebruikers</a><br>
        <a href="admin_products.php">Beheer producten</a><br>
        <a href="admin_orders.php">Beheer bestellingen</a><br>
    </div>
</body>

</html>