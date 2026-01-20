<?php
// 1. SECURITY: Use the Master Guard we just created
$required_role = 'student';
include 'php/session_check.php'; 

// DUMMY DATA (For specific student stats - replace with SQL later)
// In real life: SELECT SUM(points) FROM logs WHERE student_id = $_SESSION['user_id']
$student_points = 150; 
$recycling_kg = 45.5;
$events_joined = 3;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/EO_style.css"> 
</head>

<body>

    <div class="sidebar">
        <h2>EcoClub Student</h2>
        <ul>
            <li onclick="showSection('dashboard')" class="active"> <i class="fas fa-home"></i> Dashboard</li>
            <li onclick="showSection('register')"> <i class="fas fa-calendar-plus"></i> Register Event</li>
            <li onclick="showSection('logs')"> <i class="fas fa-edit"></i> Submit Logs</li>
            <li onclick="showSection('tasks')"> <i class="fas fa-tasks"></i> My Tasks</li>
            <li onclick="location.href='php/logout.php'" style="color: #ff6b6b; margin-top: 50px;"> <i class="fas fa-sign-out-alt"></i> Logout</li>
        </ul>
    </div>

    <div class="main-content">
        
        <div id="dashboard" class="section active-section">
            <header>
                <h1>Welcome, <?php echo $_SESSION['username'] ?? 'Student'; ?>!</h1>
                <p>Track your personal contribution to the planet.</p>
            </header>
            
            <div class="cards-container">
                <div class="card" style="border-left: 5px solid #28a745;">
                    <h3>Total Points Earned</h3>
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $student_points; ?> pts</p>
                </div>
                <div class="card" style="border-left: 5px solid #17a2b8;">
                    <h3>Recycling Contribution</h3>
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $recycling_kg; ?> kg</p>
                </div>
                <div class="card" style="border-left: 5px solid #ffc107;">
                    <h3>Events Joined</h3>
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $events_joined; ?></p>
                </div>
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
                <form id="logForm" onsubmit="submitLog(event)">
                    <div class="form-group">
                        <label>Select Event You Joined:</label>
                        <select id="logEventSelect" required>
                            <option value="1">Beach Cleanup 2026</option> 
                            <option value="2">E-Waste Drive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Evidence (Description):</label>
                        <textarea rows="3" placeholder="What did you do? (e.g., Collected 5kg of plastic)" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload Photo Proof:</label>
                        <input type="file" required>
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
                    <option value="">Show All</option>
                    <option value="1">Beach Cleanup 2026</option>
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