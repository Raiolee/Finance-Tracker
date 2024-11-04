// Function to handle row click and open the savings modal
function savingsRowClick(bankId) {
    console.log("Row clicked with bank ID:", bankId);
    
    // Open the modal
    const savingsRowModal = document.getElementById("savingsRowModal");
    if (savingsRowModal) {
        savingsRowModal.style.display = "flex";
    }

    fetch(`../APIs/get_savings.php?id=${bankId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Check what data is received
            if (data.error) {
                console.error('Error fetching savings data:', data.error);
                return;
            }
            // Populate modal fields with savings data
            document.getElementById("bankName").value = data.purpose;
            document.getElementById("bank").value = data.bank;
            document.getElementById("bankAmount").value = data.balance;
        })
        .catch(error => console.error('Error fetching savings data:', error));
}

// Close the Savings Row modal
function closeSavingsRowModal() {
    const savingsRowModal = document.getElementById("savingsRowModal");
    if (savingsRowModal) {
        savingsRowModal.style.display = 'none'; // Hide modal
    }
}

// Event listener for closing the modal when clicking the close button
document.querySelectorAll('.close').forEach(function(closeButton) {
    closeButton.addEventListener('click', function() {
        closeSavingsRowModal();
    });
});

// Event listener for closing the modal when clicking outside of it
window.onclick = function(event) {
    const savingsRowModal = document.getElementById("savingsRowModal");
    if (event.target == savingsRowModal) {
        closeSavingsRowModal();
    }
};