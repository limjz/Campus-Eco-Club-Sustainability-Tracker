<?php 
include_once __DIR__ . '/../db_connect.php';

if (!isset($student_id)) {
    $student_id = $_SESSION['user_id'];
}

//--------------- point  ----------------------
$sql_points = "SELECT total_points FROM users WHERE user_id = $student_id"; 
$result_points = $conn->query($sql_points); 

//check the table if got at least one row with the student ID onot, if yes then grab the points 
if ($result_points->num_rows > 0)
{ 
    $row = $result_points->fetch_assoc();
    $student_points = $row['total_points']; // target to the row "total_points" in the table
}
else 
{
    $student_points = 0;
}

// ---------------- total recycling -----------------
$sql_weight = "SELECT SUM(weight) as total_kg FROM logs WHERE student_id = $student_id AND status = 'approved'"; //sum up the weight for the display if the logs is approved
$result_weight = $conn->query($sql_weight);
$row_weight = $result_weight->fetch_assoc(); 

if ($recycling_kg = $row_weight['total_kg'])
{ 
    $recycling_kg = round($row_weight['total_kg'], 1); //round off  
}
else 
{
    $recycling_kg = round($row_weight['total_kg'], 0);
}

// --------------- events_joined --------------
// check how many times the student appear in the registrations table 
$sql_event= "SELECT COUNT(*) as count FROM registrations WHERE student_id = $student_id"; 
$result_event = $conn->query($sql_event);
$events_joined = $result_event->fetch_assoc()['count'];



?>