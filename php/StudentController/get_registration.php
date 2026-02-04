<?php
// FILE: php/StudentController/get_registration.php

error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

include __DIR__ . '/../../php/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$studentID = $_SESSION['user_id'];

// 1. Fetch Events user is registered for + Target Goal
// We use a PREPARED STATEMENT for security.
$sql = "SELECT e.event_id, e.title, e.target_goal 
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = ? 
        AND e.status IN ('open', 'ongoing')"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {
    $event_id = $row['event_id'];
    
    //  Calculate Remaining Weight for this specific event
    $sql_sum = "SELECT SUM(weight) as total FROM logs WHERE event_id = $event_id AND status = 'approved'";
    $res_sum = $conn->query($sql_sum);
    $row_sum = $res_sum->fetch_assoc();
    
    $collected = floatval($row_sum['total']);
    
    // default as 50 just like db 
    $goal = floatval($row['target_goal']);
    if($goal == 0) $goal = 50; 
    
    // Calculate remaining 
    $remaining = max(0, $goal - $collected);
    
    // add to the response
    $row['remaining'] = number_format($remaining, 2);
    $row['goal'] = $goal;
    
    $events[] = $row;
}

echo json_encode($events);
?>