<?php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default is empty
$dbname = "ecoclub_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>