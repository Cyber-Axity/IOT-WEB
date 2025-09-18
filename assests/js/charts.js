// Chart initialization (extracted from dashboard.php)

// Keep references to charts to destroy before re-creating
if (!window.__chartRefs) { window.__chartRefs = {}; }

function initializeCharts() {
    // Always allow re-initialization for animations, but destroy existing charts first
    // Small delay to ensure canvas is ready for smooth animations
    setTimeout(() => {
        // Points Distribution Chart (Pie Chart)
    const pointsCtx = document.getElementById('pointsDistributionChart');
    if (pointsCtx && window.Chart && window.courseLabels && window.courseData && window.courseColors) {
        if (window.__chartRefs.pointsDistributionChart && typeof window.__chartRefs.pointsDistributionChart.destroy === 'function') {
            window.__chartRefs.pointsDistributionChart.destroy();
        }
        window.__chartRefs.pointsDistributionChart = new Chart(pointsCtx, {
            type: 'doughnut',
            data: {
                labels: window.courseLabels,
                datasets: [{
                    data: window.courseData,
                    backgroundColor: window.courseColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' students (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Activity Chart (Line Chart)
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx && window.Chart) {
        fetch('student/get_points_7days.php?_=' + Date.now())
          .then(r => r.json())
          .then(payload => {
            if (!payload || payload.status !== 'success') return;
            const days = payload.days || [];
            const totals = payload.totals || [];

            const weekday = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            const labels = days.map(function(d){
              const dd = new Date(d + 'T00:00:00');
              return weekday[dd.getDay()];
            });

            const info = document.getElementById('activityChartDate');
            if (info) {
              const now = new Date();
              info.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }

            if (window.__chartRefs.activityChart && typeof window.__chartRefs.activityChart.destroy === 'function') {
                window.__chartRefs.activityChart.destroy();
            }
            window.__chartRefs.activityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Points Earned',
                        data: totals,
                        borderColor: '#2e7d32',
                        backgroundColor: 'rgba(46, 125, 50, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2e7d32',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' } },
                        x: { grid: { color: 'rgba(0,0,0,0.1)' } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
          })
          .catch(() => {});
    }

    // Top Performers Chart (Bar Chart)
    const performersCtx = document.getElementById('topPerformersChart');
    if (performersCtx && window.Chart && typeof window.totalPoints === 'number') {
        if (window.__chartRefs.topPerformersChart && typeof window.__chartRefs.topPerformersChart.destroy === 'function') {
            window.__chartRefs.topPerformersChart.destroy();
        }
        window.__chartRefs.topPerformersChart = new Chart(performersCtx, {
            type: 'bar',
            data: {
                labels: ['Student 1', 'Student 2', 'Student 3', 'Student 4', 'Student 5'],
                datasets: [{
                    label: 'Points',
                    data: [window.totalPoints * 0.15, window.totalPoints * 0.12, window.totalPoints * 0.1, window.totalPoints * 0.08, window.totalPoints * 0.06],
                    backgroundColor: [
                        'rgba(46, 125, 50, 0.8)',
                        'rgba(67, 160, 71, 0.8)',
                        'rgba(102, 187, 106, 0.8)',
                        'rgba(129, 199, 132, 0.8)',
                        'rgba(165, 214, 167, 0.8)'
                    ],
                    borderColor: ['#2e7d32', '#43a047', '#66bb6a', '#81c784', '#a5d6a7'],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                animation: {
                    duration: 1800,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
    }, 100); // 100ms delay for smooth animations
}

// Expose globally
window.initializeCharts = initializeCharts;


