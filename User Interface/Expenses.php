<?php
session_start();
if(!isset($_SESSION["user"]))
{
    header("Location: ../Login.php");
}
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body class="container">
    <div class="nav-bar">
        <div class="Profile">
            <div class="Profile_img">
                <img src="https://picsum.photos/100/100" alt="" width="110">
            </div>
        </div>

        <div class="user-name">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>

        <!-- Section for Dashboard -->
        <div class="Home-Nav <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/home.svg" alt="Icon" width="50px" id="icons">
            </div>
            <div>
                <p><a href="Dashboard.php">Home</a></p>
            </div>
        </div>

        <!-- Section for Expenses -->
        <div class="Expenses-Nav <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/expenses.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Expenses.php">Expenses</a></p>
            </div>
        </div>

        <!-- Section for Income -->
        <div class="Travels-Nav <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/income.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Income.php">Income</a></p>
            </div>
        </div>

        <!-- Section for Goals -->
        <div class="Travels-Nav <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Goals.php">Goals</a></p>
            </div>
        </div>

        <!-- Section for Savings -->
        <div class="Approvals-Nav <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/reports.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Savings.php">Savings</a></p>
            </div>            
        </div>

        <!-- Settings Section -->
        <div class="Settings-Nav <?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Settings.php">Settings</a></p>
            </div>
        </div>

        <div class="Logo-Nav" id="Nav_Side">
            <div class="Penny_Logo">
                <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
            </div>
        </div>
    </div>

    <div class="content">
        <div class="right-container">
            <div class="Inner-container">
                <div id="inner_container">
                    <div class="Top-container-Approval">
                        <div class="Left-Top">
                            <p>Expenses</p>
                        </div>
                        <div class="Right-Top">
                            <button id="newExpenseButton">New Expense</button>
                        </div>
                    </div>

                    <hr class="bottom-line">

                    <table class="table-approval">
                        <thead>
                            <tr>
                                <th>DETAILS</th>
                                <th>MERCHANT</th>
                                <th>AMOUNT</th>
                                <th>REPORT</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>    
                    </table>
                </div>

                <!-- New Expense Form -->
                <div id="newExpenseForm" class="new-expense-form" style="display:none;">
                    <h3>Add New Expense</h3>
                    <div class="Expense-Outer">
                    <div class="Expense-Form">
                        <form id="expenseForm" onsubmit="return handleSubmit(event);">
                            <div class="Expense-Form-row">
                                <label for="subjectCategory" id="Expense-text">Subject</label>
                                <input type="text" id="subjectCategory" name="subjectCategory" required>
                            </div>
                            <div class="Expense-Form-row">
                                <label for="Merchant" id="Expense-text">Merchant</label>
                                <input type="text" id="Merchant" name="Merchant" required>
                            </div>
                            <div class="Expense-Form-row">
                                <label for="DateCategory" id="Expense-text">Date</label>
                                <input type="date" id="DateCategory" name="DateCategory" required>
                            </div>
                            <div class="Expense-Form-row">
                                <label for="Total" id="Expense-text">Total</label>
                                    <div class="Total-Currency">
                                    <input type="number" id="Total" name="Total" required>
                                    <select id="Currency" name="Currency" required>
                                        <option value="" disabled selected>Currency</option>
                                        <option value="Type1">Type1</option>
                                        <option value="Type2">Type2</option>
                                        <option value="Type3">Type3</option>
                                        <option value="Type4">Type4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="Expense-Form-row">
                                <input type="checkbox" id="reimburse" name="reimburse">
                                <label for="reimburse" id="Expense-text">Reimbursable</label>
                            </div>
                            <div class="Expense-Form-row">
                                <label for="ExpenseCategory" id="Expense-text">Type</label>
                                <select id="ExpenseCategory" name="ExpenseCategory">
                                    <option value="Type1">Type1</option>
                                    <option value="Type2">Type2</option>
                                    <option value="Type3">Type3</option>
                                    <option value="Type4">Type4</option>
                                </select>
                            </div>
                            <div class="Expense-Form-row">
                                <label for="Description" id="Expense-text">Description</label>
                                <input type="text" id="Description" name="Description" required>
                            </div>
                            <div class="Expense-Form-row">
                                <label for="Employee" id="Expense-text">Employee</label>
                                <input type="text" id="Employee" name="Employee" required>
                            </div>
                            <div class="Expense-Form-row">
                                <button type="submit">Submit</button>
                                <button type="button" onclick="closeExpenseForm()">Cancel</button>
                            </div>
                        </form>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
        // Check if the URL hash is set to #newExpenseForm when the page loads
        if (window.location.hash === '#newExpenseForm') {
            const form = document.getElementById('newExpenseForm');
            const rightContainer = document.getElementById('inner_container');

            rightContainer.style.display = 'none'; // Hide the right container
            form.style.display = 'block'; // Show the new expense form

            // Prevent default hash jump
            window.scrollTo(0, 0);

            // Remove the hash from the URL without refreshing the page
            window.history.pushState("", document.title, window.location.pathname);
        }};

        document.getElementById('newExpenseButton').addEventListener('click', function() {
            const rightContainer = document.getElementById('inner_container');
            const form = document.getElementById('newExpenseForm');
            rightContainer.style.display = 'none'; // Hide the right container
            form.style.display = 'block'; // Show the new expense form
        });

        function closeExpenseForm() {
            const rightContainer = document.getElementById('inner_container');
            const form = document.getElementById('newExpenseForm');

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
            document.getElementById('expenseForm').reset(); // Clear all form fields
        }
    </script>
</body>
</html>