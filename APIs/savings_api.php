<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
    exit();
}

$uid = $_SESSION["user_id"];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Include database connection
include '../connection/config.php';

// Fetch only the user_dp (profile picture) from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Prepare the profile picture for display
if ($user && $user['user_dp']) {
    // Convert BLOB to base64-encoded image
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    // If no profile picture is found, use a placeholder image
    $profile_pic = 'https://picsum.photos/100/100';
}
// Check if the connection was successful
if ($conn->connect_error) {
    die(sprintf("Connection failed: %s", $conn->connect_error));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check the action parameter to differentiate between requests
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'insert_bank') {
            // Collect form data for bank insertion
            $bankName = $_POST['bank-name'];  // Updated input name
            $bank = $_POST['bank'];
            $amount = $_POST['bank-amount'];   // New input for amount

            // Prepare and bind the SQL statement
            $sql = "INSERT INTO user_db.bank (user_id, purpose, bank, balance) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // Bind parameters
                $stmt->bind_param("issi", $uid, $bankName, $bank, $amount);
                // Updated parameter types

                // Execute the statement
                if ($stmt->execute()) {
                    // Redirect back to the dashboard with success message
                    header("Location: Savings.php?success=1");
                    exit();
                } else {
                    $error_message = "Error executing statement: {$stmt->error}";
                }
            } else {
                $error_message = "Error preparing statement: {$conn->error}";
            }
        } elseif ($action === 'another_action') {
            // Collect form data
            $goal = $_POST['bank-category'] ?? '';
            $amount = $_POST['allocate-amount'] ?? 0;
            $category = $_POST['frequency'] ?? '';
            $date = $_POST['allocate-date'] ?? '';
            $bank = $_POST['bank'] ?? '';

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
                    // Prepare the SQL statement for the other action
                    $stmt = $conn->prepare("INSERT INTO user_db.savings (user_id, date, bank, subject, savings_amount, category) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssis", $uid, $date, $bank, $goal, $amount, $category);

                    // Execute the insert statement
                    if ($stmt->execute()) {
                        // Prepare the SQL statement to update the balance
                        $stmt2 = $conn->prepare("UPDATE user_db.bank SET balance = balance - ? WHERE user_id = ? AND bank = ?");
                        $stmt2->bind_param("dis", $amount, $uid, $bank); // Bind parameters for the update

                        // Execute the update statement
                        if ($stmt2->execute()) {
                            // Redirect or provide success message
                            header("Location: Savings.php?success=1");
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
        } else {
            echo "Unknown action.";
        }
    } else {
        echo "No action specified.";
    }
}
// Form Handling End
// Fetch existing savings for the user
$sql = "SELECT user_bank_id, bank, balance FROM user_db.bank WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $error_message = "Error preparing statement: {$conn->error}";
}

// Fetch existing savings for the income
$sql2 = "SELECT total FROM user_db.income WHERE user_id = ?";
$stmt2 = $conn->prepare($sql2);

if ($stmt2) {
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
} else {
    $error_message = "Error preparing statement: {$conn->error}";
}

// Fetch existing goals for the user
$sql3 = "SELECT subject, category FROM user_db.goals WHERE user_id = ?";
$stmt3 = $conn->prepare($sql3);

if ($stmt3) {
    $stmt3->bind_param("i", $uid);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
} else {
    $error_message = "Error preparing statement: {$conn->error}";
}

?>