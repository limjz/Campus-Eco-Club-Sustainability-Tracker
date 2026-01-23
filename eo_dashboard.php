<?php
// 1. SECURITY (PHP Logic)// check if the username is correct onot 
$require_role = "eo";
include 'php/session_check.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'eo') {
    header("Location: login.html"); 
    exit();
}

$eo_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Dummy stats for now (Replace with real SQL later)
$active_events = 3;
$pending_logs = 8;
$total_participants = 45;

?>



<!-- this is the EO Dashboard UI code -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Organizer Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/EO_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>


<!-- sideBar UI --> 
<body>

    <div class="sidebar">
        <h2><i class="fas fa-leaf"></i> Campus Eco-Club Sustainability Tracker</h2>
        <ul>
            <li onclick="showSection('dashboard')" class="active"> 
                <i class="fas fa-home"> </i> Dashboard
            </li>
            <li onclick="showSection('proposals')"> 
                <i class="fas fa-file-signature"> </i> Proposals
            </li>
            <li onclick="showSection('volunteers')">  
                <i class="fas fa-tasks"> </i> Task & Instruction Management
            </li>
            <!-- <li onclick="showSection('management')"> <i class="fas fa-tasks"> </i> Event Tasks</li> -->
            <li onclick="showSection('statistics')"> 
                <i class="fas fa-chart-pie"> </i> Recycling Stats
            </li>
            <li onclick="showSection('notifications')">
                 <i class="fas fa-bell"> </i> Notifications
            </li>
            <li onclick="location.href='php/logout.php'" style="color: #ff6b6b; margin-top: 50px;"> 
                <i class="fas fa-sign-out-alt"></i> Logout
            </li>
        </ul>
    </div>

    <div class="main-content">
        
        <div id="dashboard" class="section active-section">
            <header>
                <h1>Welcome, Organizer!</h1>
                <p>Overview of your active eco-activities.</p>
            </header>
            <div class="cards-container">
                <div class="card" style="border-left: 5px solid #28a745;">
                    <h3>Active Events</h3>
                    <p class = "number_card"><?php echo $active_events; ?> </p>
                </div>
                <div class="card" style="border-left: 5px solid #17a2b8;">
                    <h3>Pending Proposals</h3>
                    <p class = "number_card"><?php echo $pending_logs; ?> </p>
                </div>
                <div class="card" style="border-left: 5px solid #ffc107;">
                    <h3>Volunteers</h3>
                    <p class = "number_card"><?php echo $total_participants; ?></p>
                </div>
            </div>
        </div>

        <div id="proposals" class="section">
            <header>
                <h1>Event Proposals</h1>
            </header>
            
            <div class="panel">
                <h3>Submit New Proposal</h3>
                <form id="proposalForm" onsubmit="submitProposal(event)">
                    <div class="form-group">
                        <label>Event Title</label>
                        <input type="text" id="propTitle" required placeholder="e.g. Beach Cleanup">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" id="propDate" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" id="propTime" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Venue</label>
                        <input type="text" id="propVenue" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="propDesc" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Submit Proposal</button>
                    
                </form>
            </div>

            <div class="panel">
                <h3>Your Proposal Status</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody id="proposalTableBody">
                        </tbody>
                </table>
            </div>
        </div>

        <!-- <div id="management" class="section">
            <header>
                <h1>Event Management</h1>
            </header>
            
            <div class="form-group">
                <label>Select Active Event:</label>
                <select id="eventSelectManage" onchange="loadEventVolunteers()">
                    <option value="1">Beach Cleanup 2026</option>
                    <option value="2">E-Waste Drive</option>
                </select>
            </div>

            <div class="panel">
                <h3>Assign Tasks & Instructions</h3>
                <div class="task-grid" id="volunteerList">
                    </div>
            </div>
        </div> -->

        <div id="volunteers" class = "section"> 
            <header>
                <h1>Task & Instruction Management</h1>
            </header>
            

            <!-- option selector -->
            <div style = "margin-bottom: 20px;"> 
                <label> Select Event: </label> 

                <!-- onchange trigger the function in js -->
                <select id="eventSelect" onchange = "filterParticipants()"> 
                    <!-- value is the eventID user choose -->
                    <option value=""> ----Choose an Event ----</option>
                </select>

            </div>

            <!-- table display -->
            <table class = "data-table">
                <thead> 
                    <tr> 
                        <th> Name</th>
                        <th> Role</th>
                        <th> Current Task (Event Assignment)</th>
                        <!-- <th> Action</th> -->
                        
                    </tr>
                </thead>
                <tbody id = "volunteerTableBody"> 
                    </tbody>
            </table>
        </div>




        <div id="statistics" class="section">
            <header>
                <h1>Event Recycling Statistics</h1>
            </header>
            
            <div class="form-group">
                <label>View Stats For:</label>
                <select id="eventSelectStats" onchange="updateChart()">
                    <option value="1">Beach Cleanup 2026</option>
                    <option value="2">E-Waste Drive</option>
                </select>
            </div>

            <div class="panel chart-panel">
                <canvas id="recyclingChart"></canvas>
                <div class="stats-summary" id="statsSummary">
                    </div>
            </div>
        </div>

        <div id="notifications" class="section">
            <header>
                <h1>Notifications</h1>
            </header>

            <div class="two-col-layout">
                <div class="panel">
                    <h3>Send Broadcast</h3>
                    <form onsubmit="sendNotification(event)">
                        <div class="form-group">
                            <label>Target Audience</label>
                            <select>
                                <option>All Participants</option>
                                <option>Volunteers Only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" required placeholder="Subject">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea rows="4" required placeholder="Type your update..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary">Send Alert</button>
                    </form>
                </div>

                <div class="panel">
                    <h3>Inbox (System Alerts)</h3>
                    <ul class="notif-list">
                        <li class="notif-item unread">
                            <strong>Admin</strong>
                            <p>Your proposal "Park Cleanup" was Approved.</p>
                            <span class="time">2 mins ago</span>
                        </li>
                        <li class="notif-item">
                            <strong>System</strong>
                            <p>Beach Cleanup target (500kg) reached!</p>
                            <span class="time">1 hour ago</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <script src="js/eo_script.js"></script>

</body>
</html>