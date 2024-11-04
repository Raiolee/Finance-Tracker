// Function to handle row click and open the income modal
function incomeRowClick(incomeId) {
    console.log("Row clicked with income ID:", incomeId);
    
    // Open the modal
    const incomeRowModal = document.getElementById("incomeRowModal");
    if (incomeRowModal) {
        incomeRowModal.style.display = "flex";
    }

    fetch(`../APIs/get_income.php?id=${incomeId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Check what data is received
            if (data.error) {
                console.error('Error fetching income data:', data.error);
                return;
            }
            // Populate modal fields with income data
            document.getElementById("incomeSourceRow").value = data.source;
            document.getElementById("incomeBankRow").value = data.bank;
            document.getElementById("incomeAmountRow").value = data.total;
            document.getElementById("incomeCategoryRow").value = data.category;
            document.getElementById("incomeDateRow").value = data.date;
            document.getElementById("incomeDescriptionRow").value = data.description;
        })
        .catch(error => console.error('Error fetching income data:', error));
}

// Close the Income Row modal
function closeIncomeRowModal() {
    const incomeRowModal = document.getElementById("incomeRowModal");
    if (incomeRowModal) {
        incomeRowModal.style.display = 'none'; // Hide modal
    }
}

// Event listener for closing the modal when clicking the close button
document.querySelectorAll('.close').forEach(function(closeButton) {
    closeButton.addEventListener('click', function() {
        closeIncomeRowModal();
    });
});

// Event listener for closing the modal when clicking outside of it
window.onclick = function(event) {
    const incomeRowModal = document.getElementById("incomeRowModal");
    if (event.target == incomeRowModal) {
        closeIncomeRowModal();
    }
};