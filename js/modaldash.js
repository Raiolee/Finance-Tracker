// Function to open the modal
function openModalExpense() {
    document.getElementById("expenseModal").style.display = "flex";
}

// Function to close the modal
function closeModalExpense() {
           document.getElementById("expenseModal").style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
       const modal = document.getElementById("expenseModal");
       if (event.target == modal) {
       modal.style.display = "none";
       }
   }
function openModalIncome() {
    document.getElementById("incomeModal").style.display = "flex";
}

// Function to close the modal
function closeModalIncome() {
           document.getElementById("incomeModal").style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
       const modal = document.getElementById("incomeModal");
       if (event.target == modal) {
       modal.style.display = "none";
       }
   }  
   
   function openGoalIncome() {
    document.getElementById("goalModal").style.display = "flex";
}

// Function to close the modal
function closeModalGoal() {
           document.getElementById("goalModal").style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
       const modal = document.getElementById("goalModal");
       if (event.target == modal) {
       modal.style.display = "none";
       }
   }    

// Function to open the modal
function openModalBank() {
    document.getElementById("bankModal").style.display = "flex";
}

// Function to close the modal
function closeModalBank() {
           document.getElementById("bankModal").style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
       const modal = document.getElementById("bankModal");
       if (event.target == modal) {
       modal.style.display = "none";
       }
   }         
