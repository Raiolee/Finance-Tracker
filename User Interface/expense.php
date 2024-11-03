<?php
include('../APIs/init.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $subject = $_POST['subject'];
    $category = $_POST['category'];
    $expense_date = $_POST['expense-date'];
    $recurrence_type = $_POST['recurrence_type'];
    $merchant = $_POST['merchant'];
    $bank_id = $_POST['bank']; // Get the bank_id from the form submission
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $reimbursable = $_POST['reimbursable'];
    $bank_name = $_POST['bank_name'];

    $stmtBankName = $conn->prepare("SELECT bank FROM user_db.bank WHERE bank_id = ? AND user_id = ?");
    $stmtBankName->bind_param("si", $bank_id, $uid);
    $stmtBankName->execute();
    $stmtBankName->bind_result($bank_name);
    $stmtBankName->fetch();
    $stmtBankName->close();


    // Ensure $uid is defined; this should be set earlier in your code
    if (isset($uid)) {
        // Prepare the SQL statement to check the balance
        $stmtBalance = $conn->prepare("SELECT balance FROM user_db.bank WHERE user_id = ? AND bank_id = ?");
        $stmtBalance->bind_param("is", $uid, $bank_id);
        $stmtBalance->execute();
        $stmtBalance->bind_result($balance);
        $stmtBalance->fetch();
        $stmtBalance->close();

        // Check if the user has sufficient balance
        if (isset($balance) && $balance >= $amount) {
            // Insert the expense record
            $sql = "INSERT INTO expenses (user_id, subject, category, date, recurrence_type, merchant, bank, amount, description, reimbursable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // Bind parameters
                $stmt->bind_param("issssssdss", $uid, $subject, $category, $expense_date, $recurrence_type, $merchant, $bank_name, $amount, $description, $reimbursable);

                if ($stmt->execute()) {
                    // Update the balance using bank_id
                    $stmt2 = $conn->prepare("UPDATE user_db.bank SET balance = balance - ? WHERE user_id = ? AND bank_id = ?");
                    $stmt2->bind_param("dis", $amount, $uid, $bank_id);

                    if ($stmt2->execute()) {
                        // Redirect or provide success message
                        header("Location: Expense.php?success=1");
                        exit();
                    } else {
                        echo "Error updating balance: " . $stmt2->error;
                    }
                    $stmt2->close();
                } else {
                    echo "Error inserting data: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            // Inform the user about insufficient balance
            if (isset($balance)) {
                $shortfall = $amount - $balance;
                $errorMessage = "Transaction cannot proceed. Your balance is short by $" . number_format($shortfall, 2) . ".";
            } else {
                $errorMessage = "Could not retrieve balance information.";
            }
            echo "<div id='error-message' style='color: red;'>$errorMessage</div>";
            echo "<script>
                    setTimeout(function() {
                        var errorMsg = document.getElementById('error-message');
                        if (errorMsg) {
                            errorMsg.style.display = 'none';
                        }
                    }, 5000); // Hide message after 5 seconds
                  </script>";
        }
    } else {
        echo "User ID is not set."; // Ensure $uid is set correctly
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link rel="stylesheet" href="../Styles/custom-style.css">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <!-- All content is stored in container -->
    <div class="container">
        <!-- Include the Navbar -->
        <?php include('navbar.php'); ?>

        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header">Expenses</h1>
                        <div class="custom-header">
                            <button class="New-Saving" id="newExpenseBtn">+ Add an Expense</button>
                            <!-- Filter form -->
                            <form class="filter-form" id="filterForm" action="" method="GET">
                                <select class="var-input medium pointer" id="FilterGoalsCategory"
                                    name="FilterGoalsCategory">
                                    <option value="" disabled selected>Category</option>
                                    <option value="Travels">Travels</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                    <option value="Others">Others</option>
                                </select>
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/filter.svg" alt=""></i>
                                </button>
                            </form>
                            <!-- Search Form -->
                            <form class="search-form" action="" method="GET">
                                <input type="search" name="Incomequery" placeholder="Search here ..."
                                    style="text-transform: capitalize;">
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/magnifying-glass.svg" alt=""
                                            width="20px"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Put main code here -->

                    <table class="table-approval">
                        <thead>
                            <tr>
                                <th>Details</th>
                                <th>Merchant</th>
                                <th>Bank</th>
                                <th>Amount</th>
                                <th>Reimbursable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once "../connection/config.php";

                            // Query to fetch the required details from the expenses table
                            $query = "SELECT expense_id, subject AS details, merchant, bank, amount, reimbursable FROM expenses WHERE user_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='row-interact' onclick='expenseRowClick(" . $row['expense_id'] . ")'>";
                                    echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['merchant']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reimbursable']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No expenses found.</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <!-- APIs (Put APIs below this comment)-->
    <?php include('modals/modal-expense.php'); ?>
    <?php include('modals/modal-expense-row.php'); ?>
    <?php include('../APIs/get_expense.php'); ?>
    <script src="../js/modal.js"></script>
    <script src="../js/expense.js"></script>
</body>

</html>