<?php

session_start();
include __DIR__ . '/../../php/db_connect.php'; 

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Update ALL notifications for this user from 0 to 1 (read)
    $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
}
?>