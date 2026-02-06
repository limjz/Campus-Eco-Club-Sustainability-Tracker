
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll(".section").forEach(sec => sec.classList.remove("active-section"));
    // Show selected section
    const activeSection = document.getElementById(sectionId);
    if (activeSection) activeSection.classList.add("active-section");

    // Sidebar Highlight
    document.querySelectorAll(".sidebar ul li").forEach(li => li.classList.remove("active"));
    const activeBtn = Array.from(document.querySelectorAll(".sidebar ul li"))
        .find(li => (li.getAttribute("onclick") || "").includes(sectionId));
    if (activeBtn) activeBtn.classList.add("active");

    // Dynamic Loading
    if (sectionId === 'proposals') loadPendingProposals(); 
    if (sectionId === 'logs') loadPendingLogs(); 
    if (sectionId === "statistics") loadOverallStats();
}

// --------------- Proposal --------------- 
function loadPendingProposals() { 
    const tbody = document.getElementById("proposalTableBody"); 
    if (!tbody) return; // Safety check

    tbody.innerHTML = "<tr><td colspan='5' style='text-align:center'>Loading...</td></tr>";

    fetch('php/AdminController/get_pending_proposals.php')
    .then(responseponseponse => responseponseponse.json())
    .then(data => { 
        tbody.innerHTML = "";

        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='5' style='text-align:center'>No pending proposals found.</td></tr>";
            return;
        }

        data.forEach(prop => {
            const row = `
            <tr id="prop-${prop.proposal_id}">
                <td>${prop.title}</td>
                <td>${prop.organizer_name}</td> 
                <td>${prop.event_date}</td>
                <td>${prop.description}</td>
                <td>
                    <button class="btn-primary" onclick="approveProposal(${prop.proposal_id})" style="background:#28a745; margin-right:5px; padding: 5px 10px;">
                        Approve
                    </button>
                    <button class="btn-primary" onclick="rejectProposal(${prop.proposal_id})" style="background:#dc3545; padding: 5px 10px;">
                        Reject
                    </button>
                </td>
            </tr>`;
            tbody.innerHTML += row;
        });
    }) 
    .catch(err => {
        console.error("Error loading proposals:", err);
        tbody.innerHTML = "<tr><td colspan='5' style='color:red; text-align:center'>Error loading data.</td></tr>";
    });
}

function approveProposal(id) {
    if (!confirm("Approve this event proposal?")) return;

    fetch('php/AdminController/approve_proposal.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'proposal_id=' + id
    })
    .then(responseponseponse => responseponseponse.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            
            // 1. Remove row from table
            const row = document.getElementById(`prop-${id}`);
            if (row) row.remove();

            // 2. Decrement Dashboard Counter instantly
            updateCounter("stat-proposals", -1);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => console.error("Approve Error:", err));
}

function rejectProposal(id) {
    if (!confirm("Reject this event proposal?")) return;

    fetch('php/AdminController/reject_proposal.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'proposal_id=' + id 
    })
    .then(responseponseponse => responseponseponse.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            const row = document.getElementById(`prop-${id}`);
            if (row) row.remove();
            
            // Decrement Dashboard Counter
            updateCounter("stat-proposals", -1);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => console.error("Reject Error:", err));
}

// Helper to update numbers on the dashboard without refresponseponsehing
function updateCounter(elementId, change) {
    const el = document.getElementById(elementId);
    if (el) {
        let current = parseInt(el.innerText) || 0;
        el.innerText = Math.max(0, current + change);
    }
}


