<?php
// FILE: php/AdminController/get_admin_dashboard_statistic.php

// 1. Robust DB Connection
// We check if $conn exists. If not, we try to include it.
if (!isset($conn)) {
    // Try the relative path from AdminController
    if (file_exists(__DIR__ . '/../db_connect.php')) {
        include __DIR__ . '/../db_connect.php';
    } 
    // Fallback: Try the path from the root (if included from admin_dashboard.php)
    else if (file_exists('php/db_connect.php')) {
        include 'php/db_connect.php';
    }
}

// If it is STILL not set, we can't proceed.
if (!isset($conn)) {
    $pending_proposals = "Err"; 
    $pending_logs = "Err";
    $total_recycable = "Err";
    return;
}

// 2. Pending Proposals
// We use TRIM() to ignore accidental spaces like "Pending "
$sql_prop = "SELECT COUNT(*) as count FROM proposals WHERE LOWER(TRIM(status)) = 'pending'";
$result_prop = $conn->query($sql_prop);

if ($result_prop) {
    $pending_proposals = $result_prop->fetch_assoc()['count'];
} else {
    // If query fails, show the error in the dashboard so we know why
    $pending_proposals = "SQL Error: " . $conn->error;
}

// 3. Pending Logs
$sql_logs = "SELECT COUNT(*) as count FROM logs WHERE LOWER(TRIM(status)) = 'pending'";
$result_logs = $conn->query($sql_logs);

if ($result_logs) {
    $pending_logs = $result_logs->fetch_assoc()['count'];
} else {
    $pending_logs = "SQL Error";
}

// 4. Total Impact
$sql_impact = "SELECT SUM(weight) as total FROM logs WHERE LOWER(TRIM(status)) = 'approved'";
$result_impact = $conn->query($sql_impact);
$row_impact = $result_impact ? $result_impact->fetch_assoc() : null;

// Default to 0 if null
$total_recycable = ($row_impact && $row_impact['total']) ? number_format($row_impact['total'], 1) : 0;

?>