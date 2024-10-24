<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
}
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch user data from the session
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Include database connection
include('../connection/config.php');

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <script src="../User Interface/notifications.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="navbar">
            <div class="Profile">
                <div class="Profile_img">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                </div>
            </div>

            <div class="user-name">
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>

            <!-- Home Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/home.svg" alt="Icon">
                <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
            </div>

            <!-- Expenses Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/expenses.svg" alt="Icon">
                <p><a class="navbar-items" href="Expenses.php">Expenses</a></p>
            </div>

            <!-- Income Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/income.svg" alt="Icon">
                <p><a class="navbar-items" href="Income.php">Income</a></p>
            </div>

            <!-- Goal Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/approvals.svg" alt="Icon">
                <p><a class="navbar-items" href="Goals.php">Goals</a></p>
            </div>

            <!-- Savings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
                <img class="navbar-icon" src="../Assets/Icons/reports.svg" alt="Icon">
                <p><a class="navbar-items" href="Savings.php">Savings</a></p>
            </div>

            <!-- Settings Nav Item -->
            <div class="navbar-div <?php echo ($current_page == 'Settings.php' || $current_page == 'Profile.php') ? 'active' : ''; ?>" id="Nav_Button">
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
                        <div class="toggle-notification">
                            <input type="checkbox" id="notificationToggle" class="toggle-checkbox">
                            <label for="notificationToggle" class="toggle-label">
                                <span class="toggle-button"></span>
                            </label>
                        </div>
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
    </div>



</body>

</html>