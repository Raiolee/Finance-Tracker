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
        <!-- Include the Navbar -->
        <?php include('navbar.php'); ?>

        <!-- Main Content -->
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
                        <h2 class="pfp-labels"><a href="Report.php">Submit a Report</a></h2>
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

    <script src="../js/notifications.js" defer></script>
</body>
</html>
