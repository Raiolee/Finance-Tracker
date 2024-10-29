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

// Fetch existing savings for the income
$sql2 = "SELECT investment, total FROM user_db.income WHERE user_id = ?";
$stmt2 = $conn->prepare($sql2);

if ($stmt2) {
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/mobilestyles.scss">
    <style>
    </style>
</head>

<body>
    <!-- All content is stored in container -->
    <div class="container">
        <div class="burger" onclick="toggleMenu()">
            <div class="burger-outer">
                <div class="burger-icon">
                    <img src="../Assets/Icons/magnifying-glass.svg" alt="" width="30px">
                </div>
                <div class="search-icon">
                    <img src="../Assets/Icons/magnifying-glass.svg" alt="" width="30px">
                </div>
            </div>
            <hr class="bottom-line">
        </div> <!--Burger End-->

        <div class="navbar" id="burger-nav-bar">
            <!-- Profile Picture -->
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
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active-tab' : ''; ?>"
                id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Expenses.php') ? 'active-tab' : ''; ?>"
                id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="Expenses.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Income.php') ? 'active-tab' : ''; ?>" id="Nav_Button">
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
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'profile.php') ? 'active' : ''; ?>"
                id="Nav_Button">
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
        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Bank</h1>
                        <button class="New-Saving">New Bank</button>
                    </div>

                    <div class="Bank">
                        <table class="table-Bank">

                            <thead>
                                <tr>
                                    <th>Bank</th>
                                    <th>Balance</th>
                                    <th>Manage Savings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($result2) && $result2->num_rows > 0) {
                                    while ($row2 = $result2->fetch_assoc()) {
                                        // Use the correct variables from the current row
                                        echo "<tr>
                                                                    <td>" . htmlspecialchars($row2['investment']) . "</td>
                                                                    <td>" . htmlspecialchars($row2['total']) . "</td>
                                                                    <td class='mobile-data'>
                                                                        <button onclick=\"BankForm()\">Allocate</button>
                                                                    </td>
                                                                    </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No results found</td></tr>"; // Ensure column span matches the number of columns
                                }
                                ?>
                            </tbody>
                        </table><!--Table End-->

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
                    </div> <!-- Popup End -->
                    <div id="popup-Bank" class="popup" style="display: none;">
                        <div class="popup-content">
                            <h2 id="popup-title-Bank"></h2>
                            <p id="popup-description-Bank"></p>
                            <div class="popup-buttons">
                                <button id="cancel-btn" onclick="closePopupBank()">Close</button>
                            </div>
                        </div>
                    </div>





                    <div id="newSavingForm" class="new-expense-form" style="display:none;">
                        <h3>New Saving</h3>
                        <hr class="bottom-line">
                        <form id="SavingForm" method="post">
                            <div class="Saving-Form-Format" id="Date-Row">
                                <label for="SavingsDate" class="Savings-Label">Date</label>
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
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="Saving-Form-Format" id="Subject-Row">
                                <label for="SavingsSubject" class="Savings-Label">Subject</label>
                                <input type="text" id="SavingsSubject" name="Subject" required>
                            </div>
                            <div class="Saving-Form-Format" id="Description-Row">
                                <label for="SavingsDescription" class="Savings-Label">Description</label>
                                <textarea id="SavingsDescription" name="Description" required></textarea>
                            </div>
                            <div class="Saving-Form-Format" id="Savings-Button-Row">
                                <div class="Savings-button-div-row">
                                    <button type="submit" class="button-savings">Save</button>
                                    <button type="button" class="button-savings"
                                        onclick="closeExpenseForm()">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div> <!--Form End-->

                </div><!--Content End-->



            </div> <!--Main-Container End-->
        </section> <!--Section End-->

    </div>


    <!-- APIs (Put APIs below this comment)-->
    <script>


        document.getElementById('newSavingButton').addEventListener('click', function () {
            const rightContainer = document.getElementById('content-container');
            const form = document.getElementById('newSavingForm');

            rightContainer.style.display = 'none'; // Hide the right container
            form.style.display = 'block'; // Show the new saving form
        });

        function closeExpenseForm() {
            const rightContainer = document.getElementById('content-container'); // Corrected ID
            const form = document.getElementById('newSavingForm');

            form.style.display = 'none'; // Hide the new expense form
            rightContainer.style.display = 'block'; // Show the right container again
            clearForm(); // Clear the form fields
        }

        function clearForm() {
            document.getElementById('SavingForm').reset(); // Clear all form fields
        }

        function toggleMenu() {
            const menu = document.getElementById('burger-nav-bar');
            menu.classList.toggle('active');

            if (menu.classList.contains('active')) {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }
    </script>

    <script>
        function showPopup(subject, balance, bank, category, date) {
            document.getElementById('popup-title').innerText = `Description`;
            document.getElementById('popup-description').innerText = `Bank: ${bank}\nAmount: ${balance}\nDate: ${date}\nSubject: ${subject}\nCategory: ${category}`;
            document.getElementById('popup').style.display = 'block';
        }

        function BankForm() {
            document.getElementById('popup-title-Bank').innerText = `Manage Savings`;
            // Create the form HTML
            const formHTML = `
                <form id="bank-form">
                    <label for="bank">Select Bank:</label>
                    <select id="bank" name="bank">
                        <option value="bank1">Bank 1</option>
                        <option value="bank2">Bank 2</option>
                        <option value="bank3">Bank 3</option>
                    </select>
                    <br>
                    <label for="goal">Savings Goal:</label>
                    <input type="text" id="goal" name="goal" required>
                    <br>
                    <label for="amount">Amount:</label>
                    <input type="number" id="amount" name="amount" required>
                    <br>
                    <button type="submit">Submit</button>
                </form>
            `;

            // Set the innerHTML of the popup description
            document.getElementById('popup-description-Bank').innerHTML = formHTML;

            // Show the popup
            document.getElementById('popup-Bank').style.display = 'block';

            // Optionally, reset the form if it's already displayed
            document.getElementById('bank-form')?.reset();

            // // Attach an event listener to handle form submission
            // document.getElementById('bank-form').addEventListener('submit', function(event) {
            //     event.preventDefault(); // Prevent default form submission
            //     const bank = document.getElementById('bank').value;
            //     const goal = document.getElementById('goal').value;
            //     const amount = document.getElementById('amount').value;

            //     // Process the data as needed
            //     console.log(`Bank: ${bank}, Goal: ${goal}, Amount: ${amount}`);

            //     // Optionally hide the popup after submission
            //     document.getElementById('popup-Bank').style.display = 'none';
            // });
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
    </script>
</body>

</html>