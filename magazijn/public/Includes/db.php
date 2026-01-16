<?php
/**
 * Database Connection for ResQ
 * Uses PDO for prepared statements and security
 */

$servername = "localhost";
$username = "root";
$password = "";
$database = "resq_db";

try {
    $db = new PDO(
        "mysql:host=$servername;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database verbinding mislukt: " . $e->getMessage());
}

// For backward compatibility with mysqli code (if any)
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>