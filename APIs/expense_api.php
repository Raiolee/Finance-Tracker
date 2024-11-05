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