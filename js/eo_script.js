// ---------- NAVIGATION LOGIC ------------
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active-section'));
    document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
    
    // Show selected
    document.getElementById(sectionId).classList.add('active-section');
    
    // Highlight selected sidebar (simple way, assumes order)
    const menuMap = { 'dashboard':0, 'proposals':1, 'assign-tasks' :2, 'statistics':3, 'notifications':4 };
    document.querySelectorAll('.sidebar li')[menuMap[sectionId]].classList.add('active');

    if (sectionId === 'statistics') {
        // Wait 100ms for the tab to become visible, then draw
        setTimeout(() => {
            updateChart();
        }, 100);
    }
}


// ---------------------- PROPOSAL ------------------------
let allProposals = [];

function loadProposals () //Function to load data from php 
{
    const tbody = document.getElementById("proposalTableBody"); 
    tbody.innerHTML = "<tr><td>Loading...</td></tr>"; // loading text 

    // Call the PHP file
    fetch('php/EoController/get_proposals.php')
    .then(response => response.json()) // Convert text to JSON
    .then(data => {
        allProposals = data; // save the data into the arrays for display purpose in view button
        tbody.innerHTML = ""; // Clear the "Loading..." text

        if (data.length === 0){ 
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 20px;">No proposals found.</td></tr>'; 
            return;
        }

        //loop thru every proposal 
        data.forEach((prop, index) => {


            let actionButtons = '';

            // 1. CHECK STATUS
            if (prop.status === 'Approved' || prop.status === 'Rejected') {
                
            // CASE A: Read-Only (Only show 'View')
            actionButtons = `
                <button class="btn-primary" style="padding:5px 10px;" onclick="viewProposal(${index})">
                    View
                </button>
            `;

            } else {
                
                // CASE B: Editable (Show View, Delete, Edit) - Using your exact HTML
                actionButtons = `
                    <button class="btn-primary" style="padding:5px 10px;" onclick="viewProposal(${index})">
                        View
                    </button>

                    <button class="btn-danger" style="padding:5px 10px;" onclick="deleteProposal(${index})">
                        Delete
                    </button>

                    <button class="btn-primary" style="padding:5px 10px;" onclick="editProposal(${index})">
                        Edit
                    </button>
                `;
            }

            // Create the HTML row
            const row = `<tr>
                <td>${prop.title}</td>
                <td>${prop.event_date}</td>
                <td><span class="status-${prop.status.toLowerCase().trim()}">
                    ${prop.status}
                </span></td>
                <td>
                    ${actionButtons}
                </td>
                
            </tr>`;

            //next row
            tbody.innerHTML += row;
        });
    })
    .catch(error => {console.error('Error:', error)});
}

function submitProposal (e) {

    e.preventDefault(); // Stop page from refreshing 

    // check if is editing or adding the details of proposal 
    const editId = document.getElementById('editProposalId').value;

    // Gather data from the form or table
    const formData = {
        title: document.getElementById('propTitle').value,
        event_date: document.getElementById('propDate').value,
        event_time: document.getElementById('propTime').value,
        venue: document.getElementById('propVenue').value,
        description : document.getElementById('propDesc').value,
        target_goal: document.getElementById('target_goal').value 
    };

    let url = 'php/EoController/add_proposal.php'; // set default: adding the proposal - direct to the add_proposal.php

    if (editId) {
        url = 'php/EoController/edit_proposal.php'; // if the edit button got trigger - direct to the edit_proposal.php
        formData.proposal_id = editId; // Add ID to the data sent to PHP
    }


    // Send it to PHP
    fetch(url, {
        method: 'POST', // send data instead of receive data // default = GET
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData), // Convert JS object to Text for php to save data
    })
    .then(response => response.json()) // Read the text back from PHP
    .then(data => {
        // Show the result
        alert(data.message); 
        
        // If success, clear form
        if(data.status === "success") {
            document.getElementById('proposalForm').reset();
            document.getElementById('editProposalId').value = ""; // Clear ID
            
            const submitBtn = document.querySelector('#proposalForm button[type="submit"]');
            if(submitBtn) submitBtn.innerText = "Submit Proposal"; // Reset text
            
            loadProposals(); // refresh table 
            
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Something went wrong!");
    });
}

function viewProposal (index){ 
    const prop = allProposals[index];

    console.log("Button clicked! Index:", index);
    console.log("All Data:", allProposals);

    // Fill the HTML elements with data
    document.getElementById('viewTitle').innerText = prop.title;
    document.getElementById('viewDate').innerText = prop.event_date;
    document.getElementById('viewTime').innerText = prop.event_time;
    document.getElementById('viewStatus').innerText = prop.status;
    
    const goal = prop.target_goal; 
    document.getElementById('viewGoal').innerText = goal + " kg";

    // Check if these fields exist in the db, if not will display empty
    document.getElementById('viewVenue').innerText = prop.venue;
    document.getElementById('viewDesc').innerText = prop.description;
    

    // Show the modal
    document.getElementById('proposalModal').style.display = 'block';

}

