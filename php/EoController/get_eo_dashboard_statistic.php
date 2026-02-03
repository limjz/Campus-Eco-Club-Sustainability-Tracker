<?php 
include_once __DIR__ . '/../db_connect.php';

if (!isset ($_SESSION["user_id"] ) || $_SESSION["role"] !== 'eo' ){
  header ("Location: login.html");
  exit;
} 


$eo_id = $_SESSION["user_id"];

// ------------ Active Events ------------
$sql_approvedEvent = "SELECT COUNT(*) AS count FROM events WHERE organizer_id = ?";
$stmt1 = $conn->prepare($sql_approvedEvent);
$stmt1->bind_param("i", $eo_id);
$stmt1->execute();
$active_events = $stmt1->get_result()->fetch_assoc()['count'];


// ----------- Pending Proposal ------------ 
$sql_pendingProposal = "SELECT COUNT(*) AS count FROM proposals WHERE organizer_id = ? AND status = 'pending'";
$stmt2 = $conn->prepare($sql_pendingProposal);
$stmt2->bind_param("i", $eo_id);
$stmt2->execute();
$pending_proposals = $stmt2->get_result()->fetch_assoc()['count'];


// ---------- Volunteers number ----------- 
$sql_volunteer = "SELECT COUNT(*) AS count FROM registrations r 
         JOIN events e ON r.event_id = e.event_id 
         WHERE e.organizer_id = ? AND r.role = 'volunteer'";
$stmt3 = $conn->prepare($sql_volunteer);
$stmt3->bind_param("i", $eo_id);
$stmt3->execute();
$total_volunteers = $stmt3->get_result()->fetch_assoc()['count'];


// ------------- Participants number ------------
$sql_participant = "SELECT COUNT(*) AS count FROM registrations r 
         JOIN events e ON r.event_id = e.event_id 
         WHERE e.organizer_id = ? AND r.role = 'participant'";
$stmt4 = $conn->prepare($sql_participant);
$stmt4->bind_param("i", $eo_id);
$stmt4->execute();
$total_participants = $stmt4->get_result()->fetch_assoc()['count'];


?> 