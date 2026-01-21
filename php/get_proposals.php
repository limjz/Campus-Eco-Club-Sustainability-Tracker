<?php
include 'db_connect.php';

//SQL Command to get everything
$sql = "SELECT * FROM proposals ORDER BY event_date DESC"; // start with the latest first 
$result = $conn->query($sql);

$proposals = array();

// Loop through the results and add to array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $proposals[] = $row;
    }
}

// Send back as JSON (JavaScript Object Notation)
echo json_encode($proposals);

$conn->close();
?>