function deleteProposal (id) { 
    if (!confirm("Are you sure you want to delete this proposal?")) {
        return;
    }

    fetch('php/EoController/delete_proposal.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ proposal_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            loadProposals(); // Refresh the table, the original proposal will gone 
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Something went wrong deleting the proposal.");
    });

}


function editProposal(index) {
    // Get the data from our global list
    const prop = allProposals[index];
    
    // check to prevent editing if it's already approved
    const status = prop.status.toLowerCase().trim()
    if (status === 'approved' || status === 'rejected') {
        alert(`You cannot edit an ${status} event.`);
        return;
    }

    // Fill the form inputs with the existing data 
    document.getElementById('editProposalId').value = prop.id; // Store ID and update in html code line 100
    document.getElementById('propTitle').value = prop.title;
    document.getElementById('propDate').value = prop.event_date;
    document.getElementById('propTime').value = prop.event_time;
    document.getElementById('propVenue').value = prop.venue;
    document.getElementById('propDesc').value = prop.description;
    document.getElementById('target_goal').value = prop.target_goal;

    //Change Button Text from "Submit" to "Update"
    const submitBtn = document.querySelector('#proposalForm button[type="submit"]');
    if(submitBtn) submitBtn.innerText = "Update Proposal";
    
    document.getElementById('proposalForm').scrollIntoView({ behavior: 'smooth' });
}

 function closeModal (){ 
    document.getElementById('proposalModal').style.display = 'none';
 }



// include load chart, task and notif 
function loadEventDropDown () {
    fetch ('php/EoController/get_events.php')
    .then (response => response.json())
    .then (data=> { 

        const statSelect = document.getElementById('eventSelectStats'); // for chart page 
        const taskSelect = document.getElementById('eventSelect');  // for task page 
        const notifSelect = document.getElementById('notifEventSelect');  // for notif page 
        

        const fillSelect = (selectElement) => {
            if (!selectElement) return; // Skip if this dropdown doesn't exist
            
            selectElement.innerHTML = '<option value="">-- Select Event --</option>'; // Reset
            
            data.forEach(event => { 
                selectElement.innerHTML += `<option value="${event.event_id}">${event.title}</option>`;
            });
        };

        fillSelect(taskSelect);
        fillSelect(statSelect);
        fillSelect(notifSelect);

        // Auto load the chart for the first event in the drop down
        if (statSelect && data.length > 0) {
            statSelect.value = data[0].event_id; // Select first item
            updateChart(); // Load the chart immediately
        }

    })
    .catch(err => console.error("Error loading events:", err));
}


// --------------- EVENT TASK MANAGEMENT ----------------
let currentAttendees = []; // Store data globally for filtering

// Load the data when Event Dropdown changes
function filterParticipants() {
    const eventID = document.getElementById('eventSelect').value; 
    const tbody = document.getElementById('volunteerTableBody');
    const progressDiv = document.getElementById('taskProgressContainer');

    // Reset table if no event selected
    if (!eventID) {
        tbody.innerHTML = "<tr><td colspan='4' style='text-align:center;'>Please select an event.</td></tr>";
        if(progressDiv) progressDiv.style.display = "none";
        return; 
    }

    if(progressDiv) {
        // Show loading state first
        progressDiv.style.display = "block";
        progressDiv.innerHTML = "<p>Loading progress...</p>";
        
        fetch(`php/EoController/get_event_progress.php?event_id=${eventID}`)
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {

                //html code for the progress check 
                progressDiv.innerHTML = `
                    <div style="background: #eef2f3; padding: 12px; border-radius: 5px; border-left: 5px solid #3498db;">
                
                        <p style="margin: 0 0 5px 0; font-weight: bold; color: #333; font-size: 1em;">
                            Event Progress:
                        </p>

                        <span style="font-size: 1.1em; color: #2c3e50;">
                            Goal: <strong>${data.goal}kg</strong> 
                            &nbsp;|&nbsp; 
                            Collected: <strong style="color: #27ae60;">${data.collected}kg</strong>
                            &nbsp;|&nbsp; 
                            Remaining: <strong style="color: #e67e22;">${data.remaining}kg</strong>
                        </span>
                    </div>
                `;
            }
        })
        .catch(err => console.error("Progress fetch error:", err));
    }



    tbody.innerHTML = "<tr><td colspan='4' style='text-align:center;'>Loading...</td></tr>";

    // Fetch from the PHP file we just fixed
    fetch(`php/EoController/get_participantsList_by_event.php?event_id=${eventID}`)
    .then(response => response.json())
    .then(data => { 
        currentAttendees = data; // Save data to global variable
        renderTaskTable();       // Draw the table
    })
    .catch(err => {
        console.error(err);
        tbody.innerHTML = "<tr><td colspan='4' style='text-align:center; color:red;'>Error loading data.</td></tr>";
    });
}





