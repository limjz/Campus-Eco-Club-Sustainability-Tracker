<?php 
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');


$proposal_id = 0;

if (isset($_POST['proposal_id'])) {
    $proposal_id = intval($_POST['proposal_id']); //JS sends $_POST not JSON
} else {

    $data = json_decode(file_get_contents('php://input'), true);
    $proposal_id = intval($data['proposal_id'] ?? 0);
}

if ($proposal_id == 0) {
  echo json_encode(['status'=> 'error','message'=> 'Invalid ID received']);
  exit;
} 

// Fetch the Proposal Data
$sql_get = "SELECT * FROM proposals WHERE proposal_id = ?"; 
$stmt = $conn ->prepare($sql_get);
$stmt->bind_param("i", $proposal_id);
$stmt->execute(); 
$result = $stmt -> get_result();
$proposal = $result ->fetch_assoc();

if (!$proposal) { 
  echo json_encode (["status"=> "error","message"=> "Proposal not found"]);
  exit;
}


// If proposal has no goal, default to 50
$target_goal = isset($proposal['target_goal']) ? floatval($proposal['target_goal']) : 50.00;

// Added 'target_goal' to the list
$sql_event = "INSERT INTO events (title, description, event_date, event_time, venue, status, organizer_id, target_goal) 
              VALUES (?, ?, ?, ?, ?, 'open', ?, ?)";
              
$stmt_event = $conn -> prepare($sql_event);

// "sssssid" -> String, String, String, String, String, Int, Decimal
$stmt_event->bind_param("sssssid", 
    $proposal['title'], 
    $proposal['description'], 
    $proposal['event_date'], 
    $proposal['event_time'], 
    $proposal['venue'], 
    $proposal['organizer_id'],
    $target_goal 
); 

if ($stmt_event->execute()) {

    $conn->query("UPDATE proposals SET status = 'Approved' WHERE proposal_id = $proposal_id");
    
    echo json_encode(['status' => 'success', 'message' => 'Event Approved and Published!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create event: ' . $conn->error]);
}
?>