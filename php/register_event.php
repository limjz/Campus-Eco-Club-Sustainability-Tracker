<?php
require 'db_connect.php';
session_start();


$json_text = file_get_contents("php://input");
$data = json_decode($json_text);

// check the existing of role registered, and register to what event 
if(isset($_SESSION['user_id']) && isset($data->event_id) && isset($data->role)) 
{    
    $studentID = $_SESSION['user_id'];
    $eventID = $data->event_id;
    $role = $data->role; // 'Participant' or 'Volunteer'

    // Check if already registered
    $sql_check ="SELECT * FROM registrations WHERE student_id = '$studentID' AND event_id = '$eventID'";
    $result_check = $conn->query($sql_check);


    if($result_check->num_rows > 0) 
    {
        // return the error msg 
        echo json_encode(["status" => "error", "message" => "You are already registered!"]);
        exit();
    } 
    else 
    {
        // register student // Insert into registrations table 
        $sql = "INSERT INTO registrations (student_id, event_id, role) VALUES ('$studentID', '$eventID', '$role')";
        
        // msg display 
        if($conn->query($sql) === TRUE) 
        {
            echo json_encode(["status" => "success", "message" => "Registered as " . $role]);
        } 
        else 
        {
            echo json_encode(["status" => "error", "message" => "Database error"]);
        }
    }
    
} 


$conn->close();
?>