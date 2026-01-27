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
        'register': 'nav-register',
        'logs': 'nav-logs',
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

    else if(sectionId === 'register') {
        loadAvailableEvents();
    }
    
    // for participants(logs) and volunteers page(tasks)
    else if (sectionId === 'logs' || sectionId === 'tasks') {
        // Load the dropdowns for specific modes
        loadRegisteredEvents();
    }
    else if (sectionId === 'history'){
        loadMyLogs();
    }


}

// Load "Register Event" List (Open Events)
function loadAvailableEvents() {
    const tbody = document.getElementById('eventListBody');
    tbody.innerHTML = "<tr><td colspan='4'>Loading open events...</td></tr>"; //loading text if thers no data show up

    fetch('php/StudentController/get_open_events.php')
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = ""; //clear the loading text, page is empty now 
        
        if (data.length === 0) {
            tbody.innerHTML = "<tr><td colspan='4'>No upcoming events found.</td></tr>";
            return;
        }

        // register event button also in the table, either register as volunteer or participant 
        data.forEach(event => {
            const row = `<tr>
                <td>${event.title}</td>
                <td>${event.date}</td>
                <td>${event.venue || 'TBA'}</td>
                <td>
                    <button class="btn-primary" onclick="registerForEvent('${event.event_id}', 'Participant')">Join as Participant</button>
                    <button class="btn-primary" style="background-color:#2c3e50;" onclick="registerForEvent('${event.event_id}', 'Volunteer')">Join as Volunteer</button>
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
function loadRegisteredEvents() {

    fetch('php/StudentController/get_registration.php')
    .then(response => response.json())
    .then(data => {

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

    fetch(`php/StudentController/get_volunteer_task.php?event_id=${eventId}`)
    .then(response => response.json()) 
    .then(data => {
        container.innerHTML = ""; 

        if (data.length === 0) { // no data found in the table 
            container.innerHTML = "<p>No tasks assigned for this event yet.</p>";
            return;
        }


        data.forEach(task => {
            const html = `
            <div class="task-card">
                <div class="task-header">
                    <strong><i class="fas fa-thumbtack"></i> Task Assigned</strong>
                </div>
                <p class="task-desc">${task.task_description}</p>
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