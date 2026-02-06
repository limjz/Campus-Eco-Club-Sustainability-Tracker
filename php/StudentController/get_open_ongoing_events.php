<?php
// FILE: php/StudentController/get_open_events.php

include __DIR__ . '/../../php/db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$student_id = $_SESSION['user_id'];

//  LOGIC: Get events where the ID is NOT IN my registration list
$sql = "SELECT e.event_id, e.title, e.event_date, e.venue 
        FROM events e
        WHERE e.status IN  ('open', 'ongoing') 
        AND e.event_id NOT IN (
            SELECT event_id FROM registrations WHERE user_id = ?
        )
        ORDER BY e.event_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
?>