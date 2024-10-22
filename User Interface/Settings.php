<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
}
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="navbar">
            <div class="Profile">
                <div class="Profile_img">
                    <img src="https://picsum.photos/100/100" alt="" width="110">
                </div>
            </div>

            <div class="user-name">
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="Expenses.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items" href="Savings.php">Savings</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                <p><a class="navbar-items" href="Settings.php">Settings</a></p>
            </div>

            <div class="Logo-Nav" id="Nav_Side">
                <div class="Penny_Logo">
                    <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
                </div>
            </div>
        </div>

        <!-- Settings Page -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Settings</h1>
                    </div>
                    <!-- Profile Button -->
                    <div class="buttons">
                        <h2 class="pfp-labels"><a href="profile.php">Profile</a></h2>
                    </div>
                    <!-- Notifications Button -->
                    <div class="buttons">
                        <h2 class="pfp-labels">Notifications</h2>
                    </div>
                    <!-- Back up and Sync Button -->
                    <div class="buttons">
                        <h2 class="pfp-labels">Back up and Sync</h2>
                    </div>
                    <!-- Help and Support Button -->
                    <div class="buttons">
                        <h2 class="pfp-labels">Help and Support</h2>
                    </div>

                </div>
                <!-- Sign Out Button -->
                <div class="sign-out">
                    <a href="../logout.php">
                        <button class="sign-out-button">Sign Out</button>
                    </a>
                </div>
            </div>
        </section>




</body>

</html>