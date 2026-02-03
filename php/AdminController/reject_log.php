<?php
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');

// Log ID (Universal Receiver)
$log_id = 0;
if (isset($_POST['log_id'])) {
    $log_id = intval($_POST['log_id']);
} else {
    $data = json_decode(file_get_contents("php://input"), true);
    $log_id = intval($data['log_id'] ?? 0);
}

if ($log_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Log ID']);
    exit();
}

// just update the status of the logs, it will still appear in the student log history 
$sql = "UPDATE logs SET status = 'rejected' WHERE log_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $log_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Log has been rejected.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $conn->error]);
}
?>