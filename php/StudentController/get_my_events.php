<?php
// FILE: php/StudentController/get_my_events.php

include __DIR__ . '/../../php/db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$student_id = $_SESSION['user_id'];

//  LOGIC: Get events joined by THIS student
$sql = "SELECT e.title, e.event_date, e.venue, e.status, r.role 
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = ? 
        ORDER BY e.event_date DESC";

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