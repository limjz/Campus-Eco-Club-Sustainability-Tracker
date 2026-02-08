// student_script.js

// Navigation Logic
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
        'join-event': 'nav-join-event',
        'logs': 'nav-logs',
        'volunteer-hub': 'nav-volunteer',
        'history': 'nav-history',
        'tasks': 'nav-tasks'
    };
    
    // Highlight sidebar
    let navId = 'nav-' + sectionId; // simple mapping

    if(sectionId === 'notifications') navId = 'nav-notif';
    
    if(document.getElementById(navId)) {
        document.getElementById(navId).classList.add('active');
    }

    // --- DYNAMIC DATA LOADING ---


    if (sectionId === 'notifications') {
        loadNotifications();
    }

    // else if(sectionId === 'register') {
    //     loadAvailableEvents();
    // }

    if (sectionId === 'join-event') {
        loadAvailableEvents();
        loadMyEvents(); // <--- Add this!
    }
    
    // for participants(logs) and volunteers page(tasks)
    else if (sectionId === 'logs' || sectionId === 'tasks') {
        // Load the dropdowns for specific modes
        loadRegisteredEvents();
    }
    if (sectionId === 'volunteer-hub') {
        loadVolunteerEvents();
    }
    else if (sectionId === 'history'){
        loadMyLogs();
    }


}

// Load "Register Event" List (Open Events)
function loadAvailableEvents() {
    const tbody = document.getElementById('eventListBody');
    if (!tbody) return;

    tbody.innerHTML = "<tr><td colspan='4' style='text-align:center'>Loading available events...</td></tr>";

    fetch('php/StudentController/get_open_ongoing_events.php')
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = ""; 
        
        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='4' style='text-align:center'>No new events available to join.</td></tr>";
            return;
        }

        data.forEach(event => {
            const row = `<tr>
                <td>${event.title}</td>
                <td>${event.event_date}</td>
                <td>${event.venue || 'TBA'}</td>
                <td style="text-align: center;">
                    <button class="btn-primary" onclick="registerForEvent('${event.event_id}', 'participant')">Join as Participant</button>
                    <button class="btn-primary" style="background-color:#2c3e50; color:white;" onclick="registerForEvent('${event.event_id}', 'volunteer')">Join as Volunteer</button>
                </td>
            </tr>`;
            tbody.innerHTML += row;
        });
    })
    .catch(err => console.error(err));
}

