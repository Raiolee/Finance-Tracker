// Function to handle row click and open the expense modal
function expenseRowClick(expenseId) {
    console.log("Row clicked with expense ID:", expenseId);
    
    // Open the modal
    const expenseRowModal = document.getElementById("expenseRowModal");
    if (expenseRowModal) {
        expenseRowModal.style.display = "flex";
    }

    fetch(`../APIs/get_expense.php?id=${expenseId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Check what data is received
            if (data.error) {
                console.error('Error fetching expense data:', data.error);
                return;
            }
            // Populate modal fields with expense data
            document.getElementById("subject").value = data.subject;
            document.getElementById("category").value = data.category;
            document.getElementById("expense-date").value = data.date;
            document.getElementById("recurrence_type").value = data.recurrence_type;
            document.getElementById("Merchant").value = data.merchant;
            document.getElementById("bank").value = data.bank;
            document.getElementById("amount").value = data.amount;
            document.getElementById("description").value = data.description;
            document.getElementById("reimbursable-yes").checked = data.reimbursable === "yes";
            document.getElementById("reimbursable-no").checked = data.reimbursable === "no";
        })
        .catch(error => console.error('Error fetching expense data:', error));
}

// Event listener for closing the modal when clicking the close button
document.querySelectorAll('.close').forEach(function(closeButton) {
    closeButton.addEventListener('click', function() {
        closeExpenseRowModal();
    });
});