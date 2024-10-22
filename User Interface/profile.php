<?php
// Include database connection
include('../connection/config.php');

// Fetch user data from the database (assuming user_id is stored in session)
session_start();
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
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
                    <p><a class="navbar-items" href="Dashboard.php">Home</a></p>
                </div>
            </div>

            <!-- Section for Expenses -->
            <div class="Expenses-Nav <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
                <div>
                    <img src="../Assets/Icons/expenses.svg" alt="Icon" width="50px">
                </div>
                <div>
                    <p><a class="navbar-items" href="Expenses.php">Expenses</a></p>
                </div>
            </div>

            <!-- Section for Income -->
            <div class="Travels-Nav <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
                <div>
                    <img src="../Assets/Icons/income.svg" alt="Icon" width="50px">
                </div>
                <div>
                    <p><a class="navbar-items" href="Income.php">Income</a></p>
                </div>
            </div>

            <!-- Section for Goals -->
            <div class="Travels-Nav <?php echo ($current_page == 'Goals.php') ? 'active' : ''; ?>" id="Nav_Button">
                <div>
                    <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
                </div>
                <div>
                    <p><a class="navbar-items" href="Goals.php">Goals</a></p>
                </div>
            </div>

            <!-- Section for Savings -->
            <div class="Approvals-Nav <?php echo ($current_page == 'Savings.php') ? 'active' : ''; ?>" id="Nav_Button">
                <div>
                    <img src="../Assets/Icons/reports.svg" alt="Icon" width="50px">
                </div>
                <div>
                    <p><a class="navbar-items" href="Savings.php">Savings</a></p>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="Settings-Nav <?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>" id="Nav_Button">
                <div>
                    <img src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
                </div>
                <div>
                    <p><a class="navbar-items" href="Settings.php">Settings</a></p>
                </div>
            </div>

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
                        <h1 class="header">Profile</h1>
                    </div>
                    <form class="pfp-form" action="profile.php" method="POST" enctype="multipart/form-data">
                        <div class="big-divider">
                            <div class="row-form">
                                <label for="first_name" class="form-labels">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

                                <label for="last_name" class="form-labels">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

                                <label for="email" class="form-labels">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                <label for="new_password" class="form-labels">New Password</label>
                                <input type="password" id="new_password" name="new_password">

                                <label for="confirm_password" class="form-labels">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        <div class="small-divider">
                            <div class="Profile_img">
                                <img src="https://picsum.photos/100/100" alt="" width="110">
                            </div>
                            <button class="change-pfp-button" type="submit" name="pfp">Change Profile Picture</button>
                        </div>
                    </form>
                    <div class="save-div">
                        <button>Save</button>
                    </div>
                </div>
            </div>
        </section>
    </div>



</body>

</html>

<?php
if (isset($_POST['save'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Handle profile picture upload
    if ($_FILES['profile_picture']['name']) {
        $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    } else {
        $profile_picture = $user['profile_picture'];
    }

    // Validate and update password if provided
    if (!empty($new_password) && $new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE user SET first_name = ?, last_name = ?, email = ?, profile_picture = ?, password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $profile_picture, $hashed_password, $user_id);
    } else {
        $query = "UPDATE user SET first_name = ?, last_name = ?, email = ?, profile_picture = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $profile_picture, $user_id);
    }

    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>