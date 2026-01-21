<?php
// Include security check
session_start();
// Initialize variables with default values to prevent "Undefined variable" errors
$student_points = 150; 
$recycling_kg = 45.5;
$events_joined = 3;


if(!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Student";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Campus Eco-Club</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/EO_style.css"> 
</head>

<body>

    <div class="sidebar">
        <h2>EcoClub Student</h2>
        <ul>
            <li onclick="showSection('dashboard')" class="active" id="nav-dashboard"> 
                <i class="fas fa-home"></i> Dashboard
            </li>
            <li onclick="showSection('notifications')" id="nav-notif"> 
                <i class="fas fa-bell"></i> Notifications
            </li>
            <li onclick="showSection('register')" id="nav-register"> 
                <i class="fas fa-calendar-plus"></i> Register Event
            </li>
            <li onclick="showSection('logs')" id="nav-logs"> 
                <i class="fas fa-edit"></i> Participant 
            </li>
            <li onclick="showSection('tasks')" id="nav-tasks"> 
                <i class="fas fa-tasks"></i> Volunteer 
            </li>
            <li onclick="location.href='php/logout.php'" style="color: #ff6b6b; margin-top: 50px;"> 
                <i class="fas fa-sign-out-alt"></i> Logout
            </li>
        </ul>
    </div>

    <div class="main-content">
        
        <div id="dashboard" class="section active-section">
            <header>
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Track your personal contribution to the planet.</p>
            </header>
            <div class="cards-container">
                <div class="card" style="border-left: 5px solid #28a745;">
                    <h3>Total Points Earned</h3>
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $student_points; ?> pts</p>
                </div>
                <div class="card" style="border-left: 5px solid #17a2b8;">
                    <h3>Recycling Contribution</h3>
                    <p style="font-size: 2em; font-weight: bold;">45.5 kg</p>
                </div>
                <div class="card" style="border-left: 5px solid #ffc107;">
                    <h3>Events Joined</h3>
                    <p style="font-size: 2em; font-weight: bold;">3</p>
                </div>
            </div>
        </div>

        <div id="notifications" class="section">
            <h2>🔔 Your Notifications</h2>
            <div class="panel">
                <ul class="notif-list" id="notificationListBody">
                    <li class="notif-item">Loading notifications...</li>
                </ul>
            </div>
        </div>

        <div id="register" class="section">
            <h2>📢 Upcoming Events</h2>
            <p>Select an event to join as a participant.</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="eventListBody">
                    </tbody>
            </table>
        </div>

        <div id="logs" class="section">
            <h2>📝 Submit Activity Log</h2>
            <div class="panel">
                <form id="logForm" onsubmit="submitLog(event)" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Select Event You Joined:</label>
                        <select name="event_id" id="logEventSelect" required>
                             <option value="">Loading your events...</option> 
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Category:</label>
                            <input type="text" name="description" placeholder="e.g. Plastic, Paper" required>
                        </div>
                        <div class="form-group">
                            <label>Weight (kg):</label>
                            <input type="number" step="0.1" name="weight" placeholder="0.0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Upload Photo Proof:</label>
                        <input type="file" name="evidence" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn-primary">Submit Log</button>
                </form>
            </div>
        </div>

        <div id="tasks" class="section">
            <h2>👷 My Volunteer Tasks</h2>
            <p>Check tasks assigned to you by the Event Organizer.</p>
            
            <div class="form-group">
                <label>Filter by Event:</label>
                <select id="taskEventSelect" onchange="loadMyTasks()">
                    <option value="">Loading your events...</option>
                </select>
            </div>

            <div class="panel">
                <div id="myTaskDisplay" style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <p><i>Select an event to view your assigned tasks...</i></p>
                </div>
            </div>
        </div>

    </div>

    <script src="js/student_script.js"></script>

</body>
</html>