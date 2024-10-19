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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
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
    <!-- Settings Page -->
    <section class="main-section">
        <div class="main-container">
            <div class="content">
                <div class="top-bar">
                    <h1 class="settings">Settings</h1>
                </div>
                <!-- Profile Button -->
                <div class="buttons">
                    <h2 class="pfp-labels">Profile</h2>

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
                <button class="sign-out-button"><a href=""></a>Sign Out</button>
            </div>
        </div>
    </section>




</body>

</html>