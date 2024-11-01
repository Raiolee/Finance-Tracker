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
