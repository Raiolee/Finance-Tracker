// Get elements for New Bank Modal
const bankButton = document.getElementById('BankButton');
const bankModal = document.getElementById('bankModal');

// Get elements for Allocate Modal
let bankModalAllocate; // Will be initialized when an Allocate button is clicked
const allocateButtons = document.querySelectorAll('button[onclick^="BankForm"]');

// Show the New Bank modal when "New Bank" button is clicked
bankButton.addEventListener('click', function () {
    bankModal.style.display = 'flex'; // Show modal as a flex container
});

// Close the New Bank modal
function closeModal() {
    bankModal.style.display = 'none'; // Hide modal
}

// Close modal if the user clicks outside the New Bank modal content
window.onclick = function (event) {
    if (event.target == bankModal) {
        closeModal();
    }
}

// Show Allocate Modal and populate it with data
function BankForm(bank, balance) {
    // Set up the modal for allocation (bankModalAllocate)
    bankModalAllocate = document.getElementById('bankModalAllocate'); // ID of the new modal
    bankModalAllocate.style.display = 'flex'; // Show the Allocate modal

    // Populate fields
    document.querySelector('input[name="bank"]').value = bank; // Set bank name
    document.querySelector('input[name="bank-balance"]').value = balance; // Set balance

    // Close modal if the user clicks outside the Allocate modal content
    window.onclick = function (event) {
        if (event.target == bankModalAllocate) {
            closeModalAllocate();
        }
    }
}

// Close the Allocate modal
function closeModalAllocate() {
    bankModalAllocate.style.display = 'none'; // Hide Allocate modal
}
