<?php
// FILE: php/StudentController/get_volunteer_data.php

error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

include __DIR__ . '/../../php/db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['event_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit();
}

$student_id = $_SESSION['user_id'];
$event_id = intval($_GET['event_id']);

// 1. SECURITY: Check if user is a Volunteer for this specific event
$check_sql = "SELECT role FROM registrations WHERE user_id = ? AND event_id = ? AND LOWER(role) = 'Volunteer'";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $student_id, $event_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Access Denied: You are not a volunteer for this event.']);
    exit();
}

// 2. FETCH EVENT INFO
$sql_event = "SELECT target_goal, title FROM events WHERE event_id = ?";
$stmt_e = $conn->prepare($sql_event);
$stmt_e->bind_param("i", $event_id);
$stmt_e->execute();
$event_info = $stmt_e->get_result()->fetch_assoc();

// 3. FETCH PROGRESS
$sql_sum = "SELECT SUM(weight) as total FROM logs WHERE event_id = ? AND status = 'approved'";
$stmt_s = $conn->prepare($sql_sum);
$stmt_s->bind_param("i", $event_id);
$stmt_s->execute();
$sum_info = $stmt_s->get_result()->fetch_assoc();

$goal = floatval($event_info['target_goal']);
$collected = floatval($sum_info['total'] ?? 0);
$remaining = max(0, $goal - $collected);

// 4. FETCH PARTICIPANTS (FIXED QUERY)
// We check for 'participant' OR 'Participant' to be safe
$sql_part = "SELECT u.username, r.role
             FROM registrations r 
             JOIN users u ON r.user_id = u.user_id 
             WHERE r.event_id = ? 
             ORDER BY r.role DESC, u.username ASC";

$stmt_p = $conn->prepare($sql_part);
$stmt_p->bind_param("i", $event_id);
$stmt_p->execute();
$participants_result = $stmt_p->get_result();

$participants = [];
while ($row = $participants_result->fetch_assoc()) {
    $participants[] = $row;
}

// 5. FETCH LOGS
$sql_logs = "SELECT u.username, l.category, l.weight, l.status, l.photo_evidence, l.submission_date 
             FROM logs l 
             JOIN users u ON l.user_id = u.user_id 
             WHERE l.event_id = ? 
             ORDER BY l.submission_date DESC";
$stmt_l = $conn->prepare($sql_logs);
$stmt_l->bind_param("i", $event_id);
$stmt_l->execute();
$logs_result = $stmt_l->get_result();
$logs = [];
while ($row = $logs_result->fetch_assoc()) {
    $logs[] = $row;
}

// 6. RETURN JSON
echo json_encode([
    'status' => 'success',
    'debug_info' => [                   // <--- CHECK THIS IN CONSOLE
        'checking_event_id' => $event_id,
        'event_title' => $event_info['title']
    ],
    'progress' => [
        'goal' => $goal,
        'collected' => $collected,
        'remaining' => number_format($remaining, 2)
    ],
    'participants' => $participants,
    'logs' => $logs
]);
?>