//--------------------- Logs ------------------------------
function loadPendingLogs() {
    const tbody = document.getElementById("logsTableBody");
    if (!tbody) return;

    tbody.innerHTML = "<tr><td colspan='5' style='text-align:center'>Loading logs...</td></tr>";

    fetch('php/AdminController/get_pending_logs.php')
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = "";

        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='5' style='text-align:center'>No pending logs found.</td></tr>";
            return;
        }

        data.forEach(log => {
            // Check if proof_url exists, otherwise show 'No Image'
            const evidenceLink = log.photo_evidence 
                ? `<a href="${log.photo_evidence}" target="_blank" style="color:blue; text-decoration:underline;">View Proof</a>` 
                : "No Proof";

            const row = `
            <tr id="log-${log.log_id}">
                <td>${log.student_name}</td>
                <td>${log.event_title}</td>
                <td> Recycled ${log.category}: <b>${log.weight}kg</b></td>
                <td>${evidenceLink}</td>
                <td>
                    <button class="btn-primary" onclick="approveLog(${log.log_id})" style="background:#007bff; padding: 5px 10px;">
                        Approve
                    </button>
                    <button class="btn-primary" onclick="rejectLog(${log.log_id})" style="background:#dc3545; padding: 5px 10px;">
                        Reject
                    </button>
                    </td>
            </tr>`;
            tbody.innerHTML += row;
        });
    })
    .catch(err => {
        console.error("Error loading logs:", err);
        tbody.innerHTML = "<tr><td colspan='5' style='color:red; text-align:center'>Error loading data.</td></tr>";
    });
}

function approveLog(id) {

    if (!id) {
          alert("Error: Missing Log ID in JavaScript");
          return;
    }

    if (!confirm("Are you sure you want to APPROVE this log?" )) return;

    const formData = new FormData();
    formData.append('log_id', id);


    fetch('php/AdminController/approve_log.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log("RAW PHP RESPONSE:", text); // <--- Check this in Console!
        return JSON.parse(text); // This will still fail, but you'll see why above
    })
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            
            // Remove row from table
            const row = document.getElementById(`log-${id}`);
            if (row){ 
              row.remove()
            };

            // Update Dashboard Counter (Yellow Card)
            updateCounter("stat-logs", -1); 
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => console.error(err));
}


function rejectLog (id) { 
    if (!id) {
        alert("Error: Cannot reject, ID is missing.");
        return;
    }

    if (!confirm("Are you sure you want to REJECT this log?")) return;

    const formData = new FormData();
    formData.append('log_id', id);

    fetch('php/AdminController/reject_log.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            
            // Remove row from table
            const row = document.getElementById(`log-${id}`);
            if (row) row.remove();

            // Update Dashboard Counter
            const counter = document.getElementById("stat-logs");
            if (counter) counter.innerText = Math.max(0, parseInt(counter.innerText) - 1);
        } else {
            alert("Server Error: " + data.message);
        }
    })
    .catch(err => {
        console.error("Reject Error:", err);
        alert("Connection failed. See console for details.");
    });
}



// ---------------- STATISTICS (Chart.js) ------------------- 

function loadOverallStats() {
    fetch('php/AdminController/get_dashboard_stats.php')
    .then(response => response.json())
    .then(data => {
        
        //bar chartt
        const ctx = document.getElementById('recyclingBarChart');
        if (ctx) {
            if (window.myBarChart) window.myBarChart.destroy();

            window.myBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Total Recycled (kg)',
                        data: data.data,
                        backgroundColor: ['#3498db', '#e74c3c', '#f1c40f', '#2ecc71', '#9b59b6'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Ensures it fits the container
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Weight (kg)' }
                        }
                    },
                    plugins: {
                        legend: { display: false } // Hide legend since bars have colors
                    }
                }
            });
        }

        //description for each of the category 
        const listContainer = document.getElementById('statsDescription');
        if (listContainer) {
            listContainer.innerHTML = ""; // Clear old data

            if (data.breakdown.length === 0) {
                listContainer.innerHTML = "<p>No data available yet.</p>";
            } else {
                // Create the <ul> wrapper dynamically
                const ul = document.createElement("ul");
                ul.style.listStyle = "none";
                ul.style.padding = "0";

                data.breakdown.forEach(item => {
                    const li = document.createElement("li");
                    
                    li.classList.add("stat-item"); // css come here
                    li.innerHTML = `<strong>${item.name}</strong> 
                                    <span style="color: #28a745; font-weight: bold;">${item.weight} kg</span>`;

                    ul.appendChild(li);
                });
                listContainer.appendChild(ul);
            }
        }

    })
    .catch(err => console.error("Stats Error:", err));
}

// init 
document.addEventListener("DOMContentLoaded", () => {
    loadOverallStats();
});
