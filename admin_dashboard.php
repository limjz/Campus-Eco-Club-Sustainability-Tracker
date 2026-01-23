<?php
// 1. SECURITY: Use the Master Guard
$required_role = 'admin';
include 'php/session_check.php'; 

// DUMMY DATA FOR DASHBOARD COUNTERS (Replace with SQL later)
// $pending_proposals = $conn->query("SELECT COUNT(*) FROM events WHERE status='pending'")->fetch_row()[0];
$pending_proposals = 4;
$pending_logs = 12;
$total_recycling = 1250.5; // kg
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

    <div class="sidebar" style="background: #1a252f;"> <h2>EcoClub Admin</h2>
        <ul>
            <li onclick="showSection('dashboard')" class="active"> <i class="fas fa-tachometer-alt"></i> Overview</li>
            <li onclick="showSection('proposals')"> <i class="fas fa-file-contract"></i> Review Proposals</li>
            <li onclick="showSection('logs')"> <i class="fas fa-check-circle"></i> Review Logs</li>
            <li onclick="showSection('statistics')"> <i class="fas fa-chart-bar"></i> Overall Stats</li>
            <li onclick="location.href='php/logout.php'" style="color: #ff6b6b; margin-top: 50px;"> <i class="fas fa-sign-out-alt"></i> Logout</li>
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
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $pending_proposals; ?></p>
                    <!-- <small>Requires Approval</small> -->
                </div>
                <div class="card" style="border-left: 5px solid #17a2b8;">
                    <h3>Pending Logs</h3>
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $pending_logs; ?></p>
                    <!-- <small>Student Submissions</small> -->
                </div>
                <div class="card" style="border-left: 5px solid #28a745;">
                    <h3>Total Impact</h3>
                    <p style="font-size: 2em; font-weight: bold;"><?php echo $total_recycling; ?> kg</p>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="proposalTableBody">
                        <tr id="prop-101">
                            <td>River Cleaning Campaign</td>
                            <td>EO Sarah</td>
                            <td>2026-05-20</td>
                            <td>Cleaning the Klang riverbank...</td>
                            <td>
                                <button class="btn-primary" onclick="approveProposal(101)" style="background:green;">Approve</button>
                                <button class="btn-primary" onclick="rejectProposal(101)" style="background:red;">Reject</button>
                            </td>
                        </tr>
                        <tr id="prop-102">
                            <td>Zero Waste Workshop</td>
                            <td>EO John</td>
                            <td>2026-06-15</td>
                            <td>Teaching students composting...</td>
                            <td>
                                <button class="btn-primary" onclick="approveProposal(102)" style="background:green;">Approve</button>
                                <button class="btn-primary" onclick="rejectProposal(102)" style="background:red;">Reject</button>
                            </td>
                        </tr>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <tr id="log-55">
                            <td>Ali bin Abu</td>
                            <td>Beach Cleanup 2026</td>
                            <td>Collected 5kg Plastic</td>
                            <td><a href="#" style="color:blue;">View Photo</a></td>
                            <td>
                                <button class="btn-primary" onclick="verifyLog(55)" style="background:#007bff;">Verify & Award Points</button>
                            </td>
                        </tr>
                        <tr id="log-56">
                            <td>Mei Ling</td>
                            <td>E-Waste Drive</td>
                            <td>Donated 2 Laptops</td>
                            <td><a href="#" style="color:blue;">View Photo</a></td>
                            <td>
                                <button class="btn-primary" onclick="verifyLog(56)" style="background:#007bff;">Verify & Award Points</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="statistics" class="section">
            <header><h1>Overall Recycling Statistics</h1></header>
            
            <div class="panel chart-panel">
                <canvas id="adminChart" style="max-height: 400px;"></canvas>
            </div>

            <div class="panel">
                <h3>Impact Summary</h3>
                <ul>
                    <li><strong>Plastic:</strong> 500 kg</li>
                    <li><strong>Paper:</strong> 300 kg</li>
                    <li><strong>E-Waste:</strong> 200 kg</li>
                    <li><strong>Metal:</strong> 250 kg</li>
                </ul>
            </div>
        </div>

    </div>

    <script src="js/admin_script.js"></script>

</body>
</html>


<!-- 
For Proposals: Create php/approve_event.php. It should run: UPDATE events SET status = 'approved' WHERE event_id = ?

For Logs: Create php/verify_log.php. It should run: UPDATE activity_logs SET status = 'verified' WHERE log_id = ? 
(And optionally: UPDATE users SET points = points + 10 WHERE id = student_id)

-->