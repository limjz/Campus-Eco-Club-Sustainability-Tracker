<?php 

include 'db_connect.php'; 

// sum the points for each students, combine logs and user table to get names
// sort flw the top 5 total points 
$sql = "SELECT u.username, SUM(l.points_awarded) as total_points
        FROM logs l 
        JOIN users u ON l.student_id = u.user_id 
        WHERE l.status = 'approved' 
        GROUP BY l.student_id 
        ORDER BY total_points DESC
        LIMIT 5";


$result = $conn ->query ( $sql );

if ($result && $result -> num_rows > 0) {
  $rank = 1; 
  while ( $row = $result -> fetch_assoc () ) {
    $icon = ""; 
    if($rank == 1) $icon = "🥇";
    if ($rank == 2) $icon = "🥈";
    if ($rank == 3) $icon = "🥉";

    echo"<li class='leaderboard-item'>
            <span class='rank'>{$icon} #{$rank}</span>
            <span class='name'>" . htmlspecialchars($row['username']) . "</span>
            <span class='points'>{$row['total_points']} pts</span>
        </li>";
    $rank++;
  }
}
else {
    echo "<li style='padding:15px; text-align:center;'>No points awarded yet. </li>";
}

$conn -> close ();

?> 