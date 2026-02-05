<?php
// FILE: php/EoController/add_proposal.php

include '../../php/db_connect.php';
session_start();

header('Content-Type: application/json');

$input = file_get_contents("php://input");
$data = json_decode($input);

if (isset($_SESSION['user_id'])) {
    $organizer_id = $_SESSION['user_id'];
    
    if (isset($data->title) && isset($data->description) && isset($data->event_date) && isset($data->event_time) && isset($data->venue)) {
        
        $title = $data->title;

        $event_date = $data->event_date;
        $event_time = $data->event_time;
        $venue = $data->venue;
        $description = $data->description;
        // default 50 just incase 
        $target_goal = isset($data->target_goal) ? floatval($data->target_goal) : 50.00;

        $sql = "INSERT INTO proposals (title, event_date, event_time, venue, description, target_goal, organizer_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            
            $stmt->bind_param("sssssdi", $title, $event_date, $event_time, $venue, $description, $target_goal, $organizer_id);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Proposal submitted successfully with a goal of $target_goal kg!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Database Error: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "SQL Prepare Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
}

$conn->close();
?>