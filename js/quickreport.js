document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('quickReportChart').getContext('2d');
    
    if (ctx) {
        console.log("Canvas found, creating pie chart...");

        // Destroy existing chart if it exists
        if (window.quickReportChart && window.quickReportChart.destroy) {
            console.log("Destroying existing chart...");
            window.quickReportChart.destroy();
        }

        // Fetch data from the server
        fetch('../APIs/get_expenses.php')
            .then(response => response.json())
            .then(data => {
                // Prepare data for the chart
                const labels = data.map(item => item.subject);
                const expensesData = data.map(item => parseFloat(item.total_expense));
                
                window.quickReportChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Expenses',
                            data: expensesData,
                            backgroundColor: ['#2c3e50', '#16a085', '#9b59b6', '#c0392b'],
                            borderColor: ['#2c3e50', '#16a085', '#9b59b6', '#c0392b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 20,
                                bottom: 20
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching expenses:', error));
    } else {
        console.error("Canvas not found.");
    }
});
