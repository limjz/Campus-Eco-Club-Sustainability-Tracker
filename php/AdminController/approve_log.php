<?php
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');

// Get Log ID safely
$log_id = 0;
if (isset($_POST['log_id'])) {
    $log_id = intval($_POST['log_id']);
} else {
    $data = json_decode(file_get_contents("php://input"), true);
    $log_id = intval($data['log_id'] ?? 0);
}

if ($log_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Log ID']);
    exit();
}

// Fetch Log Data (Points, User, Status, Event ID)
// event_id here tp check the goal later
$sql_get = "SELECT weight, user_id, status, event_id FROM logs WHERE log_id = ?";
$stmt = $conn->prepare($sql_get);
$stmt->bind_param("i", $log_id);
$stmt->execute();
$log_data = $stmt->get_result()->fetch_assoc();

if (!$log_data) {
    echo json_encode(['status' => 'error', 'message' => 'Log not found']);
    exit();
}

if (strtolower($log_data['status']) === 'approved') {
    echo json_encode(['status' => 'error', 'message' => 'Already verified!']);
    exit();
}

$user_id = $log_data['user_id']; 
$event_id = $log_data['event_id']; // Captured for later
$points = round($log_data['weight']); 
if ($points < 1) $points = 1; 

// 3. Update Log Status
$sql_log = "UPDATE logs SET status = 'approved', points_awarded = ? WHERE log_id = ?";
$stmt_log = $conn->prepare($sql_log);
$stmt_log->bind_param("ii", $points, $log_id);

if (!$stmt_log->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $conn->error]);
    exit();
}

// 4. Sync User Points
$sql_sync = "UPDATE users u
             SET u.points = (
                 SELECT IFNULL(SUM(l.points_awarded), 0)
                 FROM logs l 
                 WHERE l.user_id = u.user_id 
                 AND l.status = 'approved'
             )
             WHERE u.user_id = ?";
$stmt_sync = $conn->prepare($sql_sync);
$stmt_sync->bind_param("i", $user_id);
$stmt_sync->execute();

// 5. Send Student Notification
$notif_title = "Log Approved";
$notif_msg = "Your recycling log was approved! You earned $points points.";
$sql_notif = "INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, ?, ?, 0)";
$stmt_notif = $conn->prepare($sql_notif);
$stmt_notif->bind_param("iss", $user_id, $notif_title, $notif_msg);
$stmt_notif->execute();

// ---------------------------------------------------------
// CHECK EVENT GOAL LOGIC
// ---------------------------------------------------------

$goal_msg = ""; // Empty by default

// Fetch Goal & Title from EVENTS table (Corrected Logic)
$sql_event = "SELECT target_goal, title FROM events WHERE event_id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event_data = $stmt_event->get_result()->fetch_assoc();

if ($event_data) {
    $target_goal = floatval($event_data['target_goal']);
    $event_title = $event_data['title'];

    // Calculate total approved weight for this event
    $sql_total = "SELECT SUM(weight) as total FROM logs WHERE event_id = ? AND status = 'approved'";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $event_id);
    $stmt_total->execute();
    $row_total = $stmt_total->get_result()->fetch_assoc();
    
    $current_total = floatval($row_total['total']);

    // Check if Goal is Reached
    if ($current_total >= $target_goal) {
        
        // Close the Event
        $close_stmt = $conn->prepare("UPDATE events SET status = 'closed' WHERE event_id = ?");
        $close_stmt->bind_param("i", $event_id);
        $close_stmt->execute();
        
        $goal_msg = " Event Goal Reached! Event Closed.";

        // Notify All Participants
        $broadcast_title = "Event Goal Reached! "; 
        $broadcast_msg = "We hit our goal of $current_total kg for '$event_title'! The event is now closed.";

        // Get all participants
        $parts_stmt = $conn->prepare("SELECT user_id FROM registrations WHERE event_id = ?");
        $parts_stmt->bind_param("i", $event_id);
        $parts_stmt->execute();
        $participants = $parts_stmt->get_result();

        if ($participants) {
            $stmt_broad = $conn->prepare("INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, ?, ?, 0)");
            while ($p = $participants->fetch_assoc()) {
                $pid = $p['user_id'];
                $stmt_broad->bind_param("iss", $pid, $broadcast_title, $broadcast_msg);
                $stmt_broad->execute(); 
            }
        }
    }
}


echo json_encode([
    'status' => 'success', 
    'message' => "Verified! Awarded $points pts.$goal_msg"
]);
exit();
?>