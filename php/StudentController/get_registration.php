<?php
require '../db_connect.php';
session_start();

header ('Content-Type: application/json');

$studentID = $_SESSION['user_id'];

// Join registrations table with events table to get events titles
$sql = "SELECT e.event_id, e.title 
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = '$studentID'";

$result = $conn->query($sql);

$events =  [];
while($row = $result->fetch_assoc()) {
    $events[] = $row; // seperate the object in the array then store each independently 
}

echo json_encode($events);
$conn->close();
?>