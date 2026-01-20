
// 1. Navigation Logic (Same as EO)
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.section');
    sections.forEach(sec => sec.classList.remove('active-section'));
    
    // Show selected section
    document.getElementById(sectionId).classList.add('active-section');
    
    // Optional: Load data when section opens
    if(sectionId === 'register') {
        loadAvailableEvents();
    }
}

// 2. Load "Register Event" List (Fetch from API)
function loadAvailableEvents() {
    const tbody = document.getElementById('eventListBody');
    tbody.innerHTML = "<tr><td colspan='4'>Loading events...</td></tr>";

    // need to create another php code to get the approved events and pass it here 
    fetch('php/get_approved_events.php')
    .then(res => res.json())
    .then(data => {
        tbody.innerHTML = "";
        data.forEach(event => {
            const row = `<tr>
                <td>${event.event_name}</td>
                <td>${event.event_date}</td>
                <td>${event.venue || 'TBA'}</td>
                <td>
                    <button class="btn-primary" onclick="registerForEvent(${event.event_id})">Register</button>
                </td>
            </tr>`;
            tbody.innerHTML += row;
        });
    })
    .catch(err => console.error(err));
}

// 3. Dummy Register Function
function registerForEvent(id) {
    // In real life: fetch('php/register_event.php', { method: 'POST', body: ... })
    alert("Simulation: You have successfully registered for Event ID: " + id);
}

// 4. Dummy Submit Log
function submitLog(e) {
    e.preventDefault();
    alert("Log Submitted! Waiting for EO verification.");
    // Clear form
    document.getElementById('logForm').reset();
}

// 5. Load My Tasks (Dummy Data for now)
function loadMyTasks() {
    const eventId = document.getElementById('taskEventSelect').value;
    const display = document.getElementById('myTaskDisplay');

    // Simulate database response
    if (eventId == "1") {
        display.innerHTML = `
            <h3>Your Role: Logistics Team</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="background: #fff; padding: 10px; border: 1px solid #ddd; margin-bottom: 5px;">
                    <strong>Task:</strong> Set up the registration booth <br>
                    <span style="color: green;">Status: In Progress</span>
                </li>
                <li style="background: #fff; padding: 10px; border: 1px solid #ddd;">
                    <strong>Task:</strong> Distribute gloves to participants <br>
                    <span style="color: orange;">Status: Pending</span>
                </li>
            </ul>
        `;
    } else {
        display.innerHTML = "<p>No tasks found for this event.</p>";
    }
}