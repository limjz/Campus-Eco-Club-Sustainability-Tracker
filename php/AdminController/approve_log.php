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

    // 5. STEP 2: THE BETTER ALTERNATIVE (Recalculate Total)
    // Instead of "points + 10", we calculate the EXACT total from scratch.
    // This fixes any previous errors automatically.
    
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
?>