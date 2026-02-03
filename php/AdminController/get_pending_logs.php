<?php

include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');

if (!isset($conn) || $conn->connect_error) {
    echo json_encode([]); 
    exit();
}

// join logs, users & events table 
$sql = "SELECT l.log_id, l.weight, l.photo_evidence, 
               u.username as student_name, e.title as event_title
        FROM logs l
        JOIN users u ON l.user_id = u.user_id
        JOIN events e ON l.event_id = e.event_id
        WHERE LOWER(l.status) = 'pending'";

$result = $conn->query($sql);
$logs = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

echo json_encode($logs);
?>