<?php
session_start(); 
include __DIR__ . '/../db_connect.php';
header ('Component-Type: application/json'); 


$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id']; 

//task directly from the registrations table 
// get task and also their role, display on the My Tasks
$sql = "SELECT e.title, r.role, r.task_or_instruction AS task_description
        FROM registrations r 
        JOIN events e ON r.event_id = e.event_id 
        WHERE r.user_id = ? AND r.event_id = ? AND r.task_or_instruction IS NOT NULL";

$stmt = $conn->prepare($sql); 
$stmt ->bind_param("ii", $user_id, $event_id);
$stmt -> execute();
$result = $stmt->get_result();


$tasks = [];

while ($row = $result->fetch_array()) {
  $row['role'] = ucfirst($row['role']); 
  $tasks[] = $row; //place the object into "task" array
}


echo json_encode($tasks);

$conn->close();
$stmt->close();
?>