<?php

include __DIR__ . '/../db_connect.php';
session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'eo') {
    echo json_encode(["status" => "error", "message" => "Unauthorized."]);
    exit();
}


$json_text = file_get_contents("php://input");
$data = json_decode($json_text, true);
$organizer_id = $_SESSION['user_id'];

// 3. Extract Variables
$proposal_id = $data['proposal_id'];
$title = $data['title'];
$date = $data['event_date'];
$time = $data['event_time'];
$venue = $data['venue'];
$description = $data['description'];
$target_goal = isset($data['target_goal']) ? floatval($data['target_goal']) : 50.00;

// Update sql code
// 'AND organizer_id = ?'to ensure only edit the logged in eo proposal 
$sql = "UPDATE proposals 
        SET title = ?, event_date = ?, event_time = ?, venue = ?, description = ?, target_goal = ?
        WHERE proposal_id = ? AND organizer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssdii", $title, $date, $time, $venue, $description, $target_goal, $proposal_id, $organizer_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Proposal updated successfully!"]);
    } else {
        // If no rows changed, it means either ID was wrong OR data was exactly the same
        echo json_encode(["status" => "success", "message" => "No changes made or proposal not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Update Failed: " . $stmt->error]);
}
?>