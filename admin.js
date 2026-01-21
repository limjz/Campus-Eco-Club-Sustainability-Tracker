// ===== Mock data (replace later with DB/API) =====
let logs = [
  { id:"L1001", student:"Aina (S001)", event:"Beach Clean-up", kg:3.4, status:"Pending" },
  { id:"L1002", student:"Hakim (S002)", event:"Recycling Drive", kg:1.8, status:"Approved" },
  { id:"L1003", student:"Lina (S003)", event:"Beach Clean-up", kg:2.1, status:"Pending" },
];

let proposals = [
  { id:"P2001", title:"Campus Composting", organiser:"Farah", status:"Pending", desc:"Compost bins around campus." },
  { id:"P2002", title:"Plastic-Free Week", organiser:"Nabil", status:"Approved", desc:"Reduce single-use plastics." },
  { id:"P2003", title:"Carpool Campaign", organiser:"Syafiq", status:"Pending", desc:"Encourage carpooling." }
];

let notifications = [
  { title:"Maintenance", msg:"System maintenance tonight 11PM.", time:"2026-01-18 18:00" },
  { title:"Bring Gloves", msg:"Bring gloves & water bottle.", time:"2026-01-16 07:40" }
];

// ===== Navigation =====
function showSection(id){
  document.querySelectorAll(".section").forEach(s => s.classList.remove("active-section"));
  document.getElementById(id).classList.add("active-section");

  document.querySelectorAll(".menu li").forEach(li => li.classList.remove("active"));
  const menuItem = Array.from(document.querySelectorAll(".menu li"))
    .find(li => li.getAttribute("onclick")?.includes(id));
  if(menuItem) menuItem.classList.add("active");

  const titles = {
    dashboard: ["Dashboard","Overview"],
    logs: ["Review Logs","Approve or reject student logs"],
    proposals: ["Review Proposals","Approve or reject event proposals"],
    stats: ["Statistics","Overall performance & leaderboard"],
    notifications: ["Notifications","Inbox and send messages"]
  };
  document.getElementById("pageTitle").innerText = titles[id][0];
  document.getElementById("pageDesc").innerText = titles[id][1];

  updateKPI();
  renderLogs();
  renderProposals();
  renderStats();
  renderNotifications();
}

// ===== KPI =====
function updateKPI(){
  const totalEvents = new Set(logs.map(l => l.event)).size;
  const pendingLogs = logs.filter(l => l.status === "Pending").length;
  const pendingProposals = proposals.filter(p => p.status === "Pending").length;

  document.getElementById("kpiEvents").innerText = totalEvents;
  document.getElementById("kpiPendingLogs").innerText = pendingLogs;
  document.getElementById("kpiPendingProposals").innerText = pendingProposals;

  document.getElementById("badgeLogs").innerText = pendingLogs;
  document.getElementById("badgeProposals").innerText = pendingProposals;
}

// ===== Logs table =====
function renderLogs(){
  const filter = document.getElementById("logFilter")?.value || "Pending";
  const q = (document.getElementById("logSearch")?.value || "").trim().toLowerCase();
  const tbody = document.getElementById("logTableBody");
  if(!tbody) return;

  let data = logs.slice();
  if(filter !== "All") data = data.filter(l => l.status === filter);
  if(q){
    data = data.filter(l =>
      l.id.toLowerCase().includes(q) ||
      l.student.toLowerCase().includes(q) ||
      l.event.toLowerCase().includes(q)
    );
  }

  tbody.innerHTML = "";
  if(data.length === 0){
    tbody.innerHTML = `<tr><td colspan="6">No logs found.</td></tr>`;
    return;
  }

  data.forEach(l => {
    tbody.innerHTML += `
      <tr>
        <td>${l.id}</td>
        <td>${l.student}</td>
        <td>${l.event}</td>
        <td>${l.kg}</td>
        <td>${statusBadge(l.status)}</td>
        <td>
          <button class="btn-outline" onclick="viewLog('${l.id}')">View</button>
          <button class="btn-primary" onclick="updateLog('${l.id}','Approved')" ${l.status!=="Pending"?"disabled":""}>Approve</button>
          <button class="btn-outline" onclick="updateLog('${l.id}','Rejected')" ${l.status!=="Pending"?"disabled":""}>Reject</button>
        </td>
      </tr>
    `;
  });
}

function updateLog(id, newStatus){
  const item = logs.find(l => l.id === id);
  if(!item) return;
  item.status = newStatus;

  updateKPI();
  renderLogs();
  renderStats();
}

// modal content
function viewLog(id){
  const l = logs.find(x => x.id === id);
  if(!l) return;

  openModal(
    "Log Details",
    `${l.student} • ${l.event}`,
    `
    <div class="details-card">
      <div class="details-section">
        <div class="details-section-title">Log Information</div>
        <div class="kv-grid">
          <div class="kv-key">Log ID</div><div class="kv-val">${l.id}</div>
          <div class="kv-key">Student</div><div class="kv-val">${l.student}</div>
          <div class="kv-key">Event</div><div class="kv-val">${l.event}</div>
          <div class="kv-key">Weight</div><div class="kv-val">${l.kg} KG</div>
          <div class="kv-key">Status</div><div class="kv-val">${l.status}</div>
        </div>
      </div>

      <div class="details-section">
        <div class="details-section-title">Evidence</div>
        <div class="evidence-box">
          Photo evidence will be shown here after database integration.
        </div>
      </div>
    </div>
    `
  );
}

