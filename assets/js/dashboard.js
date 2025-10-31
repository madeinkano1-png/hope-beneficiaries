/**
 * Dashboard JavaScript
 * Handles chart rendering and dashboard interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts when page loads
    initializeCharts();
    
    // Auto-refresh dashboard every 5 minutes
    setInterval(refreshDashboard, 300000);
});

/**
 * Initialize all dashboard charts
 */
function initializeCharts() {
    if (typeof window.chartData === 'undefined') {
        console.error('Chart data not available');
        return;
    }
    
    // Initialize individual charts
    initializeStatusChart();
    initializeGenderChart();
    initializeDisbursementChart();
    initializeStateChart();
}

/**
 * Initialize tranche status pie chart
 */
function initializeStatusChart() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: window.chartData.tranche_status.labels,
            datasets: [{
                data: window.chartData.tranche_status.data,
                backgroundColor: window.chartData.tranche_status.colors,
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
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize gender distribution chart
 */
function initializeGenderChart() {
    const ctx = document.getElementById('genderChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: window.chartData.gender_distribution.labels,
            datasets: [{
                data: window.chartData.gender_distribution.data,
                backgroundColor: window.chartData.gender_distribution.colors,
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
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize disbursement trend chart
 */
function initializeDisbursementChart() {
    const ctx = document.getElementById('disbursementChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: window.chartData.disbursement_trend.labels,
            datasets: [{
                label: 'Amount Disbursed (₦)',
                data: window.chartData.disbursement_trend.amounts,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }, {
                label: 'Beneficiaries Count',
                data: window.chartData.disbursement_trend.counts,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 3,
                fill: false,
                tension: 0.4,
                pointBackgroundColor: '#198754',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Amount (₦)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return `Amount: ₦${context.parsed.y.toLocaleString()}`;
                            } else {
                                return `Count: ${context.parsed.y}`;
                            }
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize state distribution chart
 */
function initializeStateChart() {
    const ctx = document.getElementById('stateChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.chartData.state_distribution.labels,
            datasets: [{
                label: 'Beneficiaries',
                data: window.chartData.state_distribution.data,
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderColor: '#0d6efd',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed.x} beneficiaries`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Beneficiaries'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'States'
                    }
                }
            }
        }
    });
}

/**
 * Refresh dashboard data
 */
function refreshDashboard() {
    fetch('/dashboard/analytics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update chart data
                window.chartData = {
                    tranche_status: {
                        labels: ['Not Started', 'Partial', 'Completed'],
                        data: [
                            data.data.stats.tranche_status.NotStarted,
                            data.data.stats.tranche_status.Partial,
                            data.data.stats.tranche_status.Completed
                        ],
                        colors: ['#6c757d', '#ffc107', '#198754']
                    },
                    gender_distribution: {
                        labels: ['Male', 'Female'],
                        data: [
                            data.data.stats.gender_distribution.Male,
                            data.data.stats.gender_distribution.Female
                        ],
                        colors: ['#0d6efd', '#e83e8c']
                    },
                    state_distribution: data.data.state_distribution,
                    disbursement_trend: data.data.disbursement_trend
                };
                
                // Re-initialize charts with new data
                initializeCharts();
                
                // Update statistics cards
                updateStatisticsCards(data.data.stats);
                
                console.log('Dashboard refreshed successfully');
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
        });
}

/**
 * Update statistics cards with new data
 */
function updateStatisticsCards(stats) {
    // Update total beneficiaries
    const totalElement = document.querySelector('.stat-card .stat-number');
    if (totalElement) {
        totalElement.textContent = stats.total_beneficiaries.toLocaleString();
    }
    
    // Update completed count
    const completedElements = document.querySelectorAll('.stat-card.success .stat-number');
    if (completedElements.length > 0) {
        completedElements[0].textContent = stats.tranche_status.Completed.toLocaleString();
    }
    
    // Update pending count
    const totalPending = stats.tranche_status.NotStarted + stats.tranche_status.Partial;
    const pendingElements = document.querySelectorAll('.stat-card.warning .stat-number');
    if (pendingElements.length > 0) {
        pendingElements[0].textContent = totalPending.toLocaleString();
    }
    
    // Update total disbursed
    const disbursedElements = document.querySelectorAll('.stat-card.info .stat-number');
    if (disbursedElements.length > 0) {
        disbursedElements[0].textContent = '₦' + parseFloat(stats.total_disbursed).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

/**
 * Export dashboard data
 */
function exportDashboard(format = 'pdf') {
    const exportButton = document.querySelector(`[data-export="${format}"]`);
    if (exportButton) {
        exportButton.disabled = true;
        exportButton.innerHTML = '<i class="bi bi-spinner-border spinner-border-sm me-1"></i>Exporting...';
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/dashboard/export';
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    
    form.appendChild(formatInput);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after delay
    setTimeout(() => {
        if (exportButton) {
            exportButton.disabled = false;
            exportButton.innerHTML = `<i class="bi bi-download me-1"></i>Export ${format.toUpperCase()}`;
        }
    }, 3000);
}

/**
 * Show loading spinner
 */
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = `
        <div class="spinner-border text-primary spinner-border-lg" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(spinner);
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    const spinner = document.querySelector('.spinner-overlay');
    if (spinner) {
        spinner.remove();
    }
}

/**
 * Format currency for display
 */
function formatCurrency(amount) {
    return '₦' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format number for display
 */
function formatNumber(number) {
    return parseInt(number).toLocaleString();
}
