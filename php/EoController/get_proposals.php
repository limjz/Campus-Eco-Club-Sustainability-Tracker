<?php
include __DIR__ . '/../db_connect.php';
header ('Content-Type: application/json');

session_start(); 
$organizer_id = $_SESSION['user_id'] ?? 0;

if ($organizer_id === 0) {
    echo json_encode([]);
    exit();  
}


//SQL Command to get everything
$sql = "SELECT proposal_id, title, event_date, event_time, venue, status, description 
        FROM proposals 
        WHERE organizer_id = ?"; 


$stmt = $conn->prepare($sql);
$stmt -> bind_param("i", $organizer_id);
$stmt -> execute();
$result = $stmt->get_result();

$proposals = array();

// Loop through the results and add to array
while ($row = $result->fetch_assoc()) {

    // If time is 14:30:00, this makes it 14:30
    $formatted_time = $row['event_time'] ? date('H:i', strtotime($row['event_time'])) : 'N/A';

    $proposals[] = [
        'id' => $row['proposal_id'],
        'title' => $row['title'],
        'event_date' => $row['event_date'],
    
        'event_time' => $formatted_time, 
        
        'venue' => $row['venue'],
        'status' => ucfirst($row['status']), //capitalize the first letter 
        'description' => $row['description'] ?? "No description provided."
    ];
}

// Send back as JSON (JavaScript Object Notation)
echo json_encode($proposals);

$conn->close();
?>