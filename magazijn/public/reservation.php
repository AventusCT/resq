<?php
session_start();

include 'includes/header.php';
?>
<html>

<head>
    <title>Status veranderen</title>
    <link rel="stylesheet" href="css/reservation.css">
    <script src="magazijn/public/js/script.js"></script>
    <script src="magazijn/public/js/reservation.js"></script>
</head>

<body>
    <div class="container">
        <h1>Status veranderen</h1>
        <form id="reservationForm">

            <p>Je bent momenteel de volgende bestelling aan het bewerken: <span id="currentReservationId"></span></p>
            <label for="reservationId">Bestelling ID:</label>
            <input type="text" id="reservationId" name="reservationId" readonly>

            <p>Datum van reservatie: <span id="reservationDate"></span></p>
            <p>Huidige status: <span id="currentStatus"></span></p
                <label for="status">Status wisselen:</label>
            <select id="status" name="status">
                <option value="reserveren">Reserveren</option>
                <option value="ophalen">Ophalen</option>
                <option value="retour">Terughalen</option>
                <option value="verloren">Verloren</option>
            </select>
            <button class="statusproduct">Update productstatus</button>
        </form>
        <div id="message"></div>
    </div>
</body>

</html>