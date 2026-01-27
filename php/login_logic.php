<?php
session_start(); 
include 'db_connect.php'; 

$username = $_POST['username']; 
$password = $_POST['password'];

//check database 
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $conn ->query($sql);

if ($result -> num_rows > 0){ 
  $user = $result -> fetch_assoc();

  // create badge using session variable 
  $_SESSION['user_id'] = $user['user_id'];
  $_SESSION['username'] = $user['username'];
  $_SESSION['email'] = $user['email'];
  $_SESSION['role'] = $user['role'];

  //redirect to each dashboard 
  if ($user['role'] == 'admin'){
    header('Location:../admin_dashboard.php');
  } else if ($user['role'] == 'eo'){
    header ('Location:../eo_dashboard.php');
  } else if ($user['role'] == 'student'){
    header ('Location:../student_dashboard.php');
  }
  
    
} else { 
  echo "Invalid Username or Password";
}

?>