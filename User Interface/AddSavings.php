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

// Include database connection
include '../connection/config.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and validate form data
    $date = $_POST['Date'] ?? null;
    $bank = $_POST['Bank'] ?? null;
    $savingsamount = $_POST['SavingsAmount'] ?? null;
    $category = $_POST['SavingsCategory'] ?? null;
    $subject = $_POST['Subject'] ?? null;
    $description = $_POST['Description'] ?? null;

    // Check that required fields are not empty
    if ($date && $bank && $savingsamount && $category && $subject) {
        // Prepare and bind the SQL statement
        $sql = "INSERT INTO user_db.savings (user_id, date, bank, savings_amount, category, subject) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("issdss", $uid, $date, $bank, $savingsamount, $category, $subject);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect back to the savings page with success message
                header("Location: Savings.php?success=1");
                exit();
            } else {
                $error_message = "Error executing statement: " . $stmt->error;
            }
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savings</title>
    <link rel="stylesheet" href="../Styles/styles.css">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link rel="stylesheet" href="../Styles/mobilestyles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>New Saving</title>
</head>

<body>
<div class="container">
<?php include '../User Interface/navbar.php'; ?>

    <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Savings</h1>
                    </div>

                    <div id="newSavingForm" class="new-expense-form">
                        
                        <form id="SavingForm" method="post" action="AddSavings.php">
                            <div class="Saving-Form-Format" id="Date-Row">
                                <label for="SavingsDate" class="Savings-Label">Date*</label>
                                <input type="date" id="SavingsDate" name="Date" required>
                            </div>
                            <div class="Saving-Form-Format" id="Bank-Row">
                                <label for="Bank" class="Savings-Label">Bank*</label>
                                <input type="text" id="Bank" name="Bank" required>
                            </div>
                            <div class="Saving-Form-Format" id="SavingsAmount-Row">
                                <label for="SavingsAmount" class="Savings-Label">Amount*</label>
                                <input type="number" name="SavingsAmount" id="SavingsAmount" required>
                            </div>
                            <div class="Saving-Form-Format" id="Category-Row">
                                <label for="SavingsCategory" class="Savings-Label">Category*</label>
                                <select id="SavingsCategory" name="SavingsCategory" required>
                                    <option value="" disabled selected>Category</option>
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="Saving-Form-Format" id="Subject-Row">
                                <label for="SavingsSubject" class="Savings-Label">Subject*</label>
                                <input type="text" id="SavingsSubject" name="Subject" required>
                            </div>
                            <div class="Saving-Form-Format" id="Savings-Button-Row">
                                <div class="Savings-button-div-row">
                                    <button type="submit" class="button-savings">Save</button>
                                    <button type="button" class="button-savings" onclick="window.location.href='Savings.php'">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div id="popup" class="popup" style="display:none;">
                <div class="popup-content">
                    <h2 id="popup-title"></h2>
                    <p id="popup-description"></p>
                    <div class="popup-buttons">
                        <button id="cancel-btn" onclick="closePopup()">Close</button>
                    </div>
                </div>
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

        function toggleMenu()
        {
            const menu = document.getElementById('burger-nav-bar');
            menu.classList.toggle('active');

        if(menu.classList.contains('active'))
        {
            menu.style.display = 'none';
        } else{
            menu.style.display = 'block';
        }
        }
    </script>

    <script>
         function showPopup(subject, savings_amount, bank , category, date) {
            document.getElementById('popup-title').innerText = `Description`;
            document.getElementById('popup-description').innerText = `Bank: ${bank}\nAmount: ${savings_amount}\nDate: ${date}\nSubject: ${subject}\nCategory: ${category}`;
            document.getElementById('popup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('popup')) {
                closePopup();
            }
        }
    </script> 
</div>
</body>
</html>
