// --- NAVIGATION LOGIC ---
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active-section'));
    document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
    
    // Show selected
    document.getElementById(sectionId).classList.add('active-section');
    
    // Highlight selected sidebar (simple way, assumes order)
    const menuMap = { 'dashboard':0, 'proposals':1, 'volunteers' :2, 'statistics':3, 'notifications':4 };
    document.querySelectorAll('.sidebar li')[menuMap[sectionId]].classList.add('active');
}


// ---------------------- PROPOSAL ------------------------
//Function to load data from php 
function loadProposals ()
{
    const tbody = document.getElementById("proposalTableBody"); 
    tbody.innerHTML = "<tr><td>Loading...</td></tr>"; // loading text 

    // Call the PHP file
    fetch('php/get_proposals.php')
    .then(response => response.json()) // Convert text to JSON
    .then(data => {
        tbody.innerHTML = ""; // Clear the "Loading..." text

        //loop thru every proposal 
        data.forEach(prop => {
            // Create the HTML row
            const row = `<tr>
                <td>${prop.title}</td>
                <td>${prop.event_date}</td>
                <td class="status-${prop.status.toLowerCase()}">${prop.status}</td>
                <td>
                    <button class="btn-primary" style="padding:5px 10px;">View</button>
                </td>
            </tr>`;

            //next row
            tbody.innerHTML += row;
        });
    })
    .catch(error => {console.error('Error:', error)});
}

function submitProposal(e) {

    e.preventDefault(); // Stop page from refreshing 

    // Gather data from the form or table
    const formData = {
        title: document.getElementById('propTitle').value,
        date: document.getElementById('propDate').value,
        venue: document.getElementById('propVenue').value
    };


    // Send it to PHP
    fetch('php/add_proposal.php', {
        method: 'POST', // send data instead of receive data // default = GET
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData), // Convert JS object to Text for php to save data
    })
    .then(response => response.text()) // Read the text back from PHP
    .then(result => {
        // Show the result
        alert(result); 
        
        // If success, clear form
        if(result.includes("Success")) {
            document.getElementById('proposalForm').reset();
            loadProposals();
            
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Something went wrong!");
    });
}





// --------------- EVENT TASK MANAGEMENT ----------------

function updateTask (volunteerID){ 

    const inputID = `task-input-${volunteerID}`;
    const newTask = document.getElementById(inputID).value; 

    //send to php 
    fetch ('php/udpate_task.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({id: volunteerID, task: newTask})

    })
    .then (response => response.text())
    .then (result => { 
        alert(result); //Task udpated successful
        //loadVolunteers(); //refresh the list 
    })
}

function loadEventDropDown () {
    fetch ('php/get_events.php')
    .then (response => response.json())
    .then (data=> { 
        const select = document.getElementById('eventSelect'); 
        data.forEach (event => { 
            // add option in the selector flw the event_id
            select.innerHTML += `<option value = "${event.event_id}"> 
                                ${event.event_name}                     
                                </option>`;
        });
    })


}

// call in html 
function filterParticipants (){ 
    const eventID = document.getElementById('eventSelect').value; 
    const tbody = document.getElementById('volunteerTableBody');

    if (!eventID) return; //nothing found 

    tbody.innerHTML =   `<tr>
                            <td>Loading...</td>
                        </tr>`;

    // call php filter file with the ID 
    fetch (`php/get_participantsList_by_event.php?event_id= ${eventID}`)
    .then (response => response.json())
    .then (data => { 
        tbody.innerHTML = "";

        // no data found 
        if (data.length == 0){ 
            tbody.innerHTML = "<tr><td colspan='4'>No participants found for this event.</td></tr>"; 
            return; 
        }
        

        // have textField in assigned_task column 
        // have udpate button in action column 
        data.forEach (vol =>{
            const row = `<tr>
                <td> ${vol.name} </td> 
                <td> ${vol.role} </td> 
                <td> 
                    <input type="text" id="task-input-${vol.id}" value="${vol.current_task}" placeholder="Assign a task..."> 
                </td> 
                <td> 
                    <button onclick = "updateTask(${vol.id})" class = "btn-primary"> Update </button> 
                </td>

            </tr> `;
            tbody.innerHTML += row;
        })

    })

    
    
    


}






// --- STATISTICS LOGIC (Chart.js) ---
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

// ---  NOTIFICATION LOGIC ---
function sendNotification(e) {
    e.preventDefault();
    alert("Notification sent successfully to all selected users!");
}



// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // done modify 
    loadProposals();
    loadEventDropDown();

    updateChart();
});