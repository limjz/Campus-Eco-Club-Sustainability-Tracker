<?php 
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');

$json_text = file_get_contents('php://input');
$data = json_decode($json_text , true);

$proposal_id = intval($data['proposal_id'] ?? 0); // force it to be INT 


if ($proposal_id == 0) {
  echo json_encode(['status'=> 'error','message'=> 'Invalid ID']);
  exit;
} 

$sql_get = "SELECT * FROM proposals WHERE proposal_id = ?"; 
$stmt = $conn ->prepare($sql_get);
$stmt->bind_param("i", $proposal_id);
$stmt->execute(); 
$result = $stmt -> get_result();
$proposal = $result ->fetch_assoc();


if (!$proposal) { //nothing save in the $proposal object 
  echo json_encode (["status"=> "error","message"=> "Proposal not found"]);
  exit;
}

$sql_event = "INSERT INTO events (title, description, event_date, event_time, venue, status, organizer_id) 
              VALUES (?, ?, ?, ?, ?, 'open', ?)";
$stmt_event = $conn -> prepare($sql_event);
$stmt_event->bind_param("sssssi", 
    $proposal['title'], 
    $proposal['description'], 
    $proposal['event_date'], 
    $proposal['event_time'], 
    $proposal['venue'], 
    $proposal['organizer_id']
); 

if ($stmt_event->execute()) {
    //Update Proposal Status to 'Approved'
    $conn->query("UPDATE proposals SET status = 'Approved' WHERE proposal_id = $proposal_id");
    
    echo json_encode(['status' => 'success', 'message' => 'Event Approved and Published!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create event: ' . $conn->error]);
}

?>