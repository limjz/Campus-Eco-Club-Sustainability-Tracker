<?php
// 1. Start the Session (Access the browser's ID card)
session_start();


//check is the user logged in onot
if (!isset($_SESSION['user_id']))
{
  header ("Location:../login.html");  
  exit();
}
if (isset($require_role) && $_SESSION['role'] !== $require_role)
{ 
  echo "<hl> Access Denied! </h1>";
  echo "<p> You are logged in as a <b> ". $_SESSION['role'] . "</b>.</p>";
  echo "<a href= '../login.html'> Go back to login </a> ";
  exit(); 
}


// If code reaches here, the user is safe. The dashboard will now load.
?>