document.addEventListener('DOMContentLoaded', () => {
    // Check which analytics page we're on
    const isUserAnalytics = document.querySelector('.analytics-grid') !== null;
    const isAdminAnalytics = document.getElementById('admin-analytics') !== null;

    if (isUserAnalytics || isAdminAnalytics) {
        loadAnalytics();
    }
});

const loadAnalytics = async () => {
    try {
        const response = await fetch('php/fetch_analytics.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseData = await response.json();
        console.log('Response from server:', responseData); // Debug log
        
        if (responseData.success) {
            const data = responseData.data;
            
            // Check if we're on the user analytics page
            const userAnalyticsGrid = document.querySelector('.analytics-grid');
            if (userAnalyticsGrid) {
                updateUserAnalytics(data);
            }

            // Check if we're on the admin analytics page
            const adminAnalytics = document.getElementById('admin-analytics');
            if (adminAnalytics) {
                updateAdminAnalytics(data);
                // Show admin analytics section after update
                adminAnalytics.style.display = 'block';
            }
        } else {
            console.error('Server returned error:', responseData.error);
            showError(responseData.error || 'Failed to load analytics data');
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
        showError('Unable to connect to the server. Please try again later.');
    }
};

const updateUserAnalytics = (data) => {
    console.log('Updating user analytics with data:', data); // Debug log

    // Update trip counts
    if (data.totalTrips !== undefined) {
        document.getElementById('total-trips').textContent = formatNumber(data.totalTrips);
    }

    // Update completed and upcoming trips if available
    if (data.completedTrips !== undefined) {
        document.getElementById('completed-trips').textContent = formatNumber(data.completedTrips);
    }
    if (data.upcomingTrips !== undefined) {
        document.getElementById('upcoming-trips').textContent = formatNumber(data.upcomingTrips);
    }
    
    // Update budget information
    if (data.totalBudget !== undefined) {
        document.getElementById('total-budget').textContent = formatCurrency(data.totalBudget);
    }
    if (data.averageBudget !== undefined) {
        document.getElementById('avg-trip-cost').textContent = formatCurrency(data.averageBudget);
    }

    // Create budget breakdown chart if data exists
    if (data.budgetBreakdown && data.budgetBreakdown.length > 0) {
        const ctx = document.getElementById('budget-chart');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.budgetBreakdown.map(item => item.Category),
                    datasets: [{
                        data: data.budgetBreakdown.map(item => item.TotalAmount),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
};

const updateAdminAnalytics = (data) => {
    console.log('Updating admin analytics with data:', data); // Debug log

    // Update statistics
    if (data.totalUsers !== undefined) {
        const totalUsers = document.getElementById('total-users');
        if (totalUsers) {
            totalUsers.textContent = formatNumber(data.totalUsers);
        }
    }
    
    if (data.totalTrips !== undefined) {
        const totalTrips = document.getElementById('total-trips');
        if (totalTrips) {
            totalTrips.textContent = formatNumber(data.totalTrips);
        }
    }

    // Update average budget
    if (data.averageBudget !== undefined) {
        const averageBudget = document.getElementById('average-budget');
        if (averageBudget) {
            console.log('Setting average budget:', data.averageBudget); // Debug log
            averageBudget.textContent = formatNumber(data.averageBudget);
        }
    }

    // Update popular destinations
    if (data.popularDestinations && data.popularDestinations.length > 0) {
        const destinationsList = document.getElementById('popular-destinations');
        if (destinationsList) {
            destinationsList.innerHTML = data.popularDestinations
                .map(dest => `<li>${dest.Destination} (${dest.TripsCount} trips)</li>`)
                .join('');
        }
    }

    // Create budget breakdown chart if data exists
    if (data.budgetBreakdown && data.budgetBreakdown.length > 0) {
        const ctx = document.getElementById('budget-chart');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.budgetBreakdown.map(item => item.Category),
                    datasets: [{
                        data: data.budgetBreakdown.map(item => item.TotalAmount),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
};

// Utility Functions
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '$0.00';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount);
};

const formatNumber = (num) => {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('en-US').format(num);
};

const showError = (message) => {
    // Create error element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = `
        background-color: #ffebee;
        color: #c62828;
        padding: 15px;
        margin: 10px 0;
        border-radius: 4px;
        border-left: 5px solid #c62828;
    `;
    errorDiv.textContent = message;

    // Find a suitable container
    const container = document.querySelector('.analytics-container, main') || document.body;
    container.insertBefore(errorDiv, container.firstChild);

    // Remove after 5 seconds
    setTimeout(() => errorDiv.remove(), 5000);
};
