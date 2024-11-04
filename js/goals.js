// Function to handle row click and open the goal modal
function goalRowClick(goalId) {
    console.log("Row clicked with goal ID:", goalId);
    
    // Open the modal
    const goalRowModal = document.getElementById("goalRowModal");
    if (goalRowModal) {
        goalRowModal.style.display = "flex";
    }

    fetch(`../APIs/get_goal.php?id=${goalId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Check what data is received
            if (data.error) {
                console.error('Error fetching goal data:', data.error);
                return;
            }
            // Populate modal fields with goal data
            document.getElementById("goalNameRow").value = data.subject;
            document.getElementById("goalCategoryRow").value = data.category;
            document.getElementById("goalStartRow").value = data.start_date;
            document.getElementById("goalAmountRow").value = data.target_amount;
            document.getElementById("goalDescriptionRow").value = data.description;
        })
        .catch(error => console.error('Error fetching goal data:', error));
}

// Close the Goal Row modal
function closeGoalRowModal() {
    const goalRowModal = document.getElementById("goalRowModal");
    if (goalRowModal) {
        goalRowModal.style.display = 'none'; // Hide modal
    }
}

// Event listener for closing the modal when clicking the close button
document.querySelectorAll('.close').forEach(function(closeButton) {
    closeButton.addEventListener('click', function() {
        closeGoalRowModal();
    });
});

// Event listener for closing the modal when clicking outside of it
window.onclick = function(event) {
    const goalRowModal = document.getElementById("goalRowModal");
    if (event.target == goalRowModal) {
        closeGoalRowModal();
    }
};