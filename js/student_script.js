// student_script.js

// 1. Navigation Logic
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.section');
    sections.forEach(sec => sec.classList.remove('active-section'));
    
    // Show selected section
    document.getElementById(sectionId).classList.add('active-section');
    
    // Update Sidebar Active State
    document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
    
    // Map section IDs to Sidebar IDs
    const navMap = {
        'dashboard': 'nav-dashboard',
        'notif': 'nav-notif',
        'register': 'nav-register',
        'logs': 'nav-logs',
        'tasks': 'nav-tasks'
    };
    
    // Highlight sidebar
    let navId = 'nav-' + sectionId; // simple mapping
    if(sectionId === 'notifications') navId = 'nav-notif';
    
    if(document.getElementById(navId)) {
        document.getElementById(navId).classList.add('active');
    }

    // --- DYNAMIC DATA LOADING ---
    if(sectionId === 'register') {
        loadAvailableEvents();
    }
    else if (sectionId === 'notifications') {
        loadNotifications();
    }
    else if (sectionId === 'logs' || sectionId === 'tasks') {
        // Load the dropdowns for specific modes
        loadRegisteredEvents();
    }
}

// 2. Load "Register Event" List (Open Events)
function loadAvailableEvents() {
    const tbody = document.getElementById('eventListBody');
    tbody.innerHTML = "<tr><td colspan='4'>Loading open events...</td></tr>";

    fetch('php/get_approved_events.php')
    .then(res => res.json())
    .then(data => {
        tbody.innerHTML = "";
        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='4'>No upcoming events found.</td></tr>";
            return;
        }

        data.forEach(event => {
            const row = `<tr>
                <td>${event.title}</td>
                <td>${event.date}</td>
                <td>${event.venue || 'TBA'}</td>
                <td>
                    <button class="btn-primary" onclick="registerForEvent('${event.eventID}', 'Participant')">Join</button>
                    <button class="btn-primary" style="background-color:#2c3e50;" onclick="registerForEvent('${event.eventID}', 'Volunteer')">Volunteer</button>
                </td>
            </tr>`;
            tbody.innerHTML += row;
        });
    })
    .catch(err => {
        console.error(err);
        tbody.innerHTML = "<tr><td colspan='4'>Error loading events.</td></tr>";
    });
}

// 3. Register Function
function registerForEvent(id, role) {
    if(!confirm(`Register as ${role} for this event?`)) return;

    fetch('php/register_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: id, role: role })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.status === 'success') loadAvailableEvents();
    });
}

// 4. Submit Log
function submitLog(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('logForm'));

    fetch('php/submit_log.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.status === 'success') document.getElementById('logForm').reset();
    })
    .catch(err => console.error(err));
}

// 5. Load Notifications (NEW)
function loadNotifications() {
    const list = document.getElementById('notificationListBody');
    list.innerHTML = "<li class='notif-item'>Loading...</li>";

    fetch('php/get_notifications.php')
    .then(res => res.json())
    .then(data => {
        list.innerHTML = "";
        if(data.length === 0) {
            list.innerHTML = "<li class='notif-item'>No new notifications.</li>";
            return;
        }

        data.forEach(notif => {
            // Apply 'unread' class if status is unread (optional logic)
            const html = `
            <li class="notif-item unread">
                <strong>${notif.title}</strong>
                <p>${notif.content}</p>
                <span class="time">${notif.dateSent}</span>
            </li>`;
            list.innerHTML += html;
        });
    })
    .catch(err => console.error(err));
}

// 6. Load Registered Events for Dropdowns (NEW)
function loadRegisteredEvents() {
    // Select both dropdowns
    const logSelect = document.getElementById('logEventSelect');
    const taskSelect = document.getElementById('taskEventSelect');

    fetch('php/get_my_registrations.php')
    .then(res => res.json())
    .then(data => {
        // Reset Dropdowns
        let optionsHtml = '<option value="">Select Event...</option>';
        
        if(data.length === 0) {
            optionsHtml = '<option value="">No registered events found</option>';
        } else {
            data.forEach(reg => {
                // reg.title comes from the Event table join
                optionsHtml += `<option value="${reg.eventID}">${reg.title} (${reg.role})</option>`;
            });
        }

        if(logSelect) logSelect.innerHTML = optionsHtml;
        if(taskSelect) taskSelect.innerHTML = optionsHtml;
    })
    .catch(err => console.error(err));
}

// 7. Load Tasks (Updated to use dynamic event ID)
function loadMyTasks() {
    const eventId = document.getElementById('taskEventSelect').value;
    const display = document.getElementById('myTaskDisplay');

    if (eventId) {
        // In a real app, you would fetch tasks from DB based on Event ID
        // fetch('php/get_tasks.php?event_id=' + eventId)...
        
        // Simulating response for UI demo
        display.innerHTML = `
            <h3>Your Tasks for this Event</h3>
            <div class="task-grid">
                <div class="volunteer-card">
                    <h4><i class="fas fa-check-circle"></i> Setup</h4>
                    <p>Ensure bins are labeled correctly.</p>
                    <div class="task-input-group">
                        <input type="checkbox"> <span>Mark Complete</span>
                    </div>
                </div>
            </div>
        `;
    } else {
        display.innerHTML = "<p>Please select an event to see your tasks.</p>";
    }
}