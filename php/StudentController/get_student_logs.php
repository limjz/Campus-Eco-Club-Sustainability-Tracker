<?php
session_start(); 
include __DIR__ . '/../db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]); // Return empty list if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

//join logs and event table tgt
$sql = "SELECT l.log_id, e.title, l.category, l.weight, l.status, l.submission_date, l.points_awarded 
        FROM logs l
        LEFT JOIN events e ON l.event_id = e.event_id
        WHERE l.user_id = $user_id
        ORDER BY l.submission_date DESC";

$result = $conn ->query($sql); 

if (!$result) {
    // DEBUG: If SQL fails, send the error to the browser console
    echo json_encode(["error" => $conn->error]);
    exit();
}

$logs = [];

while ($row = $result -> fetch_assoc())
{ 
  $logs[] = $row;
}


echo json_encode($logs);
$conn -> close();
?> 