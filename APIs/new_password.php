<?php
session_start();
require_once "../connection/config.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_GET['email'] ?? '';
$alertMessage = '';
$otpVerified = false;

// Verify OTP if POST request for OTP verification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['otp'])) {
    $otp = $_POST['otp'];

    // Check if OTP is set in the session
    if (isset($_SESSION['otp']) && $otp === $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true; // Set session variable to indicate OTP is verified
        $alertMessage = "<div class='alert alert-success'>OTP verified successfully! Please create a new password.</div>";
    } else {
        $alertMessage = "<div class='alert alert-danger'>Invalid OTP. Please try again.</div>";
    }
}

// Create new password logic
if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified'] && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword) || empty($confirmPassword)) {
        $alertMessage = "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif (strlen($newPassword) < 8) {
        $alertMessage = "<div class='alert alert-danger'>Password must be at least 8 characters long.</div>";
    } elseif ($newPassword !== $confirmPassword) {
        $alertMessage = "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        // Hash the new password
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        if ($stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?")) {
            $stmt->bind_param("ss", $passwordHash, $email);
            if ($stmt->execute()) {
                $alertMessage = "<div class='alert alert-success'>Password created successfully! Redirecting to login page...</div>";
                // Redirect to login page after 3 seconds
                $alertMessage .= "<script>
                    setTimeout(function() {
                        window.location.href = '../login.php';
                    }, 3000);
                </script>";
            } else {
                $alertMessage = "<div class='alert alert-danger'>Error creating password. Please try again.</div>";
            }
            $stmt->close();
        } else {
            $alertMessage = "<div class='alert alert-danger'>Database error: Could not prepare statement.</div>";
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>Create New Password</title>
    <link rel="stylesheet" href="../Styles/non-user.css">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center" style="height: 100vh; margin: 0;">
    <div class="container bg-secondary p-4 rounded shadow text-center" style="max-width: 400px;">
        <?php if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']): ?>
            <h2 class="mb-3">Verify OTP</h2>
            <?php if ($alertMessage): ?>
                <div class="mb-3">
                    <?= $alertMessage ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <input type="text" name="otp" class="form-control bg-dark text-light" placeholder="Enter OTP" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Verify OTP</button>
            </form>
        <?php else: ?>
            <h2 class="mb-3">Create New Password</h2>
            <?php if ($alertMessage): ?>
                <div class="mb-3">
                    <?= $alertMessage ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <input type="password" name="new_password" class="form-control bg-dark text-light" placeholder="New Password" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="confirm_password" class="form-control bg-dark text-light" placeholder="Confirm New Password" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Create Password</button>
            </form>
        <?php endif; ?>
    </div>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
