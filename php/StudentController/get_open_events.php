<?php
include __DIR__ . '/../db_connect.php';
header('Content-Type: application/json');

$sql = "SELECT event_id, title, event_date, venue 
        FROM events 
        WHERE status IN  ('open', 'ongoing') 
        ORDER BY event_date ASC";
$result = $conn->query($sql);

$event = []; //init a empty array

if ($result->num_rows > 0) //found at least one event in the table
{
  while ($row = $result->fetch_assoc()) //fetch all the data we want and save in the variable $row
  { 
    $event[] = $row; //put into the array


  }
}

echo json_encode($event);
$conn->close();

?>

