// Chart initialization (extracted from dashboard.php)

function initializeCharts() {
    // Points Distribution Chart (Pie Chart)
    const pointsCtx = document.getElementById('pointsDistributionChart');
    if (pointsCtx && window.Chart && window.courseLabels && window.courseData && window.courseColors) {
        new Chart(pointsCtx, {
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

            // Convert Y-m-d to weekday labels (Mon..Sun)
            const weekday = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            const labels = days.map(d => {
              const dd = new Date(d + 'T00:00:00');
              return weekday[dd.getDay()];
            });

            // Append current date below chart
            const info = document.getElementById('activityChartDate');
            if (info) {
              const now = new Date();
              info.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }

            new Chart(activityCtx, {
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
        new Chart(performersCtx, {
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
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
}

// Expose globally
window.initializeCharts = initializeCharts;


