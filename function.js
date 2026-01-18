// --- 1. NAVIGATION LOGIC ---
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active-section'));
    document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
    
    // Show selected
    document.getElementById(sectionId).classList.add('active-section');
    
    // Highlight sidebar (simple way, assumes order)
    const menuMap = { 'dashboard':0, 'proposals':1, 'management':2, 'statistics':3, 'notifications':4 };
    document.querySelectorAll('.sidebar li')[menuMap[sectionId]].classList.add('active');
}

// --- 2. PROPOSAL LOGIC ---
const mockProposals = [
    { title: "Campus Recycling Drive", date: "2026-03-12", status: "Approved" },
    { title: "Tree Planting Day", date: "2026-04-22", status: "Pending" }
];

function renderProposals() {
    const tbody = document.getElementById('proposalTableBody');
    tbody.innerHTML = "";
    mockProposals.forEach(prop => {
        const row = `<tr>
            <td>${prop.title}</td>
            <td>${prop.date}</td>
            <td class="status-${prop.status.toLowerCase()}">${prop.status}</td>
            <td><button class="btn-primary" style="padding:5px 10px; font-size:0.8rem">View</button></td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

function submitProposal(e) {
    e.preventDefault();
    const title = document.getElementById('propTitle').value;
    const date = document.getElementById('propDate').value;
    
    // Add to mock data
    mockProposals.push({ title: title, date: date, status: "Pending" });
    
    alert("Proposal Submitted to Admin!");
    document.getElementById('proposalForm').reset();
    renderProposals();
}

// --- 3. EVENT TASK MANAGEMENT LOGIC ---
const volunteers = [
    { id: 1, name: "Ali Bin Abu", role: "Volunteer", currentTask: "Unassigned" },
    { id: 2, name: "Sarah Tan", role: "Volunteer", currentTask: "Registration Desk" },
    { id: 3, name: "Muthu Kumar", role: "Participant", currentTask: "Cleaning Team A" }
];

function loadEventVolunteers() {
    const list = document.getElementById('volunteerList');
    list.innerHTML = "";
    
    volunteers.forEach(vol => {
        const card = `
        <div class="volunteer-card">
            <h4>${vol.name} <span style="font-size:0.8em; color:#666">(${vol.role})</span></h4>
            <p>Current Task: <strong id="task-display-${vol.id}">${vol.currentTask}</strong></p>
            <div class="task-input-group">
                <input type="text" id="task-input-${vol.id}" placeholder="Assign new task...">
                <button class="btn-primary" onclick="assignTask(${vol.id})">Assign</button>
            </div>
        </div>`;
        list.innerHTML += card;
    });
}

function assignTask(id) {
    const newTask = document.getElementById(`task-input-${id}`).value;
    if(newTask) {
        document.getElementById(`task-display-${id}`).innerText = newTask;
        alert("Task Updated Successfully!");
    }
}

// --- 4. STATISTICS LOGIC (Chart.js) ---
let myChart = null;

function updateChart() {
    const eventId = document.getElementById('eventSelectStats').value;
    const ctx = document.getElementById('recyclingChart').getContext('2d');
    
    // Mock Data switching based on event selection
    let dataValues = eventId === "1" ? [120, 80, 40] : [30, 150, 90]; 
    let eventName = eventId === "1" ? "Beach Cleanup" : "E-Waste Drive";
    let labels = eventId === "1" ? ['Plastic (kg)', 'Glass (kg)', 'Metal (kg)'] : ['Electronics (kg)', 'Batteries (kg)', 'Cables (kg)'];

    // Destroy old chart if exists
    if(myChart) myChart.destroy();

    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: `Recycled Amount for ${eventName}`,
                data: dataValues,
                backgroundColor: ['#3498db', '#e74c3c', '#f1c40f']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// --- 5. NOTIFICATION LOGIC ---
function sendNotification(e) {
    e.preventDefault();
    alert("Notification sent successfully to all selected users!");
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    renderProposals();
    loadEventVolunteers();
    updateChart();
});