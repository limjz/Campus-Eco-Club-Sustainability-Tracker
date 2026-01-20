// js/admin_function.js

// 1. Navigation Logic
function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(sec => sec.classList.remove('active-section'));
    document.getElementById(sectionId).classList.add('active-section');

    // If Stats tab is opened, load the chart
    if(sectionId === 'statistics') {
        loadAdminChart();
    }
}

// 2. Proposal Functions (Simulated)
function approveProposal(id) {
    if(confirm("Are you sure you want to APPROVE this event?")) {
        // DATABASE CONNECT HERE: 
        // fetch('php/approve_proposal.php', { method: 'POST', body: JSON.stringify({id: id}) })
        
        // Remove row from table visually
        document.getElementById(`prop-${id}`).remove();
        alert("Event Approved! It is now visible to students.");
    }
}

function rejectProposal(id) {
    if(confirm("Reject this proposal?")) {
        // DATABASE CONNECT HERE: fetch('php/reject_proposal.php' ...)
        document.getElementById(`prop-${id}`).remove();
    }
}

// 3. Log Verification Function
function verifyLog(id) {
    // DATABASE CONNECT HERE: Update log status to 'verified' AND add points to student user
    document.getElementById(`log-${id}`).remove();
    alert("Log Verified! Points added to student account.");
}

// 4. Statistics Chart
let adminChartInstance = null;

function loadAdminChart() {
    const ctx = document.getElementById('adminChart').getContext('2d');
    
    // Destroy old chart if exists (prevents glitching when switching tabs)
    if (adminChartInstance) {
        adminChartInstance.destroy();
    }

    // DATABASE CONNECT HERE: Fetch these numbers from your DB
    adminChartInstance = new Chart(ctx, {
        type: 'bar', // or 'pie'
        data: {
            labels: ['Plastic', 'Paper', 'E-Waste', 'Metal', 'Glass'],
            datasets: [{
                label: 'Total Collected (kg)',
                data: [500, 300, 200, 250, 100], // <-- Connect this array to DB data
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}