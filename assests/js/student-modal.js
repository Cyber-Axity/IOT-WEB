// Student Details Modal JavaScript
// Global variable to store current student data
let currentStudentData = {};

// Function to open student details modal
function openStudentModal(id, studentId, firstName, middleName, lastName, course, yearLevel, cardNo, points) {
    currentStudentData = {
        id: id,
        studentId: studentId,
        firstName: firstName,
        middleName: middleName,
        lastName: lastName,
        course: course,
        yearLevel: yearLevel,
        cardNo: cardNo,
        points: points
    };
    
    // Populate modal with student data
    document.getElementById('modal-student-id').textContent = studentId;
    document.getElementById('modal-student-name').textContent = lastName + ', ' + firstName + ' ' + (middleName ? middleName.charAt(0).toUpperCase() + '.' : '');
    document.getElementById('modal-student-course').textContent = course;
    document.getElementById('modal-student-year').textContent = yearLevel;
    document.getElementById('modal-student-card').textContent = cardNo;
    
    // Get current points from the table display (most up-to-date)
    const tablePointsElement = document.getElementById('points-' + id);
    const currentTablePoints = tablePointsElement ? parseFloat(tablePointsElement.textContent.replace(/,/g, '')) : points;
    document.getElementById('modal-current-points').textContent = currentTablePoints.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    currentStudentData.points = currentTablePoints;
    
    document.getElementById('redeemPoints').value = '';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
    modal.show();
}

// Function to update points display in table
function updatePointsDisplay(studentId, newPoints) {
    const pointsDisplay = document.getElementById('points-' + studentId);
    if (pointsDisplay) {
        pointsDisplay.textContent = newPoints.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// Function to redeem points
function redeemPoints() {
    const redeemAmount = parseFloat(document.getElementById('redeemPoints').value);
    const currentPoints = currentStudentData.points;
    
    if (!redeemAmount || redeemAmount <= 0) {
        alert('Please enter a valid amount to redeem.');
        return;
    }
    
    if (redeemAmount > currentPoints) {
        alert('Insufficient points. You only have ' + currentPoints.toLocaleString() + ' points.');
        return;
    }
    
    if (confirm('Are you sure you want to redeem ' + redeemAmount + ' points?')) {
        // Send AJAX request to redeem points
        fetch('student/redeem_points.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                student_id: currentStudentData.id,
                redeem_amount: redeemAmount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update modal display
                const newPoints = data.new_points !== undefined ? parseFloat(data.new_points) : (currentPoints - redeemAmount);
                document.getElementById('modal-current-points').textContent = newPoints.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                currentStudentData.points = newPoints;
                
                // Update table display
                updatePointsDisplay(currentStudentData.id, newPoints);
                
                // Clear input
                document.getElementById('redeemPoints').value = '';
                
                // Optional: message that could be displayed on RFID device UI
                const rfidMsg = data.rfid_message ? ('\n' + data.rfid_message) : '';
                alert('Points redeemed successfully! ' + redeemAmount + ' points have been deducted.' + rfidMsg);
                
                // Force refresh the points from database on next poll
                if (typeof refreshVisiblePoints === 'function') {
                    setTimeout(() => {
                        refreshVisiblePoints();
                    }, 1000);
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: update display locally
            const newPoints = currentPoints - redeemAmount;
            document.getElementById('modal-current-points').textContent = newPoints.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            currentStudentData.points = newPoints;
            updatePointsDisplay(currentStudentData.id, newPoints);
            document.getElementById('redeemPoints').value = '';
            
            alert('Points redeemed successfully! ' + redeemAmount + ' points have been deducted.');
        });
    }
}

// Function to close modal and clear data
function closeStudentModal() {
    currentStudentData = {};
    document.getElementById('redeemPoints').value = '';
}

// Make functions globally accessible
window.openStudentModal = openStudentModal;
window.redeemPoints = redeemPoints;
window.closeStudentModal = closeStudentModal;
window.updatePointsDisplay = updatePointsDisplay;
