// Get elements for New Bank Modal
const bankButton = document.getElementById('BankButton');
const bankModal = document.getElementById('bankModal');

// Get elements for Allocate Modal
let bankModalAllocate; // Will be initialized when an Allocate button is clicked
const allocateButtons = document.querySelectorAll('button[onclick^="BankForm"]');

// Get elements for New Goal Modal
const goalButton = document.getElementById('newGoalBtn');
const goalModal = document.getElementById('goalModal');

// Get elements for Income Modal
const incomeButton = document.getElementById('newIncomeBtn');
const incomeModal = document.getElementById('incomeModal');

// Get elements for Expense Modal
const expenseButton = document.getElementById('newExpenseBtn');
const expenseModal = document.getElementById('expenseModal');

// Show the New Goal modal when "New Goal" button is clicked
if (goalButton && goalModal) {
    goalButton.addEventListener('click', function () {
        goalModal.style.display = 'flex'; // Show modal as a flex container
    });
}

// Close the New Goal modal
function closeModalGoal() {
    if (goalModal) {
        goalModal.style.display = 'none'; // Hide modal
    }
}

// Close modal if the user clicks outside the New Goal modal content
window.onclick = function (event) {
    if (event.target == goalModal) {
        closeGoalModal();
    } else if (event.target == bankModal) {
        closeModal();
    } else if (event.target == bankModalAllocate) {
        closeModalAllocate();
    } else if (event.target == incomeModal) {
        closeModalIncome();
    } else if (event.target == expenseModal) {
        closeModalExpense();
    } else if (event.target == expenseRowModal) {
        closeExpenseRowModal();
    }
}

// Show the New Bank modal when "New Bank" button is clicked
if (bankButton && bankModal) {
    bankButton.addEventListener('click', function () {
        bankModal.style.display = 'flex'; // Show modal as a flex container
    });
}

// Close the New Bank modal
function closeModal() {
    if (bankModal) {
        bankModal.style.display = 'none'; // Hide modal
    }
}

// Show Allocate Modal and populate it with data
function BankForm(bank, balance) {
    // Set up the modal for allocation (bankModalAllocate)
    bankModalAllocate = document.getElementById('bankModalAllocate'); // ID of the new modal
    if (bankModalAllocate) {
        bankModalAllocate.style.display = 'flex'; // Show the Allocate modal

        // Populate fields
        document.querySelector('input[name="bank"]').value = bank; // Set bank name
        document.querySelector('input[name="bank-balance"]').value = balance; // Set balance
    }
}

// Close the Allocate modal
function closeModalAllocate() {
    if (bankModalAllocate) {
        bankModalAllocate.style.display = 'none'; // Hide Allocate modal
    }
}


// Show the New Income modal when "New Income" button is clicked
if (incomeButton && incomeModal) {
    incomeButton.addEventListener('click', function () {
        incomeModal.style.display = 'flex'; // Show modal as a flex container
    });
}

// Close the New Income modal
function closeModalIncome() {
    if (incomeModal) {
        incomeModal.style.display = 'none'; // Hide modal
    }
}

// Show the New Expense modal when "New Expense" button is clicked
if (expenseButton && expenseModal) {
    expenseButton.addEventListener('click', function () {
        expenseModal.style.display = 'flex'; // Show modal as a flex container
    });
}

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

// Close the Expense Row modal
function closeExpenseRowModal() {
    const expenseRowModal = document.getElementById("expenseRowModal");
    if (expenseRowModal) {
        expenseRowModal.style.display = 'none'; // Hide modal
    }
}

// Close modal if the user clicks outside the Expense Row modal content
window.onclick = function (event) {
    const expenseRowModal = document.getElementById("expenseRowModal");
    if (event.target == expenseRowModal) {
        closeExpenseRowModal();
    }
}