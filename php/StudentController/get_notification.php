<?php 
session_start(); 
include __DIR__ . '/../db_connect.php';
header ('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY is_read ASC, created_at DESC"; 

$result = $conn->query($sql);
$notifs = []; 

while ($row = $result->fetch_assoc()) {
  $notifs[] = $row;
}

echo json_encode ($notifs);
$conn->close();
?> 