// Load My Registered Events (The New Table)
function loadMyEvents() {
    const tbody = document.getElementById('myEventsBody');
    if (!tbody) return;

    tbody.innerHTML = "<tr><td colspan='5'>Loading...</td></tr>";

    fetch('php/StudentController/get_my_events.php')
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = ""; 
        
        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='5' style='text-align:center; color:#777;'>You haven't joined any events yet.</td></tr>";
            return;
        }

        data.forEach(event => {
            // Role Colors
            let roleBadge = '';
            if (event.role === 'volunteer') {
                roleBadge = `<span style="background:#f1c40f; color:white; padding:4px 8px; border-radius:12px; font-weight:bold; font-size:0.9em;">Volunteer</span>`;
            } else {
                roleBadge = `<span style="background:#3498db; color:white; padding:4px 8px; border-radius:12px; font-weight:bold; font-size:0.9em;">Participant</span>`;
            }

            // Status Colors
            let statusColor = event.status === 'open' ? 'green' : (event.status === 'closed' ? 'red' : 'orange');

            const row = `<tr>
                <td>${event.title}</td>
                <td>${event.event_date}</td>
                <td>${event.venue || 'TBA'}</td>
                <td>${roleBadge}</td>
                <td style="color:${statusColor}; font-weight:bold; text-transform:capitalize;">${event.status}</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    })
    .catch(err => console.error(err));
}

// Register Function
function registerForEvent(eventID, role) {

    // confirm msg
    if(!confirm(`Register as ${role} for this event?`)) 
    {
        return;
    }

    fetch('php/StudentController/register_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            event_id: eventID, 
            role: role 
        })
    })
    .then(response => response.json())
    .then(data => {
        
        if(data.status === 'success') 
        {   
            alert("DONE " + data.message);
            loadAvailableEvents();
        }
        else 
        { 
            alert("FAIL " + data.message);
        }
    });
}

// Submit Log
function submitLog(e) {
    e.preventDefault();// stop page refresh 

    const form = document.getElementById('logForm');
    const formData = new FormData(form); // object for files 

    fetch('php/StudentController/submit_log.php', {
        method: 'POST',
        body: formData // no headers needed, FormData will handles automatically 
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert("DONE " + data.message);
            form.reset(); // Clear the form
        } else {
            alert("FAIL " + data.message);
        }
    })
    .catch(err => console.error(err));
}

// get the logs that been submited, including the pending, rejected & approved
function loadMyLogs() {

    const tbody = document.getElementById('logsTableBody'); 

    tbody.innerHTML = "<tr><td colspan='6'>Loading data...</td></tr>";

    fetch('php/StudentController/get_student_logs.php')
    .then(res => res.json())
    .then(data => {
        tbody.innerHTML = ""; 

        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='6'>No records found.</td></tr>";
            return;
        }

        data.forEach(log => {
            // status color 
            let statusClass = 'status-pending'; // Default yellow: pending 
            // check the status to change the status color accordingly 
            if (log.status === 'approved') statusClass = 'status-approved';
            if (log.status === 'rejected') statusClass = 'status-rejected';

            let points = 0; // Default value

            if (log.points_awarded) {
                points = log.points_awarded;
            }

            const row = `
                <tr>
                    <td>${log.title}</td>
                    <td>${log.category}</td>
                    <td>${log.weight} kg</td>
                    <td><span class="${statusClass}">${log.status}</span></td>
                    <td>${points} pts</td>
                    <td>${log.submission_date}</td>
                </tr>
            `;

            tbody.innerHTML += row;
        });
    })
    .catch(err => {
        console.error("Fetch Error:", err);
        tbody.innerHTML = "<tr><td colspan='6' style='color:red;'>Error loading data.</td></tr>";
    });
}


// Load Registered Events for Dropdown
let myRegisteredEvents = [];

function loadRegisteredEvents() {

    fetch('php/StudentController/get_registration.php')
    .then(response => response.json())
    .then(data => {
        myRegisteredEvents = data;

        // Select both dropdowns
        const logSelect = document.getElementById('logEventSelect');
        const taskSelect = document.getElementById('taskEventSelect');

        let option = '<option value="">-- Select Event --</option>';

        if(data.length === 0) {
            option = "<option value=''>No registered events found</option>";
        } else 
        {
            data.forEach(event => {
                
                option += `<option value="${event.event_id}"> ${event.title} </option>`;
            });
        }

        if (logSelect) 
        {
            logSelect.innerHTML = option;
        }
            
        if (taskSelect) 
        {
            taskSelect.innerHTML = option;
        }
    })
}

function updateEventGoalInfo() {
    const select = document.getElementById('logEventSelect');
    const display = document.getElementById('goalDisplay');
    const selectedId = select.value;

    if (!selectedId) {
        display.style.display = "none";
        return;
    }

    // Find the specific event data from our global array
    const event = myRegisteredEvents.find(e => e.event_id == selectedId);

    if (event) {
        display.style.display = "block";
        display.innerHTML = `
            <div style="background: #eef2f3; padding: 10px; border-radius: 5px; border-left: 5px solid #3498db;">
                <p style="margin:0; font-weight:bold; color:#333;">Event Target:</p>
                <span style="font-size: 1.1em;">
                     Goal: <strong>${event.goal}kg</strong> &nbsp;|&nbsp; 
                     Remaining: <strong style="color: #e67e22;">${event.remaining}kg</strong>
                </span>
            </div>
        `;
    }
}



// ----------- volunteer hub ------------------
function loadVolunteerEvents() {
    const select = document.getElementById('volunteerEventSelect');
    if (!select) return;

    select.innerHTML = '<option>Loading...</option>';

    // We reuse get_registration because it has the data we need
    fetch('php/StudentController/get_volunteer_events.php')
    .then(res => res.json())
    .then(data => {
        select.innerHTML = '<option value="">-- Select Event --</option>';
        
        let count = 0;
        data.forEach(ev => {

            select.innerHTML += `<option value="${ev.event_id}">${ev.title}</option>`;
             count++;
        });

        if(count === 0) {
             select.innerHTML = '<option value="">No volunteer events found</option>';
        }
    })
    .catch(err => console.error(err));
}

function loadVolunteerDashboard() {
    const eventId = document.getElementById('volunteerEventSelect').value;
    if(!eventId) return;

    // Show Loading
    const progContainer = document.getElementById('volProgressContainer');
    progContainer.innerHTML = 'Loading stats...';
    progContainer.style.display = 'block';

    fetch(`php/StudentController/get_volunteer_data.php?event_id=${eventId}`)
    .then(res => res.text()) // Use .text() first to debug PHP errors
    .then(text => {
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("PHP Error:", text);
            throw new Error("Server returned invalid JSON. Check console.");
        }
    })
    .then(data => {
        console.log("Volunteer Data:", data); // <--- CHECK THIS IN CONSOLE (F12)

        if(data.status === 'error') {
            progContainer.innerHTML = `<p style="color:red;">${data.message}</p>`;
            return;
        }

        // 1. Render Progress
        const prog = data.progress;
        progContainer.innerHTML = `
            <div style="background: #eef2f3; padding: 12px; border-radius: 5px; border-left: 5px solid #3498db;">
                <p style="margin: 0 0 5px 0; font-weight: bold; color: #333;">Event Progress:</p>
                <span style="font-size: 1.1em; color: #2c3e50;">
                    Goal: <strong>${prog.goal}kg</strong> &nbsp;|&nbsp; 
                    Collected: <strong style="color: #27ae60;">${prog.collected}kg</strong> &nbsp;|&nbsp; 
                    Remaining: <strong style="color: #e67e22;">${prog.remaining}kg</strong>
                </span>
            </div>
        `;

        // 2. Render all student who join the specific events
        const pBody = document.getElementById('volParticipantsBody');
        const pPanel = document.getElementById('volParticipantsPanel');
        
        if (pBody && pPanel) {
            pBody.innerHTML = '';
            pPanel.style.display = 'block';

            if(data.participants.length === 0) {
                pBody.innerHTML = '<tr><td>No participants registered yet.</td></tr>';
            } else {
                data.participants.forEach(p => {
                    
                    // Determine Icon & Color based on Role
                    let roleBadge = '';
                    
                    // Normalize role string (handle lowercase/uppercase)
                    const rawRole = p.role ? p.role : 'Participant';
                    const role = rawRole.toLowerCase();

                    if (role === 'volunteer') {
                        // Gold Icon + Label
                        roleBadge = `<span style="color:#f39c12; font-size:0.9em; margin-left:8px;">
                                        <i class="fas fa-user-shield"></i> (Volunteer)
                                    </span>`;
                    } else {
                        // Green Icon + Label
                        roleBadge = `<span style="color:#27ae60; font-size:0.9em; margin-left:8px;">
                                        <i class="fas fa-user"></i> (Participant)
                                    </span>`;
                    }

                    pBody.innerHTML += `
                        <tr>
                            <td>
                                <span style="font-weight:500;">${p.username}</span> 
                                ${roleBadge}
                            </td>
                        </tr>`;
       
                });
            }
        }

        // 3. Render Logs
        const lBody = document.getElementById('volLogsBody');
        const lPanel = document.getElementById('volLogsPanel');
        
        if (lBody && lPanel) {
            lBody.innerHTML = '';
            lPanel.style.display = 'block';

            if(data.logs.length === 0) {
                lBody.innerHTML = '<tr><td colspan="5">No submissions yet.</td></tr>';
            } else {
                data.logs.forEach(l => {
                    const proof = l.photo_evidence ? `<a href="${l.photo_evidence}" target="_blank" style="color:blue;">View</a>` : '-';
                    
                    let statusColor = 'orange';
                    if(l.status === 'approved') statusColor = 'green';
                    if(l.status === 'rejected') statusColor = 'red';

                    lBody.innerHTML += `
                        <tr>
                            <td>${l.username}</td>
                            <td>${l.category}</td>
                            <td>${l.weight} kg</td>
                            <td><span style="color:${statusColor}; font-weight:bold;">${l.status}</span></td>
                            <td>${proof}</td>
                        </tr>`;
                });
            }
        }
    })
    .catch(err => {
        console.error("Vol Dashboard Error:", err);
        progContainer.innerHTML = `<p style="color:red;">Error loading data.</p>`;
    });
}





// Load Notifications
function loadNotifications() {
    const list = document.getElementById('notificationListBody');
    list.innerHTML = "<li class='notif-item'>Loading...</li>";

    fetch('php/StudentController/get_notification.php')
    .then(response => response.json())
    .then(data => {
        list.innerHTML = "";
        if(data.length === 0) {
            list.innerHTML = "<li class='notif-item'>No new notifications.</li>";
            return;
        }

        data.forEach(notif => {
  
            let readClass = '';

            // 0 = Unread, 1 = Read
            if (notif.is_read == 0) {
                readClass = 'unread';
            } else {
                readClass = '';
            }

            const html = `
            <li class="notif-item ${readClass}">
                <strong>${notif.title}</strong>
                <p>${notif.message}</p>
                <span class="time">${notif.created_at}</span>
            </li>`;
            list.innerHTML += html;


            if (data.length > 0) {
                fetch('php/StudentController/mark_read.php');
            }
        });
    })
    .catch(err => console.error(err));
}


// Load Tasks
function loadMyTasks() {
    const eventSelect = document.getElementById('taskEventSelect');
    const eventId = eventSelect.value;
    const container = document.getElementById('myTaskDisplay');

    if (!eventId) {
        container.innerHTML = "<p><i>Select an event to view your assigned tasks...</i></p>";
        return;
    }

    container.innerHTML = "<p>Loading tasks...</p>";

    fetch(`php/StudentController/get_student_task.php?event_id=${eventId}`)
    .then(response => response.json()) 
    .then(data => {
        container.innerHTML = ""; 

        if (data.length === 0) { // no data found in the table 
            container.innerHTML = "<p>No tasks assigned for this event yet.</p>";
            return;
        }


        data.forEach(task => {
            // check the color based on the color
            const badgeColor = task.role === 'Volunteer' ? '#f1c40f' : '#3498db'; // Yellow or Blue

            const html = `
            <div class="task-card">
                <div class="task-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <strong><i class="fas fa-thumbtack"></i> Task Assigned</strong>
                    
                    <span style="background-color: ${badgeColor}; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.85em;">
                        ${task.role}
                    </span>
                </div>
                <hr style="margin: 8px 0; border: 0; border-top: 1px solid #eee;">
                <p class="task-desc" style="font-size: 1.1em; color: #333;">${task.task_description}</p>
            </div>`;
            container.innerHTML += html;
        });
    })
    .catch(err => {
        //error handling
        console.error("Task load error:", err);
        container.innerHTML = "<p style='color:red'>Unable to load tasks. Please try again later.</p>";
    });
}

document.addEventListener('DOMContentLoaded', function() {
    //default tab 
    showSection('dashboard');
    loadMyLogs (); 
    loadMyTasks (); 

    loadRegisteredEvents(); 
});