//  Draw the table based on Checkboxes
function renderTaskTable() {
    const tbody = document.getElementById('volunteerTableBody');
    tbody.innerHTML = "";

    const showVolunteers = document.getElementById('chkVolunteer').checked;
    const showParticipants = document.getElementById('chkParticipant').checked;

    const filteredData = currentAttendees.filter(person => {
        if (person.role === 'Volunteer' && showVolunteers) return true;
        if (person.role === 'Participant' && showParticipants) return true;
        return false;
    });

    if (filteredData.length === 0) {
        tbody.innerHTML = "<tr><td colspan='4' style='text-align:center;'>No students found.</td></tr>";
        return; 
    }

    filteredData.forEach(vol => {
        //get ID & Name (Matching your uploaded PHP file)
        const regId = vol.registration_id; 
        
        // Your PHP file sends 'username', so we must use 'username'
        const nameToShow = vol.username || "Unknown"; 

        const placeholder = vol.role === 'Volunteer' ? 'Assign Task...' : 'Give Instructions...';
        const badgeColor = vol.role === 'Volunteer' ? '#f1c40f' : '#3498db';

        // Generate Row
        // The onclick only sends 'regId'. It does NOT send 'Participant' anymore.
        const row = `<tr>
            <td>${nameToShow}</td>
            <td>
                <span style="background-color: ${badgeColor}; color: white; padding: 3px 8px; border-radius: 4px;">
                    ${vol.role}
                </span>
            </td>
            <td> 
                <input type="text" id="task-input-${regId}" class="form-control" 
                       value="${vol.current_task || ''}" placeholder="${placeholder}" style="width: 100%;"> 
            </td> 
            <td> 
                <button onclick="updateTask(${regId})" class="btn-primary" style="padding: 5px 15px;">Update</button> 
            </td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

// Save the task when "Update" is clicked
function updateTask(regId) {
    const inputID = `task-input-${regId}`;
    const newTask = document.getElementById(inputID).value; 

    fetch('php/EoController/update_task.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({
            registration_id: regId, // Sending the correct ID
            task_description: newTask
        })
    })
    .then(response => response.json())
    .then(result => { 
        if(result.status === 'success') {
            alert("Updated successfully!");
        } else {
            alert("Error: " + result.message);
        }
    })
    .catch(err => console.error(err));
}






// --- STATISTICS (RECYLING STATS) ---
let myChart = null;

function updateChart() {
    const eventId = document.getElementById('eventSelectStats').value;
    const ctx = document.getElementById('recyclingChart').getContext('2d');

    if (!eventId){
        return; //if no event selected, dont load 
    }

    // get data from database 
    fetch (`php/EoController/get_event_statis.php?event_id=${eventId}`)
    .then (response => response.json())
    .then (data => { 
        console.log("Chart Data Received:", data); // <--- CHECK THIS IN CONSOLE
        
        //destroy the old chart if exits 
        if (myChart) myChart.destroy();

        if (!data || !data.labels || data.labels.length === 0) {
            console.log("Data is empty");
            if(myChart) myChart.destroy(); // Clear the old chart if needed
            return;
        }

        myChart = new Chart(ctx, { 
            type: 'bar',
            data: {
                labels: data.labels, // Data from PHP file 
                datasets: [{
                    label: `Recycled Amount (kg)`,
                    data: data.values, // Data from PHP file 
                    backgroundColor: ['#3498db', '#e74c3c', '#f1c40f', '#2ecc71', '#9b59b6'], 
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false, // fits the container
                scales: {
                    y: { beginAtZero: true }
                },
                responsive: true
            }


        });
 
    })
    .catch(error => console.error('Error loading stats:', error));
}

// ---  NOTIFICATION  ---
function sendNotification(e) {
    e.preventDefault(); // stop page refresh 

    // 1. Get Values
    const eventId = document.getElementById('notifEventSelect').value;
    const title = document.getElementById('notifTitle').value;
    const message = document.getElementById('notifMessage').value;
    const form = document.getElementById('notifForm');

   // make sure all the field have been filled in 
    if (!eventId || !title || !message) {
        alert("Please fill in all fields.");
        return;
    }

    //data send
    fetch('php/EoController/send_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            event_id: eventId, 
            title: title, 
            message: message 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message); // "Sent to X students!"
            form.reset();        // Clear the form
        } else {
            alert("Server Error: " + data.message);
        }
    })
    .catch(err => {
        console.error("Error sending notification:", err);
        alert("Failed to send notification. Check console for details.");
    });
}




// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadProposals();
    loadEventDropDown();

    updateChart();
});