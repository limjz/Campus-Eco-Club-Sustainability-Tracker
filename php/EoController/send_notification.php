<?php
include __DIR__ . '/../db_connect.php';
header ('Content-Type: application/json');


$json_text = file_get_contents("php://input");
$data = json_decode($json_text, true);

$event_id = intval ($data['event_id'] ?? 0);
$title = $conn ->real_escape_string($data ['title'] ?? 'Announcement ');
$message = $conn ->real_escape_string($data ['message'] ?? '');

// validate the event  
if ($event_id === 0 || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing Event or Message']);
    exit();
}

$sql_users = "SELECT user_id FROM registrations WHERE event_id = $event_id";
$result = $conn->query($sql_users);

if ($result->num_rows > 0) {
  $stmt = $conn -> prepare("INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, ?, ?, 0)");

  //Checking if the database accept the command
  if ($stmt === false) {
      echo json_encode(['status' => 'error', 'message' => 'Database SQL Error: ' . $conn->error]);
      exit();
  }


  $count = 0; 
  while ($row = $result->fetch_assoc()) {
  
      $user_id = $row ['user_id'];

      // user_id (int), title (String), message (String)
      $stmt->bind_param('iss', $user_id,$title,$message);
      if ($stmt->execute());
      { 
        $count++;
      }

  } 
  echo json_encode(['status' => 'success', 'message' => "Sent to $count students!"]);

} 
else 
{ 
  echo json_encode(['status' => 'error', 'message' => 'No students found for this event.']);
}

?> 