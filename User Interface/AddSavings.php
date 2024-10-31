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
            // Redirect back to the savings page with success message
            header("Location: Savings.php?success=1");
            exit();
        } else {
            $error_message = "Error executing statement: {$stmt->error}";
        }
    } else {
        $error_message = "Error preparing statement: {$conn->error}";
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
    <div class="burger"  onclick="toggleMenu()">
        <div class="burger-outer">
            <div class="burger-icon">
                <img src="../Assets/Icons/magnifying-glass.svg" alt="" width="30px">
            </div>
            <div class="search-icon">
                <img src="../Assets/Icons/magnifying-glass.svg" alt="" width="30px">
            </div>
        </div>
        <hr class="bottom-line">
    </div>

    <div class="nav-bar" id="burger-nav-bar">
        <div class="Profile" id='mobile'>
            <div class="Profile_img">
                <img src="https://picsum.photos/100/100" alt="" width="110">
            </div>
        </div>

        <div class="user-name" id='mobile'>
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>

        <!-- Section for Dashboard -->
        <body>
   
    <div class="container">
        <div class="navbar">
            
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
            </div>
            <!-- Username Section -->
            <div class="user-name">
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="Expenses.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Expenses.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Goals.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Savings.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items" href="Savings.php">Savings</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'profile.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                <p><a class="navbar-items" href="Settings.php">Settings</a></p>
            </div>
            <!-- Logo in the navbar -->
            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
                </div>
            </div>
        </div>

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
                            <div class="Saving-Form-Format" id="Balance-Row">
                                <label for="Balance" class="Savings-Label">Balance*</label>
                                <input type="number" id="Balance" name="Balance" required>
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
                            <div class="Saving-Form-Format" id="Description-Row">
                                <label for="SavingsDescription" class="Savings-Label">Description</label>
                                <textarea id="SavingsDescription" name="Description" required></textarea>
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
         function showPopup(subject, balance, bank , category, date) {
            document.getElementById('popup-title').innerText = `Description`;
            document.getElementById('popup-description').innerText = `Bank: ${bank}\nAmount: ${balance}\nDate: ${date}\nSubject: ${subject}\nCategory: ${category}`;
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
