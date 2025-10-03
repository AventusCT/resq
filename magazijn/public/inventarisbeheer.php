<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';
include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Inventarisbeheer</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">

    <header>
      <h2>Inventarisbeheer</h2>
      <button class="logout-btn">Uitloggen</button>
    </header>

    <main>
      <section class="controls">
        <button>Toevoegen</button>
        <button>Wijzigen</button>
        <button>Verwijderen</button>
      </section>

      <section class="content">
        <table>
          <thead>
            <tr>
              <th>Naam</th>
              <th>Type</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Item naam</td>
              <td>Item type</td>
              <td>Item status</td>
            </tr>
            <tr>
              <td>Item naam</td>
              <td>Item type</td>
              <td>Item status</td>
            </tr>
            <tr>
              <td>Item naam</td>
              <td>Item type</td>
              <td>Item status</td>
            </tr>
            <tr>
              <td>Item naam</td>
              <td>Item type</td>
              <td>Item status</td>
            </tr>
          </tbody>
        </table>

        <aside class="details">
          <h3>Itemdetails</h3>
          <p><b>Naam:</b> Item naam</p>
          <p><b>Type:</b> Item type</p>
          <p><b>Status:</b> Item status</p>
          <button>QR-code genereren</button>
        </aside>
      </section>
    </main>

  </div>
</body>
</html>
