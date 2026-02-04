<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include __DIR__ . '/../../php/db_connect.php';

// group by 'category' to get the total weight for each type (Plastic, Paper, etc.)
$sql = "SELECT category, SUM(weight) as total_weight 
        FROM logs 
        WHERE status = 'approved' 
        GROUP BY category";

$result = $conn->query($sql);

$labels = [];
$values = [];
$breakdown = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Handle empty category names
        $catName = empty($row['category']) ? 'Uncategorized' : ucfirst($row['category']);
        $weight = floatval($row['total_weight']);

        // Data for Chart
        $labels[] = $catName;
        $values[] = $weight;

        // Data for List Description
        $breakdown[] = [
            'name' => $catName,
            'weight' => $weight
        ];
    }
}

// 2. Return JSON
echo json_encode([
    'labels' => $labels,
    'data' => $values,
    'breakdown' => $breakdown
]);
?>