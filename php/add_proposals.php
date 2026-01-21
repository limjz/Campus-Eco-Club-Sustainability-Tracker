<?php
include 'db_connect.php';

// Get the text data sent from JavaScript 
$json_text = file_get_contents("php://input");
$data = json_decode($jason_text);


if(isset($data->title) && isset($data->date)) {
    
    //Clean the data (Security)
    $title = $conn->real_escape_string($data->title);
    $date = $conn->real_escape_string($data->date);
    $venue = $conn->real_escape_string($data->venue); // Added venue

    // SQL Command 
    $sql = "INSERT INTO proposals (title, event_date, venue, status) VALUES ('$title', '$date', '$venue', 'Pending')";

    //Run the command
    if ($conn->query($sql) === TRUE) {
        echo "Success: Proposal Saved!";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Error: Missing data";
}

$conn->close();
?>