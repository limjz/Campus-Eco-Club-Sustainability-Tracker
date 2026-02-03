<?php 
include __DIR__ . '/../db_connect.php'; 
header ('Content-Type: application/json');

$json_text = file_get_contents("php://input");
$data = json_decode($json_text, true);

// Get the specific Registration ID
$registration_id = intval($data['registration_id'] ?? 0);
$task_desc = trim($data['task_description'] ?? '');

if ($registration_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Error: Missing Registration ID']);
    exit();
}

// Update the registrations table directly
$sql = "UPDATE registrations SET task_or_instruction = ? WHERE registration_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $task_desc, $registration_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Task updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
}
?>