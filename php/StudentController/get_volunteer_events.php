<?php

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

// ✅ SPECIFIC QUERY: Only fetch events where role = 'Volunteer'
$sql = "SELECT e.event_id, e.title 
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = ? 
        AND r.role = 'Volunteer'
        AND e.status IN ('open', 'ongoing')"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
?>