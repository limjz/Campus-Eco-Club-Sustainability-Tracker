<?php
include 'db_connect.php'; 

$sql = "SELECT * FROM events"; 
$result = $conn->query($sql); 
$events = []; 

while ($row = $result->fetch_assoc()) {
  $events[] = $row;
}

echo json_encode($events);
?>