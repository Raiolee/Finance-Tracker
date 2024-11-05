<?php
include('../APIs/init.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the action type from the form
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'insert_expense') {
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

            // Fetch the bank name based on bank_id and user_id
            $stmtBankName = $conn->prepare("SELECT bank FROM user_db.bank WHERE bank_id = ? AND user_id = ?");
            $stmtBankName->bind_param("si", $bank_id, $uid);
            $stmtBankName->execute();
            $stmtBankName->bind_result($bank_name);
            $stmtBankName->fetch();
            $stmtBankName->close();

            // Ensure $uid is defined
            if (isset($uid)) {
                // Check the balance
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
                            // Update the balance
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
                    // Insufficient balance
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
                            }, 5000);
                          </script>";
                }
            } else {
                echo "User ID is not set.";
            }
        } // <-- End of insert_expense action block
        elseif ($action === 'edit-action') {
            // Collect form data for editing the expense
            $expense_id = $_POST['expense-id']; // Ensure you pass the expense_id from the form
            $subject = $_POST['subject'];
            $category = $_POST['category'];
            $expense_date = $_POST['expense-date'];
            $recurrence_type = $_POST['recurrence_type'];
            $merchant = $_POST['merchant'];
            $bank_id = $_POST['bank']; // Get the bank_id from the form submission
            $amount = $_POST['amount'];
            $description = $_POST['description'];
            $reimbursable = $_POST['reimbursable'];
            
            // Ensure $uid is defined, and that you have a valid expense ID
            if (empty($expense_id)) {
                echo "Error: Missing required fields.";
                exit();
            }
            // Prepare the SQL statement to update the expense record
            $stmt = $conn->prepare("UPDATE user_db.expenses SET subject = ?, category = ?, date = ?, recurrence_type = ?, merchant = ?, bank = ?, amount = ?, description = ?, reimbursable = ? WHERE expense_id = ?");
            
            // Bind the parameters (s = string, d = decimal, i = integer)
            $stmt->bind_param("ssssssdsdi", 
                $subject, $category, $expense_date, $recurrence_type, $merchant, $bank_id, $amount, $description, $reimbursable, $expense_id);
            
            // Execute the prepared statement
            if ($stmt->execute()) {
                // If the update is successful, redirect or show a success message
                header("Location: Expense.php?edit_success=1");
                exit();
            } else {
                $errorMessage = "Could not retrieve balance information.";
                // If there is an error, show the error message
                echo "Error updating expense record: " . $stmt->error;
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
            $stmt->close();
        } // <-- End of edit-expense action block
        elseif ($action === 'delete-action') {
            // Get the expense ID from the form submission
            $expense_id = $_POST['expense-id'];

            // Ensure the expense ID is not empty
            if (empty($expense_id)) {
                echo "Error: Missing expense ID.";
                exit();
            }

            // Prepare the SQL statement to delete the expense record
            $stmt = $conn->prepare("DELETE FROM user_db.expenses WHERE expense_id = ?");
            $stmt->bind_param("i", $expense_id);  // 'i' for integer (expense_id is assumed to be an integer)

            // Execute the statement
            if ($stmt->execute()) {
                // If the deletion is successful, redirect to the Expense page with a success message
                header("Location: Expense.php?delete_success=1");
                exit();
            } else {
                // If there is an error, show the error message
                echo "Error deleting expense record: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Invalid action.";
        }
    } // <-- End of isset($_POST['action']) block
}


?>