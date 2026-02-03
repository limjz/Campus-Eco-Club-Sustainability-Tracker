<?php 
include __DIR__ . '/../db_connect.php'; 
header ('Content-Type: application/json'); 

$event_id = intval ($_GET['event_id'] ?? 0);

if ($event_id == 0)
{ 
  echo json_encode([]); 
  exit ();

}

$responses = [
  'labels' => [], 
  'values' => []
];

if ($event_id > 0)
{
  $sql = "SELECT category, SUM(weight) as total_weight
        FROM logs
        WHERE event_id = ? AND status = 'approved'
        GROUP BY category";

  if ($stmt = $conn ->prepare ($sql))
  { 
    $stmt ->bind_param("i", $event_id);
    $stmt ->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()){ 
      $labels[] = $row["category"];
      $values[] = $row["total_weight"]; 
    }
  $stmt -> close();
  }
}




echo json_encode(['labels' => $labels, 'values'=> $values]);

?> 