<?php
include '../db_connect.php';
// Verwerking van registratiegegevens
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $email = $_POST['email'];
    $wachtwoord = $_POST['wachtwoord'];
    $bevestig_wachtwoord = $_POST['bevestig_wachtwoord'];

    // Validatie
    if ($wachtwoord !== $bevestig_wachtwoord) {
        die("Wachtwoorden komen niet overeen.");
    }

    // Wachtwoord hashen
    $hashed_password = password_hash($wachtwoord, PASSWORD_BCRYPT);

    // Gebruiker toevoegen aan de database
    $stmt = $pdo->prepare("INSERT INTO gebruikers (gebruikersnaam, email, wachtwoord) VALUES (:gebruikersnaam, :email, :wachtwoord)");
    $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':wachtwoord', $hashed_password);

    try {
        $stmt->execute();
        echo "Registratie succesvol! U kunt nu inloggen.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            die("Gebruikersnaam of e-mail is al in gebruik.");
        } else {
            die("Fout bij registratie: " . $e->getMessage());
        }
    }
}
