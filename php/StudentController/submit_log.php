<?php
session_start();
include __DIR__ . '/../db_connect.php';
header('Content-Type: application/json');



if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in."]);
    exit();
}

//validate fle
if (!isset($_FILES['evidence']) || $_FILES['evidence']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "Please upload a valid photo."]);
    exit();
}

//setup path for save the upload material
$base_dir = dirname(__DIR__ , 2); 
$target_dir = $base_dir . "/uploads/";

// Create folder if it's missing (Safety net)
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// create a unique name to prevent the same name of file save in 'upload' folder and replace 
$file_extension = pathinfo($_FILES["evidence"]["name"], PATHINFO_EXTENSION);

// Allow only valid images
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($file_extension), $allowed_ext)) {
    echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, PNG, GIF allowed."]);
    exit();
}


$new_filename = time() . "_" . $_SESSION['user_id'] . "." . $file_extension; 

$target_file = $target_dir . $new_filename;
$db_path = "uploads/" . $new_filename; // save the path to sql instead of the filename oni

// Move File & Save to DB
if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $target_file)) {
    
    // Get Form Data
    $user_id = $_SESSION['user_id'];
    $event_id = intval($_POST['event_id']);
    $category = $_POST['category']; 
    $weight = floatval($_POST['weight']);

    // prepare a template for user input data to fill in 
    $stmt = $conn->prepare("INSERT INTO logs (user_id, event_id, category, weight, photo_evidence, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    
    //iisds: int, int, string, double, string;
    $stmt->bind_param("iisds", $user_id, $event_id, $category, $weight, $db_path);

    //execute to put the input data into template and ssave to sql 
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Log submitted successfully!"]);
    } else {
        // If DB fails, we should technically delete the uploaded photo, but let's keep it simple for now
        echo json_encode(["status" => "error", "message" => "Database Error."]);
    }
    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Server failed to save the file."]);
}

$conn->close();
?>