window.onload = function () {
    // Check if the URL hash is set to #newExpenseForm when the page loads
    if (window.location.hash === '#newExpenseForm') {
        const form = document.getElementById('expenseForm'); // Updated form ID
        const rightContainer = document.getElementById('inner_container');

        rightContainer.style.display = 'none'; // Hide the right container
        form.style.display = 'block'; // Show the new expense form

        // Prevent default hash jump
        window.scrollTo(0, 0);
        
        window.history.pushState("", document.title, window.location.pathname);
    }
};

document.getElementById('newExpenseButton').addEventListener('click', function () {
    const rightContainer = document.getElementById('inner_container');
    const form = document.getElementById('expenseForm'); // Updated form ID
    rightContainer.style.display = 'none'; // Hide the right container
    form.style.display = 'block'; // Show the new expense form
});

function closeExpenseForm() {
    const rightContainer = document.getElementById('inner_container');
    const form = document.getElementById('expenseForm'); // Updated form ID

    form.style.display = 'none'; // Hide the new expense form
    rightContainer.style.display = 'block'; // Show the right container again
    clearForm(); // Clear the form fields
}

function handleSubmit(event) {
    event.preventDefault(); // Prevent the default form submission
    // Add form submission logic here if needed
    closeExpenseForm(); // Close the form and return to the right container
}

function clearForm() {
    document.getElementById('expenseForm').reset(); // Updated form ID to clear form fields
}
