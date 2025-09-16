// Points & last activity auto-refresh logic (extracted from dashboard.php)

// Function to update points display (for RFID integration and polling)
function updatePointsDisplay(studentId, newPoints) {
    const pointsDisplay = document.getElementById('points-' + studentId);
    if (pointsDisplay) {
        pointsDisplay.textContent = newPoints.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// Polling: periodically refresh visible students' points
function refreshVisiblePoints() {
    const badges = document.querySelectorAll('.student-table .points-display[id^="points-"]');
    if (badges.length === 0) return;
    const ids = Array.from(badges).map(b => b.id.replace('points-', '')).join(',');
    fetch('student/get_points.php?ids=' + encodeURIComponent(ids) + '&_=' + Date.now())
      .then(r => r.json())
      .then(payload => {
          if (!payload || payload.status !== 'success') return;
          const data = payload.data || {};
          Object.keys(data).forEach(function(id){
              const rec = data[id] || {};
              const newPoints = parseFloat(rec.points || 0);
              const lastActivity = rec.last_activity || null;
              updatePointsDisplay(id, newPoints);
              updateLastActivityDisplay(id, lastActivity);
              
              // Also update modal if it's open for this student
              if (window.currentStudentData && window.currentStudentData.id == id) {
                  window.currentStudentData.points = newPoints;
                  const modalPoints = document.getElementById('modal-current-points');
                  if (modalPoints) {
                      modalPoints.textContent = newPoints.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                  }
              }
          });
      })
      .catch(() => {});
}

// Expose globally
window.refreshVisiblePoints = refreshVisiblePoints;
window.updatePointsDisplay = updatePointsDisplay;

// Update last activity cell by id
function updateLastActivityDisplay(studentId, newLastActivity) {
    if (!newLastActivity) return;
    const el = document.getElementById('activity-' + studentId);
    if (!el) return;
    const d = new Date(newLastActivity);
    const dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const timeStr = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    el.innerHTML = dateStr + '<br><span class="text-success">' + timeStr + '</span>';
}
window.updateLastActivityDisplay = updateLastActivityDisplay;


