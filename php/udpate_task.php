<?php 

include 'db_connect.php'; 


$json_text = file_get_contents("php://input");
$data = json_decode($json_text);

if (isset($data-> id) && isset($data->task)){

  $id = (int) $data ->id; // int
  $task = $conn->real_escape_string($data->task); //string


  // update the specific volunteer // sql command
  $sql = "UPDATE volunteers SET current_task = '$task' WHERE id = $id"; 

  if ($conn->query($sql) === TRUE) {
  echo "Task Updated Successfully"; 
  }
  else {
    echo "Error: " . $conn ->error;
  }
}
$conn->close();

?>