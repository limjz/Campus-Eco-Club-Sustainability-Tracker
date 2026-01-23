// 1) Navigation
function showSection(sectionId) {
  document.querySelectorAll(".section").forEach(sec => sec.classList.remove("active-section"));
  document.getElementById(sectionId).classList.add("active-section");

  // sidebar active highlight
  document.querySelectorAll(".sidebar ul li").forEach(li => li.classList.remove("active"));
  const active = Array.from(document.querySelectorAll(".sidebar ul li"))
    .find(li => (li.getAttribute("onclick") || "").includes(sectionId));
  if (active) active.classList.add("active");

  // load chart only when stats page opens
  if (sectionId === "statistics") loadAdminChart();

  // update dashboard counts based on current pending rows
  updateDashboardCounts();
}

// 2) Dashboard counts (based on table rows)
function updateDashboardCounts() {
  const propCount = document.querySelectorAll("#proposalTableBody tr").length;
  const logCount = document.querySelectorAll("#logsTableBody tr").length;

  // update cards (uses the same titles from your HTML)
  setCardValue("Pending Proposals", propCount);
  setCardValue("Pending Logs", logCount);
}

function setCardValue(cardTitle, value) {
  document.querySelectorAll(".cards-container .card").forEach(card => {
    const h3 = card.querySelector("h3");
    const p = card.querySelector("p");
    if (!h3 || !p) return;

    if (h3.innerText.trim().toLowerCase() === cardTitle.toLowerCase()) {
      // keep "kg" if exists
      const hasKg = p.innerText.toLowerCase().includes("kg");
      p.innerText = hasKg ? `${value} kg` : value;
    }
  });
}

// 3) Proposal approve/reject (simulated)
function approveProposal(id) {
  if (!confirm("Approve this event proposal?")) return;

  // DATABASE CONNECT HERE:
  // fetch('php/approve_proposal.php', { method:'POST', body: JSON.stringify({id}) })

  const row = document.getElementById(`prop-${id}`);
  if (row) row.remove();

  updateDashboardCounts();
  alert("Event Approved!");
}

function rejectProposal(id) {
  if (!confirm("Reject this event proposal?")) return;

  // DATABASE CONNECT HERE:
  // fetch('php/reject_proposal.php', { method:'POST', body: JSON.stringify({id}) })

  const row = document.getElementById(`prop-${id}`);
  if (row) row.remove();

  updateDashboardCounts();
  alert("Proposal Rejected.");
}

// 4) Log verification (simulated)
function verifyLog(id) {
  if (!confirm("Verify this log and award points?")) return;

  // DATABASE CONNECT HERE:
  // fetch('php/verify_log.php', { method:'POST', body: JSON.stringify({id}) })

  const row = document.getElementById(`log-${id}`);
  if (row) row.remove();

  updateDashboardCounts();
  alert("Log Verified! Points awarded.");
}

// 5) Statistics Chart (Chart.js)
let adminChartInstance = null;

function loadAdminChart() {
  const canvas = document.getElementById("adminChart");
  if (!canvas) return;

  const ctx = canvas.getContext("2d");
  if (adminChartInstance) adminChartInstance.destroy();

  // DATABASE CONNECT HERE: replace data with real DB totals
  adminChartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Plastic", "Paper", "E-Waste", "Metal", "Glass"],
      datasets: [{
        label: "Total Collected (kg)",
        data: [500, 300, 200, 250, 100],
        backgroundColor: ["#4ca626", "#70cbff", "#ffa51f", "#ad8364", "#2d8cff"]
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });
}

// Init
document.addEventListener("DOMContentLoaded", () => {
  updateDashboardCounts();
});
