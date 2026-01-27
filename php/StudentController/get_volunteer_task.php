<?php
session_start(); 
include __DIR__ . '/../db_connect.php';
header ('Component-Type: application/json'); 


$student_id = $_SESSION['user_id'];
$event_id = $_GET['event_id']; 

$sql = "SELECT * FROM tasks 
        WHERE student_id = $student_id
        AND event_id = $event_id";


$result = $conn->query($sql); 

$task = [];

while ($row = $result->fetch_array()) {
  $task[] = $row;
}


echo json_encode($task);

$conn->close();

?>