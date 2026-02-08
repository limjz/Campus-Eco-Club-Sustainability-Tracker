<?php
include __DIR__ . '/../db_connect.php'; 
header ('Content-Type: application/json');

$event_id = intval($_GET['event_id'] ?? 0);

if ($event_id === 0) { 
  echo json_encode([]); 
  return;   
}


$sql = "SELECT r.registration_id, r.user_id, u.username, r.role, r.task_or_instruction
        FROM registrations r 
        JOIN users u ON r.user_id = u.user_id 
        WHERE r.event_id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];

while($row = $result->fetch_assoc()) { 
  $students[] = [
    'registration_id' => $row['registration_id'], 
    'user_id' => $row['user_id'],
    'username' => $row['username'], // Explicitly named for JS
    'role' => ucfirst($row['role']),
    'current_task' => $row['task_or_instruction'] ?? '' 
  ];
}

echo json_encode($students);
?>