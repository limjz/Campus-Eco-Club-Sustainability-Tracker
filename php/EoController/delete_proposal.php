<?php

include __DIR__ . '/../db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'eo') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$json_text = file_get_contents("php://input");
$data = json_decode($json_text, true);

if (!isset($data['proposal_id'])) {
    echo json_encode(["status" => "error", "message" => "No Proposal ID provided."]);
    exit();
}

$proposal_id = $data['proposal_id'];
$organizer_id = $_SESSION['user_id'];

// check the proposal and ensure is belongs to the eo and the status is not APPROVED 
// eo cannot delete the event if the event is registered by the students 
$sql_check = "SELECT status FROM proposals WHERE proposal_id = ? AND organizer_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $proposal_id, $organizer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Proposal not found"]);
    exit();
}

$row = $result->fetch_assoc();
if ($row['status'] === 'approved') {
    echo json_encode(["status" => "error", "message" => "Cannot delete an Approved event."]);
    exit();
}

//Delete sql code
$sql_delete = "DELETE FROM proposals WHERE proposal_id = ?";
$del_stmt = $conn->prepare($sql_delete);
$del_stmt->bind_param("i", $proposal_id);

if ($del_stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Proposal deleted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
}
?>