<?php
include __DIR__ . '/../db_connect.php';
header ('Content-Type: application/json');

// Get the text data sent from JavaScript 
$json_text = file_get_contents("php://input");
$data = json_decode($json_text, true);

if ($data === null){ 
    echo "Error: JSON decoding fail.";
    exit();
}

$title = $data["title"];
$date = $data["date"]; 
$time = $data["time"];
$venue = $data["venue"];
$description = $data["description"];

if (empty($title) || empty($date) || empty($time) || empty($venue) || empty($description)){
    echo json_encode([
        "status" => "error", 
        "message" => "Missing fields. Please fill in Title, Date, Time, Venue, and Description."
    ]);
    exit();

}

//SQL code 
$sql = "INSERT INTO proposals (title, event_date, event_time, venue, description, status, organizer_id) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
$stmt = $conn->prepare($sql);

//if cant put into the db 
if (!$stmt) { 
    echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
    exit();
}

// Get Organizer ID (the one logged in and using the acc) and make sure the correct EO propose the proposal
session_start();
$organizer_id = $_SESSION['user_id'] ?? 1; 

//sssssi: String, String, String, String,String, Int 
$stmt->bind_param("sssssi", $title, $date, $time, $venue, $description, $organizer_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Proposal submitted successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Insert Failed: " . $stmt->error]);
}

$conn->close();
?>