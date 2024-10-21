// quickreport.js

document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('quickReportChart').getContext('2d');
    
    if (ctx) {
        console.log("Canvas found, creating pie chart...");

        // Destroy existing chart if it exists
        if (window.quickReportChart && window.quickReportChart.destroy) {
            console.log("Destroying existing chart...");
            window.quickReportChart.destroy();
        }

        window.quickReportChart = new Chart(ctx, {
            type: 'pie',  // Set chart type to 'pie'
            data: {
                labels: ['Fuel', 'Accommodation', 'Travel Expenses', 'Office Supplies'],
                datasets: [{
                    label: 'Expenses',
                    data: [10, 20, 30, 40],
                    backgroundColor: ['#2c3e50', '#16a085', '#9b59b6', '#c0392b'],
                    borderColor: ['#2c3e50', '#16a085', '#9b59b6', '#c0392b'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,  // Allow chart to resize based on container
                layout: {
                    padding: {
                        top: 20,   // Add some top padding for spacing
                        bottom: 20 // Add some bottom padding
                    }
                },
                plugins: {
                    legend: {
                        display: true,  // Ensure the legend is displayed
                        labels: {
                            usePointStyle: true  // Use a point style for the legend icons (like circles)
                        }
                    }
                }
            }
        });
    } else {
        console.error("Canvas not found.");
    }
});

