// Get elements for New Bank Modal
const bankButton = document.getElementById('BankButton');
const bankModal = document.getElementById('bankModal');

// Get elements for Allocate Modal
let bankModalAllocate; // Will be initialized when an Allocate button is clicked
const allocateButtons = document.querySelectorAll('button[onclick^="BankForm"]');

// Get elements for New Goal Modal
const goalButton = document.getElementById('newGoalBtn');
const goalModal = document.getElementById('goalModal');

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
