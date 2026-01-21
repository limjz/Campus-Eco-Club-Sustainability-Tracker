<?php
require 'db_connect.php';
session_start();

$studentID = $_SESSION['user_id'];

// Join Registration with Event to get Event Titles
$sql = "SELECT R.eventID, R.role, E.title 
        FROM Registration R
        JOIN Event E ON R.eventID = E.eventID
        WHERE R.studentID = '$studentID'";

$result = $conn->query($sql);

$registrations = array();
while($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}

echo json_encode($registrations);
$conn->close();
?>