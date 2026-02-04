<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Debugging on

session_start();
header('Content-Type: application/json');

$db_path = __DIR__ . '/../../php/db_connect.php';
if (!file_exists($db_path)) {
    echo json_encode(['error' => 'db_connect.php missing']);
    exit();
}
include $db_path;

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]); 
    exit();
}

$organizer_id = $_SESSION['user_id'];


// allow 'open', 'ongoing' X 'close' and 'cancelled'
$sql = "SELECT event_id, title FROM events 
        WHERE organizer_id = ? 
        AND status IN ('open', 'ongoing') 
        ORDER BY event_date DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL Error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
?>