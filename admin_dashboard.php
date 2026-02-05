<?php
$required_role = 'admin';
include 'php/session_check.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); 
    exit();
}

include 'php/AdminController/get_admin_dashboard_statistic.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/EO_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="sidebar"> 
        <h2><i class="fas fa-leaf"></i> Campus Eco-Club Sustainability Tracker</h2>
        <ul>
            <li onclick="showSection('dashboard')" class="active"> 
                <i class="fas fa-home"> </i> Dashboard
            </li>
            <li onclick="showSection('proposals')"> 
                <i class="fas fa-file-contract"></i> Review Proposals
            </li>
            <li onclick="showSection('logs')"> 
                <i class="fas fa-check-circle"></i> Review Logs
            </li>
            <li onclick="showSection('statistics')"> 
                <i class="fas fa-chart-bar"></i> Overall Stats
            </li>
            <li onclick="location.href='php/logout.php'" style="color: #ff6b6b; margin-top: 50px;"> 
                <i class="fas fa-sign-out-alt"></i> Logout
            </li>
        </ul>
    </div>

    <div class="main-content">

        <div id="dashboard" class="section active-section">
            <header>
                <h1>Admin Dashboard</h1>
                <p>System Overview & Pending Actions</p>
            </header>
            
            <div class="cards-container">
                <div class="card" style="border-left: 5px solid #ffc107;">
                    <h3>Pending Proposals</h3>
                    <p style="font-size: 2em; font-weight: bold;">
                        <?php echo $pending_proposals; ?>
                    </p>
                    <!-- <small>Requires Approval</small> -->
                </div>
                <div class="card" style="border-left: 5px solid #17a2b8;">
                    <h3>Pending Logs</h3>
                    <p id="stat-logs" style="font-size: 2em; font-weight: bold;">
                        <?php echo $pending_logs; ?>
                    </p>
                    <!-- <small>Student Submissions</small> -->
                </div>
                <div class="card" style="border-left: 5px solid #28a745;">
                    <h3>Total Impact</h3>
                    <p style="font-size: 2em; font-weight: bold;">
                        <?php echo $total_recycable; ?> kg
                    </p>
                    <!-- <small>All time recycling</small> -->
                </div>
            </div>
        </div>

        <div id="proposals" class="section">
            <header><h1>Event Proposals Review</h1></header>
            <div class="panel">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Proposed By (EO)</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th style="width: 30%;"> </th>
                        </tr>
                    </thead>
                    
                    <tbody id="proposalTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <div id="logs" class="section">
            <header><h1>Student Activity Logs</h1></header>
            <div class="panel">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Event</th>
                            <th>Claimed Activity</th>
                            <th>Proof</th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <div id="statistics" class="section">
            <header><h1>Overall Recycling Statistics</h1></header>
            
            <div class="panel chart-panel">
                <canvas id="recyclingBarChart" style="max-height: 400px;"></canvas>
            </div>

            <div class="panel">
                <h3>Recycable Summary</h3>
                <ul id="statsDescription" >
                    <li>Loading stats...</li>
                </ul>
            </div>
        </div>

    <script src="js/admin_script.js"></script>

</body>
</html>
