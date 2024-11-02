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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../Styles/mobilestyles.scss">
    <style>
    </style>
</head>

<body>
    <div class="container">
        <?php include("navbar.php") ?>
        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content scrollable">
                    <div class="top-bar space-between">
                        <h1 class="header">Bank</h1>
                        <button class="New-Saving" id="BankButton">+ New Bank</button>
                    </div>
                    <table class="table-approval" id="Bank-Content">
                        <thead>
                            <tr>
                                <th class="th-interact" onclick="sortTable('subject')">
                                    Number
                                </th>
                                <th>
                                    Bank
                                </th>
                                <th class="th-interact" onclick="sortTable('accomplishment_date')">
                                    Balance
                                </th>
                                <th>
                                    Manage
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($result) && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Use the correct variables from the current row
                                    echo "<tr>
                                                <td>" . htmlspecialchars($row['user_bank_id']) . "</td>
                                                <td>" . htmlspecialchars($row['bank']) . "</td>
                                                <td>" . htmlspecialchars($row['balance']) . "</td>
                                                <td>
                                                    <button onclick=\"BankForm('" . htmlspecialchars($row['bank']) . "', '" . htmlspecialchars($row['balance']) . "')\">Allocate</button>
                                                </td>
                                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No results found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </section> <!--Section End-->
    </div>

    <script>
        function showPopup(subject, balance, bank, category, date) {
            document.getElementById('popup-title').innerText = `Description`;
            document.getElementById('popup-description').innerText = `Bank: ${bank}\nAmount: ${balance}\nDate: ${date}\nSubject: ${subject}\nCategory: ${category}`;
            document.getElementById('popup').style.display = 'block';
        }

        function BankForm(bank, balance, user_bank_id) {
            document.getElementById('popup-title-Bank').innerText = `Manage Savings`;

            // Create the form HTML
            const formHTML = `
        <p>Bank: ${bank}</p>
        <p>Balance: ${balance}</p>
        <form id="bank-form" method="post" action="">
            

            <label for="goal">Subject:</label>
            <select class="var-input large" name="goal" id="goal">
                ${getCategoryOptions()}
            </select>
            <br>

            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>
            <br>

            <label for="date" class="Savings-Label">Date:</label>
            <input type="date" id="date" name="date" required>
            <br> 
            
            <label for="category">Category:</label>
            <select class="var-input large" name="category" id="category">
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
            </select>
            <br>

            <button type="submit">Submit</button>
        </form>
    `;

            // Set the innerHTML of the popup description
            document.getElementById('popup-description-Bank').innerHTML = formHTML;

            // Show the popup
            document.getElementById('popup-Bank').style.display = 'block';

            // Attach an event listener to handle form submission
            document.getElementById('bank-form').addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                // Retrieve values from the form
                const balanceValue = balance;
                const bankValue = bank; // The bank passed to the function
                const goal = document.getElementById('goal').value; // This is the subject now
                const amount = document.getElementById('amount').value;
                const date = document.getElementById('date').value; // Make sure the ID is 'date'
                const category = document.getElementById('category').value;

                // Process the data as needed
                console.log(`Bank: ${bankValue}, Subject: ${goal}, Amount: ${amount}, Date: ${date}, Category: ${category}`);

                // Send the data to the server using fetch
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'another_action',
                        balance: balanceValue,
                        bank: bankValue,
                        subject: goal, // Changed from 'goal' to 'subject'
                        amount: amount,
                        date: date,
                        category: category,
                    }),

                })
                    .then(response => {
                        if (response.ok) {
                            // Handle successful response
                            document.getElementById('popup-Bank').style.display = 'none'; // Hide the popup
                            window.location.href = 'Savings.php?success=1'; // Redirect on success
                        } else {
                            console.error('Error:', response.statusText);
                        }
                    })
                    .catch(error => {
                        console.error('Request failed:', error);
                    });
            });
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        function closePopupBank() {
            document.getElementById('popup-Bank').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('popup')) {
                closePopup();
            }
        }

        function getCategoryOptions() {
            // Fetch categories dynamically from PHP
            const categories = [
                <?php
                if (isset($result3) && $result3->num_rows > 0) {
                    while ($row3 = $result3->fetch_assoc()) {
                        echo "'" . addslashes($row3['subject']) . "',"; // Corrected to fetch category
                    }
                    echo rtrim(',', ' '); // Remove the trailing comma
                } else {
                    echo "'No categories found'"; // Provide a default value
                }
                ?>
            ];

            return categories.map(subject => `<option value="${subject}">${subject}</option>`).join('');
        }

        function getGoalOptions() {
            // Fetch subjects dynamically from PHP
            const subjects = [
                <?php
                if (isset($result3) && $result3->num_rows > 0) {
                    while ($row3 = $result3->fetch_assoc()) {
                        echo "'" . addslashes($row3['subject']) . "',"; // Corrected to fetch subject
                    }
                    echo rtrim(',', ' '); // Remove the trailing comma
                } else {
                    echo "'No subjects found'"; // Provide a default value
                }
                ?>
            ];

            return subjects.map(subject => `<option value="${subject}">${subject}</option>`).join('');
        }
    </script>

    <?php include("modals/modal-allocate.php"); ?>
    <?php include("modals/modal-savings.php"); ?>
    <script src="../js/modal.js"></script>
</body>

</html>