document.getElementById('expenseForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Sanitize and process the form data here if needed

    // Clear the form inputs
    document.getElementById('subject').value = '';
    document.getElementById('category').value = '';
    document.getElementById('expense-date').value = '';
    document.getElementById('recurrence_type').value = '';
    document.getElementById('Merchant').value = '';
    document.getElementById('bank').value = '';
    document.getElementById('amount').value = '';
    document.getElementById('description').value = '';
    document.getElementById('reimbursable-yes').checked = false;
    document.getElementById('reimbursable-no').checked = true;
    document.getElementById('file-input').value = '';

    // Optionally, close the modal
    closeModalExpense();
});

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
            document.getElementById("subject-row").value = data.subject;
            document.getElementById("category-row").value = data.category;
            document.getElementById("expense-date-row").value = data.date;
            document.getElementById("recurrence_type-row").value = data.recurrence_type;
            document.getElementById("merchant-row").value = data.merchant;
            document.getElementById("bank-row").value = data.bank;
            document.getElementById("amount-row").value = data.amount;
            document.getElementById("description-row").value = data.description;
            document.getElementById("reimbursable-yes-row").checked = data.reimbursable === "yes";
            document.getElementById("reimbursable-no-row").checked = data.reimbursable === "no";
        })
        .catch(error => console.error('Error fetching expense data:', error));
}

// Close the Expense Row modal
function closeExpenseRowModal() {
    const expenseRowModal = document.getElementById("expenseRowModal");
    if (expenseRowModal) {
        expenseRowModal.style.display = 'none'; // Hide modal
    }
}

// Event listener for closing the modal when clicking the close button
document.querySelectorAll('.close').forEach(function(closeButton) {
    closeButton.addEventListener('click', function() {
        closeExpenseRowModal();
    });
});