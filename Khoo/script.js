function handleLogin(e) {
    e.preventDefault();
    // Hide login, show app
    document.getElementById('login-view').style.display = 'none';
    document.getElementById('app-sidebar').style.display = 'flex';
    document.getElementById('app-content').style.display = 'block';
    // Load default view
    switchView('student-dash'); 
}

function switchView(viewId, event) {
    // Hide all sections
    const sections = document.querySelectorAll('.view-section');
    sections.forEach(sec => {
        sec.classList.remove('active');
        // Small timeout to allow fade out effect logic if extended later
        setTimeout(() => { 
            if(!sec.classList.contains('active')) {
                sec.style.display = 'none'; 
            }
        }, 100); 
    });

    // Show selected section
    const selected = document.getElementById(viewId);
    if(selected) {
        selected.style.display = 'block';
        setTimeout(() => selected.classList.add('active'), 10);
    }

    // Update Header Title based on view
    const titles = {
        'student-dash': 'Student Dashboard',
        'registration-page': 'Event Registration',
        'participant-dash': 'Participant Action Center',
        'volunteer-dash': 'Volunteer Command Center'
    };
    
    const pageTitle = document.getElementById('page-title');
    if(pageTitle) {
        pageTitle.innerText = titles[viewId] || 'Dashboard';
    }

    // Update Sidebar Active State
    document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
    
    // Only add active class if the click event exists (prevents error on initial load)
    if(event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

function openRegisterModal(eventName) {
    document.getElementById('reg-event-name').innerText = eventName;
    document.getElementById('reg-modal').style.display = 'block';
}

function closeModal() {
    alert('Registration Successful!'); 
    document.getElementById('reg-modal').style.display='none';
}