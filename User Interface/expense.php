<?php
include('../APIs/init.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $subject = $_POST['subject'];
    $category = $_POST['category'];
    $expense_date = $_POST['expense-date'];
    $recurrence_type = $_POST['recurrence_type'];
    $merchant = $_POST['merchant'];
    $bank = $_POST['bank'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $reimbursable = $_POST['reimbursable'];

    // Handle file upload if needed
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Directory to save uploaded files
        $attachment = $target_dir . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment);
    }

    // Ensure $uid is defined; this should be set earlier in your code
    if (isset($uid)) {
        // Prepare the SQL statement to check the balance
        $stmtBalance = $conn->prepare("SELECT balance FROM user_db.bank WHERE user_id = ? AND bank = ?");
        $stmtBalance->bind_param("is", $uid, $bank);
        $stmtBalance->execute();
        $stmtBalance->bind_result($balance);
        $stmtBalance->fetch();
        $stmtBalance->close();

        // Check if the user has sufficient balance
        if ($balance >= $amount) {
            // Prepare and bind the SQL statement to insert the expense
            $sql = "INSERT INTO expenses (user_id, subject, category, date, recurrence_type, merchant, bank, amount, description, reimbursable, receipt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // Bind parameters
                $stmt->bind_param("issssssdsss", $uid, $subject, $category, $expense_date, $recurrence_type, $merchant, $bank, $amount, $description, $reimbursable, $attachment);

                // Execute the insert statement
                if ($stmt->execute()) {
                    // Prepare the SQL statement to update the balance
                    $stmt2 = $conn->prepare("UPDATE user_db.bank SET balance = balance - ? WHERE user_id = ? AND bank = ?");
                    $stmt2->bind_param("dis", $amount, $uid, $bank);

                    // Execute the update statement
                    if ($stmt2->execute()) {
                        // Redirect or provide success message
                        header("Location: Expense.php?success=1");
                        exit();
                    } else {
                        echo "Error updating balance: " . $stmt2->error; // Debugging message for update
                    }

                    // Close the update statement
                    $stmt2->close();
                } else {
                    echo "Error inserting data: " . $stmt->error; // Debugging message for insert
                }

                // Close the insert statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            // Inform the user about insufficient balance
            $shortfall = $amount - $balance;
            $errorMessage = "Transaction cannot proceed. Your balance is short by $" . number_format($shortfall, 2) . ".";
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
                                <select class="var-input medium pointer" id="FilterGoalsCategory" name="FilterGoalsCategory">
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
                                <input type="search" name="Incomequery" placeholder="Search here ..." style="text-transform: capitalize;">
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/magnifying-glass.svg" alt="" width="20px"></i>
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
    <script src="../js/modal.js"></script>
    <script src="../js/expense.js"></script>
</body>

</html>