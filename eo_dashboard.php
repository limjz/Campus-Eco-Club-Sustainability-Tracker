<?php
// check if the username is correct onot 
$require_role = "eo";
include 'php/session_check.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'eo') {
    header("Location: login.html"); 
    exit();
}

$eo_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

include 'php/EoController/get_eo_dashboard_statistic.php';

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
            <li onclick="showSection('assign-tasks')">  
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
                <h1>Welcome, <?php echo $_SESSION ['username'] ?></h1>
                <p>Overview of your active eco-activities.</p>
            </header>
            <div class="cards-container">
                <!-- active events card on the dashboard  -->
                <div class="card" style="border-left: 5px solid #28a745;">
                    <h3>Active Events</h3>
                    <p class = "number_card"><?php echo $active_events; ?> </p>
                </div>
                <!-- pending proposal card on the dashboard  -->
                <div class="card" style="border-left: 5px solid #17a2b8;">
                    <h3>Pending Proposals</h3>
                    <p class = "number_card"><?php echo $pending_proposals; ?> </p>
                </div>
                <!-- volunteers number card on the dashboard  -->
                <div class="card" style="border-left: 5px solid #ffc107;">
                    <h3>Volunteers</h3>
                    <p class = "number_card"><?php echo $total_participants; ?></p>
                </div>
                <!-- participants number card on the dashboard  -->
                <div class="card" style="border-left: 5px solid #6f42c1;">
                    <h3>Participants</h3>
                    <p class="number_card"><?php echo $total_participants; ?></p>
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
                    <input type="hidden" id="editProposalId" value="">
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
                        <input type="text" id="propDesc" required></input>
                    </div>
                    
                    <div class="form-group">
                        <label>Recycling Goal (kg):</label>
                        <input type="number" id="target_goal" step="0.1" min="1" placeholder="e.g. 50" required>
                        <small>Event will auto-close when this amount is collected.</small>
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
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody id="proposalTableBody">
                        </tbody>
                </table>
            </div>
        </div>

        <div id ="proposalModal" class ="modal" style="display: none">
            <div class = "modal-content"> 
                <span class="close-btn" onclick="closeModal()"> &times;</span>

                <h2 id="viewTitle" syle="color: #2c3e50"> Proposal Title</h2>
                
                <!-- lines -->
                <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;"> 
                

                <div class="modal-body">
            
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span id="viewStatus" class="detail-value"></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span id="viewDate" class="detail-value"></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Time:</span>
                        <span id="viewTime" class="detail-value"></span> 
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Venue:</span>
                        <span id="viewVenue" class="detail-value"></span>
                    </div>
                    
                    <div class="description-section">
                        <span class="detail-label">Description:</span>
                        <div id="viewDesc" class="description-box">
                            </div>
                    </div>

                    <div class="detail-row" style="margin-top: 15px; display: flex; align-items: center; white-space: nowrap;">
                        <span class="detail-label" style="min-width: 140px;">Recycling Goal:</span>
                        <span id="viewGoal" class="detail-value" style=" font-weight: bold; color: #27ae60;"></span>
                    </div>
                </div>

                <div style="margin-top: 20px; text-align: right;">
                    <button class="btn-primary" onclick="closeModal()">Close</button>
                </div>

            </div> 
        </div>


        <div id="assign-tasks" class ="section"> 
            <header>
                <h1>Task & Instruction Management</h1>
                <p>Delegate responsibilities to volunteers or participants.</p>
            </header>

            <div class="panel">
                <h3>Manage Volunteers & Participants</h3>

                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
                    
                    <div class="form-group" style="margin-bottom: 0; flex: 1;"> 
                        <label style="font-weight: bold; margin-right: 10px;">Select Event:</label> 
                        <select id="eventSelect" class="form-control" onchange="filterParticipants()" style="display: inline-block; width: auto; min-width: 250px;"> 
                            <option value="">-- Select Event --</option>
                        </select>
                    </div>

                    <div class="filter-controls" style="background: #f8f9fa; padding: 10px 15px; border-radius: 5px; border: 1px solid #ddd;">
                        <span style="margin-right: 10px; font-weight: bold; color: #555;">Filter:</span>
                        
                        <label style="margin-right: 15px; cursor: pointer; display: inline-flex; align-items: center;">
                            <input type="checkbox" id="chkVolunteer" checked onchange="renderTaskTable()" style="margin-right: 5px;"> 
                            Show Volunteers
                        </label>
                        
                        <label style="cursor: pointer; display: inline-flex; align-items: center;">
                            <input type="checkbox" id="chkParticipant" checked onchange="renderTaskTable()" style="margin-right: 5px;"> 
                            Show Participants
                        </label>
                    </div>  

                </div>
                
                <div id="taskProgressContainer" style="display: none; margin-bottom: 20px;">
                        </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th style="width: 15%;">Role</th>
                            <th style="width: 45%;">Task / Instruction</th>
                            <th style="width: 15%;"> </th>
                        </tr>
                    </thead>
                    <tbody id="volunteerTableBody">
                        <tr><td colspan="4" style="text-align:center; padding: 20px;">Please select an event to manage tasks.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div id="statistics" class="section">
            <header>
                <h1>Event Recycling Statistics</h1>
            </header>
            
            <div class="form-group">
                <label>View Stats For:</label>
                <select id="eventSelectStats" onchange="updateChart()">
                    <option value="">-- Loading... --</option>
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
                <h1>Send Announcement </h1>
                <p>Notify all participants of a specific event.</p>
            </header>

            <div class="two-col-layout">
                <div class="panel">

                    <h3>Send Broadcast</h3>
                    <form id = "notiForm" onsubmit="sendNotification(event)">

                        <div class="form-group">
                            <label>Select Audience (Event):</label>
                            <select  id="notifEventSelect" required>
                                <option>Loading</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" id = "notifTitle" required placeholder="e.g Change of date">
                        </div>

                        <div class="form-group">
                            <label>Message:</label>
                            <textarea id= "notifMessage" rows="4" required placeholder="Type your message here..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary">
                            Send Notification
                        </button>

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