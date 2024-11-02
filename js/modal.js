// Get elements
const bankButton = document.getElementById('BankButton');
const bankModal = document.getElementById('bankModal');

// Show the modal when "New Bank" button is clicked
bankButton.addEventListener('click', function() {
    bankModal.style.display = 'flex'; // Show modal as a flex container
});

// Close the modal
function closeModal() {
    bankModal.style.display = 'none'; // Hide modal
}

// Close the modal if the user clicks outside the modal content
window.onclick = function(event) {
    if (event.target == bankModal) {
        closeModal();
    }
}
