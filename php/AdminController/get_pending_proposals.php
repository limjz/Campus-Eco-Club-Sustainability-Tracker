<?php 
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');


if (!isset($conn) || $conn->connect_error) {
    echo json_encode([]); // Return empty array instead of crashing
    exit();
}


// join users & proposals table to get the eo name and the proposal in 'pending' status
$sql = "SELECT p.proposal_id, p.title, p.event_date, p.description, u.username as organizer_name
        FROM proposals p 
        JOIN users u ON p.organizer_id = u.user_id
        WHERE p.status = 'pending'"; 

$result = $conn -> query($sql);
$proposals = []; 

while ($row = $result -> fetch_assoc()){ 
  $proposals[] = $row;
}

echo json_encode($proposals);

?>