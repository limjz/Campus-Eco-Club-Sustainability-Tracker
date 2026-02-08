<?php
// FILE: php/EOController/delete_proposal.php

include __DIR__ . '/../../php/db_connect.php';
session_start();
header('Content-Type: application/json');

// 1. Check Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'eo') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

// 2. Get Input
$json_text = file_get_contents("php://input");
$data = json_decode($json_text, true);

$proposal_id = isset($data['proposal_id']) ? intval($data['proposal_id']) : 0;
$organizer_id = $_SESSION['user_id'];

// --- DEBUGGING: UNCOMMENT THIS IF YOU ARE STUCK ---
// This writes to your server error log (e.g., php_error_log)
//error_log("Deleting Proposal ID: " . $proposal_id . " for User ID: " . $organizer_id);

if ($proposal_id === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid ID sent. Check your JS!"]);
    exit();
}

// 3. Security Check: Does this proposal exist AND belong to me?
$sql_check = "SELECT status FROM proposals WHERE proposal_id = ? AND organizer_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $proposal_id, $organizer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // TEACHING MOMENT: If we get here, either the ID is wrong OR the user doesn't own it.
    echo json_encode(["status" => "error", "message" => "Proposal not found (or you don't have permission)."]);
    exit();
}

$row = $result->fetch_assoc();

// 4. Case-Insensitive Status Check (FIXED)
// We use strtolower() so 'Approved', 'approved', and 'APPROVED' are all caught.
if (strtolower($row['status']) === 'approved') {
    echo json_encode(["status" => "error", "message" => "Cannot delete an Approved event."]);
    exit();
}

// 5. Delete Logic
$sql_delete = "DELETE FROM proposals WHERE proposal_id = ?";
$del_stmt = $conn->prepare($sql_delete);
$del_stmt->bind_param("i", $proposal_id);

if ($del_stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Proposal deleted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
}
?>