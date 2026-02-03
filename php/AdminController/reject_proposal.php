<?php
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');

$json_text = file_get_contents('php://input');
$data = json_decode($json_text , true);

$proposal_id = intval($data['proposal_id'] ?? 0);

if ($proposal_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit();
}

// Just update status to 'Rejected'
$sql = "UPDATE proposals SET status = 'Rejected' WHERE proposal_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $proposal_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Proposal Rejected.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>