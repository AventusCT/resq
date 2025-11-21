<?php
session_start();

include 'includes/header.php';
?>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <script src="js/script.js"></script>
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <form id="loginForm" method="POST" action="authenticate.php">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Inloggen</button>
        </form>
        <div id="message"></div>
    </div>
</body>

</html>