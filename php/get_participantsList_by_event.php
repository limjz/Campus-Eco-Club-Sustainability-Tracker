<?php
include 'db_connect.php'; 

// get the event id from the URL 
$event_id = $_GET['event_id']; 

//targeted on specific event id and put the participants into a list 
$sql = "SELECT * FROM volunteers WHERE assigned_event_id = $event_id"; 

$result = $conn->query($sql);

$participants =[];

while($row = $result->fetch_assoc())  { 
  $participants[] = $row;
}

echo json_encode($participants);

?>
