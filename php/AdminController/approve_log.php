<?php
include __DIR__ . '/../db_connect.php'; 
header('Content-Type: application/json');


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

// fetch data from logs table 
$sql_get = "SELECT weight, user_id, status FROM logs WHERE log_id = ?";
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

// calc point for logs (1kg = 1point)
// round off the weight so no decimal for points 
$points = round($log_data['weight']); 

if ($points < 1){ 
    $points = 1;  // effort point, got submit got 1 point
}

$user_id = $log_data['user_id']; 

// update the logs table 
$sql_log = "UPDATE logs SET status = 'approved', points_awarded = ? WHERE log_id = ?";
$stmt_log = $conn->prepare($sql_log);
$stmt_log->bind_param("ii", $points, $log_id);

if ($stmt_log->execute()) {

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

    echo json_encode([
        'status' => 'success', 
        'message' => "Verified! Log awarded $points pts. Total points synced."
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $conn->error]);
}


// ------------ Notification send after award a point ------------
$stmt_sync->execute();

$notif_title = "Log Approved";
$notif_msg = "Your recycling log was approved! You earned $points points.";

$sql_notif = "INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, ?, ?, 0)";
$stmt_notif = $conn->prepare($sql_notif);
$stmt_notif->bind_param("iss", $user_id, $notif_title, $notif_msg);
$stmt_notif->execute();

echo json_encode([
        'status' => 'success', 
        'message' => "Verified! Awarded $points pts. Notification sent."
    ]);


// --------------- once the logs got approved, check the goal, if reach the goal then will close the events ---------- 

// $event_target_goal = 50; 

//get the specific event id 
$check_event = $conn->query("SELECT event_id, target_goal FROM logs WHERE log_id = $log_id");
$event_row = $check_event->fetch_assoc();
$this_event_id = $event_row["event_id"]; // only grab the event_id 
$event_target_goal = floatval($event_row['target_goal']);

//total up the weight approved by admin for the specific event
$sql_total = "SELECT SUM(weight) as total FROM logs WHERE event_id = $this_event_id AND status = 'approved'"; 
$res_total = $conn->query($sql_total);
$row_total = $res_total->fetch_assoc();
$current_total_recycable = floatval($row_total["total"]);

if ($current_total_recycable > $event_target_goal){ 

    // event status: closed 
    $conn->query("UPDATE events status = 'closed' WHERE event_id = $this_event_id");
    //event name 
    $event_title = $conn->query("SELECT title WHERE event_id = $this_event_id");

    //notif all participants in the event 
    $notif_title = "Event ($event_title) Goal Reached !!"; 
    $notif_msg = "We hit our goal of $current_total_recycable kg ! The event now is closed, well done everyone";

    // get all participants id 
    $all_participant = $conn->query("SELECT user_id FROM regitrations WHERE event_id = $this_event_id");

    if ($all_participant)
    { 
        $stmt_broadcast = $conn->prepare("INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, ?, ?, 0)");
        while ($p = $all_participant->fetch_assoc())
        {
            $uid = $p["user_id"];
            $stmt_broadcast->bind_param("iss", $uid,$notif_title, $notif_msg);
            $stmt_broadcast->execute(); 

        }
    
    }
}

echo json_encode([
        'status' => 'success', 
        'message' => "Verified! Points awarded. (Total Event Weight: $current_total kg)"
    ]);

?>