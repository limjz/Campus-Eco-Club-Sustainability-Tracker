<?php
include 'db_connect.php' ;

$sql = "SELECT * FROM volunteers"; 
$result = $conn-> query($sql);

$volunteers = array();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $volunteers[] = $row;
  }
}

echo json_encode($volunteers);
$conn->close();
?>