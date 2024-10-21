<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

$uid = $_SESSION["user_id"];
$username = $_SESSION["name"] ?? 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);

// Include database connection
include '../connection/config.php';

// Check if the connection was successful
if ($conn->connect_error) {
    die(sprintf("Connection failed: %s", $conn->connect_error));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $date = $_POST['Date'];
    $bank = $_POST['Bank'];
    $balance = $_POST['Balance'];
    $category = $_POST['SavingsCategory'];
    $subject = $_POST['Subject'];
    $description = $_POST['Description'];

    // Prepare and bind the SQL statement
    $sql = "INSERT INTO user_db.savings (user_id, date, bank, balance, category, subject, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("issdsss", $uid, $date, $bank, $balance, $category, $subject, $description);
        
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
}

// Fetch existing savings for the user
$sql = "SELECT subject, balance, bank, category, date FROM user_db.savings WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $error_message = "Error preparing statement: {$conn->error}";
}
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
                <div id="inner-container">
                    <div class="Top-container-Approval">
                        <div class="Left-Top">
                            <p>Savings</p>
                        </div>
                        <div class="Right-Top"> 
                            <button class="New-Saving" id="newSavingButton">+ New Saving</button>
                        </div>
                    </div>
                    <div class="Lower-container">
                        <hr class="bottom-line">
                    </div>

                    <div class="Lower-container">
                        <table class="table-approval">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Balance</th>
                                    <th>Bank</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($result) && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>" . htmlspecialchars($row['subject']) . "</td>
                                                <td>" . htmlspecialchars($row['balance']) . "</td>
                                                <td>" . htmlspecialchars($row['bank']) . "</td>
                                                <td>" . htmlspecialchars($row['category']) . "</td>
                                                <td>" . htmlspecialchars($row['date']) . "</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No results found</td></tr>";
                                }
                                ?>
                            </tbody>    
                        </table>
                    </div>
                </div>

                <div id="newSavingForm" class="new-expense-form" style="display:none;">
                    <h3>New Saving</h3>
                    <hr class="bottom-line">
                    <form id="SavingForm" method="post">
                        <div class="Saving-Form-Format" id="Date-Row">
                            <label for="Date" class="Savings-Label">Date</label>
                            <input type="date" id="SavingsDate" name="Date" required>
                        </div>
                        <div class="Saving-Form-Format" id="Bank-Row">
                            <label for="Bank" class="Savings-Label">Bank</label>
                            <input type="text" id="Bank" name="Bank" required>
                        </div>
                        <div class="Saving-Form-Format" id="Balance-Row">
                            <label for="Balance" class="Savings-Label">Balance</label>
                            <input type="number" id="Balance" name="Balance" required>
                        </div>
                        <div class="Saving-Form-Format" id="Category-Row">
                            <label for="SavingsCategory" class="Savings-Label">Category</label>
                            <select id="SavingsCategory" name="SavingsCategory" required>
                                <option value="" disabled selected>Category</option>
                                <option value="Type1">Daily</option>
                                <option value="Type2">Weekly</option>
                                <option value="Type3">Monthly</option>
                                <option value="Type4">Yearly</option>
                            </select>
                        </div>
                        <div class="Saving-Form-Format" id="Subject-Row">
                            <label for="Subject" class="Savings-Label">Subject</label>
                            <input type="text" id="SavingsSubject" name="Subject" required>
                        </div>
                        <div class="Saving-Form-Format" id="Description-Row">
                            <label for="Description" class="Savings-Label">Description</label>
                            <textarea id="SavingsDescription" name="Description" required></textarea>
                        </div>
                        <div class="Saving-Form-Format" id="Savings-Button-Row">
                            <div class="Savings-button-div-row">
                                <button type="submit" class="button-savings">Save</button>
                                <button type="button" class="button-savings" onclick="closeExpenseForm()">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
            </div> <!-- Closing Inner-container -->
        </div> <!-- Closing right-container -->
    </div>

    <script>
        document.getElementById('newSavingButton').addEventListener('click', function() {
            const rightContainer = document.getElementById('inner-container');
            const form = document.getElementById('newSavingForm');

            rightContainer.style.display = 'none'; // Hide the right container
            form.style.display = 'block'; // Show the new saving form
        });

        function closeExpenseForm() {
            const rightContainer = document.getElementById('inner-container');
            const form = document.getElementById('newSavingForm');

            form.style.display = 'none'; // Hide the new expense form
            rightContainer.style.display = 'block'; // Show the right container again
            clearForm(); // Clear the form fields
        }

        function clearForm() {
            document.getElementById('SavingForm').reset(); // Clear all form fields
        }
    </script>

</body>

</html>