// ===== Proposals table =====
function renderProposals(){
  const filter = document.getElementById("proposalFilter")?.value || "Pending";
  const q = (document.getElementById("proposalSearch")?.value || "").trim().toLowerCase();
  const tbody = document.getElementById("proposalTableBody");
  if(!tbody) return;

  let data = proposals.slice();
  if(filter !== "All") data = data.filter(p => p.status === filter);
  if(q){
    data = data.filter(p =>
      p.id.toLowerCase().includes(q) ||
      p.title.toLowerCase().includes(q) ||
      p.organiser.toLowerCase().includes(q)
    );
  }

  tbody.innerHTML = "";
  if(data.length === 0){
    tbody.innerHTML = `<tr><td colspan="5">No proposals found.</td></tr>`;
    return;
  }

  data.forEach(p => {
    tbody.innerHTML += `
      <tr>
        <td>${p.id}</td>
        <td>${p.title}</td>
        <td>${p.organiser}</td>
        <td>${statusBadge(p.status)}</td>
        <td>
          <button class="btn-outline" onclick="viewProposal('${p.id}')">View</button>
          <button class="btn-primary" onclick="updateProposal('${p.id}','Approved')" ${p.status!=="Pending"?"disabled":""}>Approve</button>
          <button class="btn-outline" onclick="updateProposal('${p.id}','Rejected')" ${p.status!=="Pending"?"disabled":""}>Reject</button>
        </td>
      </tr>
    `;
  });
}

function updateProposal(id, newStatus){
  const item = proposals.find(p => p.id === id);
  if(!item) return;
  item.status = newStatus;

  updateKPI();
  renderProposals();
}

function viewProposal(id){
  const p = proposals.find(x => x.id === id);
  if(!p) return;

  openModal(
    "Proposal Details",
    `${p.title} • Organiser: ${p.organiser}`,
    `
    <div class="details-card">
      <div class="details-section">
        <div class="details-section-title">Proposal Information</div>
        <div class="kv-grid">
          <div class="kv-key">Proposal ID</div><div class="kv-val">${p.id}</div>
          <div class="kv-key">Title</div><div class="kv-val">${p.title}</div>
          <div class="kv-key">Organiser</div><div class="kv-val">${p.organiser}</div>
          <div class="kv-key">Status</div><div class="kv-val">${p.status}</div>
        </div>
      </div>

      <div class="details-section">
        <div class="details-section-title">Description</div>
        <div class="kv-val" style="font-weight:600; color:#374151;">
          ${p.desc}
        </div>
      </div>
    </div>
    `
  );
}

// ===== Stats + Leaderboard =====
function renderStats(){
  const total = logs.length;
  const approvedLogs = logs.filter(l => l.status === "Approved");
  const approvedCount = approvedLogs.length;
  const approvedKg = approvedLogs.reduce((s,l)=> s + l.kg, 0);

  document.getElementById("statTotalLogs").innerText = total;
  document.getElementById("statApprovedLogs").innerText = approvedCount;
  document.getElementById("statApprovedKg").innerText = approvedKg.toFixed(1);

  const map = {};
  approvedLogs.forEach(l => { map[l.student] = (map[l.student] || 0) + l.kg; });
  const sorted = Object.entries(map).sort((a,b)=> b[1]-a[1]);

  const list = document.getElementById("leaderboard");
  list.innerHTML = "";
  if(sorted.length === 0){
    list.innerHTML = `<li>No approved logs yet.</li>`;
    return;
  }
  sorted.forEach(([name, kg], i) => {
    list.innerHTML += `<li><b>#${i+1} ${name}</b><span>${kg.toFixed(1)} KG</span></li>`;
  });
}

// ===== Notifications =====
function renderNotifications(){
  const q = (document.getElementById("notifSearch")?.value || "").trim().toLowerCase();
  const box = document.getElementById("notifList");
  if(!box) return;

  let list = notifications.slice().reverse();
  if(q){
    list = list.filter(n =>
      n.title.toLowerCase().includes(q) ||
      n.msg.toLowerCase().includes(q) ||
      n.time.toLowerCase().includes(q)
    );
  }

  box.innerHTML = "";
  if(list.length === 0){
    box.innerHTML = `<div class="notif-item">No notifications found.</div>`;
    return;
  }

  list.forEach(n => {
    box.innerHTML += `
      <div class="notif-item">
        <b>${n.title}</b>
        <div>${n.msg}</div>
        <small>${n.time}</small>
      </div>
    `;
  });
}

function sendNotification(e){
  e.preventDefault();
  const target = document.getElementById("notifTarget").value;
  const title = document.getElementById("notifTitle").value.trim();
  const msg = document.getElementById("notifMsg").value.trim();
  if(!title || !msg) return;

  const now = new Date();
  const time = now.toISOString().slice(0,16).replace("T"," ");

  notifications.push({ title: `[${target}] ${title}`, msg, time });
  document.getElementById("notifTitle").value = "";
  document.getElementById("notifMsg").value = "";

  renderNotifications();
  alert("Notification sent!");
}

// ===== Modal helpers =====
function openModal(title, sub, html){
  document.getElementById("modalTitle").innerText = title;
  document.getElementById("modalSub").innerText = sub || "Information";
  document.getElementById("modalBody").innerHTML = html;
  document.getElementById("modal").classList.remove("hidden");
}
function closeModal(){ document.getElementById("modal").classList.add("hidden"); }
function backdropClose(e){ if(e.target.id === "modal") closeModal(); }

// ===== Utilities =====
function statusBadge(status){
  const cls = status === "Pending" ? "pending" : status === "Approved" ? "approved" : "rejected";
  return `<span class="status ${cls}">${status}</span>`;
}
function logout(){ alert("Logout (demo). Later connect to login/session."); }

// Init
document.addEventListener("DOMContentLoaded", () => {
  updateKPI();
  renderLogs();
  renderProposals();
  renderStats();
  renderNotifications();
});
