<?php
require '../db_connect.php'; 
session_start();


$json_text = file_get_contents("php://input");
$data = json_decode($json_text);

// check the existing of role registered, and register to what event 
if(isset($_SESSION['user_id']) && isset($data->event_id) && isset($data->role)) 
{    
    $studentID = $_SESSION['user_id'];
    $eventID = $data->event_id;
    $role =strtolower($data->role); // 'Participant' or 'Volunteer'


    // check the event status: open @ ongoing ( dowan closed )
    $stmt_status_check = $conn->prepare("SELECT status FROM events WHERE event_id = ?");
    $stmt_status_check->bind_param("i", $eventID);
    $stmt_status_check->execute();
    $status_res = $stmt_status_check->get_result()->fetch_assoc();

    if (!$status_res) {
        echo json_encode(["status" => "error", "message" => "Event not found."]);
        exit();
    }

    if ($status_res['status'] === 'closed' || $status_res['status'] === 'cancelled') {
        echo json_encode(["status" => "error", "message" => "Sorry, this event is closed."]);
        exit();
    }



    // Check if already registered
    $stmt_check = $conn->prepare("SELECT registration_id FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt_check->bind_param("ii", $studentID, $eventID);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();


    if($result_check->num_rows > 0) 
    {
        // return the error msg 
        echo json_encode(["status" => "error", "message" => "You are already registered!"]);
        exit(); // stop the script here 
    } 
    else 
    {
        // register student // Insert into registrations table 
        $stmt_insert = $conn->prepare("INSERT INTO registrations (user_id, event_id, role) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iis", $studentID, $eventID, $role);
        if ($stmt_insert->execute())
        { 
            // after the students register, meaning the event is ongoing now, change the status from "open" to "ongoing"
            $sql_update_status = "UPDATE events SET status = 'ongoing' WHERE event_id = ? AND status = 'open'";
            $stmt_status = $conn->prepare($sql_update_status);
            $stmt_status->bind_param("i", $eventID);
            $stmt_status->execute();
    
            echo json_encode(['status' => 'success', 'message' => 'Registered successfully!']);

        }
        else 
        {
            echo json_encode(["status" => "error", "message" => "Invalid request or session expired."]);
        }
    }
    
} 







$conn->close();
?>