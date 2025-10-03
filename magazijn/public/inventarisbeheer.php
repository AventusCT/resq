<?php
// Database connectie
$host = "localhost"; 
$user = "root";      // pas aan naar jouw gebruikersnaam
$pass = "";          // pas aan naar jouw wachtwoord
$dbname = "inventaris_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connectie
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// Query om categorieën op te halen
$sql = "SELECT id, naam FROM categories";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Inventarisbeheer</title>
  <link rel="stylesheet" href="style.css">
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
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Categorie</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['naam']) ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="2">Geen categorieën gevonden</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <aside class="details">
          <h3>Itemdetails</h3>
          <p><b>Categorie:</b> Selecteer een categorie</p>
        </aside>
      </section>
    </main>

  </div>
</body>
</html>
