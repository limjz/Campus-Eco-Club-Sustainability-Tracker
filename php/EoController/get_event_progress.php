<?php
// FILE: php/EoController/get_event_progress.php

error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

include __DIR__ . '/../../php/db_connect.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'eo') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$organizer_id = $_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id === 0) {
    echo json_encode(['error' => 'Invalid Event ID']);
    exit();
}

// target the specific event organized by the eo 
$sql = "SELECT target_goal, title FROM events WHERE event_id = ? AND organizer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $event_id, $organizer_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['error' => 'Event not found or access denied']);
    exit();
}

$event = $res->fetch_assoc();
$goal = floatval($event['target_goal']);
if ($goal == 0) $goal = 50; // Safety default

// get the approved and sum all the weight for progress check
$sql_sum = "SELECT SUM(weight) as total FROM logs WHERE event_id = ? AND status = 'approved'";
$stmt_sum = $conn->prepare($sql_sum);
$stmt_sum->bind_param("i", $event_id);
$stmt_sum->execute();
$res_sum = $stmt_sum->get_result();
$row_sum = $res_sum->fetch_assoc();

$collected = floatval($row_sum['total']);
$remaining = max(0, $goal - $collected);

//return data
echo json_encode([
    'status' => 'success', //api status // not the event status
    'title' => $event['title'],
    'goal' => $goal,
    'collected' => number_format($collected, 2),
    'remaining' => number_format($remaining, 2)
]);

$stmt->close();
$conn->close();
?>