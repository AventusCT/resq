<?php

$servername = "localhost";

$username = "root";

$password = "";

$database = "projectfase2"; 


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {

die("Verbinding mislukt: " . $conn->connect_error);

}

?>