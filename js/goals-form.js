function showGoalForm() {
    // Show the Goal form
    document.getElementById('GoalForm').style.display = 'block';

    // Hide other elements
    document.getElementById('newGoalsBtn').style.display = 'none';
    document.getElementById('filterForm').style.display = 'none';
    document.getElementById('searchForm').style.display = 'none';
    document.getElementById('goalsTable').style.display = 'none';

    // Change the header text
    document.getElementById('headerText').textContent = 'Add a Goal';
}

function closeGoalForm() {
    // Hide the Goal form
    document.getElementById('GoalForm').style.display = 'none';

    // Show other elements
    document.getElementById('newGoalsBtn').style.display = 'inline-block';
    document.getElementById('filterForm').style.display = 'inline-block';
    document.getElementById('searchForm').style.display = 'inline-block';
    document.getElementById('goalsTable').style.display = 'table';

    // Reset the header text
    document.getElementById('headerText').textContent = 'Goals';
}

function sortTable(column) {
    const currentUrl = new URL(window.location.href);
    const params = new URLSearchParams(currentUrl.search);
    
    // Check the current sort order for the selected column and toggle it
    let nextSortOrder = params.get('sortOrder') === 'asc' ? 'desc' : 'asc';
    
    // Set the sort order and the column to sort
    params.set('sortOrder', nextSortOrder);
    params.set('sortColumn', column); // You can set the column name to sort by
    
    // Update the URL and reload the page
    window.location.search = params.toString();
}
