<?php
// Include database connection
include('../connection/config.php');

// Start session and fetch user data from session
session_start();
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'];

// Fetch user data, including the profile picture (user_dp) from the database
$query = "SELECT first_name, last_name, email, user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Prepare the profile picture for display
if ($user['user_dp']) {
    // Convert BLOB to base64-encoded image
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    // If no profile picture is found, use a placeholder image
    $profile_pic = 'https://picsum.photos/100/100';
}

$current_page = 'profile.php';
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
</head>

<body>
    <div class="container">
        <?php include('navbar.php'); ?>
        <!-- Main Section -->
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
                                <input class="var-input" type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

                                <label for="last_name" class="form-labels">Last Name</label>
                                <input class="var-input" type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

                                <label for="email" class="form-labels">Email</label>
                                <input class="var-input" readonly type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                                <label for="new_password" class="form-labels">New Password</label>
                                <input class="var-input" type="password" id="new_password" name="new_password">

                                <label for="confirm_password" class="form-labels">Confirm Password</label>
                                <input class="var-input" type="password" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        <div class="small-divider">
                            <div class="Profile_img">
                                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="110">
                            </div>
                            <!-- Change Profile Picture -->
                            <input type="file" name="new_pfp" accept="image/*" id="file-input" required>
                            <label for="file" class="file-label">Change Profile Picture</label>

                            <div class="btn-options">
                                <a href="Settings.php" class="link-btn"><button type="button" class="cancel">Cancel</button></a>
                                <button type="submit" name="save" class="save">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <!-- APIs -->
    <script src="../js/profile.js"></script>
</body>

</html>

<?php
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
}

// Include database connection
include('../connection/config.php');

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (isset($_POST['save'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Handle profile picture upload
    if ($_FILES['new_pfp']['name']) {
        // Get the binary content of the uploaded image
        $profile_picture = file_get_contents($_FILES['new_pfp']['tmp_name']);
    } else {
        // Keep the current profile picture if no new one is uploaded
        $profile_picture = $user['user_dp'];
    }

    // Validate and update password if provided
    if (!empty($new_password) && $new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE user SET first_name = ?, last_name = ?, email = ?, user_dp = ?, password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssbi", $first_name, $last_name, $email, $profile_picture, $hashed_password, $user_id);
    } else {
        $query = "UPDATE user SET first_name = ?, last_name = ?, email = ?, user_dp = ? WHERE user_id = ?";
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