document.addEventListener("DOMContentLoaded", function() {
    function updateButtonText() {
        const buttons = [
            { id: "newExpenseBtn", fullText: "+ Add an Expense" },
            { id: "newIncomeBtn", fullText: "+ Add an Income" },
            { id: "newGoalBtn", fullText: "+ Add a Goal" },
            { id: "BankButton", fullText: "+ Add a Bank" }
        ];

        buttons.forEach(buttonInfo => {
            const button = document.getElementById(buttonInfo.id);
            if (button) {
                if (window.innerWidth <= 768) {
                    button.textContent = "+"; // Change text to "+"
                } else {
                    button.textContent = buttonInfo.fullText; // Change text to full text
                }
            }
        });
    }

    // Update button text on page load
    updateButtonText();

    // Update button text on window resize
    window.addEventListener("resize", updateButtonText);
});